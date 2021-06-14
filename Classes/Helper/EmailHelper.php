<?php
namespace Slub\SlubEvents\Helper;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2015 Alexander Bigga <alexander.bigga@slub-dresden.de>
 *  All rights reserved
 *
 *  This script is part of the Typo3 project. The Typo3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

/**
 * Scheduler Task for Statistics
 *
 * @author    Alexander Bigga <alexander.bigga@slub-dresden.de>
 */
class EmailHelper
{

    /**
     * sendTemplateEmail
     *
     * @param array                         $recipient    recipient of the email in the format array('recipient@domain.tld' => 'Recipient Name')
     * @param array                         $sender       sender of the email in the format array('sender@domain.tld' => 'Sender Name')
     * @param string                        $subject      subject of the email
     * @param string                        $templateName template name (UpperCamelCase)
     * @param array                         $variables    variables to be passed to the Fluid view
     * @param ConfigurationManagerInterface $configurationManager
     *
     * @return boolean TRUE on success, otherwise false
     */
    public static function sendTemplateEmail(
        array $recipient,
        array $sender,
        $subject,
        $templateName,
        array $variables = [],
        $configurationManager = null
    ) {

        /** @var \TYPO3\CMS\Extbase\Object\ObjectManager $objectManager */
        $objectManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');

        if (version_compare(VersionNumberUtility::getNumericTypo3Version(), '10.0.0', '<')) {
            $useSimfonyMailer = false;
        } else {
            $useSimfonyMailer = true;
        }

        // array of files to unlink after email has been sent
        $unlinkFiles = [];

        /** @var \TYPO3Fluid\Fluid\View\StandaloneView $emailViewHTML */
        $emailViewHTML = $objectManager->get('TYPO3\\CMS\\Fluid\\View\\StandaloneView');
        $emailViewHTML->getRequest()->setControllerExtensionName('SlubEvents');
        $emailViewHTML->setFormat('html');
        $emailViewHTML->assignMultiple($variables);

        $emailViewHTML->setTemplateRootPaths(self::resolveTemplateRootPaths($configurationManager));
        $emailViewHTML->setPartialRootPaths(self::resolvePartialRootPaths($configurationManager));

        $emailViewHTML->setTemplate('Email/' . $templateName . '.html');

        /** @var $message \TYPO3\CMS\Core\Mail\MailMessage */
        $message = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Mail\\MailMessage');
        $message->setTo($recipient)
            ->setFrom($sender)
            ->setSubject($subject);

        // Plain text example
        $emailTextHTML = $emailViewHTML->render();

        if ($useSimfonyMailer) {
            $message->text(EmailHelper::html2rest($emailTextHTML));
            $message->html($emailTextHTML);
        } else {
            $message->setBody(EmailHelper::html2rest($emailTextHTML));
            $message->addPart($emailTextHTML, 'text/html');
        }

        // attach ics-File
        if ($variables['attachIcs'] == true) {
            /** @var \TYPO3Fluid\Fluid\View\StandaloneView $ics */
            $ics = $objectManager->get('TYPO3\\CMS\\Fluid\\View\\StandaloneView');
            $emailViewHTML->getRequest()->setControllerExtensionName('SlubEvents');
            $ics->setFormat('ics');
            $ics->assignMultiple($variables);

            $ics->setTemplateRootPaths(self::resolveTemplateRootPaths($configurationManager));
            $ics->setPartialRootPaths(self::resolvePartialRootPaths($configurationManager));

            $ics->setTemplate('Email/' . $templateName . '.ics');

            // the total basename length must not be more than 60 characters --> see writeFileToTypo3tempDir()
            $eventIcsFile = Environment::getPublicPath() . '/typo3temp/tx_slubevents/' .
                substr(
                    preg_replace('/[^\w]/', '', strtolower($variables['nameTo'])),
                    0,
                    20
                )
                . '-' . strtolower($templateName) . '-' . $variables['event']->getUid() . '.ics';

            GeneralUtility::writeFileToTypo3tempDir(
                $eventIcsFile,
                implode("\r\n", array_filter(explode("\r\n", $ics->render())))
            );

            $unlinkFiles[] = $eventIcsFile;

            // attach additionally ics as file
            if ($useSimfonyMailer) {
                $message->attachFromPath($eventIcsFile);
            } else {
                $message->attach(\Swift_Attachment::fromPath($eventIcsFile)
                    ->setContentType('text/calendar'));
                // add ics as part
                $message->addPart(implode("\r\n", array_filter(explode("\r\n", $ics->render()))), 'text/calendar', 'utf-8');
            }

            if ($variables['settings']['email']['keepLocalFilesForDebugging']) {
                $debugFile = Environment::getPublicPath() . 'typo3temp/tx_slubevents/' .
                    substr(
                        preg_replace('/[^\w]/', '', strtolower($variables['nameTo'])),
                        0,
                        20
                    )
                    . '-' . strtolower($templateName) . (isset($variables['event']) ? '-' . $variables['event']->getUid() : '' ) . '.html';
                GeneralUtility::writeFileToTypo3tempDir($debugFile, $emailViewHTML->render());
            }

        }
        // attach CSV-File
        if ($variables['attachCsv'] == true) {

            /** @var \TYPO3Fluid\Fluid\View\StandaloneView $csv */
            $csv = $objectManager->get('TYPO3\\CMS\\Fluid\\View\\StandaloneView');
            $emailViewHTML->getRequest()->setControllerExtensionName('SlubEvents');
            $csv->setFormat('csv');
            $csv->assignMultiple($variables);

            $csv->setTemplateRootPaths(self::resolveTemplateRootPaths($configurationManager));
            $csv->setPartialRootPaths(self::resolvePartialRootPaths($configurationManager));

            $csv->setTemplate('Email/' . $templateName . '.csv');

            $eventCsvFile = Environment::getPublicPath() . '/typo3temp/tx_slubevents/' .
                substr(
                    preg_replace('/[^\w]/', '', strtolower($variables['nameTo'])),
                    0,
                    20
                )
                . '-' . strtolower($templateName) . '.csv';
            GeneralUtility::writeFileToTypo3tempDir($eventCsvFile, $csv->render());

            $unlinkFiles[] = $eventCsvFile;

            // attach CSV-File
            if ($useSimfonyMailer) {
                $message->attachFromPath($eventCsvFile);
            } else {
                $message->attach(\Swift_Attachment::fromPath($eventCsvFile)
                    ->setContentType('text/csv'));
            }
        }

        if ($variables['settings']['email']['keepLocalFilesForDebugging']) {
            $debugFile = Environment::getPublicPath() . '/typo3temp/tx_slubevents/' .
                substr(
                    preg_replace('/[^\w]/', '', strtolower($variables['nameTo'])),
                    0,
                    20
                )
                . '-' . strtolower($templateName) . (isset($variables['event']) ? '-' . $variables['event']->getUid() : '' ) . '.html';
            GeneralUtility::writeFileToTypo3tempDir($debugFile, $emailTextHTML);
        }

        $message->send();

        // remove files from typo3temp if not kept for debuggin purpose
        if (empty($variables['settings']['email']['keepLocalFilesForDebugging'])) {
            foreach ($unlinkFiles as $file) {
                GeneralUtility::unlink_tempfile($file);
            }
        }

        return $message->isSent();
    }

