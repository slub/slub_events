<?php

$LL = 'LLL:EXT:slub_events/Resources/Private/Language/locallang_db.xlf:';

return [
    'ctrl'      => [
        'title'                    => $LL . 'tx_slubevents_domain_model_event',
        'label'                    => 'title',
        'tstamp'                   => 'tstamp',
        'crdate'                   => 'crdate',
        'cruser_id'                => 'cruser_id',
        'dividers2tabs'            => true,
        'sortby'                   => 'sorting',
        'versioningWS'             => 2,
        'versioning_followPages'   => true,
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
        'iconfile'                 => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('slub_events') . 'Resources/Public/Icons/tx_slubevents_domain_model_event.gif',
        'requestUpdate'            => 'genius_bar',
    ],
    'interface' => [
        'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, title, start_date_time, all_day, end_date_time, sub_end_date_time, teaser, description, min_subscriber, max_subscriber, audience, sub_end_date_info_sent, no_search, genius_bar, cancelled, categories, subscribers, location, discipline, contact',
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
                'description;;;richtext[paste|bold|italic|formatblock|class|left|center|right|orderedlist|unorderedlist|outdent|indent|link|image]:rte_transform[mode=ts_css],' .
                '--div--;Anmeldebedingungen,' .
                'contact,' .
                'min_subscriber,' .
                'max_subscriber,' .
                '--palette--;' . $LL . 'tx_slubevents_domain_model_event.sub_end;paletteEndSubscription,' .
                '--div--;Kategorisierung,' .
                'audience,' .
                'categories,' .
                'discipline,' .
                '--div--;Angemeldete Teilnehmer,' .
                'subscribers,' .
                '--div--;Extras,' .
                'hidden;;1,' .
                'onlinesurvey,' .
                'no_search',
        ],
    ],
    'palettes'  => [
        'paletteStart'           => [
            'showitem'       => 'start_date_time,all_day,cancelled',
            'canNotCollapse' => true,
        ],
        'paletteEnd'             => [
            'showitem'       => 'end_date_time_select, --linebreak--, end_date_time',
            'canNotCollapse' => true,
        ],
        'paletteEndSubscription' => [
            'showitem'       => 'sub_end_date_time_select, --linebreak--,  sub_end_date_time, sub_end_date_info_sent',
            'canNotCollapse' => true,
        ],
    ],
    'columns'   => [
        'sys_language_uid'         => [
            'exclude' => 1,
            'label'   => 'LLL:EXT:lang/locallang_general.xlf:LGL.language',
            'config'  => [
                'type'                => 'select',
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
                'size'     => 13,
                'max'      => 20,
                'eval'     => 'datetime',
                'checkbox' => 0,
                'default'  => 0,
                'range'    => [
                    'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y')),
                ],
            ],
        ],
        'endtime'                  => [
            'exclude'   => 1,
            'l10n_mode' => 'mergeIfNotBlank',
            'label'     => 'LLL:EXT:lang/locallang_general.xlf:LGL.endtime',
            'config'    => [
                'type'     => 'input',
                'size'     => 13,
                'max'      => 20,
                'eval'     => 'datetime',
                'checkbox' => 0,
                'default'  => 0,
                'range'    => [
                    'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y')),
                ],
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
                'size'     => 10,
                'eval'     => 'datetime,required',
                'checkbox' => 1,
                'default'  => time(),
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
                'size'     => 10,
                'eval'     => 'datetime',
                'checkbox' => 1,
                'default'  => 0,
            ],
        ],
        'end_date_time_select'     => [
            'displayCond' => 'FIELD:end_date_time:=:0',
            'exclude'     => 0,
            'label'       => $LL . 'tx_slubevents_domain_model_event.end_date_time_select',
            'config'      => [
                'type'     => 'select',
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
                'default'  => 0,
            ],
        ],
        'sub_end_date_time'        => [
            'exclude' => 0,
            'label'   => $LL . 'tx_slubevents_domain_model_event.sub_end_date_time',
            'config'  => [
                'type'     => 'input',
                'size'     => 10,
                'eval'     => 'datetime',
                'checkbox' => 1,
                'default'  => 0,
            ],
        ],
        'sub_end_date_time_select' => [
            'displayCond' => 'FIELD:sub_end_date_time:=:0',
            'exclude'     => 0,
            'label'       => $LL . 'tx_slubevents_domain_model_event.sub_end_date_time_select',
            'config'      => [
                'type'     => 'select',
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
                ],
                'size'     => 1,
                'maxitems' => 1,
                'eval'     => '',
                'default'  => 0,
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
                        'icon'          => 'wizard_rte2.gif',
                        'notNewRecords' => 1,
                        'RTEonly'       => 1,
                        'module'        => ['name' => 'wizard_rte'],
                        'title'         => 'LLL:EXT:cms/locallang_ttc.xlf:bodytext.W.RTE',
                    ],
                ],
            ],
            'defaultExtras' => 'richtext[]:rte_transform[mode=ts_css]',
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
                        'icon'          => 'wizard_rte2.gif',
                        'notNewRecords' => 1,
                        'RTEonly'       => 1,
                        'module'        => ['name' => 'wizard_rte'],
                        'title'         => 'LLL:EXT:cms/locallang_ttc.xlf:bodytext.W.RTE',
                    ],
                ],
            ],
            'defaultExtras' => 'richtext[]:rte_transform[mode=ts_css]',
        ],
        'min_subscriber'           => [
            'exclude' => 0,
            'label'   => $LL . 'tx_slubevents_domain_model_event.min_subscriber',
            'config'  => [
                'type' => 'input',
                'size' => 4,
                'eval' => 'int',
            ],
        ],
        'max_subscriber'           => [
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
                'renderMode'          => 'tree',
                'subType'             => 'db',
                'treeConfig'          => [
                    'parentField' => 'parent',
                    'appearance'  => [
                        'expandAll'  => true,
                        'showHeader' => false,
                        'maxLevels'  => 10,
                        'width'      => 400,
                    ],

                ],
                'size'                => 10,
                'autoSizeMax'         => 30,
                'minitems'            => 1,
                'maxitems'            => 30,
                'multiple'            => false,
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
                'renderMode'          => 'tree',
                'subType'             => 'db',
                'treeConfig'          => [
                    'parentField' => 'parent',
                    'appearance'  => [
                        'expandAll'          => false,
                        'showHeader'         => true,
                        'allowRecursiveMode' => false,
                        'width'              => 500,
                    ],
                ],
                'size'                => 10,
                'autoSizeMax'         => 30,
                'minitems'            => 1,
                'maxitems'            => 1,
                'multiple'            => false,
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
                'renderMode'          => 'tree',
                'subType'             => 'db',
                'treeConfig'          => [
                    'parentField' => 'parent',
                    'appearance'  => [
                        'expandAll'          => false,
                        'showHeader'         => false,
                        'allowRecursiveMode' => false,
                        'width'              => 500,
                    ],
                ],
                'size'                => 10,
                'autoSizeMax'         => 30,
                'minitems'            => 1,
                'maxitems'            => 10,
                'multiple'            => false,
            ],
        ],
        'contact'                  => [
            'exclude' => 0,
            'label'   => $LL . 'tx_slubevents_domain_model_event.contact',
            'config'  => [
                'allowed'             => 'pages',
                'type'                => 'select',
                'foreign_table'       => 'tx_slubevents_domain_model_contact',
                'foreign_table_where' => 'AND tx_slubevents_domain_model_contact.pid = ###CURRENT_PID### AND tx_slubevents_domain_model_contact.deleted = 0 AND tx_slubevents_domain_model_contact.hidden = 0 ORDER BY tx_slubevents_domain_model_contact.sorting',
                'minitems'            => 1,
                'maxitems'            => 2,    // this forces a working required select box!
                // stupid but true... it should be "1"
                // --> bug in TYPO3 4.7
                'size'                => 8,
                'selectedListStyle'   => 'width:400px;',
                'itemListStyle'       => 'width:400px;',
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
    ],
];
