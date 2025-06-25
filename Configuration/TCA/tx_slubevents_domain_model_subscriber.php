<?php

$LL = 'LLL:EXT:slub_events/Resources/Private/Language/locallang_db.xlf:';

return [
    'ctrl'      => [
        'title'                    => $LL . 'tx_slubevents_domain_model_subscriber',
        'label'                    => 'name',
        'tstamp'                   => 'tstamp',
        'crdate'                   => 'crdate',
        'cruser_id'                => 'cruser_id',
        'sortby'                   => 'tstamp',
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
        'iconfile'                 => 'EXT:slub_events/Resources/Public/Icons/tx_slubevents_domain_model_subscriber.gif',
    ],
    'interface' => [
        'showRecordFieldList' => 'hidden, name, email, telephone, institution, customerid, number, message, editcode, crdate',
    ],
    'types'     => [
        '1' => ['showitem' => 'hidden, --palette--;;1, name, email, telephone, institution, customerid, number, message, editcode, acceptpp, crdate,--div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access,starttime, endtime'],
    ],
    'palettes'  => [
        '1' => ['showitem' => ''],
    ],
    'columns'   => [
        'hidden'           => [
            'exclude' => 1,
            'label'   => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.hidden',
            'config'  => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
            ],
        ],
        'starttime'        => [
            'exclude'   => 1,
            'l10n_mode' => 'exclude',
            'label'     => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.starttime',
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
            'l10n_mode' => 'exclude',
            'label'     => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.endtime',
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
                'eval' => 'trim',
            ],
        ],
        'acceptpp' => [
            'exclude' => 1,
            'label' => $LL . 'tx_slubevents_domain_model_subscriber.acceptpp',
            'config' => [
                'type' => 'check',
                'readOnly' => true,
                'items' => [
                    [$LL . 'tx_slubevents_domain_model_subscriber.acceptpp.accepted', ''],
                ],
            ]
        ],
        'crdate'           => [
            'exclude' => 1,
            'label'   => $LL . 'tx_slubevents_domain_model_subscriber.crdate',
            'config'  => [
                'type'     => 'input',
                'renderType' => 'inputDateTime',
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
