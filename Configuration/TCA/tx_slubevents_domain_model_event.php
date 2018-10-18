<?php

$LL = 'LLL:EXT:slub_events/Resources/Private/Language/locallang_db.xlf:';

return [
    'ctrl'      => [
        'title'                    => $LL . 'tx_slubevents_domain_model_event',
        'label'                    => 'title',
        'tstamp'                   => 'tstamp',
        'crdate'                   => 'crdate',
        'cruser_id'                => 'cruser_id',
        'sortby'                   => 'sorting',
        'versioningWS'             => true,
        'versioning_followPages'   => true, /* TYPO3 7.6 */
        'origUid'                  => 't3_origuid',
        'languageField'            => 'sys_language_uid',
        'transOrigPointerField'    => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'delete'                   => 'deleted',
        'enablecolumns'            => [
            'disabled'  => 'hidden',
            'starttime' => 'starttime',
            'endtime'   => 'endtime',
        ],
        'searchFields'             => 'title,start_date_time,all_day,end_date_time,sub_end_date_time,teaser,description,min_subscriber,max_subscriber,audience,categories,subscribers,location,discipline,',
        'iconfile'                 => 'EXT:slub_events/Resources/Public/Icons/tx_slubevents_domain_model_event.gif',
        'requestUpdate'            => 'genius_bar, external_registration, recurring',
    ],
    'interface' => [
        'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, title, start_date_time, all_day, end_date_time, sub_end_date_time, teaser, description, min_subscriber, max_subscriber, audience, sub_end_date_info_sent, no_search, genius_bar, parent, recurring, recurring_options, recurring_end_date_time, cancelled, categories, subscribers, location, discipline, contact',
    ],
    'types'     => [
        // Single event
        '0' => [
            'showitem' => '' .
                '--div--;Was und Wann,' .
                'genius_bar,title,' .
                '--palette--;' . $LL . 'tx_slubevents_domain_model_event.start;paletteStart,' .
                '--palette--;' . $LL . 'tx_slubevents_domain_model_event.end;paletteEnd,' .
                'location,' .
                'teaser,' .
                'description,' .
                '--div--;Anmeldebedingungen,' .
                'contact,' .
                'external_registration,' .
                'min_subscriber,' .
                'max_subscriber,' .
                '--palette--;' . $LL . 'tx_slubevents_domain_model_event.sub_end;paletteEndSubscription,' .
                '--div--;Kategorisierung,' .
                'audience,' .
                'categories,' .
                'discipline,' .
                '--div--;Angemeldete Teilnehmer,' .
                'subscribers,' .
                '--div--;Wiederholung,' .
                '--palette--;;paletteRecurring,' .
                '--div--;Extras,' .
                'hidden, --palette--;;1,' .
                'onlinesurvey,' .
                'no_search',
        ],
    ],
    'palettes'  => [
        'paletteStart'           => [
            'showitem'       => 'start_date_time,all_day,cancelled',
        ],
        'paletteEnd'             => [
            'showitem'       => 'end_date_time_select, --linebreak--, end_date_time',
        ],
        'paletteEndSubscription' => [
            'showitem'       => 'sub_end_date_time_select, --linebreak--,  sub_end_date_time, sub_end_date_info_sent',
        ],
        'paletteRecurring'       => [
            'showitem'       => 'parent, recurring, --linebreak--, recurring_options, recurring_end_date_time, --linebreak--, recurring_events',
         ],
    ],
    'columns'   => [
        'sys_language_uid'         => [
            'exclude' => 1,
            'label'   => 'LLL:EXT:lang/locallang_general.xlf:LGL.language',
            'config'  => [
                'type'                => 'select',
                'renderType'          => 'selectSingle',
                'foreign_table'       => 'sys_language',
                'foreign_table_where' => 'ORDER BY sys_language.title',
                'items'               => [
                    ['LLL:EXT:lang/locallang_general.xlf:LGL.allLanguages', -1],
                    ['LLL:EXT:lang/locallang_general.xlf:LGL.default_value', 0],
                ],
            ],
        ],
        'l10n_parent'              => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'exclude'     => 1,
            'label'       => 'LLL:EXT:lang/locallang_general.xlf:LGL.l18n_parent',
            'config'      => [
                'type'                => 'select',
                'renderType'          => 'selectSingle',
                'items'               => [
                    ['', 0],
                ],
                'foreign_table'       => 'tx_slubevents_domain_model_event',
                'foreign_table_where' => 'AND tx_slubevents_domain_model_event.pid=###CURRENT_PID### AND tx_slubevents_domain_model_event.sys_language_uid IN (-1,0)',
            ],
        ],
        'l10n_diffsource'          => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        't3ver_label'              => [
            'label'  => 'LLL:EXT:lang/locallang_general.xlf:LGL.versionLabel',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max'  => 255,
            ],
        ],
        'hidden'                   => [
            'exclude' => 1,
            'label'   => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
            'config'  => [
                'type' => 'check',
            ],
        ],
        'starttime'                => [
            'exclude'   => 1,
            'l10n_mode' => 'mergeIfNotBlank',
            'label'     => 'LLL:EXT:lang/locallang_general.xlf:LGL.starttime',
            'config'    => [
                'type'     => 'input',
                // 'renderType' => 'inputDateTime', /* required as of TYPO3 8.7 */
                'size'     => 13,
                'max'      => 20,
                'eval'     => 'datetime',
                'default'  => 0,
            ],
        ],
        'endtime'                  => [
            'exclude'   => 1,
            'l10n_mode' => 'mergeIfNotBlank',
            'label'     => 'LLL:EXT:lang/locallang_general.xlf:LGL.endtime',
            'config'    => [
                'type'     => 'input',
                // 'renderType' => 'inputDateTime', /* required as of TYPO3 8.7 */
                'size'     => 13,
                'max'      => 20,
                'eval'     => 'datetime',
                'default'  => 0,
            ],
        ],
        'title'                    => [
            'displayCond' => 'FIELD:genius_bar:<:1',
            'exclude'     => 0,
            'label'       => $LL . 'tx_slubevents_domain_model_event.title',
            'config'      => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,required',
            ],
        ],
        'start_date_time'          => [
            'exclude' => 0,
            'label'   => $LL . 'tx_slubevents_domain_model_event.start_date_time',
            'config'  => [
                'type'     => 'input',
                // 'renderType' => 'inputDateTime', /* required as of TYPO3 8.7 */
                'size'     => 13,
                'max'      => 20,
                'eval'     => 'datetime,required',
                'default'  => 0,
            ],
        ],
        'all_day'                  => [
            'exclude' => 0,
            'label'   => $LL . 'tx_slubevents_domain_model_event.all_day',
            'config'  => [
                'type'    => 'check',
                'default' => 0,
            ],
        ],
        'end_date_time'            => [
            'exclude' => 0,
            'label'   => $LL . 'tx_slubevents_domain_model_event.end_date_time',
            'config'  => [
                'type'     => 'input',
                // 'renderType' => 'inputDateTime', /* required as of TYPO3 8.7 */
                'size'     => 13,
                'max'      => 20,
                'eval'     => 'datetime',
                'default'  => 0,
            ],
        ],
        'end_date_time_select'     => [
            'displayCond' => 'FIELD:end_date_time:=:0',
            'exclude'     => 0,
            'label'       => $LL . 'tx_slubevents_domain_model_event.end_date_time_select',
            'config'      => [
                'type'    => 'select',
                'renderType'  => 'selectSingle',
                'items'    => [
                    [
                        $LL . 'tx_slubevents_domain_model_event.end_date_time_select_value',
                        0,
                    ],
                    ['00:15', 15],
                    ['00:30', 30],
                    ['00:45', 45],
                    ['01:00', 60],
                    ['01:30', 90],
                    ['02:00', 120],
                    ['03:00', 180],
                    ['04:00', 240],
                    ['05:00', 300],
                    ['06:00', 360],
                ],
                'size'     => 1,
                'maxitems' => 1,
                'eval'     => '',
                'default'  => 60,
            ],
        ],
        'sub_end_date_time'        => [
            'displayCond' => 'FIELD:external_registration:REQ:false',
            'exclude' => 0,
            'label'   => $LL . 'tx_slubevents_domain_model_event.sub_end_date_time',
            'config'  => [
                'type'     => 'input',
                // 'renderType' => 'inputDateTime', /* required as of TYPO3 8.7 */
                'size'     => 13,
                'max'      => 20,
                'eval'     => 'datetime',
                'default'  => 0,
            ],
        ],
        'sub_end_date_time_select' => [
            'displayCond' => [
              'AND' => [
                'FIELD:sub_end_date_time:=:0',
                'displayCond' => 'FIELD:external_registration:REQ:false'
              ]
            ],
            'exclude'     => 0,
            'label'       => $LL . 'tx_slubevents_domain_model_event.sub_end_date_time_select',
            'config'      => [
                'type'    => 'select',
                'renderType'  => 'selectSingle',
                'items'    => [
                    [
                        $LL . 'tx_slubevents_domain_model_event.sub_end_date_time_select_value',
                        0,
                    ],
                    ['01:00', 60],
                    ['02:00', 120],
                    ['04:00', 240],
                    ['12:00', 720],
                    ['24:00', 1440],
                    ['48:00', 2880],
                    ['72:00', 4320],
                ],
                'size'     => 1,
                'maxitems' => 1,
                'eval'     => '',
                'default'  => 1440,
            ],
        ],
        'teaser'                   => [
            'displayCond'   => 'FIELD:genius_bar:<:1',
            'exclude'       => 0,
            'label'         => $LL . 'tx_slubevents_domain_model_event.teaser',
            'config'        => [
                'type'    => 'text',
                'cols'    => 40,
                'rows'    => 15,
                'eval'    => 'trim',
                'wizards' => [
                    'RTE' => [
                        'notNewRecords' => 1,
                        'RTEonly'       => 1,
                        'type'          => 'script',
                        'title'         => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:bodytext.W.RTE',
                        'icon'          => 'actions-wizard-rte',
                        'module'        => ['name' => 'wizard_rte'],
                        'title'         => 'LLL:EXT:cms/locallang_ttc.xlf:bodytext.W.RTE',
                    ],
                ],
                'enableRichtext' => true, /* TYPO3 8.7 */
            ],
            'defaultExtras' => 'richtext[]:rte_transform[mode=ts_css]', /* TYPO3 7.6 */
        ],
        'description'              => [
            'displayCond'   => 'FIELD:genius_bar:<:1',
            'exclude'       => 0,
            'label'         => $LL . 'tx_slubevents_domain_model_event.description',
            'config'        => [
                'type'    => 'text',
                'cols'    => 40,
                'rows'    => 15,
                'eval'    => 'trim',
                'wizards' => [
                    'RTE' => [
                        'type'          => 'script',
                        'title'         => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:bodytext.W.RTE',
                        'icon'          => 'actions-wizard-rte',
                        'notNewRecords' => 1,
                        'RTEonly'       => 1,
                        'module'        => ['name' => 'wizard_rte'],
                    ],
                ],
                'enableRichtext' => true, /* TYPO3 8.7 */
            ],
            'defaultExtras' => 'richtext[]:rte_transform[mode=ts_css]', /* TYPO3 7.6 */
        ],
        'min_subscriber'           => [
          'displayCond' => 'FIELD:external_registration:REQ:false',
            'exclude' => 0,
            'label'   => $LL . 'tx_slubevents_domain_model_event.min_subscriber',
            'config'  => [
                'type' => 'input',
                'size' => 4,
                'eval' => 'int',
            ],
        ],
        'max_subscriber'           => [
          'displayCond' => 'FIELD:external_registration:REQ:false',
            'exclude' => 0,
            'label'   => $LL . 'tx_slubevents_domain_model_event.max_subscriber',
            'config'  => [
                'type' => 'input',
                'size' => 4,
                'eval' => 'int',
            ],
        ],
        'audience'                 => [
            'exclude' => 0,
            'label'   => $LL . 'tx_slubevents_domain_model_event.audience',
            'config'  => [
                'type'     => 'select',
                'renderType' => 'selectSingle',
                'items'    => [
                    [
                        $LL . 'tx_slubevents_domain_model_event.audience.I.0',
                        0,
                    ],
                    [
                        $LL . 'tx_slubevents_domain_model_event.audience.I.1',
                        1,
                    ],
                    [
                        $LL . 'tx_slubevents_domain_model_event.audience.I.4',
                        4,
                    ],
                    [
                        $LL . 'tx_slubevents_domain_model_event.audience.I.2',
                        2,
                    ],
                    [
                        $LL . 'tx_slubevents_domain_model_event.audience.I.3',
                        3,
                    ],
                    [
                        $LL . 'tx_slubevents_domain_model_event.audience.I.5',
                        5,
                    ],
                ],
                'size'     => 1,
                'maxitems' => 1,
                'eval'     => 'required',
            ],
        ],
        'sub_end_date_info_sent'   => [
            'displayCond' => 'FIELD:external_registration:REQ:false',
            'exclude' => 0,
            'label'   => $LL . 'tx_slubevents_domain_model_event.sub_end_date_info_sent',
            'config'  => [
                'type'    => 'check',
                'default' => 0,
            ],
        ],

        'no_search'   => [
            'exclude' => 0,
            'label'   => $LL . 'tx_slubevents_domain_model_event.no_search',
            'config'  => [
                'type'    => 'check',
                'default' => 0,
            ],
        ],

        'genius_bar'               => [
            'exclude' => 0,
            'label'   => $LL . 'tx_slubevents_domain_model_event.genius_bar',
            'config'  => [
                'type'    => 'check',
                'default' => 0,
            ],
        ],
        'parent'           => [
            'displayCond' => 'FIELD:parent:REQ:true',
            'exclude'   => 0,
            'l10n_mode' => 'exclude',
            'label'     => $LL . 'tx_slubevents_domain_model_event.parent',
            'config'    => [
                'type' => 'user',
                'userFunc' => 'Slub\\SlubEvents\\Slots\\Tceforms->eventParentString',
            ],
        ],
        'recurring'               => [
             'displayCond' => 'FIELD:parent:REQ:false',
             'exclude' => 1,
             'label'   => $LL . 'tx_slubevents_domain_model_event.recurring',
             'config'  => [
                 'type'    => 'check',
                 'default' => 0,
             ],
         ],
         'recurring_options'        => [
             'displayCond' => 'FIELD:recurring:REQ:true',
             'exclude' => 1,
             'label'   => $LL . 'tx_slubevents_domain_model_event.recurring_options',
             'config'  => [
                 'type'  => 'user',
                 'size'  => 60,
                 'userFunc' => 'Slub\\SlubEvents\\Slots\\Tceforms->recurring_options',
                 'parameters' => array(
                    'color' => 'green'
                 )
             ],
         ],
         'recurring_events'        => [
             'displayCond' => 'FIELD:recurring:REQ:true',
             'exclude' => 1,
             'label'   => $LL . 'tx_slubevents_domain_model_event.recurring_events',
             'config'  => [
                 'type'  => 'user',
                 'size'  => 60,
                 'userFunc' => 'Slub\\SlubEvents\\Slots\\Tceforms->recurring_events',
                 'parameters' => array(
                    'color' => 'green'
                 )
             ],
         ],
         'recurring_end_date_time'        => [
             'displayCond' => 'FIELD:recurring:REQ:true',
             'exclude' => 1,
             'label'   => $LL . 'tx_slubevents_domain_model_event.recurring_end_date_time',
             'config'  => [
                 'type'     => 'input',
                 'size'     => 10,
                 'eval'     => 'datetime',
                 'checkbox' => 1,
                 'default'  => 0,
             ],
         ],
        'cancelled'                => [
            'exclude' => 0,
            'label'   => $LL . 'tx_slubevents_domain_model_event.cancelled',
            'config'  => [
                'type'    => 'check',
                'default' => 0,
            ],
        ],
        'categories'               => [
            'exclude'   => 0,
            'l10n_mode' => 'mergeIfNotBlank',
            'label'     => $LL . 'tx_slubevents_domain_model_event.categories',
            'config'    => [
                'type'                => 'select',
                'foreign_table'       => 'tx_slubevents_domain_model_category',
                'foreign_table_where' => 'AND tx_slubevents_domain_model_category.genius_bar = ###REC_FIELD_genius_bar### AND tx_slubevents_domain_model_category.pid = ###CURRENT_PID### AND (tx_slubevents_domain_model_category.sys_language_uid = 0 OR tx_slubevents_domain_model_category.l10n_parent = 0) AND tx_slubevents_domain_model_category.hidden = 0 ORDER BY tx_slubevents_domain_model_category.sorting ASC',
                'MM'                  => 'tx_slubevents_event_category_mm',
                'renderType'          => 'selectTree',
                'subType'             => 'db',
                'treeConfig'          => [
                    'parentField' => 'parent',
                    'appearance'  => [
                        'expandAll'  => true,
                        'showHeader' => true,
                        'allowRecursiveMode' => false,
                        'maxLevels'  => 10,
                        'width'      => 600,
                    ],

                ],
                'size'                => 10,
                'autoSizeMax'         => 30,
                'minitems'            => 1,
                'maxitems'            => 30,
                'wizards' => [
                     '_VERTICAL' => 1,
                     'add' => [
                        'type' => 'script',
                        'title' => 'LLL:EXT:lang/locallang_tca.xlf:be_users.usergroup_add_title',
                        'icon' => 'actions-add',
                        'params' => array(
                           'table' => 'tx_slubevents_domain_model_category',
                           'pid' => '###CURRENT_PID###',
                           'setValue' => 'prepend'
                        ),
                        'module' => array(
                           'name' => 'wizard_add'
                        )
                     ],
                  ],
            ],
        ],
        'subscribers'              => [
            'exclude' => 0,
            'label'   => $LL . 'tx_slubevents_domain_model_event.subscribers',
            'config'  => [
                'type'          => 'inline',
                'foreign_table' => 'tx_slubevents_domain_model_subscriber',
                'foreign_field' => 'event',
                'maxitems'      => 9999,
                'appearance'    => [
                    'collapseAll'                     => 1,
                    'expandSingle'                    => 1,
                    'levelLinksPosition'              => 'bottom',
                    'showSynchronizationLink'         => 0,
                    'showPossibleLocalizationRecords' => 0,
                    'showAllLocalizationLink'         => 0,
                ],
            ],
        ],
        'location'                 => [
            'exclude' => 0,
            'label'   => $LL . 'tx_slubevents_domain_model_event.location',
            'config'  => [
                'type'                => 'select',
                'foreign_table'       => 'tx_slubevents_domain_model_location',
                'foreign_table_where' => ' AND (tx_slubevents_domain_model_location.sys_language_uid = 0 OR tx_slubevents_domain_model_location.l10n_parent = 0) AND tx_slubevents_domain_model_location.pid = ###CURRENT_PID### AND tx_slubevents_domain_model_location.deleted = 0 AND tx_slubevents_domain_model_location.hidden = 0 ORDER BY tx_slubevents_domain_model_location.sorting',
                'renderType'          => 'selectTree',
                'subType'             => 'db',
                'treeConfig'          => [
                    'parentField' => 'parent',
                    'appearance'  => [
                        'expandAll'          => true,
                        'showHeader'         => true,
                        'allowRecursiveMode' => false,
                        'width'              => 600,
                    ],
                ],
                'size'                => 10,
                'minitems'            => 1,
                'maxitems'            => 1,
                'wizards' => [
                     '_VERTICAL' => 1,
                     'add' => [
                        'type' => 'script',
                        'title' => 'LLL:EXT:lang/locallang_tca.xlf:be_users.usergroup_add_title',
                        'icon' => 'actions-add',
                        'params' => array(
                           'table' => 'tx_slubevents_domain_model_location',
                           'pid' => '###CURRENT_PID###',
                           'setValue' => 'prepend'
                        ),
                        'module' => array(
                           'name' => 'wizard_add'
                        )
                     ],
                  ],
            ],
        ],
        'discipline'               => [
            'displayCond' => 'FIELD:genius_bar:<:1',
            'exclude'     => 0,
            'label'       => $LL . 'tx_slubevents_domain_model_event.discipline',
            'config'      => [
                'type'                => 'select',
                'foreign_table'       => 'tx_slubevents_domain_model_discipline',
                'foreign_table_where' => ' AND (tx_slubevents_domain_model_discipline.sys_language_uid = 0 OR tx_slubevents_domain_model_discipline.l10n_parent = 0) AND tx_slubevents_domain_model_discipline.pid = ###CURRENT_PID### AND tx_slubevents_domain_model_discipline.deleted = 0 AND tx_slubevents_domain_model_discipline.hidden = 0 ORDER BY tx_slubevents_domain_model_discipline.sorting',
                'MM'                  => 'tx_slubevents_event_discipline_mm',
                'renderType'          => 'selectTree',
                'subType'             => 'db',
                'treeConfig'          => [
                    'parentField' => 'parent',
                    'appearance'  => [
                        'expandAll'          => true,
                        'showHeader'         => true,
                        'allowRecursiveMode' => false,
                        'width'              => 600,
                    ],
                ],
                'size'                => 10,
                'autoSizeMax'         => 30,
                'minitems'            => 1,
                'maxitems'            => 10,
                'wizards' => [
                     '_VERTICAL' => 1,
                     'add' => [
                        'type' => 'script',
                        'title' => 'LLL:EXT:lang/locallang_tca.xlf:be_users.usergroup_add_title',
                        'icon' => 'actions-add',
                        'params' => array(
                           'table' => 'tx_slubevents_domain_model_discipline',
                           'pid' => '###CURRENT_PID###',
                           'setValue' => 'prepend'
                        ),
                        'module' => array(
                           'name' => 'wizard_add'
                        )
                     ],
                  ],
            ],
        ],
        'contact'                  => [
            'exclude' => 0,
            'label'   => $LL . 'tx_slubevents_domain_model_event.contact',
            'config'  => [
                'allowed'             => 'pages',
                'type'                => 'select',
                'renderType'          => 'selectMultipleSideBySide',
                'enableMultiSelectFilterTextfield' => TRUE,
                'foreign_table'       => 'tx_slubevents_domain_model_contact',
                'foreign_table_where' => 'AND tx_slubevents_domain_model_contact.pid = ###CURRENT_PID### AND tx_slubevents_domain_model_contact.deleted = 0 AND tx_slubevents_domain_model_contact.hidden = 0 ORDER BY tx_slubevents_domain_model_contact.sorting',
                'minitems'            => 1,
                'maxitems'            => 1,
                'size'                => 8,
                'selectedListStyle'   => 'width:600px;',
                'itemListStyle'       => 'width:600px;',
                'wizards' => [
                     '_VERTICAL' => 1,
                     'add' => [
                        'type' => 'script',
                        'title' => 'LLL:EXT:lang/locallang_tca.xlf:be_users.usergroup_add_title',
                        'icon' => 'actions-add',
                        'params' => array(
                           'table' => 'tx_slubevents_domain_model_contact',
                           'pid' => '###CURRENT_PID###',
                           'setValue' => 'prepend'
                        ),
                        'module' => array(
                           'name' => 'wizard_add'
                        )
                     ],
                  ],
            ],
        ],
        'onlinesurvey'             => [
            'exclude' => 0,
            'label'   => $LL . 'tx_slubevents_domain_model_event.onlinesurvey',
            'config'  => [
                'type' => 'input',
                'size' => 50,
                'eval' => 'trim',
            ],
        ],
        'external_registration'             => [
            'exclude' => 0,
            'label'   => $LL . 'tx_slubevents_domain_model_event.external_registration',
            'config'  => [
                'type' => 'input',
                'size' => 50,
                'eval' => 'trim',
            ],
        ],
    ],
];
