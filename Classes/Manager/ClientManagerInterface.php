<?php
namespace NamelessCoder\Rpc\Manager;

use NamelessCoder\Rpc\Security\Token;

/**
 * Interface ClientManagerInterface
 */
interface ClientManagerInterface {

    /**
     * @return Token
     */
    public function issueNewToken();

    /**
     * @param Token $token
     * @return boolean
     */
    public function validateToken(Token $token);

    /**
     * @param Token[] $tokens
     * @return $this
     */
    public function initializeWithTokens(array $tokens);
}