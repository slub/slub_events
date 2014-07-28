<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

Tx_Extbase_Utility_Extension::registerPlugin(
	$_EXTKEY,
	'Eventlist',
	'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_be.xlf:plugin.Eventlist',
);

$pluginSignature = str_replace('_', '', $_EXTKEY) . '_eventlist';
$TCA['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
t3lib_extMgm::addPiFlexFormValue($pluginSignature, 'FILE:EXT:' . $_EXTKEY . '/Configuration/FlexForms/flexform_eventlist.xml');

Tx_Extbase_Utility_Extension::registerPlugin(
	$_EXTKEY,
	'Eventsubscribe',
	'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_be.xlf:plugin.Eventsubscribe',
);

$pluginSignature = str_replace('_', '', $_EXTKEY) . '_eventsubscribe';
$TCA['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
t3lib_extMgm::addPiFlexFormValue($pluginSignature, 'FILE:EXT:' . $_EXTKEY . '/Configuration/FlexForms/flexform_eventsubscribe.xml');

Tx_Extbase_Utility_Extension::registerPlugin(
	$_EXTKEY,
	'Eventuserpanel',
	'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_be.xlf:plugin.Eventuserpanel',
);

$pluginSignature = str_replace('_', '', $_EXTKEY) . '_eventuserpanel';
$TCA['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
t3lib_extMgm::addPiFlexFormValue($pluginSignature, 'FILE:EXT:' . $_EXTKEY . '/Configuration/FlexForms/flexform_eventuserpanel.xml');

Tx_Extbase_Utility_Extension::registerPlugin(
	$_EXTKEY,
	'Eventgeniusbar',
	'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_be.xlf:plugin.Eventgeniusbar',
);

$pluginSignature = str_replace('_', '', $_EXTKEY) . '_eventgeniusbar';
$TCA['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
t3lib_extMgm::addPiFlexFormValue($pluginSignature, 'FILE:EXT:' . $_EXTKEY . '/Configuration/FlexForms/flexform_eventgeniusbar.xml');

if (TYPO3_MODE === 'BE') {
	/**
	 * Registers a Backend Module
	 */
	Tx_Extbase_Utility_Extension::registerModule(
		$_EXTKEY,
		'web',	 		// Make module a submodule of 'web'
		'slubevents',	// Submodule key
		'',				// Position
		array(
			'Event' => 'beList, beCopy, show, new, create, edit, update, delete, listOwn',
			'Category' => '',
			'Subscriber' => 'beIcsInvitation, list, beList, beOnlineSurvey',
			'Location' => '',
		),
		array(
			'access' => 'user,group',
			'icon'   => 'EXT:' . $_EXTKEY . '/ext_icon.gif',
			'labels' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_slubevents.xlf',
		)
	);
}


t3lib_extMgm::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'SLUB: Event Registration');

t3lib_extMgm::addLLrefForTCAdescr('tx_slubevents_domain_model_event', 'EXT:slub_events/Resources/Private/Language/locallang_csh_tx_slubevents_domain_model_event.xlf');
t3lib_extMgm::allowTableOnStandardPages('tx_slubevents_domain_model_event');
$TCA['tx_slubevents_domain_model_event'] = array(
	'ctrl' => array(
		'title'	=> 'LLL:EXT:slub_events/Resources/Private/Language/locallang_db.xlf:tx_slubevents_domain_model_event',
		'label' => 'title',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'dividers2tabs' => TRUE,
		'sortby' => 'sorting',
		'versioningWS' => 2,
		'versioning_followPages' => TRUE,
		'origUid' => 't3_origuid',
		'languageField' => 'sys_language_uid',
		'transOrigPointerField' => 'l10n_parent',
		'transOrigDiffSourceField' => 'l10n_diffsource',
		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
		),
		'searchFields' => 'title,start_date_time,all_day,end_date_time,sub_end_date_time,teaser,description,min_subscriber,max_subscriber,audience,categories,subscribers,location,discipline,',
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'Configuration/TCA/Event.php',
		'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY) . 'Resources/Public/Icons/tx_slubevents_domain_model_event.gif',
		'requestUpdate' => 'genius_bar'
	),
);

t3lib_extMgm::addLLrefForTCAdescr('tx_slubevents_domain_model_category', 'EXT:slub_events/Resources/Private/Language/locallang_csh_tx_slubevents_domain_model_category.xlf');
t3lib_extMgm::allowTableOnStandardPages('tx_slubevents_domain_model_category');
$TCA['tx_slubevents_domain_model_category'] = array(
	'ctrl' => array(
		'title'	=> 'LLL:EXT:slub_events/Resources/Private/Language/locallang_db.xlf:tx_slubevents_domain_model_category',
		'label' => 'title',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'dividers2tabs' => TRUE,
		'sortby' => 'sorting',
		'versioningWS' => 2,
		'versioning_followPages' => TRUE,
		'origUid' => 't3_origuid',
		'languageField' => 'sys_language_uid',
		'transOrigPointerField' => 'l10n_parent',
		'transOrigDiffSourceField' => 'l10n_diffsource',
		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
		),
		'searchFields' => 'title,parent,',
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'Configuration/TCA/Category.php',
		'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY) . 'Resources/Public/Icons/tx_slubevents_domain_model_category.gif',
		'requestUpdate' => 'genius_bar'
	),
);

t3lib_extMgm::addLLrefForTCAdescr('tx_slubevents_domain_model_subscriber', 'EXT:slub_events/Resources/Private/Language/locallang_csh_tx_slubevents_domain_model_subscriber.xlf');
t3lib_extMgm::allowTableOnStandardPages('tx_slubevents_domain_model_subscriber');
$TCA['tx_slubevents_domain_model_subscriber'] = array(
	'ctrl' => array(
		'title'	=> 'LLL:EXT:slub_events/Resources/Private/Language/locallang_db.xlf:tx_slubevents_domain_model_subscriber',
		'label' => 'name',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'dividers2tabs' => TRUE,
		'sortby' => 'sorting',
		'versioningWS' => 2,
		'versioning_followPages' => TRUE,
		'origUid' => 't3_origuid',
		'languageField' => 'sys_language_uid',
		'transOrigPointerField' => 'l10n_parent',
		'transOrigDiffSourceField' => 'l10n_diffsource',
		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
		),
		'searchFields' => 'name,email,telephone,customerid,number,editcode,',
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'Configuration/TCA/Subscriber.php',
		'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY) . 'Resources/Public/Icons/tx_slubevents_domain_model_subscriber.gif'
	),
);

