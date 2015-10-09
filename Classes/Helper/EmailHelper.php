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
 * @author	Alexander Bigga <alexander.bigga@slub-dresden.de>
 */
	use TYPO3\CMS\Core\Utility\GeneralUtility;

class EmailHelper {
	/**
	 * sendTemplateEmail
	 *
	 * @param array $recipient recipient of the email in the format array('recipient@domain.tld' => 'Recipient Name')
	 * @param array $sender sender of the email in the format array('sender@domain.tld' => 'Sender Name')
	 * @param string $subject subject of the email
	 * @param string $templateName template name (UpperCamelCase)
	 * @param array $variables variables to be passed to the Fluid view
	 * @return boolean TRUE on success, otherwise false
	 */
	public static function sendTemplateEmail(array $recipient, array $sender, $subject, $templateName, array $variables = array()) {

		$objectManager = GeneralUtility::makeInstance('\TYPO3\CMS\Extbase\Object\ObjectManager');

		$emailViewHTML = $objectManager->create('\TYPO3\CMS\Fluid\View\StandaloneView');
		$emailViewHTML->getRequest()->setControllerExtensionName('SlubEvents');
		$emailViewHTML->setFormat('html');
		$emailViewHTML->assignMultiple($variables);

		$templateRootPath = PATH_site . 'typo3conf/ext/slub_events/Resources/Private/Backend/Templates/';
		$partialRootPath = PATH_site . 'typo3conf/ext/slub_events/Resources/Private/Backend/Partials/';

		$emailViewHTML->setTemplatePathAndFilename($templateRootPath . 'Email/' . $templateName . '.html');
		$emailViewHTML->setPartialRootPath($partialRootPath);

		$message = GeneralUtility::makeInstance('\TYPO3\CMS\Core\Mail\MailMessage');
		$message->setTo($recipient)
			->setFrom($sender)
			->setCharset('utf-8')
			->setSubject($subject);

		// attach ICS-File
		//~ $message->attach(\Swift_Attachment::fromPath($eventIcsFile)
		//~ ->setFilename('invite.ics')
		//~ ->setContentType('application/ics'));
		// Plain text example
		$emailTextHTML = $emailViewHTML->render();
		$message->setBody(EmailHelper::html2rest($emailTextHTML), 'text/plain');

		// HTML Email
		$message->addPart($emailTextHTML, 'text/html');

		// attach ics-File
		if ($variables['attachIcs'] == TRUE) {

			$ics = $objectManager->create('\TYPO3\CMS\Fluid\View\StandaloneView');
			$emailViewHTML->getRequest()->setControllerExtensionName('SlubEvents');
			$ics->setFormat('ics');
			$ics->assignMultiple($variables);

			$ics->setTemplatePathAndFilename($templateRootPath . 'Email/' . $templateName . '.ics');

			$eventIcsFile = PATH_site.'typo3temp/tx_slubevents/'. preg_replace('/[^\w]/', '', $variables['helper']['nameto']).'-'. $templateName.'-'.$variables['event']->getUid().'.ics';
			GeneralUtility::writeFileToTypo3tempDir($eventIcsFile,  $ics->render());

			// add ics as part
			$message->addPart($ics->render(), 'text/calendar', 'utf-8');

		}
		// attach CSV-File
		if ($variables['attachCsv'] == TRUE) {
			$csv = $objectManager->create('\TYPO3\CMS\Fluid\View\StandaloneView');
			$emailViewHTML->getRequest()->setControllerExtensionName('SlubEvents');
			$csv->setFormat('csv');
			$csv->assignMultiple($variables);

			$csv->setTemplatePathAndFilename($templateRootPath . 'Email/' . $templateName . '.csv');
			$csv->setPartialRootPath($partialRootPath);

			$eventCsvFile = PATH_site.'typo3temp/tx_slubevents/'. preg_replace('/[^\w]/', '', $variables['helper']['nameto']).'-'. strtolower($templateName).'.csv';
			GeneralUtility::writeFileToTypo3tempDir($eventCsvFile,  $csv->render());

			// attach CSV-File
			$message->attach(\Swift_Attachment::fromPath($eventCsvFile)
				->setContentType('text/csv'));
		}

		$message->send();

		return $message->isSent();
	}

	/**
	 * Function foldline folds the line after 73 signs
	 * rfc2445.txt: lines SHOULD NOT be longer than 75 octets
	 *
	 * @param string		$content: Anystring
	 * @return string		$content: Manipulated string
	 */
	private function foldline($content) {

		$text = trim(strip_tags( html_entity_decode($content), '<br>,<p>,<li>'));
		$text = preg_replace('/<p[\ \w\=\"]{0,}>/', '', $text);
		$text = preg_replace('/<li[\ \w\=\"]{0,}>/', '- ', $text);
		// make newline formated (yes, really write \n into the text!
		$text = str_replace('</p>', '\n', $text);
		$text = str_replace('</li>', '\n', $text);
		// remove tabs
		$text = str_replace("\t", ' ', $text);
		// remove multiple spaces
		$text = preg_replace('/[\ ]{2,}/', '', $text);
		$text = str_replace('<br />', '\n', $text);
		// remove more than one empty line
		$text = preg_replace('/[\n]{3,}/', '\n\n', $text);
		// remove windows linkebreak
		$text = preg_replace('/[\r]/', '', $text);
		// newlines are not allowed
		$text = str_replace("\n", '\n', $text);
		// semicolumns are not allowed
		$text = str_replace(';', '\;', $text);

		$firstline = substr($text, 0, (75-12));
		$restofline = implode("\n ", str_split(trim(substr($text, (75-12), strlen($text))), 73) );

		return $firstline . "\n ". $restofline;
	}

	/**
	 * html2rest
	 *
	 * this converts the HTML email to something Rest-Style like text form
	 *
	 * @param $htmlString
	 * @return
	 */
	private function html2rest($text) {

		$text = strip_tags( html_entity_decode($text, ENT_COMPAT, 'UTF-8'), '<br>,<p>,<b>,<h1>,<h2>,<h3>,<h4>,<h5>,<a>,<li>');
		// header is getting **
		$text = preg_replace('/<h[1-5]>|<\/h[1-5]>/', '**', $text);
		// bold is getting * ([[\w\ \d:\/~\.\?\=&%\"]+])
		$text = preg_replace('/<b>|<\/b>/', '*', $text);
		// get away links but preserve href with class slub-event-link
		$text = preg_replace('/(<a[\ \w\=\"]{0,})(class=\"slub-event-link\" href\=\")([\w\d:\-\/~\.\?\=&%]+)([\"])([\"]{0,1}>)([\ \w\d\p{P}]+)(<\/a>)/', "$6\n$3", $text);
		// Remove separator characters (like non-breaking spaces...)
		$text = preg_replace( '/\p{Z}/u', ' ', $text );
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
		// remove all remaining html tags
		$text = strip_tags($text);

		return $text;
	}

}
