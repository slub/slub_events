<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "slub_events".
 *
 ***************************************************************/

$EM_CONF[$_EXTKEY] = [
    'title'            => 'SLUB: Event Registration',
    'description'      => 'Tool for event registration and experts booking.

This extension is developped and used in production at the Saxony State and University Library (SLUB) Dresden, Germany.',
    'category'         => 'plugin',
    'author'           => 'SLUB TYPO3 Team',
    'author_email'     => 'typo3@slub-dresden.de',
    'author_company'   => 'SLUB Dresden',
    'state'            => 'stable',
    'uploadfolder'     => true,
    'createDirs'       => 'typo3temp/tx_slubevents/',
    'version'          => '6.0.1',
    'constraints'      => [
        'depends'   => [
            'typo3'   => '11.5.0-11.5.99',
        ],
        'conflicts' => [
        ],
        'suggests'  => [
        ],
    ],
];
