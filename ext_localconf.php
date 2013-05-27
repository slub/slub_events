<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

Tx_Extbase_Utility_Extension::configurePlugin(
	$_EXTKEY,
	'Eventlist',
	array(
		'Event' => 'list, show, new, update, create, delete, listMiniMonth, listMonth, listWeek, listDay',
		
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
		'Subscriber' => 'new, create, delete',
		
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
		'Category' => 'list, gbList',
		
	)
);

## EXTENSION BUILDER DEFAULTS END TOKEN - Everything BEFORE this line is overwritten with the defaults of the extension builder

//  using powermail signal to save event subscription
//~ $signalSlotDispatcher = t3lib_div::makeInstance('Tx_Extbase_SignalSlot_Dispatcher');
//~ $signalSlotDispatcher->connect('Tx_Powermail_Controller_FormsController', 'createActionBeforeRenderView', 'Tx_SlubEvents_Slots_PowermailSlots', 'saveSubscription', FALSE);

//~ $signalSlotDispatcher = t3lib_div::makeInstance('Tx_Extbase_SignalSlot_Dispatcher');
//~ $signalSlotDispatcher->connect('Tx_Powermail_Domain_Validator_CustomValidator', 'isValid', 'Tx_SlubEvents_Slots_ValidationController', 'checkEvent', FALSE);

//~ $GLOBALS['TYPO3_CONF_VARS']['FE']['eID_include']['ajaxSlubEvents'] = 'EXT:' . $_EXTKEY . '/Classes/Utility/Ajax/Dispatcher.php';

if (TYPO3_MODE === 'BE') {
	// prefill BE user data in event form
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tceforms.php']['getMainFieldsClass'][] = 'EXT:' . $_EXTKEY . '/Classes/Slots/Tceforms.php:Tx_SlubEvents_Slots_Tceforms';

	require_once (t3lib_extMgm::extPath($_EXTKEY) . 'Classes/Slots/HookPreProcessing.php');
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = 'Tx_SlubEvents_Slots_HookPreProcessing';

	require_once (t3lib_extMgm::extPath($_EXTKEY) . 'Classes/Slots/HookPostProcessing.php');
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = 'Tx_SlubEvents_Slots_HookPostProcessing';

	// include cli command controller
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['extbase']['commandControllers'][] = 'Tx_SlubEvents_Command_CheckeventsCommandController';
	//~ $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tce']['formevals']['Tx_SlubEvents_Slots_TceformsEval'] = 'EXT:' . $_EXTKEY . '/Classes/Slots/TceformsEval.php';
}
?>