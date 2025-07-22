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
            \Slub\SlubEvents\Controller\Backend\EventController::class      => 'beList, beCopy, beIcsInvitation',
            \Slub\SlubEvents\Controller\Backend\SubscriberController::class => 'beList, beOnlineSurvey, beWriteNotification, beSendNotification',
        ],
        [
            'access' => 'user,group',
            'icon'   => 'EXT:slub_events/Resources/Public/Icons/Extension.svg',
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
    'tx_slubevents_domain_model_topic',
    'EXT:slub_events/Resources/Private/Language/locallang_csh_tx_slubevents_domain_model_topic.xlf'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
    'tx_slubevents_domain_model_contact',
    'EXT:slub_events/Resources/Private/Language/locallang_csh_tx_slubevents_domain_model_contact.xlf'
);
