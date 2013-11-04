<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2013 Alexander Bigga <alexander.bigga@slub-dresden.de>
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
 * Command Controller for CLI
 *
 * note: scheduler task is working from extbase 4.7
 *
 *
 * @author	Alexander Bigga <alexander.bigga@slub-dresden.de>
 */
class Tx_SlubEvents_Command_CheckeventsCommandController extends Tx_Extbase_MVC_Controller_CommandController {

	/**
	 * eventRepository
	 *
	 * @var Tx_SlubEvents_Domain_Repository_EventRepository
	 */
	protected $eventRepository;

	/**
	 * categoryRepository
	 *
	 * @var Tx_SlubEvents_Domain_Repository_CategoryRepository
	 * @inject
	 */
	protected $categoryRepository;

	/**
	 * subscriberRepository
	 *
	 * @var Tx_SlubEvents_Domain_Repository_SubscriberRepository
	 */
	protected $subscriberRepository;

	/**
	 * @var Tx_Extbase_Configuration_ConfigurationManagerInterface
	*/
	protected $configurationManager;

	/**
	 * injectEventRepository
	 *
	 * @param Tx_SlubEvents_Domain_Repository_EventRepository $eventRepository
	 * @return void
	 */
	public function injectEventRepository(Tx_SlubEvents_Domain_Repository_EventRepository $eventRepository) {
		$this->eventRepository = $eventRepository;
	}

	/**
	 * injectSubscriberRepository
	 *
	 * @param Tx_SlubEvents_Domain_Repository_SubscriberRepository $subscriberRepository
	 * @return void
	 */
	public function injectSubscriberRepository(Tx_SlubEvents_Domain_Repository_SubscriberRepository $subscriberRepository) {
		$this->subscriberRepository = $subscriberRepository;
	}

	/**
	 * initializeAction
	 *
	 * @return
	 */
	protected function initializeAction() {

		// TYPO3 doesn't set locales for backend-users --> so do it manually like this...
		// is needed with strftime
		setlocale(LC_ALL, 'de_DE.utf8');
		$GLOBALS['BE_USER']->uc['lang'] = 'de';

		//~ global $BE_USER;

		// TYPO3 doesn't set locales for backend-users --> so do it manually like this...
		// is needed especially with strftime
		//~ $BE_USER->uc['lang'] = 'de';
	}

	/**
	 * injectConfigurationManager
	 *
	 * @param Tx_Extbase_Configuration_ConfigurationManagerInterface $configurationManager
	 * @return void
	*/
	public function injectConfigurationManager(Tx_Extbase_Configuration_ConfigurationManagerInterface $configurationManager) {
		$this->configurationManager = $configurationManager;

		$this->contentObj = $this->configurationManager->getContentObject();
		$this->settings = $this->configurationManager->getConfiguration(Tx_Extbase_Configuration_ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS);
	}

