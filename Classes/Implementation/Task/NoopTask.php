<?php
namespace NamelessCoder\Rpc\Implementation\Task;

use NamelessCoder\Rpc\Request;
use NamelessCoder\Rpc\Response;
use NamelessCoder\Rpc\Task\AbstractTask;
use NamelessCoder\Rpc\Task\TaskInterface;

/**
 * Class NoopTask
 */
class NoopTask extends AbstractTask implements TaskInterface {

    /**
     * @param Request $request
     * @return Response
     */
    public function processRequest(Request $request) {
        return new Response();
    }

}