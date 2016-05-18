<?php
namespace NamelessCoder\Rpc;

use NamelessCoder\Rpc\Security\Token;

/**
 * Class Request
 */
class Request {

    /**
     * Token assigned to client by (first) Response from
     * system. Token is manually verified and has access
     * restrictions assigned on server.
     *
     * If not set the request is considered an initial
     * handshake request and a new (inactive) token is
     * returned in the response.
     *
     * @var Token
     */
    protected $token;

    /**
     * ID of a Task to be performed. If empty, a list of
     * all available tasks is returned.
     *
     * @var string
     */
    protected $task;

    /**
     * Arguments which will be passed to the Task for
     * execution. May be filled over several transactions
     * depending on how the Task operates - see README.
     *
     * @var array
     */
    protected $arguments = array();

    /**
     * @return Token
     */
    public function getToken() {
        return $this->token;
    }

    /**
     * @param Token $token
     * @return $this
     */
    public function setToken(Token $token) {
        $this->token = $token;
        return $this;
    }

    /**
     * @return string
     */
    public function getTask() {
        return $this->task;
    }

    /**
     * @param string $task
     * @return $this
     */
    public function setTask($task) {
        $this->task = $task;
        return $this;
    }

    /**
     * @return array
     */
    public function getArguments() {
        return $this->arguments;
    }

    /**
     * @param array $arguments
     * @return $this
     */
    public function setArguments($arguments) {
        $this->arguments = $arguments;
        return $this;
    }

    /**
     * @return string
     */
    public function compile() {
        return json_encode(array(
            'token' => $this->token->getTokenValue(),
            'task' => $this->task,
            'arguments' => $this->arguments
        ), JSON_HEX_TAG | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_HEX_APOS);
    }

}

