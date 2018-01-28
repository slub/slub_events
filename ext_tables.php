<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

/***************************************************************
 * Plugin Eventlist
 */
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'Slub.' . $_EXTKEY,
    'Eventlist',
    'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_be.xlf:plugin.Eventlist'
);

$pluginSignature = str_replace('_', '', $_EXTKEY) . '_eventlist';
$TCA['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    $pluginSignature,
    'FILE:EXT:' . $_EXTKEY . '/Configuration/FlexForms/flexform_eventlist.xml'
);

/***************************************************************
 * Plugin Eventsubscribe
 */
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'Slub.' . $_EXTKEY,
    'Eventsubscribe',
    'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_be.xlf:plugin.Eventsubscribe'
);

$pluginSignature = str_replace('_', '', $_EXTKEY) . '_eventsubscribe';
$TCA['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    $pluginSignature,
    'FILE:EXT:' . $_EXTKEY . '/Configuration/FlexForms/flexform_eventsubscribe.xml'
);

/***************************************************************
 * Plugin Eventuserpanel
 */
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'Slub.' . $_EXTKEY,
    'Eventuserpanel',
    'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_be.xlf:plugin.Eventuserpanel'
);

$pluginSignature = str_replace('_', '', $_EXTKEY) . '_eventuserpanel';
$TCA['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    $pluginSignature,
    'FILE:EXT:' . $_EXTKEY . '/Configuration/FlexForms/flexform_eventuserpanel.xml'
);

/***************************************************************
 * Plugin Eventgeniusbar
 */
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'Slub.' . $_EXTKEY,
    'Eventgeniusbar',
    'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_be.xlf:plugin.Eventgeniusbar'
);

$pluginSignature = str_replace('_', '', $_EXTKEY) . '_eventgeniusbar';
$TCA['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    $pluginSignature,
    'FILE:EXT:' . $_EXTKEY . '/Configuration/FlexForms/flexform_eventgeniusbar.xml'
);

/***************************************************************
 * Backend module
 */
if (TYPO3_MODE === 'BE') {
    /**
     * Registers a Backend Module
     */
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'Slub.' . $_EXTKEY,
        'web',           // Make module a submodule of 'web'
        'slubevents',    // Submodule key
        '',              // Position
        [
            'Event'      => 'beList, beCopy, show, new, create, edit, update, delete, listOwn',
            'Category'   => '',
            'Subscriber' => 'beIcsInvitation, list, beList, beOnlineSurvey',
            'Location'   => '',
        ],
        [
            'access' => 'user,group',
            'icon'   => 'EXT:' . $_EXTKEY . '/ext_icon.gif',
            'labels' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_slubevents.xlf',
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
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_slubevents_domain_model_event');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
    'tx_slubevents_domain_model_category',
    'EXT:slub_events/Resources/Private/Language/locallang_csh_tx_slubevents_domain_model_category.xlf'
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_slubevents_domain_model_category');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
    'tx_slubevents_domain_model_subscriber',
    'EXT:slub_events/Resources/Private/Language/locallang_csh_tx_slubevents_domain_model_subscriber.xlf'
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_slubevents_domain_model_subscriber');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
    'tx_slubevents_domain_model_location',
    'EXT:slub_events/Resources/Private/Language/locallang_csh_tx_slubevents_domain_model_location.xlf'
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_slubevents_domain_model_location');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
    'tx_slubevents_domain_model_discipline',
    'EXT:slub_events/Resources/Private/Language/locallang_csh_tx_slubevents_domain_model_discipline.xlf'
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_slubevents_domain_model_discipline');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
    'tx_slubevents_domain_model_contact',
    'EXT:slub_events/Resources/Private/Language/locallang_csh_tx_slubevents_domain_model_contact.xlf'
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_slubevents_domain_model_contact');
