<?php

$LL = 'LLL:EXT:slub_events/Resources/Private/Language/locallang_db.xlf:';

return [
    'ctrl'      => [
        'title'                    => $LL . 'tx_slubevents_domain_model_contact',
        'label'                    => 'name',
        'tstamp'                   => 'tstamp',
        'crdate'                   => 'crdate',
        'cruser_id'                => 'cruser_id',
        'dividers2tabs'            => true,
        'sortby'                   => 'sorting',
        'versioningWS'             => true,
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
        'searchFields'             => 'name,',
        'iconfile'                 => 'EXT:slub_events/Resources/Public/Icons/tx_slubevents_domain_model_contact.gif',
        'requestUpdate'            => 'sys_language_uid',
    ],
    'interface' => [
        'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, name, email, telephone, description, photo',
    ],
    'types'     => [
        '1' => ['showitem' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, --palette--;;1, name, email, telephone, description, photo,--div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.access,starttime, endtime'],
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
                'renderType'          => 'selectSingle',
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
                'renderType'          => 'selectSingle',
                'items'               => [
                    ['', 0],
                ],
                'foreign_table'       => 'tx_slubevents_domain_model_contact',
                'foreign_table_where' => 'AND tx_slubevents_domain_model_contact.pid=###CURRENT_PID### AND tx_slubevents_domain_model_contact.sys_language_uid IN (-1,0)',
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
                // 'renderType' => 'inputDateTime', /* required as of TYPO3 8.7 */
                'size'     => 13,
                'max'      => 20,
                'eval'     => 'datetime',
                'default'  => 0,
            ],
        ],
        'endtime'          => [
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
        'name'             => [
            'exclude' => 0,
            'label'   => $LL . 'tx_slubevents_domain_model_contact.name',
            'config'  => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,required',
            ],
        ],
        'email'            => [
            'exclude' => 0,
            'label'   => $LL . 'tx_slubevents_domain_model_contact.email',
            'config'  => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,required',
            ],
        ],
        'telephone'        => [
            'exclude' => 0,
            'label'   => $LL . 'tx_slubevents_domain_model_contact.telephone',
            'config'  => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'description'      => [
            'exclude'       => 0,
            'label'         => $LL . 'tx_slubevents_domain_model_contact.description',
            'config'        => [
                'type'    => 'text',
                'cols'    => 40,
                'rows'    => 15,
                'eval'    => 'trim',
                'wizards' => [
                    'RTE' => [
                        'type'          => 'script',
                        'title'         => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:bodytext.W.RTE',
                        'icon'          => 'EXT:backend/Resources/Public/Images/FormFieldWizard/wizard_rte.gif',
                        'notNewRecords' => 1,
                        'RTEonly'       => 1,
                        'module' => array(
                            'name' => 'wizard_rte',
                        ),
                    ],
                ],
            ],
            'defaultExtras' => 'richtext:rte_transform[flag=rte_enabled|mode=ts]',
        ],
        'photo'            => [
            'exclude' => 0,
            'label'   => $LL . 'tx_slubevents_domain_model_contact.photo',
            'config'  => [
                'type'          => 'group',
                'internal_type' => 'file',
                'allowed'       => $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'],
                'uploadfolder'  => 'uploads/tx_slubevents',
                'show_thumbs'   => 1,
                'size'          => 1,
                'minitems'      => 0,
                'maxitems'      => 1,
            ],
        ],
    ],
];
