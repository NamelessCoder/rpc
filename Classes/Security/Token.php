<?php
namespace NamelessCoder\Rpc\Security;

/**
 * Class Token
 */
class Token {

    /**
     * @var string
     */
    protected $token;

    /**
     * Token constructor.
     * @param string $token
     */
    public function __construct($token) {
        $this->token = (string) $token;
    }

    /**
     * @return string
     */
    public function __toString() {
        return $this->token;
    }

    /**
     * @return boolean
     */
    public function isEmpty() {
        return empty($this->token);
    }

    /**
     * @return string
     */
    public function getTokenValue() {
        return $this->token;
    }
}
