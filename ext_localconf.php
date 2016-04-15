<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Slub.' . $_EXTKEY,
    'Eventlist',
    [
        'Event' => 'list, show, showNotFound, new, update, create, delete',
    ],
    // non-cacheable actions
    [
        'Event' => 'new, update, create, delete',
    ]
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Slub.' . $_EXTKEY,
    'Eventsubscribe',
    [
        'Subscriber' => 'new, create, delete, eventNotFound, subscriberNotFound',
    ],
    // non-cacheable actions
    [
        'Subscriber' => 'new, create, delete',
    ]
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Slub.' . $_EXTKEY,
    'Eventuserpanel',
    [
        'Event'      => 'listOwn, show',
        'Subscriber' => 'list, show',
    ],
    // non-cacheable actions
    [
        'Event' => 'listOwn',
    ]
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Slub.' . $_EXTKEY,
    'Eventgeniusbar',
    [
        'Category' => 'list, gbList',
    ],
    // non-cacheable actions
    [
        'Category' => '',
    ]
);

/***************************************************************
 * Register eID
 */
$TYPO3_CONF_VARS['FE']['eID_include']['slubCal'] = 'EXT:slub_events/Ajaxproxy/Ajaxproxy.php';

## EXTENSION BUILDER DEFAULTS END TOKEN -
# Everything BEFORE this line is overwritten with the defaults of the extension builder

/***************************************************************
 * Backend module
 */
if (TYPO3_MODE === 'BE') {

    // prefill BE user data in event form
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tceforms.php']['getMainFieldsClass'][] =
        'EXT:' . $_EXTKEY . '/Classes/Slots/Tceforms.php:Tx_SlubEvents_Slots_Tceforms';

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tceforms.php']['getSingleFieldClass'][] =
        'EXT:' . $_EXTKEY . '/Classes/Slots/Tceforms.php:Tx_SlubEvents_Slots_Tceforms';

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] =
        'EXT:' . $_EXTKEY . '/Classes/Slots/HookPreProcessing.php:HookPreProcessing';

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] =
        'EXT:' . $_EXTKEY . '/Classes/Slots/HookPostProcessing.php:HookPostProcessing';

    // include cli command controller
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['extbase']['commandControllers'][] =
        'Slub\\SlubEvents\\Command\\CheckeventsCommandController';

    $languageDir = $_EXTKEY . '/Resources/Private/Language/';
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks']['Slub\\SlubEvents\\Task\\StatisticsTask'] = [
        'extension'        => $_EXTKEY,
        'title'            => 'LLL:EXT:' . $languageDir . 'locallang.xlf:tasks.statistics.name',
        'description'      => 'LLL:EXT:' . $languageDir . 'locallang.xlf:tasks.statistics.description',
        'additionalFields' => 'Slub\\SlubEvents\\Task\\StatisticsTaskAdditionalFieldProvider'
    ];
}
