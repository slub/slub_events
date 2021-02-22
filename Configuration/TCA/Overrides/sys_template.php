<?php
defined('TYPO3_MODE') or die();

// Register static typoscript.
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'slub_events',
    'Configuration/TypoScript',
    'SLUB: Event Registration'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'slub_events',
    'Configuration/TypoScript/FullCalendar',
    'SLUB: Event Registration - FullCalendar support'
);
