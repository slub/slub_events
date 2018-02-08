<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

// Register static typoscript.
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'slub_events',
    'Configuration/TypoScript',
    'SLUB: Event Registration'
);
