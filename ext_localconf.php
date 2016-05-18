<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

if (!(TYPO3_REQUESTTYPE & TYPO3_REQUESTTYPE_INSTALL)) {

	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptSetup('
		rpc = PAGE
		rpc {
			typeNum = 991
			config {
				no_cache = 1
				disableAllHeaderCode = 1
				additionalHeaders = Content-type:application/json
    			xhtml_cleaning = 0
    			admPanel = 0
    			debug = 0
			}
			headerData >
			10 = USER_INT
			10.userFunc = NamelessCoder\Rpc\RequestDispatcher->handleIncomingRequest
		}
	');

}

\NamelessCoder\Rpc\Manager\TaskManager::setInstance(new \NamelessCoder\Rpc\Implementation\TaskManagerImplementation());
\NamelessCoder\Rpc\Manager\ClientManager::setInstance(new \NamelessCoder\Rpc\Implementation\ClientManagerImplementation());

\NamelessCoder\Rpc\Manager\TaskManager::getInstance()->addTask(
	new \NamelessCoder\Rpc\Implementation\Task\HelpTask('help')
);

\NamelessCoder\Rpc\Manager\TaskManager::getInstance()->addTask(
	new \NamelessCoder\Rpc\Implementation\Task\ListTask('list')
);

\NamelessCoder\Rpc\Manager\TaskManager::getInstance()->addTask(
	new \NamelessCoder\Rpc\Implementation\Task\DemoTask('demo')
);

\NamelessCoder\Rpc\Implementation\Task\CommandTask::registerForCommand(
	\TYPO3\CMS\Extensionmanager\Command\ExtensionCommandController::class,
	'install'
)->setFieldTypeForArgument(
	\NamelessCoder\Rpc\Implementation\Field\AvailableExtensionsField::class,
	'extensionKey'
);
\NamelessCoder\Rpc\Implementation\Task\CommandTask::registerForCommand(
	\TYPO3\CMS\Extensionmanager\Command\ExtensionCommandController::class,
	'uninstall'
)->setFieldTypeForArgument(
	\NamelessCoder\Rpc\Implementation\Field\InstalledExtensionsField::class,
	'extensionKey'
);

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = \NamelessCoder\Rpc\Backend\DataHandlerHook::class;
