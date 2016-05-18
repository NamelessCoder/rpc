<?php
namespace NamelessCoder\Rpc\Implementation\Task;

use NamelessCoder\Rpc\ClientComponent\Report\SuccessReport;
use NamelessCoder\Rpc\Manager\TaskManager;
use NamelessCoder\Rpc\Request;
use NamelessCoder\Rpc\Response;
use NamelessCoder\Rpc\Task\AbstractTask;
use NamelessCoder\Rpc\Task\TaskInterface;

/**
 * Class ListTask
 */
class ListTask extends AbstractTask implements TaskInterface {

    /**
     * ListTask constructor.
     * @param string $id
     */
    public function __construct($id) {
        parent::__construct($id);
        $this->getTaskConfiguration()
            ->setLabel('List')
            ->setDescription('Lists all tasks available to the user');
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function processRequest(Request $request) {
        $taskIds = TaskManager::getInstance()->getTaskIdsByToken($request->getToken());
        $taskDescriptions = array_map(array($this, 'getDescriptionForTask'), $taskIds);
        $response = new Response();
        $response->setPayload(array_combine($taskIds, $taskDescriptions));
        $response->setReport(new SuccessReport('Tasks', 'Select task from list'));
        return $response;
    }

    /**
     * @param string $taskId
     * @return string
     */
    protected function getDescriptionForTask($taskId) {
        $taskConfiguration = TaskManager::getInstance()->getTaskById($taskId)->getTaskConfiguration();
        return sprintf('%s: %s', $taskConfiguration->getLabel(), $taskConfiguration->getDescription());
    }

}
