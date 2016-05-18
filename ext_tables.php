<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

$GLOBALS['TCA']['tx_rpc_token'] = array(
    'label' => 'RPC Token',
    'ctrl' => array(
        'title' => 'RPC Token',
        'label'     => 'token',
        'prependAtCopy' => '',
        'hideAtCopy' => FALSE,
        'tstamp'    => 'tstamp',
        'crdate'    => 'crdate',
        'cruser_id' => 'cruser_id',
        'versioningWS' => FALSE,
        'origUid' => 't3_origuid',
        'dividers2tabs' => TRUE,
        'sortby' => '',
        'rootLevel' => TRUE,
        'enablecolumns' => array(),
        'iconfile'          => 'EXT:rpc/ext_icon.gif',
    ),
    'types' => array(
        '0' => array(
            'showitem' => 'token, validated, allowed_tasks'
        ),
    ),
    'columns' => array(
        'token' => array(
            'label' => 'Token',
            'config' => array(
                'type' => 'input'
            )
        ),
        'validated' => array(
            'label' => 'Has access?',
            'config' => array(
                'type' => 'check'
            )
        ),
        'allowed_tasks' => array(
            'label' => 'Allowed tasks',
            'config' => array(
                'type' => 'user',
                'userFunc' => 'NamelessCoder\\Rpc\\Backend\\AllowedTasksFieldRenderer->renderField'
            )
        )
    )
);

$GLOBALS['TCA']['tx_rpc_connection'] = array(
    'label' => 'RPC Connection',
    'ctrl' => array(
        'title' => 'RPC Connection',
        'label'     => 'remote_hostname',
        'prependAtCopy' => '',
        'hideAtCopy' => FALSE,
        'tstamp'    => 'tstamp',
        'crdate'    => 'crdate',
        'cruser_id' => 'cruser_id',
        'versioningWS' => FALSE,
        'origUid' => 't3_origuid',
        'dividers2tabs' => TRUE,
        'sortby' => '',
        'rootLevel' => TRUE,
        'enablecolumns' => array(),
        'iconfile'          => 'EXT:rpc/ext_icon.gif',
    ),
    'types' => array(
        '0' => array(
            'showitem' => 'remote_hostname, uses_https, token'
        ),
    ),
    'columns' => array(
        'token' => array(
            'label' => 'Token',
            'config' => array(
                'type' => 'input'
            )
        ),
        'remote_hostname' => array(
            'label' => 'Remote hostname',
            'config' => array(
                'type' => 'input'
            )
        ),
        'uses_https' => array(
            'label' => 'Uses HTTPS?',
            'config' => array(
                'type' => 'check'
            )
        )
    )
);

if ('BE' === TYPO3_MODE) {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'NamelessCoder.Rpc',
        'tools',
        'txrpcM1',
        '',
        array(
            'Backend' => 'index,create,edit,update,delete,call',
        ),
        array(
            'access' => 'user,group',
            'icon' => 'EXT:' . $_EXTKEY . '/Resources/Public/Icons/ModuleIcon.png',
            'labels' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang.xlf'
        )
    );

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
        'rpc',
        'EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang.xlf'
    );

}