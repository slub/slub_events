<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "slub_events".
 *
 * Auto generated 15-09-2014 11:01
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = [
    'title'            => 'SLUB: Event Registration',
    'description'      => 'Tool for event registration and experts booking.

This extension is developped and used in production at the Saxony State and University Library (SLUB) Dresden, Germany.',
    'category'         => 'plugin',
    'author'           => 'Alexander Bigga',
    'author_email'     => 'typo3@slub-dresden.de',
    'author_company'   => 'SLUB Dresden',
    'state'            => 'stable',
    'uploadfolder'     => true,
    'createDirs'       => 'typo3temp/tx_slubevents/',
    'version'          => '5.1.0',
    'constraints'      => [
        'depends'   => [
            'typo3'   => '9.5.31-10.4.99',
        ],
        'conflicts' => [
        ],
        'suggests'  => [
        ],
    ],
];
