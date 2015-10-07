<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'Slub.' . $_EXTKEY,
	'Eventlist',
	array(
		'Event' => 'list, show, showNotFound, new, update, create, delete',

	),
	// non-cacheable actions
	array(
		'Event' => 'new, update, create, delete',

	)
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'Slub.' . $_EXTKEY,
	'Eventsubscribe',
	array(
		'Subscriber' => 'new, create, delete, eventNotFound',

	),
	// non-cacheable actions
	array(
		'Subscriber' => 'new, create, delete',

	)
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'Slub.' . $_EXTKEY,
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

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'Slub.' . $_EXTKEY,
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

	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = 'EXT:' . $_EXTKEY . '/Classes/Slots/HookPreProcessing.php:Tx_SlubEvents_Slots_HookPreProcessing';

	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = 'EXT:' . $_EXTKEY . '/Classes/Slots/HookPostProcessing.php:Tx_SlubEvents_Slots_HookPostProcessing';

	// include cli command controller
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['extbase']['commandControllers'][] = 'CheckeventsCommandController';

	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks']['Slub\\SlubEvents\\Task\\StatisticsTask'] = array(
		'extension' => $_EXTKEY,
		'title' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang.xlf:tasks.statistics.name',
		'description' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang.xlf:tasks.statistics.description',
		'additionalFields' => 'Slub\\SlubEvents\\Task\\StatisticsTaskAdditionalFieldProvider'
	);

}
?>
