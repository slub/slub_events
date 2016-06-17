<?php

$LL = 'LLL:EXT:slub_events/Resources/Private/Language/locallang_db.xlf:';

return [
    'ctrl'      => [
        'title'                    => $LL . 'tx_slubevents_domain_model_subscriber',
        'label'                    => 'name',
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
        'searchFields'             => 'name,email,telephone,customerid,number,editcode,',
        'iconfile'                 => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('slub_events') . 'Resources/Public/Icons/tx_slubevents_domain_model_subscriber.gif',
    ],
    'interface' => [
        'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, name, email, telephone, institution, customerid, number, message, editcode, crdate',
    ],
    'types'     => [
        '1' => ['showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, name, email, telephone, institution, customerid, number, message, editcode, crdate,--div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.access,starttime, endtime'],
    ],
    'palettes'  => [
        '1' => ['showitem' => ''],
    ],
    'columns'   => [
        'sys_language_uid' => [
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
        'l10n_parent'      => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'exclude'     => 1,
            'label'       => 'LLL:EXT:lang/locallang_general.xlf:LGL.l18n_parent',
            'config'      => [
                'type'                => 'select',
                'items'               => [
                    ['', 0],
                ],
                'foreign_table'       => 'tx_slubevents_domain_model_subscriber',
                'foreign_table_where' => 'AND tx_slubevents_domain_model_subscriber.pid=###CURRENT_PID### AND tx_slubevents_domain_model_subscriber.sys_language_uid IN (-1,0)',
            ],
        ],
        'l10n_diffsource'  => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        't3ver_label'      => [
            'label'  => 'LLL:EXT:lang/locallang_general.xlf:LGL.versionLabel',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max'  => 255,
            ],
        ],
        'hidden'           => [
            'exclude' => 1,
            'label'   => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
            'config'  => [
                'type' => 'check',
            ],
        ],
        'starttime'        => [
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
        'endtime'          => [
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
        'name'             => [
            'exclude' => 0,
            'label'   => $LL . 'tx_slubevents_domain_model_subscriber.name',
            'config'  => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,required',
            ],
        ],
        'email'            => [
            'exclude' => 0,
            'label'   => $LL . 'tx_slubevents_domain_model_subscriber.email',
            'config'  => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,required',
            ],
        ],
        'telephone'        => [
            'exclude' => 0,
            'label'   => $LL . 'tx_slubevents_domain_model_subscriber.telephone',
            'config'  => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'institution'      => [
            'exclude' => 0,
            'label'   => $LL . 'tx_slubevents_domain_model_subscriber.institution',
            'config'  => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'customerid'       => [
            'exclude' => 0,
            'label'   => $LL . 'tx_slubevents_domain_model_subscriber.customerid',
            'config'  => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'number'           => [
            'exclude' => 0,
            'label'   => $LL . 'tx_slubevents_domain_model_subscriber.number',
            'config'  => [
                'type'    => 'input',
                'size'    => 4,
                'default' => 1,
                'eval'    => 'int',
            ],
        ],
        'message'          => [
            'exclude' => 0,
            'label'   => $LL . 'tx_slubevents_domain_model_subscriber.message',
            'config'  => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
                'eval' => 'trim',
            ],
        ],
        'editcode'         => [
            'exclude' => 1,
            'label'   => $LL . 'tx_slubevents_domain_model_subscriber.editcode',
            'config'  => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,required',
            ],
        ],
        'crdate'           => [
            'exclude' => 1,
            'label'   => $LL . 'tx_slubevents_domain_model_subscriber.crdate',
            'config'  => [
                'type'     => 'input',
                'size'     => 10,
                'eval'     => 'datetime',
                'checkbox' => 1,
                'default'  => time(),
            ],
        ],
        'event'            => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
    ],
];

## EXTENSION BUILDER DEFAULTS END TOKEN - Everything BEFORE this line is overwritten with the defaults of the extension builder

