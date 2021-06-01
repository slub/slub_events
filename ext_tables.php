<?php
defined('TYPO3_MODE') || die();

/***************************************************************
 * Backend module
 */
if (TYPO3_MODE === 'BE') {
    /**
     * Registers a Backend Module
     */
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'Slub.SlubEvents',
        'web',           // Make module a submodule of 'web'
        'slubevents',    // Submodule key
        '',              // Position
        [
            'Backend\Event'      => 'beList, beCopy, beIcsInvitation',
            'Backend\Subscriber' => 'beList, beOnlineSurvey',
        ],
        [
            'access' => 'user,group',
            'icon'   => 'EXT:slub_events/ext_icon.gif',
            'labels' => 'LLL:EXT:slub_events/Resources/Private/Language/locallang_slubevents.xlf',
        ]
    );
}

/***************************************************************
 * TCA
 */

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
    'tx_slubevents_domain_model_event',
    'EXT:slub_events/Resources/Private/Language/locallang_csh_tx_slubevents_domain_model_event.xlf'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
    'tx_slubevents_domain_model_category',
    'EXT:slub_events/Resources/Private/Language/locallang_csh_tx_slubevents_domain_model_category.xlf'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
    'tx_slubevents_domain_model_subscriber',
    'EXT:slub_events/Resources/Private/Language/locallang_csh_tx_slubevents_domain_model_subscriber.xlf'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
    'tx_slubevents_domain_model_location',
    'EXT:slub_events/Resources/Private/Language/locallang_csh_tx_slubevents_domain_model_location.xlf'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
    'tx_slubevents_domain_model_discipline',
    'EXT:slub_events/Resources/Private/Language/locallang_csh_tx_slubevents_domain_model_discipline.xlf'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
    'tx_slubevents_domain_model_contact',
    'EXT:slub_events/Resources/Private/Language/locallang_csh_tx_slubevents_domain_model_contact.xlf'
);
