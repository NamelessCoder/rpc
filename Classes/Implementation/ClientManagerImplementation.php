<?php
namespace NamelessCoder\Rpc\Implementation;

use NamelessCoder\Rpc\Manager\ClientManager;
use NamelessCoder\Rpc\Manager\ClientManagerInterface;
use NamelessCoder\Rpc\Security\Token;
use TYPO3\CMS\Core\Database\DatabaseConnection;

/**
 * Class ClientManagerImplementation
 */
class ClientManagerImplementation extends ClientManager implements ClientManagerInterface {

    /**
     * Validate the token - verify that it exists and is
     * marked as validated by system administrator.
     *
     * @param Token $token
     * @return boolean
     */
    public function validateToken(Token $token) {
        return (boolean) $this->getDatabaseConnection()->exec_SELECTcountRows(
            'uid',
            'tx_rpc_token',
            sprintf(
                "token = '%s' AND validated = 1",
                $token->getTokenValue()
            )
        );
    }

    /**
     * Issues a new token for a client asking to connect,
     * OR returns an already generated token based on
     * client IP if that token is not yet validated.
     *
     * In effect: only one token can be requested per IP at
     * the same time, but any number of tokens can be valid
     * for the same IP.
     *
     * This is done in part to prevent hammering, in part
     * to prevent confusion if client accidentally requests
     * two tokens and system administrator is unaware which
     * one was intended.
     *
     * @return Token
     */
    public function issueNewToken() {
        $clientIpLong = ip2long($_SERVER['REMOTE_ADDR']);
        $existingUnvalidatedTokenRecord = $this->getDatabaseConnection()->exec_SELECTgetSingleRow(
            'token',
            'tx_rpc_token',
            sprintf('client_ip = %d AND validated = 0', $clientIpLong)
        );
        if ($existingUnvalidatedTokenRecord) {
            return new Token($existingUnvalidatedTokenRecord['token']);
        }
        $token = new Token(sha1(microtime(TRUE) . $_SERVER['REMOTE_ADDR']));
        $this->getDatabaseConnection()->exec_INSERTquery(
            'tx_rpc_token',
            array(
                'token' => $token->getTokenValue(),
                'client_ip' => $clientIpLong
            )
        );
        return $token;
    }

    /**
     * @return DatabaseConnection
     */
    protected function getDatabaseConnection() {
        return $GLOBALS['TYPO3_DB'];
    }

}