	/**
	 * checkForSubscriptionEndCommand
	 *
	 * @param int stroagePid
	 * @param string senderEmailAddress
	 * @return void
	*/
    public function checkForSubscriptionEndCommand($storagePid, $senderEmailAddress = '') {

		// do some init work...
		$this->initializeAction();

		// abort if no storagePid is found
		if (! t3lib_utility_Math::canBeInterpretedAsInteger($storagePid)) {
			echo "NO storagePid given. Please enter the storagePid in the scheduler task.\n";
			exit(1);
		}
		// abort if no senderEmailAddress is found
		if (empty($senderEmailAddress)) {
			echo "NO senderEmailAddress given. Please enter the senderEmailAddress in the scheduler task.\n";
			exit(1);
		}

		// set storagePid to point extbase to the right repositories
		$configurationArray = array(
			'persistence' => array(
				'storagePid' => $storagePid
			)
		);
		$this->configurationManager->setConfiguration($configurationArray);

		// start the work...

		// 1. get events in future which reached the subscription deadline
		$allevents = $this->eventRepository->findAllSubscriptionEnded();

		foreach($allevents as $event) {

			// startDateTime may never be empty
			$helper['start'] = $event->getStartDateTime()->getTimestamp();
			// endDateTime may be empty
			if (($event->getEndDateTime() instanceof DateTime) && ($event->getStartDateTime() != $event->getEndDateTime()))
				$helper['end'] = $event->getEndDateTime()->getTimestamp();
			else
				$helper['end'] = $helper['start'];

			if ($event->isAllDay()) {
				$helper['allDay'] = 1;
			}
			$helper['now'] = time();
			$helper['nameto'] = strtolower(str_replace(array(',', ' '), array('', '-'), $event->getContact()->getName()));
			$helper['description'] = $this->foldline($event->getDescription());
			// location may be empty...
			if (is_object($event->getLocation())) {
				$helper['location'] = $event->getLocation()->getName();
				$helper['locationics'] = $this->foldline($event->getLocation()->getName());
			}

			// check if we have to cancel the event
			if ($this->subscriberRepository->countAllByEvent($event) < $event->getMinSubscriber()) {
				// --> ok, we have to cancel the event because not enough subscriber were found

				// email to all subscribers
				foreach($event->getSubscribers() as $subscriber) {
					$cronLog .= 'Absage an Teilnehmer: ' . $event->getTitle() . ': '. strftime('%x %H:%M', $event->getStartDateTime()->getTimestamp()) . ' --> '. $subscriber->getEmail() ."\n";
					$out = $this->sendTemplateEmail(
						array($subscriber->getEmail() => $subscriber->getName()),
						array($event->getContact()->getEmail() => $event->getContact()->getName()),
						'Absage der Veranstaltung: ' . $event->getTitle(),
						'CancellEvent',
						array(	'event' => $event,
								'subscribers' => '',
								'helper' => $helper,
								'attachIcs' => TRUE,
						)
					);
				}

				$cronLog .= 'Absage an Veranstalter: ' . $event->getTitle() . ': '. strftime('%x %H:%M', $event->getStartDateTime()->getTimestamp()) . ' --> '. $event->getContact()->getEmail() ."\n";
				// email to event owner
				$out = $this->sendTemplateEmail(
					array($event->getContact()->getEmail() => $event->getContact()->getName()),
					array($senderEmailAddress => 'SLUB Veranstaltungen - noreply'),
					'Absage der Veranstaltung: ' . $event->getTitle(),
					'CancellEvent',
					array(	'event' => $event,
							'subscribers' => $event->getSubscribers(),
							'helper' => $helper,
							'attachCsv' => TRUE,
							'attachIcs' => TRUE,
					)
				);
				if ($out >= 1) {
					$event->setSubEndDateInfoSent(TRUE);
					$event->setCancelled(TRUE);
				}
			} else {
				// event takes place but subscription is not possible anymore...
				// email to event owner
				$cronLog .= 'Anmeldefrist abgelaufen an Veranstalter: ' . $event->getTitle() . ': '. strftime('%x %H:%M', $event->getStartDateTime()->getTimestamp()) . ' --> '. $event->getContact()->getEmail() ."\n";
				$out = $this->sendTemplateEmail(
					array($event->getContact()->getEmail() => $event->getContact()->getName()),
					array($senderEmailAddress => 'SLUB Veranstaltungen - noreply'),
					'Veranstaltung Anmeldefrist abgelaufen: ' . $event->getTitle(),
					'DeadlineReached',
					array(	'event' => $event,
							'subscribers' => $event->getSubscribers(),
							'helper' => $helper,
							'attachCsv' => TRUE,
							'attachIcs' => TRUE,
					)
				);
				if ($out == 1)
					$event->setSubEndDateInfoSent(TRUE);
			}
		}

		echo $cronLog;
		//~ echo count($allevents);
		//~ return;
    }

