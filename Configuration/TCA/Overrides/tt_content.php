<?php
defined('TYPO3_MODE') or die();

/***************************************************************
 * Plugin Eventlist
 */
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'Slub.SlubEvents',
    'Eventlist',
    'LLL:EXT:slub_events/Resources/Private/Language/locallang_be.xlf:plugin.Eventlist'
);

$pluginSignature = 'slubevents_eventlist';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    $pluginSignature,
    'FILE:EXT:slub_events/Configuration/FlexForms/flexform_eventlist.xml'
);

/***************************************************************
 * Plugin Eventsubscribe
 */
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'Slub.SlubEvents',
    'Eventsubscribe',
    'LLL:EXT:slub_events/Resources/Private/Language/locallang_be.xlf:plugin.Eventsubscribe'
);

$pluginSignature = 'slubevents_eventsubscribe';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    $pluginSignature,
    'FILE:EXT:slub_events/Configuration/FlexForms/flexform_eventsubscribe.xml'
);

/***************************************************************
 * Plugin Eventuserpanel
 */
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'Slub.SlubEvents',
    'Eventuserpanel',
    'LLL:EXT:slub_events/Resources/Private/Language/locallang_be.xlf:plugin.Eventuserpanel'
);

$pluginSignature = 'slubevents_eventuserpanel';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    $pluginSignature,
    'FILE:EXT:slub_events/Configuration/FlexForms/flexform_eventuserpanel.xml'
);

/***************************************************************
 * Plugin Eventgeniusbar
 */
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'Slub.SlubEvents',
    'Eventgeniusbar',
    'LLL:EXT:slub_events/Resources/Private/Language/locallang_be.xlf:plugin.Eventgeniusbar'
);

$pluginSignature = 'slubevents_eventgeniusbar';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    $pluginSignature,
    'FILE:EXT:slub_events/Configuration/FlexForms/flexform_eventgeniusbar.xml'
);
