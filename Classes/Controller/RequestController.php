<?php
namespace NamelessCoder\Rpc\Controller;

use NamelessCoder\Rpc\ClientComponent\Report\ErrorReport;
use NamelessCoder\Rpc\ClientComponent\Report\SuccessReport;
use NamelessCoder\Rpc\Manager\ClientManager;
use NamelessCoder\Rpc\Manager\ClientManagerInterface;
use NamelessCoder\Rpc\Manager\TaskManager;
use NamelessCoder\Rpc\Manager\TaskManagerInterface;
use NamelessCoder\Rpc\Request;
use NamelessCoder\Rpc\RequestReader;
use NamelessCoder\Rpc\Response;

/**
 * Class RequestController
 */
class RequestController {

    /**
     * @var ClientManagerInterface
     */
    protected $clientManager;

    /**
     * @var TaskManagerInterface
     */
    protected $taskManager;

    /**
     * @var RequestReader
     */
    protected $requestReader;

    /**
     * RequestController constructor.
     *
     * @param ClientManager $clientManager Mandatory argument - instance or subclass of ClientManager
     * @param TaskManager|NULL $taskManager Optional custom TaskManager, if not provided the internal one is used
     * @param RequestReader|NULL $requestReader Optional RequestReader used for auto-resolving Requests from compatible clients
     */
    public function __construct(
        ClientManager $clientManager,
        TaskManager $taskManager = NULL,
        RequestReader $requestReader = NULL
    ) {
        $this->clientManager = $clientManager;
        $this->taskManager = $taskManager ? : new TaskManager();
        $this->requestReader = $requestReader ? : new RequestReader();
    }

    /**
     * Resolves the Request and processes it automatically
     * using the RequestReader and managers provided in the
     * constructor method.
     *
     * @return Response
     */
    public function autoProcessRequest() {
        return $this->processRequest($this->requestReader->decodeRequest());
    }
    
    /**
     * @param Request $request
     * @return Response
     */
    public function processRequest(Request $request) {
        $taskId = $request->getTask();
        $token = $request->getToken();
        $task = $this->taskManager->getTaskById($taskId);
        if (!$task) {
            return $this->createErrorResponse(
                'Invalid task ID',
                sprintf(
                    'The provided task ID - %s - is not known on the system. This is a possible intrusion attempt ' .
                    'and has been reported to the system administrator along with a source IP',
                    $request->getTask()
                ),
                $request
            );
        }

        if ($token->isEmpty()) {
            $createdToken = $this->clientManager->issueNewToken();
            $tokenCreatedResponse = new Response();
            $tokenCreatedResponse->setToken($createdToken);
            $tokenCreatedResponse->setReport(
                new SuccessReport(
                    'Token received', 'Your client has received a token - if you are the system administrator ' . PHP_EOL .
                    'you can now grant access privileges by editing the token ACL on the server. ' . PHP_EOL . PHP_EOL .
                    'If you are not the administrator, ask him/her to grant privileges for you. ' . PHP_EOL . PHP_EOL .
                    'Your token: ' . $createdToken->getTokenValue()
                )
            );
            $tokenCreatedResponse->complete();
            return $tokenCreatedResponse;
        }
        if (!$this->clientManager->validateToken($request->getToken())) {
            return $this->createErrorResponse(
                'Token validation error',
                'An invalid token was provided or the token does have any access permissions granted.' . PHP_EOL . PHP_EOL .
                'If you are the administrator make sure the token has access. If you are not the ' . PHP_EOL .
                'administrator ask him/her to grant privileges for you.' . PHP_EOL . PHP_EOL .
                'Your token: ' . $request->getToken(),
                $request
            );
        }
        return $task->processRequest($request)
            ->setTask($taskId)
            ->setToken($request->getToken());
    }

    /**
     * @param string $title
     * @param string $content
     * @param Request $request
     * @return Response
     */
    protected function createErrorResponse($title, $content, Request $request) {
        $errorReport = new ErrorReport($title, $content);
        $response = new Response();
        $response->setReport($errorReport);
        $response->setToken($request->getToken());
        $response->complete();
        return $response;
    }

}
