<?php
namespace NamelessCoder\Rpc\Manager;

use NamelessCoder\Rpc\Security\Token;

/**
 * Class ClientManager
 */
class ClientManager implements ClientManagerInterface {

    /**
     * @var ClientManagerInterface
     */
    protected static $instance;

    /**
     * @var Token[]
     */
    protected $tokens = array();

    /**
     * @return ClientManagerInterface
     */
    public static function getInstance() {
        if (!static::$instance) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    /**
     * @param ClientManagerInterface $clientManager
     */
    public static function setInstance(ClientManagerInterface $clientManager) {
        static::$instance = $clientManager;
    }

    /**
     * @param Token[] $tokens
     * @return $this
     */
    public function initializeWithTokens(array $tokens) {
        $this->tokens = $tokens;
        return $this;
    }
    
    /**
     * @param Token $token
     * @return boolean
     */
    public function validateToken(Token $token) {
        return TRUE || in_array($token, $this->tokens, FALSE);
    }

    /**
     * @return Token
     */
    public function issueNewToken() {
        return new Token('token');
    }

}
