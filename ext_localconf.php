<?php
defined('TYPO3_MODE') || die();

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Slub.SlubEvents',
    'Eventlist',
    [
        'Event' => 'list, show, showNotFound, listUpcoming, new, update, create, delete, printCal',
    ],
    // non-cacheable actions
    [
        'Event' => 'new, update, create, delete',
    ]
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Slub.SlubEvents',
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
    'Slub.SlubEvents',
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
    'Slub.SlubEvents',
    'Eventgeniusbar',
    [
        'Category' => 'list, gbList',
    ],
    // non-cacheable actions
    [
        'Category' => '',
    ]
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Slub.SlubEvents',
    'Apieventlist',
    [
        'Api\Event' => 'list',
    ],
    // non-cacheable actions
    [
        'Api\Event' => 'list',
    ]
);

/**
 * Set storagePid by default to detect not configured page tree sections
 */
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptSetup('
    plugin.tx_slubevents.persistence.storagePid =
    module.tx_slubevents.persistence.storagePid < plugin.tx_slubevents.persistence.storagePid
');

## EXTENSION BUILDER DEFAULTS END TOKEN -
# Everything BEFORE this line is overwritten with the defaults of the extension builder

/***************************************************************
 * Backend module
 */
if (TYPO3_MODE === 'BE') {

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] =
        Slub\SlubEvents\Slots\HookPreProcessing::class;

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] =
        Slub\SlubEvents\Slots\HookPostProcessing::class;

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass'][] =
        Slub\SlubEvents\Slots\HookPostProcessing::class;

    $languageDir = 'slub_events/Resources/Private/Language/';
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks']['Slub\\SlubEvents\\Task\\CheckeventsTask'] = [
        'extension'        => 'slub_events',
        'title'            => 'LLL:EXT:' . $languageDir . 'locallang.xlf:tasks.checkevents.name',
        'description'      => 'LLL:EXT:' . $languageDir . 'locallang.xlf:tasks.checkevents.description',
        'additionalFields' => Slub\SlubEvents\Task\CheckeventsTaskAdditionalFieldProvider::class
    ];
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks']['Slub\\SlubEvents\\Task\\StatisticsTask'] = [
        'extension'        => 'slub_events',
        'title'            => 'LLL:EXT:' . $languageDir . 'locallang.xlf:tasks.statistics.name',
        'description'      => 'LLL:EXT:' . $languageDir . 'locallang.xlf:tasks.statistics.description',
        'additionalFields' => Slub\SlubEvents\Task\StatisticsTaskAdditionalFieldProvider::class
    ];
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks']['Slub\\SlubEvents\\Task\\CleanUpTask'] = [
        'extension'        => 'slub_events',
        'title'            => 'LLL:EXT:' . $languageDir . 'locallang.xlf:tasks.cleanup.name',
        'description'      => 'LLL:EXT:' . $languageDir . 'locallang.xlf:tasks.cleanup.description',
        'additionalFields' => Slub\SlubEvents\Task\CleanUpTaskAdditionalFieldProvider::class
    ];

    $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1600698292] = [
        'nodeName' => 'recurringOptions',
        'priority' => 40,
        'class' => Slub\SlubEvents\Helper\Form\Element\RecurringOptionsElement::class
    ];
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1600701794] = [
        'nodeName' => 'recurringEvents',
        'priority' => 40,
        'class' => Slub\SlubEvents\Helper\Form\Element\RecurringEventsElement::class
    ];
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1600702566] = [
        'nodeName' => 'recurringParent',
        'priority' => 40,
        'class' => Slub\SlubEvents\Helper\Form\Element\RecurringParentElement::class
    ];

    // register update wizard
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/install']['update']['slubEventsFileLocationUpdater']
            = Slub\SlubEvents\Updates\FileLocationUpdater::class;

}
