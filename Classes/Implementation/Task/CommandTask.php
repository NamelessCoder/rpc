<?php
namespace NamelessCoder\Rpc\Implementation\Task;

use NamelessCoder\Rpc\ClientComponent\Field\FieldInterface;
use NamelessCoder\Rpc\ClientComponent\Field\TextField;
use NamelessCoder\Rpc\ClientComponent\Field\ToggleField;
use NamelessCoder\Rpc\ClientComponent\Report\ErrorReport;
use NamelessCoder\Rpc\ClientComponent\Report\SuccessReport;
use NamelessCoder\Rpc\Manager\TaskManager;
use NamelessCoder\Rpc\Request;
use NamelessCoder\Rpc\Response;
use NamelessCoder\Rpc\Task\AbstractTask;
use NamelessCoder\Rpc\Task\TaskInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ControllerInterface;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Property\TypeConverter\CoreTypeConverter;
use TYPO3\CMS\Extbase\Property\TypeConverterInterface;
use TYPO3\CMS\Extbase\Reflection\DocCommentParser;
use TYPO3\CMS\Extbase\Reflection\MethodReflection;
use TYPO3\CMS\Extbase\Reflection\ParameterReflection;

/**
 * Class CommandTask
 *
 * Task implementation for a single command of a
 * single command controller.
 */
class CommandTask extends AbstractTask implements TaskInterface {

    /**
     * @var array
     */
    protected $fieldTypes = [];

    /**
     * @param string $commandControllerClass
     * @param string $actionName
     * @return CommandTask
     */
    public static function registerForCommand($commandControllerClass, $actionName) {
        $cutoff = strrpos($commandControllerClass, '\\') + 1;
        $controllerName = substr($commandControllerClass, $cutoff, strlen($commandControllerClass) - $cutoff - 17);
        $task = new CommandTask($commandControllerClass . '->' . $actionName);
        $task->getTaskConfiguration()
            ->setId($commandControllerClass . '->' . $actionName)
            ->setLabel('Command')
            ->setDescription($controllerName . '->' . $actionName);
        TaskManager::getInstance()->addTask($task);
        return $task;
    }

