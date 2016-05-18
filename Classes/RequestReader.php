<?php
namespace NamelessCoder\Rpc;

use NamelessCoder\Rpc\Security\Token;

/**
 * Class RequestReader
 */
class RequestReader {

    const PARAM_TOKEN = 'token';
    const PARAM_TASK = 'task';
    const PARAM_ARGUMENTS = 'arguments';

    /**
     * @return Request
     */
    public function decodeRequest() {
        $parameters = $this->fetchRequestParameters();
        return $this->createRequestInstance()
            ->setToken(new Token($parameters[static::PARAM_TOKEN]))
            ->setTask($parameters[static::PARAM_TASK])
            ->setArguments($parameters[static::PARAM_ARGUMENTS]);
    }

    /**
     * @return Request
     */
    protected function createRequestInstance() {
        return new Request();
    }

    /**
     * @return array
     */
    protected function fetchRequestParameters() {
        $body = file_get_contents('php://input');
        syslog(LOG_WARNING, $body);
        #echo 'blabla!';
        #var_dump($body);
        #exit();
        $decodedBody = json_decode($body, JSON_OBJECT_AS_ARRAY);
        return (array) $decodedBody;
    }

}
