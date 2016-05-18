<?php
namespace NamelessCoder\Rpc\Implementation\Task;

use NamelessCoder\Rpc\ClientComponent\Field\TextArea;
use NamelessCoder\Rpc\ClientComponent\Report\SuccessReport;
use NamelessCoder\Rpc\Request;
use NamelessCoder\Rpc\Response;
use NamelessCoder\Rpc\Task\AbstractTask;
use NamelessCoder\Rpc\Task\TaskInterface;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/**
 * Class HelpTask
 */
class HelpTask extends AbstractTask implements TaskInterface {

    /**
     * HelpTask constructor.
     * @param string $id
     */
    public function __construct($id) {
        parent::__construct($id);
        $this->getTaskConfiguration()
            ->setLabel('Help')
            ->setDescription('Shows user how to use the system from the client side');
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function processRequest(Request $request) {
        $response = new Response();
        $response->setReport(new SuccessReport(
            'RPC Help',
            file_get_contents(__DIR__ . '/../../../CLIENT.md')
        ))->setPayload($this->collectSystemInformation())->complete();
        return $response;
    }

    /**
     * @return array
     */
    protected function collectSystemInformation() {
        return [
            'typo3_version' => ExtensionManagementUtility::getExtensionVersion('core'),
            'rpc_version' => ExtensionManagementUtility::getExtensionVersion('rpc'),
            'php_version' => PHP_VERSION,
            'server_name' => $_SERVER['SERVER_NAME'],
            'server_software' => $_SERVER['SERVER_SOFTWARE'],
            'server_admin' => $_SERVER['SERVER_ADMIN'],
            'server_ip' => $_SERVER['SERVER_ADDR'],
            'ssl' => getenv('HTTPS')
        ];
    }

}