	/**
	 * makeStatisticsReportsCommand
	 *
	 * @param int stroagePid
	 * @param string senderEmailAddress
	 * @param string receiverEmailAddress
	 * @return void
	*/
    public function makeStatisticsReportsCommand($storagePid, $senderEmailAddress = '', $receiverEmailAddress = '') {

		// do some init work...
		$this->initializeAction();

		// abort if no storagePid is found
		if (! t3lib_utility_Math::canBeInterpretedAsInteger($storagePid)) {
			echo "NO storagePid given. Please enter the storagePid in the scheduler task.";
			exit(1);
		}
		// abort if no senderEmailAddress is found
		if (empty($senderEmailAddress)) {
			echo "NO senderEmailAddress given. Please enter the senderEmailAddress in the scheduler task.";
			exit(1);
		}
		// abort if no senderEmailAddress is found
		if (empty($receiverEmailAddress)) {
			echo "NO receiverEmailAddress given. Please enter the receiverEmailAddress in the scheduler task.";
			exit(1);
		}

		// set storagePid to point extbase to the right repositories
		$configurationArray = array(
			'persistence' => array(
				'storagePid' => $storagePid
			)
		);
		$this->configurationManager->setConfiguration($configurationArray);

		// start the work...

		// 1. get the categories
		$categories = $this->categoryRepository->findAll();
//~ t3lib_utility_Debug::debug($categories, 'categories');
		foreach ($categories as $uid => $category)
					$searchParameter['category'][$uid] = $uid;
		//~ $categories2 = $this->categoryRepository->findAllByUids($categories);

		// 2. get events of last month
		$startDateTime = strtotime('first day of last month 00:00:00');
		$endDateTime = strtotime('last day of last month 23:59:59');

		$allevents = $this->eventRepository->findAllByCategoriesAndDateInterval($searchParameter['category'], $startDateTime, $endDateTime);
		//~ $allevents = $this->eventRepository->findAllByCategories($categories2);
		//~ findFreeByCategory
//~ 	$allevents = $this->eventRepository->findAllSubscriptionEnded();
		$helper['nameto'] = $receiverEmailAddress;
		// email to event owner
		$out = $this->sendTemplateEmail(
			array($receiverEmailAddress => ''),
			array($senderEmailAddress => 'SLUB Veranstaltungen - noreply'),
			'Statistik Report Veranstaltungen: '. ': '. strftime('%x', $startDateTime) . ' - '. strftime('%x', $endDateTime),
			'Statistics',
			array(	'events' => $allevents,
					'categories' => $categories,
					'helper' => $helper,
					'attachCsv' => TRUE,
					'attachIcs' => FALSE,
			)
		);

	}

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
	protected function sendTemplateEmail(array $recipient, array $sender, $subject, $templateName, array $variables = array()) {

		$emailViewHTML = $this->objectManager->create('Tx_Fluid_View_StandaloneView');
		$emailViewHTML->getRequest()->setControllerExtensionName($this->extensionName);
		$emailViewHTML->setFormat('html');
		$emailViewHTML->assignMultiple($variables);

		$templateRootPath = PATH_site . 'typo3conf/ext/slub_events/Resources/Private/Backend/Templates/';
		$partialRootPath = PATH_site . 'typo3conf/ext/slub_events/Resources/Private/Backend/Partials/';

		$emailViewHTML->setTemplatePathAndFilename($templateRootPath . 'Email/' . $templateName . '.html');
		$emailViewHTML->setPartialRootPath($partialRootPath);

		$message = t3lib_div::makeInstance('t3lib_mail_Message');
		$message->setTo($recipient)
				->setFrom($sender)
				->setCharset('utf-8')
				->setSubject($subject);

		// attach ICS-File
		//~ $message->attach(Swift_Attachment::fromPath($eventIcsFile)
							//~ ->setFilename('invite.ics')
							//~ ->setContentType('application/ics'));
		// Plain text example
		$emailTextHTML = $emailViewHTML->render();
		$message->setBody($this->html2rest($emailTextHTML), 'text/plain');

		// HTML Email
		$message->addPart($emailTextHTML, 'text/html');

		// attach ics-File
		if ($variables['attachIcs'] == TRUE) {
			$ics = $this->objectManager->create('Tx_Fluid_View_StandaloneView');
			$ics->getRequest()->setControllerExtensionName($this->extensionName);
			$ics->setFormat('ics');
			$ics->assignMultiple($variables);

			$ics->setTemplatePathAndFilename($templateRootPath . 'Email/' . $templateName . '.ics');

			$eventIcsFile = PATH_site.'typo3temp/events/'. preg_replace('/[^\w]/', '', $variables['helper']['nameto']).'-'. $templateName.'-'.$variables['event']->getUid().'.ics';
			t3lib_div::writeFileToTypo3tempDir($eventIcsFile,  $ics->render());

			// add ics as part
			$message->addPart($ics->render(), 'text/calendar', 'utf-8');

		}
		// attach CSV-File
		if ($variables['attachCsv'] == TRUE) {
			$csv = $this->objectManager->create('Tx_Fluid_View_StandaloneView');
			$csv->getRequest()->setControllerExtensionName($this->extensionName);
			$csv->setFormat('csv');
			$csv->assignMultiple($variables);

			$csv->setTemplatePathAndFilename($templateRootPath . 'Email/' . $templateName . '.csv');
			$csv->setPartialRootPath($partialRootPath);

			$eventCsvFile = PATH_site.'typo3temp/events/'. preg_replace('/[^\w]/', '', $variables['helper']['nameto']).'-'. strtolower($templateName).'.csv';
			t3lib_div::writeFileToTypo3tempDir($eventCsvFile,  $csv->render());

			// attach CSV-File
			$message->attach(Swift_Attachment::fromPath($eventCsvFile)
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
	public function html2rest($text) {

		$text = strip_tags( html_entity_decode($text, ENT_COMPAT, 'UTF-8'), '<br>,<p>,<b>,<h1>,<h2>,<h3>,<h4>,<h5>,<a>,<li>');
		// header is getting **
		$text = preg_replace('/<h[1-5]>|<\/h[1-5]>/', "**", $text);
		// bold is getting * ([[\w\ \d:\/~\.\?\=&%\"]+])
		$text = preg_replace('/<b>|<\/b>/', "*", $text);
		// get away links but preserve href with class slub-event-link
		$text = preg_replace('/(<a[\ \w\=\"]{0,})(class=\"slub-event-link\" href\=\")([\w\d:\-\/~\.\?\=&%]+)([\"])([\"]{0,1}>)([\ \w\d\p{P}]+)(<\/a>)/', "$6\n$3", $text);
		// Remove separator characters (like non-breaking spaces...)
		$text = preg_replace( '/\p{Z}/u', ' ', $text );
		$text = str_replace('<br />', "\n", $text);
		// get away paragraphs including class, title etc.
		$text = preg_replace('/<p[\s\w\=\"]*>(?s)(.*?)<\/p>/u', "$1\n", $text);
		$text = str_replace('<li>', "- ", $text);
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