t3lib_extMgm::addLLrefForTCAdescr('tx_slubevents_domain_model_location', 'EXT:slub_events/Resources/Private/Language/locallang_csh_tx_slubevents_domain_model_location.xlf');
t3lib_extMgm::allowTableOnStandardPages('tx_slubevents_domain_model_location');
$TCA['tx_slubevents_domain_model_location'] = array(
	'ctrl' => array(
		'title'	=> 'LLL:EXT:slub_events/Resources/Private/Language/locallang_db.xlf:tx_slubevents_domain_model_location',
		'label' => 'name',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'dividers2tabs' => TRUE,
		'sortby' => 'sorting',
		'versioningWS' => 2,
		'versioning_followPages' => TRUE,
		'origUid' => 't3_origuid',
		'languageField' => 'sys_language_uid',
		'transOrigPointerField' => 'l10n_parent',
		'transOrigDiffSourceField' => 'l10n_diffsource',
		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
		),
		'searchFields' => 'name,description,link,parent,',
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'Configuration/TCA/Location.php',
		'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY) . 'Resources/Public/Icons/tx_slubevents_domain_model_location.gif'
	),
);

t3lib_extMgm::addLLrefForTCAdescr('tx_slubevents_domain_model_discipline', 'EXT:slub_events/Resources/Private/Language/locallang_csh_tx_slubevents_domain_model_discipline.xlf');
t3lib_extMgm::allowTableOnStandardPages('tx_slubevents_domain_model_discipline');
$TCA['tx_slubevents_domain_model_discipline'] = array(
	'ctrl' => array(
		'title'	=> 'LLL:EXT:slub_events/Resources/Private/Language/locallang_db.xlf:tx_slubevents_domain_model_discipline',
		'label' => 'name',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'dividers2tabs' => TRUE,
		'sortby' => 'sorting',
		'versioningWS' => 2,
		'versioning_followPages' => TRUE,
		'origUid' => 't3_origuid',
		'languageField' => 'sys_language_uid',
		'transOrigPointerField' => 'l10n_parent',
		'transOrigDiffSourceField' => 'l10n_diffsource',
		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
		),
		'searchFields' => 'name,parent,',
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'Configuration/TCA/Discipline.php',
		'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY) . 'Resources/Public/Icons/tx_slubevents_domain_model_discipline.gif'
	),
);

t3lib_extMgm::addLLrefForTCAdescr('tx_slubevents_domain_model_contact', 'EXT:slub_events/Resources/Private/Language/locallang_csh_tx_slubevents_domain_model_contact.xlf');
t3lib_extMgm::allowTableOnStandardPages('tx_slubevents_domain_model_contact');
$TCA['tx_slubevents_domain_model_contact'] = array(
	'ctrl' => array(
		'title'	=> 'LLL:EXT:slub_events/Resources/Private/Language/locallang_db.xlf:tx_slubevents_domain_model_contact',
		'label' => 'name',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'dividers2tabs' => TRUE,
		'sortby' => 'sorting',
		'versioningWS' => 2,
		'versioning_followPages' => TRUE,
		'origUid' => 't3_origuid',
		'languageField' => 'sys_language_uid',
		'transOrigPointerField' => 'l10n_parent',
		'transOrigDiffSourceField' => 'l10n_diffsource',
		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
		),
		'searchFields' => 'name,',
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'Configuration/TCA/Contact.php',
		'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY) . 'Resources/Public/Icons/tx_slubevents_domain_model_contact.gif'
	),
);

?>
