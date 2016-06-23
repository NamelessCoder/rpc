<?php
namespace NamelessCoder\Rpc\Controller;

use NamelessCoder\Rpc\Request;
use NamelessCoder\Rpc\RequestDispatcher;
use NamelessCoder\Rpc\Security\Token;
use TYPO3\CMS\Core\Database\DatabaseConnection;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * Class BackendController
 */
class BackendController extends ActionController {

    /**
     * @param array $connection
     * @return void
     */
    public function indexAction(array $connection = NULL) {
        $connections = $this->getDatabaseConnection()->exec_SELECTgetRows(
            'uid, token, remote_hostname, uses_https', 'tx_rpc_connection',
            '1=1'
        );
        $this->view->assign('connections', $connections);
        $this->view->assign('connection', $connection);
        $this->view->assign('formAction', 'create');
    }

    /**
     * @param array $connection
     * @return void
     */
    public function createAction(array $connection) {
        if ($this->getDatabaseConnection()->exec_INSERTquery('tx_rpc_connection', $connection)) {
            $this->addFlashMessage(sprintf('Connection created for hostname %s', $connection['remote_hostname']));
        } else {
            $this->addFlashMessage(sprintf('Failed to create connection for hostname %s', $connection['remote_hostname']));
        }

        $this->redirect('index');
    }

    /**
     * @param integer $connection
     * @return void
     */
    public function editAction($connection) {
        $connection = reset($this->getDatabaseConnection()->exec_SELECTgetRows(
            'uid, token, remote_hostname, uses_https',
            'tx_rpc_connection',
            sprintf('uid = %d', $connection)
        ));
        $this->view->assign('connection', $connection);
        $this->view->assign('formAction', 'update');
    }

    /**
     * @param array $connection
     * @return void
     */
    public function updateAction(array $connection) {
        $connectionUid = $connection['uid'];
        unset($connection['uid']);
        $this->getDatabaseConnection()->exec_UPDATEquery(
            'tx_rpc_connection',
            sprintf('uid = %d', $connectionUid),
            $connection
        );
        $this->addFlashMessage(sprintf('Connection %d properties updated', $connectionUid));
        $this->redirect('index');
    }

    /**
     * @param integer $connection
     * @return void
     */
    public function deleteAction($connection) {
        $this->getDatabaseConnection()->exec_DELETEquery('tx_rpc_connection', sprintf('uid = %d', $connection));
        $this->addFlashMessage(sprintf('Connection %d was deleted', $connection));
        $this->redirect('index');
    }
    
    /**
     * @param integer $connection
     * @param string $task
     * @param array $arguments
     * @return void
     */
    public function callAction($connection, $task, array $arguments = array()) {
        $dispatcher = new RequestDispatcher();
        $uidCondition = sprintf('uid = %d', $connection);
        $connection = $this->getDatabaseConnection()->exec_SELECTgetSingleRow(
            'uid, token, remote_hostname, uses_https',
            'tx_rpc_connection',
            $uidCondition
        );
        $request = new Request();
        $request->setTask($task);
        $request->setArguments($arguments);
        $request->setToken(new Token($connection['token']));
        $this->view->assign('connection', $connection);
        $this->view->assign('arguments', $arguments);
        try {
            $response = $dispatcher->dispatchOutgoingRequest($connection['remote_hostname'], (boolean) $connection['uses_https'], $request);
            $connection['token'] = $response->getToken()->getTokenValue();
            $this->getDatabaseConnection()->exec_UPDATEquery('tx_rpc_connection', $uidCondition, $connection);
            $this->view->assign('response', $response);
        } catch (\RuntimeException $error) {
            $this->view->assign('error', $error);
        }
    }

    /**
     * @return DatabaseConnection
     */
    protected function getDatabaseConnection() {
        return $GLOBALS['TYPO3_DB'];
    }

}