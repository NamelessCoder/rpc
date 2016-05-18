<?php
namespace NamelessCoder\Rpc\Implementation\Task;

use NamelessCoder\Rpc\ClientComponent\Field\PopupField;
use NamelessCoder\Rpc\ClientComponent\Field\SelectField;
use NamelessCoder\Rpc\ClientComponent\Field\TextArea;
use NamelessCoder\Rpc\ClientComponent\Field\TextField;
use NamelessCoder\Rpc\ClientComponent\Field\TextLabel;
use NamelessCoder\Rpc\ClientComponent\Field\ToggleField;
use NamelessCoder\Rpc\ClientComponent\Report\StepReport;
use NamelessCoder\Rpc\ClientComponent\Report\SuccessReport;
use NamelessCoder\Rpc\ClientComponent\Sheet;
use NamelessCoder\Rpc\Request;
use NamelessCoder\Rpc\Response;
use NamelessCoder\Rpc\Task\AbstractTask;
use NamelessCoder\Rpc\Task\TaskInterface;

/**
 * Class DemoTask
 */
class DemoTask extends AbstractTask implements TaskInterface {

    /**
     * @var array
     */
    protected $demos = array('types', 'steps', 'validation');

    /**
     * ListTask constructor.
     * @param string $id
     */
    public function __construct($id) {
        parent::__construct($id);
        $this->getTaskConfiguration()
            ->setLabel('Demo')
            ->setDescription('Demo of form fields');
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function processRequest(Request $request) {
        $arguments = $request->getArguments();

        $response = new Response();
        $sheet = new Sheet();
        $response->setSheet($sheet);

        if (empty($arguments['demo']) || !in_array($arguments['demo'], $this->demos)) {
            return $this->fieldsForDemoSelection($response, $sheet, $arguments);
        } elseif ($arguments['demo'] === 'steps') {
            return $this->fieldsForStepsDemo($response, $sheet, $arguments);
        } elseif ($arguments['demo'] === 'types') {
            return $this->fieldsForTypesDemo($response, $sheet, $arguments);
        } elseif ($arguments['demo'] === 'validation') {
            return $this->fieldsForValidationDemo($response, $sheet, $arguments);
        }
    }

    /**
     * This demonstrates the different input types that
     * can be added to the response's Sheet and then
     * displayed to the RPC client for filling in values.
     *
     * @param Response $response
     * @param Sheet $sheet
     * @param array $arguments
     * @return Response
     */
    protected function fieldsForTypesDemo(Response $response, Sheet $sheet, array $arguments) {
        // for this demo our only "is done?" marker is to check if any field is filled
        if (!empty($arguments['popup'])) {
            return $response->complete()
                ->setReport(new SuccessReport('Demo complete', 'You have finished the types demo'))
                ->setPayload($arguments);
        }
        $response->setReport(new SuccessReport('Types demonstration', 'Possible component types'));
        $textLabel = new TextLabel();
        $textLabel->setName('label')
            ->setValue('A simple text label which cannot be changed by the user');
        $textField = new TextField();
        $textField->setName('text')
            ->setLabel('A text input field for a single line of text')
            ->setValue(empty($arguments['text']) ? '' : $arguments['text']);
        $textArea = new TextArea();
        $textArea->setName('textarea')
            ->setLabel('A bigger text area for multiple lines (resize window to grow field size)')
            ->setValue('Some text');
        $richText = new TextArea();
        $richText->setName('richtext')
            ->setLabel('A bigger text area for multiple lines, with RTF support (resize window to grow field size)')
            ->setRichTextFormat(TRUE)
            ->setValue('Some text');
        $selectField = new SelectField();
        $selectField->setName('select')
            ->setLabel('A select field (with free typing capabilities)')
            ->setOptions(array('one', 'two', 'three'))
            ->setValue(empty($arguments['select']) ? 'two' : $arguments['select']);
        $toggleField = new ToggleField();
        $toggleField->setName('toggle')
            ->setLabel('A toggle field whose value is not sent unless toggled ON')
            ->setValue('yes')
            ->setOn(TRUE);
        $popupField = new PopupField();
        $popupField->setName('popup')
            ->setLabel('A pop-up menu field')
            ->setOptions(array('one', 'two', 'three'))
            ->setValue(empty($arguments['popup']) ? 'three' : $arguments['popup']);

        $sheet->addField($textLabel);
        $sheet->addField($textField);
        $sheet->addField($textArea);
        $sheet->addField($richText);
        $sheet->addField($selectField);
        $sheet->addField($toggleField);
        $sheet->addField($popupField);

        return $response;
    }

    /**
     * This demonstrates how to create multiple steps
     * in an RPC request/response cycle. This is the
     * most basic form of such a construct: the current
     * step is determined by whether the previous step's
     * variable is present in arguments. The RPC client
     * collects arguments as it goes along, filling in
     * more and more values to send with each request.
     *
     * This can be combined with error reporting where
     * the current step is determined by the validity of
     * the previous step's variable rather than just
     * checking if it is empty() as this example does,
     * you would implement actual checks and use the
     * setError() method on the component that has an
     * error, to report that to the client. And obviously
     * return the same step's form fields if that step
     * had any errors.
     *
     * The value of the previous step's form field gets
     * used as default value for the next step's field,
     * but this is not a normal use case. It is done to
     * also demonstrate that each step's form fields
     * can depend on the values the user entered in the
     * previous step - for example allowing a workflow
     * where user selects a folder then gets a selector
     * with files/records that exist in that folder only.
     *
     * @param Response $response
     * @param Sheet $sheet
     * @param array $arguments
     * @return Response
     */
    protected function fieldsForStepsDemo(Response $response, Sheet $sheet, array $arguments) {
        $response->setReport(new StepReport('Step five', 'Almost there!', 5, 5));
        $field = new TextField();
        $field->setLabel('Same field in all steps; value from previous step gets used as value for current step')
            ->setValue('Change me - I become the value of field in the next step');
        if ($arguments['step5']) {
            $response->setReport(new SuccessReport('Done!', 'You have finished all five steps of the steps demo'))
                ->setPayload($arguments)
                ->complete();
        } elseif ($arguments['step4']) {
            $response->setReport(new StepReport('Step five', 'Just one more!', 5, 5));
            $field->setName('step5')->setValue($arguments['step4']);
            $sheet->addField($field)->setSubmitButtonLabel('Finish this demo');
        } elseif ($arguments['step3']) {
            $response->setReport(new StepReport('Step four', 'Another two to go!', 4, 5));
            $field->setName('step4')->setValue($arguments['step3']);
            $sheet->addField($field)->setSubmitButtonLabel('Continue last step');
        } elseif ($arguments['step2']) {
            $response->setReport(new StepReport('Step five', 'Another three left...', 3, 5));
            $field->setName('step3')->setValue($arguments['step2']);
            $sheet->addField($field)->setSubmitButtonLabel('To step four');
        } elseif ($arguments['step1']) {
            $response->setReport(new StepReport('Step two', 'Second step...', 2, 5));
            $field->setName('step2')->setValue($arguments['step1']);
            $sheet->addField($field)->setSubmitButtonLabel('To step three');
        } else {
            $response->setReport(new StepReport('Step one', 'First step of five!', 1, 5));
            $field->setName('step1');
            $sheet->addField($field)->setSubmitButtonLabel('Start steps');
        }
        return $response;
    }

    /**
     * This demonstrates how to perform validation of
     * field values and return the same field to the
     * RPC client with an error message asking to
     * correct the mistake.
     *
     * Although this demonstrates only a single field
     * the same procedure is possible with any field
     * type. To make it easier to complete and understand
     * this demo a single field is used.
     *
     * @param Response $response
     * @param Sheet $sheet
     * @param array $arguments
     * @return Response
     */
    protected function fieldsForValidationDemo(Response $response, Sheet $sheet, array $arguments) {

        if (isset($arguments['numeric'])) {
            $fieldOneValid = !preg_match('/[^0-9]+/i', $arguments['numeric']) && strlen((string) $arguments['numeric']) > 0;
        } else {
            $fieldOneValid = NULL;
        }

        if (!empty($arguments['mustcheck'])) {
            // Note here, that clients may exchange non-string values as strings, depending on their UI. Any HTML-based
            // UI will naturally cause string values, since the client will not have any knowledge how to cast the value.
            // This being a checkbox, the value type is either a boolean (OSX/iOS) or a string of exactly '1' (HTML)
            $fieldTwoValid = $arguments['mustcheck'] === TRUE || $arguments['mustcheck'] === '1';
        } elseif (isset($arguments['mustcheck'])) {
            $fieldTwoValid = FALSE;
        } else {
            $fieldTwoValid = NULL;
        }

        if ($fieldOneValid && $fieldTwoValid) {
            // both fields are validated - return early and set a success report, mark request as complete.
            $response->setReport(new SuccessReport('Validation demo completed', 'You have entered valid values - the demo is complete.'));
            $response->complete();
            return $response;
        }

        $response->setReport(new SuccessReport('Validation demo', 'This demo shows how field validation can be implemented'));

        $fieldOne = new TextField();
        $fieldOne->setName('numeric')
            ->setLabel('This field must only contain numbers. Enter some letters to see an error.')
            ->setValue(!isset($arguments['numeric']) ? : $arguments['numeric']);
        if (!$fieldOneValid && $fieldOneValid !== NULL) {
            $fieldOne->setError(strlen((string) $arguments['numeric']) > 0 ? 'Please enter only numbers' : 'Please enter a value');
        }

        $fieldTwo = new ToggleField();
        $fieldTwo->setName('mustcheck')
            ->setValue(TRUE)
            ->setOn($fieldTwoValid)
            ->setLabel('This field must be checked. Leave it unchecked to see an error');
        if (!$fieldTwoValid && $fieldTwoValid !== NULL) {
            $fieldTwo->setError('Please check this field!');
        }

        $sheet->addField($fieldOne);
        $sheet->addField($fieldTwo);
        return $response;
    }

    /**
     * @param Response $response
     * @param Sheet $sheet
     * @param array $arguments
     * @return Response
     */
    protected function fieldsForDemoSelection(Response $response, Sheet $sheet, array $arguments) {
        $response->setReport(new SuccessReport('Select demo', 'Select the demo you wish to view'));
        $selectField = new PopupField();
        $selectField->setName('demo')
            ->setLabel('Available demos')
            ->setOptions($this->demos)
            ->setValue('types');
        $sheet->addField($selectField);
        $sheet->setSubmitButtonLabel('Run demo');
        return $response;
    }

}