    /**
     * CommandTask constructor.
     * @param string $id
     */
    public function __construct($id) {
        parent::__construct($id);
        $this->getTaskConfiguration()
            ->setLabel('Command')
            ->setDescription('Arbitrary CommandController');
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function processRequest(Request $request) {
        list ($controllerClassName, $actionName) = explode('->', $request->getTask());
        /** @var ControllerInterface $controller */
        $controller = GeneralUtility::makeInstance(ObjectManager::class)->get($controllerClassName);
        $methodName = $actionName . 'Command';
        $response = new Response();
        $response->getReport()->setTitle('Enter command arguments')->setContent('Fill in the arguments to execute');
        $expectedCommandArguments = $this->getExpectedArgumentsForMethod($controller, $methodName);
        $commandArguments = $this->buildArgumentArray($controller, $methodName, $request->getArguments());
        foreach ($expectedCommandArguments as $expectedCommandArgument) {
            $expectedCommandArgumentName = $expectedCommandArgument->getName();
            list ($expectedArgumentType, $expectedArgumentDescription) = $this->parseDocCommentOfArgument($expectedCommandArgument, $expectedCommandArgumentName);
            $fieldType = $this->getFieldForArgumentNameAndType(
                $expectedCommandArgumentName,
                $expectedArgumentType
            );
            /** @var FieldInterface $field */
            $field = new $fieldType();
            $field->setName($expectedCommandArgumentName);
            if ($expectedArgumentDescription) {
                $field->setLabel($expectedArgumentDescription);
            } else {
                $field->setLabel('Argument: ' . $expectedCommandArgumentName);
            }
            $response->getSheet()->addField($field);
        }

        if (count($expectedCommandArguments) > count($request->getArguments())) {
            // We lack some arguments - return the response now so the client can fill those values.
            // Present a small report outputting the doc comment
            $methodReflection = new \ReflectionMethod($controller, $methodName);
            $docCommentParser = new DocCommentParser();
            $docCommentParser->parseDocComment($methodReflection->getDocComment());
            $response->setReport(
                new SuccessReport(
                    $this->getTaskConfiguration()->getDescription(),
                    $docCommentParser->getDescription()
                )
            );
            return $response;
        }
        
        try {
            $temporaryResponse = new \TYPO3\CMS\Extbase\Mvc\Cli\Response();
            $responseProperty = new \ReflectionProperty($controller, 'response');
            $responseProperty->setAccessible(TRUE);
            $responseProperty->setValue($controller, $temporaryResponse);

            // Catch output of any manual calls to outputLine() or Response->send() from controller action
            ob_start();
            call_user_func_array(
                [$controller, $methodName],
                array_values($commandArguments)
            );
            $output = ob_get_clean();

            // Append any finish output accumulated in Response; this normally gets output after finishing a Request
            // but we bypass the Request and call the command methods directly - so we need to fetch this output.
            $output .= $temporaryResponse->getContent();

            $response->setPayload($output);
            $response->setReport(
                new SuccessReport(
                    sprintf('CommandController executed (exit code: %d)', $temporaryResponse->getExitCode()),
                    empty($output) ? 'There was no output from the command' : 'Output is attached as response payload'
                )
            )->complete();
        } catch (\Exception $error) {
            $response->setReport(
                new ErrorReport(
                    sprintf(
                        'CommandController error (%d)',
                        $error->getCode()
                    ),
                    $error->getMessage()
                )
            );
        }
        return $response;
    }

    /**
     * @param string $fieldType
     * @param string $argumentName
     * @return $this
     */
    public function setFieldTypeForArgument($fieldType, $argumentName) {
        $this->fieldTypes[$argumentName] = $fieldType;
        return $this;
    }

    /**
     * @param ParameterReflection $parameterReflection
     * @param string $argumentName
     * @return array
     */
    protected function parseDocCommentOfArgument(ParameterReflection $parameterReflection, $argumentName) {
        $docCommentParser = new DocCommentParser();
        $docCommentParser->parseDocComment(
            $parameterReflection->getDeclaringFunction()->getDocComment()
        );
        $parameterAnnotations = $docCommentParser->getTagValues('param');
        foreach ($parameterAnnotations as $parameterAnnotation) {
            list ($type, $name, $description) = explode(' ', $parameterAnnotation, 3);
            if ($name === '$' . $argumentName) {
                return [$type, $description];
            }
        }

        return ['mixed', 'Unknown argument - may not be supported by command controller!'];
    }

    /**
     * @param ControllerInterface $controller
     * @param string $methodName
     * @param array $arguments
     * @return array
     */
    protected function buildArgumentArray(ControllerInterface $controller, $methodName, array $arguments) {
        $sorted = array();
        foreach ($this->getExpectedArgumentsForMethod($controller, $methodName) as $parameterReflection) {
            $argumentName = $parameterReflection->getName();
            if (isset($arguments[$argumentName])) {
                list ($type, ) = $this->parseDocCommentOfArgument($parameterReflection, $argumentName);
                $sorted[$argumentName] = $this->castValueToType($arguments[$argumentName], $type);
            } elseif ($parameterReflection->isOptional()) {
                $sorted[$argumentName] = $parameterReflection->getDefaultValue();
            }
        }
        return $sorted;
    }

    /**
     * @param mixed $value
     * @param string $type
     * @return mixed
     */
    protected function castValueToType($value, $type) {
        $type = strtolower($type);
        if ($type === 'boolean' || $type === 'bool') {
            return (boolean) $value;
        }
        if ($type === 'array') {
            if (is_string($value)) {
                return explode(',', $value);
            }
            return (array) $value;
        }
        if ($type === 'int' || $type === 'integer') {
            return (integer) $value;
        }
        if ($type === 'float' || $type === 'double') {
            return (float) $value;
        }
        if ($type === 'string') {
            return (string) $value;
        }

        // Non-scalar value type annotation; convert to array then convert to that type. Failures pass through!
        if (strpos($type, '[]') !== FALSE) {
            $value = $this->castValueToType($value, 'array');
            $value = $this->convertToFrameworkObject($value, substr($type, 0, -2));
        }

        // Final attempt to convert to a class using type converters
        try {
            return $this->convertToFrameworkObject($value, $type);
        } catch (\RuntimeException $error) {
            return $value;
        }
    }

    /**
     * @param mixed $value
     * @param string $type
     * @return mixed
     * @throws \RuntimeException
     */
    protected function convertToFrameworkObject($value, $type) {
        $sourceType = gettype($value);
        foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['extbase']['typeConverters'] as $converterClass) {
            /** @var TypeConverterInterface $converter */
            $converter = GeneralUtility::makeInstance(ObjectManager::class)->get($converterClass);
            if ($converter->canConvertFrom($sourceType, $type)) {
                return $converter->convertFrom($sourceType, $type);
            }
        }
        throw new \RuntimeException(
            sprintf(
                'Unable to convert from %s to %s - no capable type converters found!',
                $sourceType,
                $type
            )
        );
    }

    /**
     * @param ControllerInterface $controller
     * @param string $methodName
     * @return ParameterReflection[]
     */
    protected function getExpectedArgumentsForMethod(ControllerInterface $controller, $methodName) {
        $reflection = new MethodReflection($controller, $methodName);
        return $reflection->getParameters();
    }

    /**
     * @param string $argumentName
     * @param string $argumentType
     * @return FieldInterface
     */
    protected function getFieldForArgumentNameAndType($argumentName, $argumentType) {
        if (isset($this->fieldTypes[$argumentName])) {
            $fieldClassName = $this->fieldTypes[$argumentName];
        } elseif ($argumentType === 'bool' || $argumentType === 'boolean') {
            $fieldClassName = ToggleField::class;
        } else {
            $fieldClassName = TextField::class;
        }
        /** @var FieldInterface $field */
        $field = new $fieldClassName();
        $field->setName($argumentName)->setLabel('Argument: ' . $argumentName);
        return $field;
    }

}
