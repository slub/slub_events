<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

Tx_Extbase_Utility_Extension::configurePlugin(
	$_EXTKEY,
	'Eventlist',
	array(
		'Event' => 'list, show, showNotFound, new, update, create, delete',

	),
	// non-cacheable actions
	array(
		'Event' => 'new, update, create, delete',

	)
);

Tx_Extbase_Utility_Extension::configurePlugin(
	$_EXTKEY,
	'Eventsubscribe',
	array(
		'Subscriber' => 'new, create, delete, eventNotFound',

	),
	// non-cacheable actions
	array(
		'Subscriber' => 'new, create, delete',

	)
);

Tx_Extbase_Utility_Extension::configurePlugin(
	$_EXTKEY,
	'Eventuserpanel',
	array(
		'Event' => 'listOwn, show',
		'Subscriber' => 'list, show',

	),
	// non-cacheable actions
	array(
		'Event' => 'listOwn',

	)
);

Tx_Extbase_Utility_Extension::configurePlugin(
	$_EXTKEY,
	'Eventgeniusbar',
	array(
		'Category' => 'list, gbList',

	),
	// non-cacheable actions
	array(
		'Category' => '',

	)
);

$TYPO3_CONF_VARS['FE']['eID_include']['slubCal'] = 'EXT:slub_events/Ajaxproxy/Ajaxproxy.php';

## EXTENSION BUILDER DEFAULTS END TOKEN - Everything BEFORE this line is overwritten with the defaults of the extension builder

if (TYPO3_MODE === 'BE') {
	// prefill BE user data in event form
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tceforms.php']['getMainFieldsClass'][]  = 'EXT:' . $_EXTKEY . '/Classes/Slots/Tceforms.php:Tx_SlubEvents_Slots_Tceforms';
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tceforms.php']['getSingleFieldClass'][] = 'EXT:' . $_EXTKEY . '/Classes/Slots/Tceforms.php:Tx_SlubEvents_Slots_Tceforms';

	require_once (t3lib_extMgm::extPath($_EXTKEY) . 'Classes/Slots/HookPreProcessing.php');
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = 'Tx_SlubEvents_Slots_HookPreProcessing';

	require_once (t3lib_extMgm::extPath($_EXTKEY) . 'Classes/Slots/HookPostProcessing.php');
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = 'Tx_SlubEvents_Slots_HookPostProcessing';

	//~ require_once (t3lib_extMgm::extPath($_EXTKEY) . 'Classes/Slots/HookPostProcessing.php');
	//~ $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = 'Tx_SlubEvents_Slots_HookPostProcessing';

	// include cli command controller
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['extbase']['commandControllers'][] = 'Tx_SlubEvents_Command_CheckeventsCommandController';
}
?>
