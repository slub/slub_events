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
    'author_email'     => 'alexander.bigga@slub-dresden.de',
    'author_company'   => 'SLUB Dresden',
    'shy'              => '',
    'priority'         => '',
    'state'            => 'beta',
    'internal'         => '',
    'uploadfolder'     => 1,
    'createDirs'       => 'typo3temp/tx_slubevents/',
    'modify_tables'    => '',
    'clearCacheOnLoad' => 0,
    'lockType'         => '',
    'version'          => '2.2.1',
    'constraints'      => [
        'depends'   => [
            'extbase' => '6.2.0-7.6.99',
            'fluid'   => '6.2.0-7.6.99',
            'typo3'   => '6.2.0-7.6.99',
        ],
        'conflicts' => [
        ],
        'suggests'  => [
        ],
    ],
    'autoload' => [
      'classmap' => ['Classes']
    ],
];