    /**
     * html2rest
     *
     * this converts the HTML email to something Rest-Style like text form
     *
     * @param $text
     *
     * @return string
     */
    public static function html2rest($text)
    {
        $text = strip_tags(
            html_entity_decode($text, ENT_COMPAT, 'UTF-8'),
            '<br>,<p>,<b>,<h1>,<h2>,<h3>,<h4>,<h5>,<a>,<li>'
        );
        // header is getting **
        $text = preg_replace('/<h[1-5]>|<\/h[1-5]>/', '**', $text);
        // bold is getting * ([[\w\ \d:\/~\.\?\=&%\"]+])
        $text = preg_replace('/<b>|<\/b>/', '*', $text);
        // get away links but preserve href with class slub-event-link
        $text = preg_replace(
            '/(<a[\ \w\=\"]{0,})(class=\"slub-event-link\" href\=\")([\w\d:\-\/~\.\?\=&%]+)([\"])([\"]{0,1}>)([\ \w\d\p{P}]+)(<\/a>)/',
            "$6\n$3",
            $text
        );
        // Remove separator characters (like non-breaking spaces...)
        $text = preg_replace('/\p{Z}/u', ' ', $text);
        $text = str_replace('<br />', "\n", $text);
        // get away paragraphs including class, title etc.
        $text = preg_replace('/<p[\s\w\=\"]*>(?s)(.*?)<\/p>/u', "$1\n", $text);
        $text = str_replace('<li>', '- ', $text);
        $text = str_replace('</li>', "\n", $text);
        // remove multiple spaces
        $text = preg_replace('/[\ ]{2,}/', '', $text);
        // remove multiple tabs
        $text = preg_replace('/[\t]{1,}/', '', $text);
        // remove more than one empty line
        $text = preg_replace('/[\n]{3,}/', "\n\n", $text);
        // yes, really do CRLF to let quoted printable work as expected!
        $text = preg_replace('/[\n]/', "\r\n", $text);
        // remove all remaining html tags
        $text = strip_tags($text);

        return $text;
    }

    /**
     * @param ConfigurationManagerInterface $configurationManager
     * @return string[]
     */
    public static function resolveTemplateRootPaths($configurationManager = null)
    {
        if ($configurationManager) {
            $extbaseFrameworkConfiguration = $configurationManager->getConfiguration(
                ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK
            );
            $templateRootPaths = $extbaseFrameworkConfiguration['view']['templateRootPaths'];
        } else {
            $templateRootPaths = [Environment::getPublicPath() . '/typo3conf/ext/slub_events/Resources/Private/Backend/Templates/'];
        }

        return $templateRootPaths;
    }

    /**
     * @param ConfigurationManagerInterface $configurationManager
     * @return string[]
     */
    public static function resolvePartialRootPaths($configurationManager = null)
    {
        if ($configurationManager) {
            $extbaseFrameworkConfiguration = $configurationManager->getConfiguration(
                ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK
            );
            $partialRootPaths = $extbaseFrameworkConfiguration['view']['partialRootPaths'];
        } else {
            $partialRootPaths = [Environment::getPublicPath() . '/typo3conf/ext/slub_events/Resources/Private/Backend/Partials/'];
        }

        return $partialRootPaths;
    }

    /**
     * Prepare name to use it in emails
     *
     * @param string Name string
     *
     * @return string Manipulated string
     */
    public static function prepareNameTo($name)
    {
        return strtolower(str_replace([',', ' '], ['', '-'], $name));
    }
}
