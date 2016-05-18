<?php
namespace NamelessCoder\Rpc\Implementation;

use NamelessCoder\Rpc\Manager\TaskManager;
use NamelessCoder\Rpc\Manager\TaskManagerInterface;
use NamelessCoder\Rpc\Security\Token;
use NamelessCoder\Rpc\Task\TaskInterface;
use TYPO3\CMS\Core\Database\DatabaseConnection;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class TaskManagerImplementation
 */
class TaskManagerImplementation extends TaskManager implements TaskManagerInterface {

    /**
     * @param Token $token
     * @param TaskInterface $task
     * @return boolean
     */
    public function tokenHasAccessToTask(Token $token, TaskInterface $task) {
        $tokenRecord = $this->getDatabaseConnection()->exec_SELECTgetSingleRow(
            'allowed_tasks',
            'tx_rpc_token',
            sprintf(
                "token = '%s' AND validated = 1",
                $token->getTokenValue()
            )
        );
        return is_array($tokenRecord) && in_array(
            $task->getTaskConfiguration()->getId(),
            GeneralUtility::trimExplode(',', $tokenRecord['allowed_tasks'])
        );
    }

    /**
     * @return DatabaseConnection
     */
    protected function getDatabaseConnection() {
        return $GLOBALS['TYPO3_DB'];
    }

}
