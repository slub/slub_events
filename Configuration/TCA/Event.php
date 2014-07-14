<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

$ll = 'LLL:EXT:slub_events/Resources/Private/Language/locallang_db.xlf:';

$TCA['tx_slubevents_domain_model_event'] = array(
	'ctrl' => $TCA['tx_slubevents_domain_model_event']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, title, start_date_time, all_day, end_date_time, sub_end_date_time, teaser, description, min_subscriber, max_subscriber, audience, sub_end_date_info_sent, genius_bar, cancelled, categories, subscribers, location, discipline, contact',
	),
	'types' => array (
		// Single event
		'0' => array('showitem' => '' .
			'--div--;Was und Wann,'.
			'genius_bar,title,'.
			'--palette--;LLL:EXT:slub_events/Resources/Private/Language/locallang_db.xlf:tx_slubevents_domain_model_event.start;paletteStart,'.
			'--palette--;LLL:EXT:slub_events/Resources/Private/Language/locallang_db.xlf:tx_slubevents_domain_model_event.end;paletteEnd,'.
			'location,'.
			'teaser,'.
			'description;;;richtext[paste|bold|italic|formatblock|class|left|center|right|orderedlist|unorderedlist|outdent|indent|link|image]:rte_transform[mode=ts_css],'.
			'--div--;Anmeldebedingungen,'.
			'contact,'.
			'min_subscriber,'.
			'max_subscriber,'.
			'--palette--;LLL:EXT:slub_events/Resources/Private/Language/locallang_db.xlf:tx_slubevents_domain_model_event.sub_end;paletteEndSubscription,'.
			'--div--;Kategorisierung,'.
			'audience,'.
			'categories,'.
			'discipline,'.
			'--div--;Angemeldete Teilnehmer,'.
			'subscribers,'.
			'--div--;Extras,'.
			'hidden;;1,'.
			'onlinesurvey'
		)
	),
	'palettes' => array (
		'paletteStart' => array(
			'showitem' => 'start_date_time,all_day,cancelled',
			'canNotCollapse' => TRUE
		),
		'paletteEnd' => array(
			'showitem' => 'end_date_time_select, --linebreak--, end_date_time',
			'canNotCollapse' => TRUE
		),
		'paletteEndSubscription' => array(
			'showitem' => 'sub_end_date_time_select, --linebreak--,  sub_end_date_time, sub_end_date_info_sent',
			'canNotCollapse' => TRUE
		),
	),
	'columns' => array(
		'sys_language_uid' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.language',
			'config' => array(
				'type' => 'select',
				'foreign_table' => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => array(
					array('LLL:EXT:lang/locallang_general.xlf:LGL.allLanguages', -1),
					array('LLL:EXT:lang/locallang_general.xlf:LGL.default_value', 0)
				),
			),
		),
		'l10n_parent' => array(
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.l18n_parent',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array('', 0),
				),
				'foreign_table' => 'tx_slubevents_domain_model_event',
				'foreign_table_where' => 'AND tx_slubevents_domain_model_event.pid=###CURRENT_PID### AND tx_slubevents_domain_model_event.sys_language_uid IN (-1,0)',
			),
		),
		'l10n_diffsource' => array(
			'config' => array(
				'type' => 'passthrough',
			),
		),
		't3ver_label' => array(
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.versionLabel',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'max' => 255,
			)
		),
		'hidden' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
			'config' => array(
				'type' => 'check',
			),
		),
		'starttime' => array(
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.starttime',
			'config' => array(
				'type' => 'input',
				'size' => 13,
				'max' => 20,
				'eval' => 'datetime',
				'checkbox' => 0,
				'default' => 0,
				'range' => array(
					'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
				),
			),
		),
		'endtime' => array(
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.endtime',
			'config' => array(
				'type' => 'input',
				'size' => 13,
				'max' => 20,
				'eval' => 'datetime',
				'checkbox' => 0,
				'default' => 0,
				'range' => array(
					'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
				),
			),
		),
		'title' => array(
			'displayCond' => 'FIELD:genius_bar:<:1',
			'exclude' => 0,
			'label' => 'LLL:EXT:slub_events/Resources/Private/Language/locallang_db.xlf:tx_slubevents_domain_model_event.title',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim,required'
			),
		),
		'start_date_time' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:slub_events/Resources/Private/Language/locallang_db.xlf:tx_slubevents_domain_model_event.start_date_time',
			'config' => array(
				'type' => 'input',
				'size' => 10,
				'eval' => 'datetime,required',
				'checkbox' => 1,
				'default' => time()
			),
		),
		'all_day' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:slub_events/Resources/Private/Language/locallang_db.xlf:tx_slubevents_domain_model_event.all_day',
			'config' => array(
				'type' => 'check',
				'default' => 0
			),
		),
		'end_date_time' => array(
			//~ 'displayCond' => 'FIELD:end_date_time:>:0',
			'exclude' => 0,
			'label' => 'LLL:EXT:slub_events/Resources/Private/Language/locallang_db.xlf:tx_slubevents_domain_model_event.end_date_time',
			'config' => array(
				'type' => 'input',
				'size' => 10,
				'eval' => 'datetime',
				'checkbox' => 1,
				'default' => 0
			),
		),
		'end_date_time_select' => array(
			'displayCond' => 'FIELD:end_date_time:=:0',
			'exclude' => 0,
			'label' => 'LLL:EXT:slub_events/Resources/Private/Language/locallang_db.xlf:tx_slubevents_domain_model_event.end_date_time_select',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array('LLL:EXT:slub_events/Resources/Private/Language/locallang_db.xlf:tx_slubevents_domain_model_event.end_date_time_select_value', 0),
					array('00:15', 15),
					array('00:30', 30),
					array('00:45', 45),
					array('01:00', 60),
					array('01:30', 90),
					array('02:00', 120),
					array('03:00', 180),
					array('04:00', 240),
					array('05:00', 300),
					array('06:00', 360),
				),
				'size' => 1,
				'maxitems' => 1,
				'eval' => '',
				'default' => 0,
			),
		),
		'sub_end_date_time' => array(
			//~ 'displayCond' => 'FIELD:sub_end_date_time:>:0',
			'exclude' => 0,
			'label' => 'LLL:EXT:slub_events/Resources/Private/Language/locallang_db.xlf:tx_slubevents_domain_model_event.sub_end_date_time',
			'config' => array(
				'type' => 'input',
				'size' => 10,
				'eval' => 'datetime',
				'checkbox' => 1,
				'default' => 0
			),
		),
		'sub_end_date_time_select' => array(
			'displayCond' => 'FIELD:sub_end_date_time:=:0',
			'exclude' => 0,
			'label' => 'LLL:EXT:slub_events/Resources/Private/Language/locallang_db.xlf:tx_slubevents_domain_model_event.sub_end_date_time_select',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array('LLL:EXT:slub_events/Resources/Private/Language/locallang_db.xlf:tx_slubevents_domain_model_event.sub_end_date_time_select_value', 0),
					array('01:00', 60),
					array('02:00', 120),
					array('04:00', 240),
					array('12:00', 720),
					array('24:00', 1440),
					array('48:00', 2880),
				),
				'size' => 1,
				'maxitems' => 1,
				'eval' => '',
				'default' => 0,
			),
		),
		'teaser' => array(
			'displayCond' => 'FIELD:genius_bar:<:1',
			'exclude' => 0,
			'label' => 'LLL:EXT:slub_events/Resources/Private/Language/locallang_db.xlf:tx_slubevents_domain_model_event.teaser',
			'config' => array(
				'type' => 'text',
				'cols' => 40,
				'rows' => 15,
				'eval' => 'trim',
				'wizards' => array(
					'RTE' => array(
						'icon' => 'wizard_rte2.gif',
						'notNewRecords'=> 1,
						'RTEonly' => 1,
						'script' => 'wizard_rte.php',
						'title' => 'LLL:EXT:cms/locallang_ttc.xlf:bodytext.W.RTE',
						'type' => 'script'
					)
				)
			),
			'defaultExtras' => 'richtext:rte_transform[flag=rte_enabled|mode=ts]',
		),
		'description' => array(
			'displayCond' => 'FIELD:genius_bar:<:1',
			'exclude' => 0,
			'label' => 'LLL:EXT:slub_events/Resources/Private/Language/locallang_db.xlf:tx_slubevents_domain_model_event.description',
			'config' => array(
				'type' => 'text',
				'cols' => 40,
				'rows' => 15,
				'eval' => 'trim',
				'wizards' => array(
					'RTE' => array(
						'icon' => 'wizard_rte2.gif',
						'notNewRecords'=> 1,
						'RTEonly' => 1,
						'script' => 'wizard_rte.php',
						'title' => 'LLL:EXT:cms/locallang_ttc.xlf:bodytext.W.RTE',
						'type' => 'script'
					)
				)
			),
			'defaultExtras' => 'richtext:rte_transform[flag=rte_enabled|mode=ts]',
		),
		'min_subscriber' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:slub_events/Resources/Private/Language/locallang_db.xlf:tx_slubevents_domain_model_event.min_subscriber',
			'config' => array(
				'type' => 'input',
				'size' => 4,
				'eval' => 'int'
			),
		),
		'max_subscriber' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:slub_events/Resources/Private/Language/locallang_db.xlf:tx_slubevents_domain_model_event.max_subscriber',
			'config' => array(
				'type' => 'input',
				'size' => 4,
				'eval' => 'int'
			),
		),
		'audience' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:slub_events/Resources/Private/Language/locallang_db.xlf:tx_slubevents_domain_model_event.audience',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array($ll . 'tx_slubevents_domain_model_event.audience.I.0', 0),
					array($ll . 'tx_slubevents_domain_model_event.audience.I.1', 1),
					array($ll . 'tx_slubevents_domain_model_event.audience.I.2', 2),
					array($ll . 'tx_slubevents_domain_model_event.audience.I.3', 3),
					array($ll . 'tx_slubevents_domain_model_event.audience.I.4', 4),
					array($ll . 'tx_slubevents_domain_model_event.audience.I.5', 5),
				),
				'size' => 1,
				'maxitems' => 1,
				'eval' => 'required'
			),
		),
		'sub_end_date_info_sent' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:slub_events/Resources/Private/Language/locallang_db.xlf:tx_slubevents_domain_model_event.sub_end_date_info_sent',
			'config' => array(
				'type' => 'check',
				'default' => 0
			),
		),
		'genius_bar' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:slub_events/Resources/Private/Language/locallang_db.xlf:tx_slubevents_domain_model_event.genius_bar',
			'config' => array(
				'type' => 'check',
				'default' => 0
			),
		),
		'cancelled' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:slub_events/Resources/Private/Language/locallang_db.xlf:tx_slubevents_domain_model_event.cancelled',
			'config' => array(
				'type' => 'check',
				'default' => 0
			),
		),
		'categories' => array(
			'exclude' => 0,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:slub_events/Resources/Private/Language/locallang_db.xlf:tx_slubevents_domain_model_event.categories',
			'config' => array(
				'type' => 'select',
				'foreign_table' => 'tx_slubevents_domain_model_category',
				'foreign_table_where' => 'AND tx_slubevents_domain_model_category.genius_bar = ###REC_FIELD_genius_bar### AND tx_slubevents_domain_model_category.pid = ###CURRENT_PID### AND (tx_slubevents_domain_model_category.sys_language_uid = 0 OR tx_slubevents_domain_model_category.l10n_parent = 0) AND tx_slubevents_domain_model_category.pid = ###CURRENT_PID### AND tx_slubevents_domain_model_category.hidden = 0 ORDER BY tx_slubevents_domain_model_category.sorting',
				'MM' => 'tx_slubevents_event_category_mm',
				'renderMode' => 'tree',
				'subType' => 'db',
				'treeConfig' => array(
					'parentField' => 'parent',
					'appearance' => array(
						'expandAll' => TRUE,
						'showHeader' => FALSE,
						'maxLevels' => 10,
						'width' => 400,
					),

				),
				'size' => 10,
				'autoSizeMax' => 30,
				'minitems' => 1,
				'maxitems' => 30,
				'multiple' => FALSE,
			),
		),
		'subscribers' =>  array(
			'exclude' => 0,
			'label' => 'LLL:EXT:slub_events/Resources/Private/Language/locallang_db.xlf:tx_slubevents_domain_model_event.subscribers',
			'config' => array(
				'type' => 'inline',
				'foreign_table' => 'tx_slubevents_domain_model_subscriber',
				'foreign_field' => 'event',
				'maxitems'      => 9999,
				'appearance' => array(
					'collapseAll' => 1,
					'expandSingle' => 1,
					'levelLinksPosition' => 'bottom',
					'showSynchronizationLink' => 0,
					'showPossibleLocalizationRecords' => 0,
					'showAllLocalizationLink' => 0
				),
			),
		),
		'location' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:slub_events/Resources/Private/Language/locallang_db.xlf:tx_slubevents_domain_model_event.location',
			'config' => array(
				'type' => 'select',
				'foreign_table' => 'tx_slubevents_domain_model_location',
				'foreign_table_where' => ' AND (tx_slubevents_domain_model_location.sys_language_uid = 0 OR tx_slubevents_domain_model_location.l10n_parent = 0) AND tx_slubevents_domain_model_location.pid = ###CURRENT_PID### AND tx_slubevents_domain_model_location.deleted = 0 AND tx_slubevents_domain_model_location.hidden = 0 ORDER BY tx_slubevents_domain_model_location.sorting',
				'renderMode' => 'tree',
				'subType' => 'db',
				'treeConfig' => array(
					'parentField' => 'parent',
					'appearance' => array(
						'expandAll' => FALSE,
						'showHeader' => FALSE,
						'allowRecursiveMode' => FALSE,
						'width' => 500
					),
				),
				'size' => 10,
				'autoSizeMax' => 30,
				'minitems' => 1,
				'maxitems' => 1,
				'multiple' => FALSE,
			),
		),
		'discipline' => array(
			'displayCond' => 'FIELD:genius_bar:<:1',
			'exclude' => 0,
			'label' => 'LLL:EXT:slub_events/Resources/Private/Language/locallang_db.xlf:tx_slubevents_domain_model_event.discipline',
			'config' => array(
				'type' => 'select',
				'foreign_table' => 'tx_slubevents_domain_model_discipline',
				'foreign_table_where' => ' AND (tx_slubevents_domain_model_discipline.sys_language_uid = 0 OR tx_slubevents_domain_model_discipline.l10n_parent = 0) AND tx_slubevents_domain_model_discipline.pid = ###CURRENT_PID### AND tx_slubevents_domain_model_discipline.deleted = 0 AND tx_slubevents_domain_model_discipline.hidden = 0 ORDER BY tx_slubevents_domain_model_discipline.sorting',
				'MM' => 'tx_slubevents_event_discipline_mm',
				'renderMode' => 'tree',
				'subType' => 'db',
				'treeConfig' => array(
					'parentField' => 'parent',
					'appearance' => array(
						'expandAll' => FALSE,
						'showHeader' => FALSE,
						'allowRecursiveMode' => FALSE,
						'width' => 500
					),
				),
				'size' => 10,
				'autoSizeMax' => 30,
				'minitems' => 0,
				'maxitems' => 10,
				'multiple' => FALSE,
			),
		),
		'contact' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:slub_events/Resources/Private/Language/locallang_db.xlf:tx_slubevents_domain_model_event.contact',
			'config' => array(
				'allowed' => 'pages',
				'type' => 'select',
				'foreign_table' => 'tx_slubevents_domain_model_contact',
				'foreign_table_where' => 'AND tx_slubevents_domain_model_contact.pid = ###CURRENT_PID### AND tx_slubevents_domain_model_contact.deleted = 0 AND tx_slubevents_domain_model_contact.hidden = 0 ORDER BY tx_slubevents_domain_model_contact.sorting',
				'minitems' => 1,
				'maxitems' => 2,	// this forces a working required select box!
									// stupid but true... it should be "1"
									// --> bug in TYPO3 4.7
				'size' => 8,
				'selectedListStyle' => 'width:400px;',
				'itemListStyle' => 'width:400px;',
			),
		),
		'onlinesurvey' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:slub_events/Resources/Private/Language/locallang_db.xlf:tx_slubevents_domain_model_event.onlinesurvey',
			'config' => array(
				'type' => 'input',
				'size' => 50,
				'eval' => 'trim'
			),
		),
	),
);

?>
