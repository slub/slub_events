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

/**
 * Scheduler Task for Statistics
 *
 *
 *
 * @author    Alexander Bigga <alexander.bigga@slub-dresden.de>
 */
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

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

        /** @var \TYPO3\CMS\Fluid\View\StandaloneView $emailViewHTML */
        $objectManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');

        /** @var \TYPO3\CMS\Fluid\View\StandaloneView $emailViewHTML */
        $emailViewHTML = $objectManager->get(\TYPO3\CMS\Fluid\View\StandaloneView::class);
        $emailViewHTML->getRequest()->setControllerExtensionName('SlubEvents');
        $emailViewHTML->setFormat('html');
        $emailViewHTML->assignMultiple($variables);

        if ($configurationManager) {
            $extbaseFrameworkConfiguration = $configurationManager->getConfiguration(
                ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK
            );
            $templateRootPath = GeneralUtility::getFileAbsFileName(
                $extbaseFrameworkConfiguration['view']['templateRootPaths'][10]
            );
            $partialRootPath = GeneralUtility::getFileAbsFileName(
                $extbaseFrameworkConfiguration['view']['partialRootPaths'][10]
            );
        } else {
            $templateRootPath = PATH_site . 'typo3conf/ext/slub_events/Resources/Private/Backend/Templates/';
            $partialRootPath = PATH_site . 'typo3conf/ext/slub_events/Resources/Private/Backend/Partials/';
        }

        $emailViewHTML->setTemplatePathAndFilename($templateRootPath . 'Email/' . $templateName . '.html');
        $emailViewHTML->setPartialRootPaths([$partialRootPath]);

        /** @var $message \TYPO3\CMS\Core\Mail\MailMessage */
        $message = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Mail\\MailMessage');
        $message->setTo($recipient)
            ->setFrom($sender)
            ->setCharset('utf-8')
            ->setSubject($subject);

        // Plain text example
        $emailTextHTML = $emailViewHTML->render();

        $message->setBody(EmailHelper::html2rest($emailTextHTML));

        // HTML Email
        $message->addPart($emailTextHTML, 'text/html');

        // attach ics-File
        if ($variables['attachIcs'] == true) {

            /** @var \TYPO3\CMS\Fluid\View\StandaloneView $ics */
            $ics = $objectManager->get('TYPO3\\CMS\\Fluid\\View\\StandaloneView');
            $emailViewHTML->getRequest()->setControllerExtensionName('SlubEvents');
            $ics->setFormat('ics');
            $ics->assignMultiple($variables);

            $ics->setTemplatePathAndFilename($templateRootPath . 'Email/' . $templateName . '.ics');

            // the total basename length must not be more than 60 characters --> see writeFileToTypo3tempDir()
            $eventIcsFile = PATH_site . 'typo3temp/tx_slubevents/' .
                substr(
                    preg_replace('/[^\w]/', '', $variables['helper']['nameto']),
                    0,
                    20
                )
                . '-inv-' . strtolower($templateName) . '-' . $variables['event']->getUid() . '.ics';
            GeneralUtility::writeFileToTypo3tempDir(
                $eventIcsFile,
                implode("\n", array_filter(explode("\n", $ics->render())))
            );

            // attach additionally ics as file
            $message->attach(\Swift_Attachment::fromPath($eventIcsFile)
                ->setContentType('text/calendar'));

            // add ics as part
            $message->addPart(implode("\n", array_filter(explode("\n", $ics->render()))), 'text/calendar', 'utf-8');
        }
        // attach CSV-File
        if ($variables['attachCsv'] == true) {

            /** @var \TYPO3\CMS\Fluid\View\StandaloneView $csv */
            $csv = $objectManager->get(\TYPO3\CMS\Fluid\View\StandaloneView::class);
            $emailViewHTML->getRequest()->setControllerExtensionName('SlubEvents');
            $csv->setFormat('csv');
            $csv->assignMultiple($variables);

            $csv->setTemplatePathAndFilename($templateRootPath . 'Email/' . $templateName . '.csv');
            $csv->setPartialRootPaths([$partialRootPath]);

            $eventCsvFile = PATH_site . 'typo3temp/tx_slubevents/' .
                substr(
                    preg_replace('/[^\w]/', '', $variables['helper']['nameto']),
                    0,
                    20
                )
                . '-sub-' . strtolower($templateName) . '.csv';
            GeneralUtility::writeFileToTypo3tempDir($eventCsvFile, $csv->render());

            // attach CSV-File
            $message->attach(\Swift_Attachment::fromPath($eventCsvFile)
                ->setContentType('text/csv'));
        }

        $message->send();

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
    protected function html2rest($text)
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
}
