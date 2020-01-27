<?php

$LL = 'LLL:EXT:slub_events/Resources/Private/Language/locallang_db.xlf:';

return [
    'ctrl'      => [
        'title'                    => $LL . 'tx_slubevents_domain_model_category',
        'label'                    => 'title',
        'tstamp'                   => 'tstamp',
        'crdate'                   => 'crdate',
        'cruser_id'                => 'cruser_id',
        'sortby'                   => 'sorting',
        'versioningWS'             => true,
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
        'searchFields'             => 'title,parent,',
        'iconfile'                 => 'EXT:slub_events/Resources/Public/Icons/tx_slubevents_domain_model_category.gif',
    ],
    'interface' => [
        'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, title, description, genius_bar, parent',
    ],
    'types'     => [
        '1' => ['showitem' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, --palette--;;1, genius_bar, title, description, parent,--div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.access,starttime, endtime'],
    ],
    'palettes'  => [
        '1' => ['showitem' => ''],
    ],
    'columns'   => [
        'sys_language_uid' => [
            'exclude' => 1,
            'label'   => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.language',
            'config'  => [
                'type'                => 'select',
                'renderType'          => 'selectSingle',
                'foreign_table'       => 'sys_language',
                'foreign_table_where' => 'ORDER BY sys_language.title',
                'items'               => [
                    ['LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.allLanguages', -1],
                    ['LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.default_value', 0],
                ],
            ],
            'onChange'  => 'reload',
        ],
        'l10n_parent'      => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'exclude'     => 1,
            'label'       => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent',
            'config'      => [
                'type'                => 'select',
                'renderType'          => 'selectSingle',
                'items'               => [
                    ['', 0],
                ],
                'foreign_table'       => 'tx_slubevents_domain_model_category',
                'foreign_table_where' => 'AND tx_slubevents_domain_model_category.pid=###CURRENT_PID### AND tx_slubevents_domain_model_category.hidden = 0 AND tx_slubevents_domain_model_category.sys_language_uid IN (-1,0) ORDER BY tx_slubevents_domain_model_category.title',
            ],
        ],
        'l10n_diffsource'  => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        't3ver_label'      => [
            'label'  => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.versionLabel',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max'  => 255,
            ],
        ],
        'hidden'           => [
            'exclude' => 1,
            'label'   => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.hidden',
            'config'  => [
                'type' => 'check',
            ],
        ],
        'starttime'        => [
            'exclude'   => 1,
            'l10n_mode' => 'mergeIfNotBlank', // deprecated in 8.7 but kept for upgrade wizard
            'label'     => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.starttime',
            'config'    => [
                'behaviour' => [
                    'allowLanguageSynchronization' => true
                ],
                'type'     => 'input',
                'renderType' => 'inputDateTime',
                'size'     => 13,
                'eval'     => 'datetime',
                'default'  => 0,
            ],
        ],
        'endtime'          => [
            'exclude'   => 1,
            'l10n_mode' => 'mergeIfNotBlank', // deprecated in 8.7 but kept for upgrade wizard
            'label'     => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.endtime',
            'config'    => [
                'behaviour' => [
                    'allowLanguageSynchronization' => true
                ],
                'type'     => 'input',
                'renderType' => 'inputDateTime',
                'size'     => 13,
                'eval'     => 'datetime',
                'default'  => 0,
            ],
        ],
        'title'            => [
            'exclude' => 0,
            'label'   => $LL . 'tx_slubevents_domain_model_category.title',
            'config'  => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,required',
            ],
        ],
        'genius_bar'       => [
            'exclude'   => 0,
            'l10n_mode' => 'exclude',
            'label'     => $LL . 'tx_slubevents_domain_model_category.genius_bar',
            'config'    => [
                'type'    => 'check',
                'default' => 0,
            ],
            'onChange'  => 'reload',
        ],
        'description'      => [
            'exclude'       => 0,
            'label'         => $LL . 'tx_slubevents_domain_model_category.description',
            'config'        => [
                'type'    => 'text',
                'cols'    => 40,
                'rows'    => 15,
                'eval'    => 'trim',
                'fieldControl' => [
                    'fullScreenRichtext' => [
                        'disabled' => false,
                        'options' => [
                            'title' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:bodytext.W.RTE',
                        ],
                    ],
                ],
                'enableRichtext' => true,
            ],
        ],
        'parent'           => [
            'exclude'   => 0,
            'l10n_mode' => 'exclude',
            'label'     => $LL . 'tx_slubevents_domain_model_category.parent',
            'config'    => [
                'type'                => 'select',
                'foreign_table'       => 'tx_slubevents_domain_model_category',
                'foreign_table_where' => 'AND tx_slubevents_domain_model_category.genius_bar = ###REC_FIELD_genius_bar### AND (tx_slubevents_domain_model_category.sys_language_uid = 0 OR tx_slubevents_domain_model_category.l10n_parent = 0) AND tx_slubevents_domain_model_category.pid = ###CURRENT_PID### AND tx_slubevents_domain_model_category.hidden = 0 ORDER BY tx_slubevents_domain_model_category.sorting ASC',
                'renderType'          => 'selectTree',
                'subType'             => 'db',
                'treeConfig'          => [
                    'parentField' => 'parent',
                    'appearance'  => [
                        'expandAll'  => true,
                        'showHeader' => true,
                        'maxLevels'  => 10,
                    ],
                ],
                'maxitems'            => 1,
            ],
        ],
        'category'         => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
    ],
];
