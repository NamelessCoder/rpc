<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "rpc".
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array(
	'title' => 'RPC: Remote Procedure Calls for TYPO3',
	'description' => 'Provides a simple and clean interface for TYPO3 to receive and respond to remove procedure calls exposed on the system as Tasks that can be individually granted access to via the client-obtained token used to authorisation. See README.md for usage and integration instructions.',
	'category' => 'misc',
	'shy' => 0,
	'version' => '1.0.0',
	'dependencies' => 'cms',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'beta',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearcacheonload' => 1,
	'lockType' => '',
	'author' => 'Claus Due',
	'author_email' => 'claus@namelesscoder.net',
	'author_company' => '',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'typo3' => '7.6.0-8.1.99',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => '',
	'suggests' => array(
	),
);
