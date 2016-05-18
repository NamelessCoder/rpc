<?php
namespace NamelessCoder\Rpc\Task;

use NamelessCoder\Rpc\Request;
use NamelessCoder\Rpc\Response;

/**
 * Interface TaskInterface
 */
interface TaskInterface {

    /**
     * @return TaskConfiguration
     */
    public function getTaskConfiguration();

    /**
     * @param Request $request
     * @return Response
     */
    public function processRequest(Request $request);

}
