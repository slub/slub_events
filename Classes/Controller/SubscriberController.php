<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Alexander Bigga <alexander.bigga@slub-dresden.de>, SLUB Dresden
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
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
 *
 *
 * @package slub_events
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class Tx_SlubEvents_Controller_SubscriberController extends Tx_SlubEvents_Controller_AbstractController {

	/**
	 * action list
	 *
	 * @return void
	 */
	public function listAction() {
		$subscribers = $this->subscriberRepository->findAll();
		$this->view->assign('subscribers', $subscribers);
	}

	/**
	 * action show
	 *
	 * @param Tx_SlubEvents_Domain_Model_Subscriber $subscriber
	 * @return void
	 */
	public function showAction(Tx_SlubEvents_Domain_Model_Subscriber $subscriber) {
		$this->view->assign('subscriber', $subscriber);
	}

	/**
	 * action EventNotfound
	 *
	 * @return void
	 */
	public function eventNotFoundAction() {

	}

	/**
	 * action initializeNew
	 *
	 * This is necessary to precheck the given event id. If the event
	 * is not found the object is NULL and the newAction is not called.
	 * Otherwise the newAction will use the propertyMapper to convert
	 * the event id to and object. If this object doesn't exist or is
	 * hidden, an exception is thrown (TYPO3 4.7.12)
	 *
	 * @return void
	 */
	public function initializeNewAction() {

		$eventId = $this->getParametersSafely('event');
		$event = NULL;

		if ($eventId != NULL)
			$event = $this->eventRepository->findByUid($eventId);

		if ($event === NULL)
			$this->redirect('eventNotFound');
	}


	/**
	 * action new
	 *
	 * @param Tx_SlubEvents_Domain_Model_Subscriber $newSubscriber
	 * @param Tx_SlubEvents_Domain_Model_Event $event
	 * @param Tx_SlubEvents_Domain_Model_Category $category
	 * @ignorevalidation $newSubscriber
	 * @ignorevalidation $category
	 * @return void
	 */
	public function newAction(Tx_SlubEvents_Domain_Model_Subscriber $newSubscriber = NULL, Tx_SlubEvents_Domain_Model_Event $event = NULL, Tx_SlubEvents_Domain_Model_Category $category = NULL) {

		// somebody is calling the action without giving an event --> useless
		if ($event === NULL)
			$this->redirect('eventNotFound');

		// this is a little stupid with the rewritten property mapper from
		// extbase 1.4, because the object is never NULL!
		// anyway we can set default values here which are overwritten if
		// already POST values exists. extbase vodoo ;-)
		if($newSubscriber === NULL) {

			$newSubscriber = t3lib_div::makeInstance('Tx_SlubEvents_Domain_Model_Subscriber');
			$newSubscriber->setNumber(1);

			if (!empty($GLOBALS['TSFE']->fe_user->user['username'])) {

					$newSubscriber->setCustomerid($GLOBALS['TSFE']->fe_user->user['username']);
				$loggedIn = 'readonly'; // css class for form
			} else
				$loggedIn = ''; // css class for form

			if (!empty($GLOBALS['TSFE']->fe_user->user['name']))
				$newSubscriber->setName($GLOBALS['TSFE']->fe_user->user['name']);

			if (!empty($GLOBALS['TSFE']->fe_user->user['email']))
				$newSubscriber->setEmail($GLOBALS['TSFE']->fe_user->user['email']);

		}

		$this->view->assign('event', $event);
		$this->view->assign('category', $category);
		$this->view->assign('newSubscriber', $newSubscriber);
		$this->view->assign('loggedIn', $loggedIn);
	}

	/**
	 * action create
	 * // gets validated automatically if name is like this: ...Tx_SlubEvents_Domain_Validator_SubscriberValidator
	 *
	 * @param Tx_SlubEvents_Domain_Model_Subscriber $newSubscriber
	 * @param Tx_SlubEvents_Domain_Model_Event $event
	 * @param Tx_SlubEvents_Domain_Model_Category $category
	 * @validate $event Tx_SlubEvents_Domain_Validator_EventSubscriptionAllowedValidator
	 * @ignorevalidation $category
	 * @return void
	 */
	public function createAction(Tx_SlubEvents_Domain_Model_Subscriber $newSubscriber, Tx_SlubEvents_Domain_Model_Event $event, Tx_SlubEvents_Domain_Model_Category $category = NULL) {

						// add subscriber to event
						$editcode = hash('sha256', rand().$newSubscriber->getEmail().time());
						$newSubscriber->setEditcode($editcode);
						$event->addSubscriber($newSubscriber);

						// Genius Bar Specials:
						if ($event->getGeniusBar()) {
							$event->setTitle($category->getTitle());
							$event->setDescription($category->getDescription());
						}

						// send email(s)
						$helper['now'] = time();
						// rfc2445.txt: lines SHOULD NOT be longer than 75 octets --> line folding
						$helper['description'] = $this->foldline($event->getDescription());
						// location may be empty...
						if (is_object($event->getLocation())) {
							$helper['location'] = $event->getLocation()->getName();
							$helper['locationics'] = $this->foldline($event->getLocation()->getName());
						}
						$helper['nameto'] = strtolower(str_replace(array(',', ' '), array('', '-'), $newSubscriber->getName()));

						// startDateTime may never be empty
						$helper['start'] = $event->getStartDateTime()->getTimestamp();
						// endDateTime may be empty
						if (($event->getEndDateTime() instanceof DateTime) && ($event->getStartDateTime() != $event->getEndDateTime()))
							$helper['end'] = $event->getEndDateTime()->getTimestamp();

						if ($event->isAllDay()) {
							$helper['allDay'] = 1;
						}

						// email to customer
						$this->sendTemplateEmail(
							array($newSubscriber->getEmail() => $newSubscriber->getName()),
							array($event->getContact()->getEmail() => $event->getContact()->getName()),
							'Ihre Anmeldung: ' . $event->getTitle(),
							'Subscribe',
							array(	'event' => $event,
									'subscriber' => $newSubscriber,
									'helper' => $helper,
									'settings' => $this->settings,
							)
						);

						// only send, if maximum is reached...
						if (($this->subscriberRepository->countAllByEvent($event) + $newSubscriber->getNumber()) == $event->getMaxSubscriber()) {
							$helper['nameto'] = strtolower(str_replace(array(',', ' '), array('', '-'), $event->getContact()->getName()));

							// email to event owner
							$this->sendTemplateEmail(
								array($event->getContact()->getEmail() => $event->getContact()->getName()),
								array($this->settings['senderEmailAddress'] => Tx_Extbase_Utility_Localization::translate('tx_slubevents.be.eventmanagement', 'slub_events') . ' - noreply'),
								'Veranstaltung ausgebucht: ' . $event->getTitle(),
								'Maximumreached',
								array(	'event' => $event,
										'subscribers' => $event->getSubscribers(),
										'helper' => $helper,
										'settings' => $this->settings,
										'attachSubscriberAsCsv' => TRUE,
								)
							);
						}

						// reset session data
						$this->setSessionData('editcode', '');

						// clear cache on all cached list pages
						$this->clearAllEventListCache();
						$this->view->assign('event', $event);
						$this->view->assign('category', $category);
						$this->view->assign('newSubscriber', $newSubscriber);
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

			$ics = $this->objectManager->create('Tx_Fluid_View_StandaloneView');
			$ics->getRequest()->setControllerExtensionName($this->extensionName);
			$ics->setFormat('ics');
			$ics->assignMultiple($variables);

			$extbaseFrameworkConfiguration = $this->configurationManager->getConfiguration(Tx_Extbase_Configuration_ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
			$templateRootPath = t3lib_div::getFileAbsFileName($extbaseFrameworkConfiguration['view']['templateRootPath']);
			$partialRootPath = t3lib_div::getFileAbsFileName($extbaseFrameworkConfiguration['view']['partialRootPath']);

			$emailViewHTML->setTemplatePathAndFilename($templateRootPath . 'Email/' . $templateName . '.html');
			$emailViewHTML->setPartialRootPath($partialRootPath);

			$ics->setTemplatePathAndFilename($templateRootPath . 'Email/' . $templateName . '.ics');

			$eventIcsFile = PATH_site.'typo3temp/events/'. preg_replace('/[^\w]/', '', $variables['helper']['nameto']).'-'. strtolower($templateName).'-'.$variables['event']->getUid().'.ics';
			t3lib_div::writeFileToTypo3tempDir($eventIcsFile,  $ics->render());

			$message = t3lib_div::makeInstance('t3lib_mail_Message');
			$message->setTo($recipient)
					->setFrom($sender)
					->setCharset('utf-8')
					->setSubject($subject);

			// attach ICS-File
			//~ $message->attach(Swift_Attachment::fromPath($eventIcsFile)
								//~ ->setFilename('invite.ics')
								//~ ->setDisposition('inline')
								//~ ->setContentType('text/calendar; charset="utf-8"; method=REQUEST'));

			// attach CSV-File
			if ($variables['attachSubscriberAsCsv'] == TRUE) {
				$csv = $this->objectManager->create('Tx_Fluid_View_StandaloneView');
				$csv->getRequest()->setControllerExtensionName($this->extensionName);
				$csv->setFormat('csv');
				$csv->assignMultiple($variables);

				$csv->setTemplatePathAndFilename($templateRootPath . 'Email/' . $templateName . '.csv');
				$csv->setPartialRootPath($partialRootPath);

				$eventCsvFile = PATH_site.'typo3temp/events/'. preg_replace('/[^\w]/', '', $variables['helper']['nameto']).'-'. strtolower($templateName).'-'.$variables['event']->getUid().'.csv';
				t3lib_div::writeFileToTypo3tempDir($eventCsvFile,  $csv->render());

				$message->attach(Swift_Attachment::fromPath($eventCsvFile)
							->setContentType('text/csv'));
			}

			// Plain text example
			//~ $message->setBody($emailView->render(), 'text/plain');
			$emailTextHTML = $emailViewHTML->render();
			$message->setBody($this->html2rest($emailTextHTML), 'text/plain');

			$message->addPart($ics->render(), 'text/calendar', 'utf-8');

			// HTML Email
			$message->addPart($emailTextHTML, 'text/html');

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

	/**
	 * Set session data
	 *
	 * @param $key
	 * @param $data
	 * @return
	 */
	public function setSessionData($key, $data) {

		$GLOBALS["TSFE"]->fe_user->setKey("ses", $key, $data);

		return;
	}

	/**
	 * Get session data
	 *
	 * @param $key
	 * @return
	 */
	public function getSessionData($key) {

					    return $GLOBALS["TSFE"]->fe_user->getKey("ses", $key);
	}

	/**
	 * Clear cache of all pages with slubevents_eventlist plugin
	 * This way the plugin may stay cached but on every delete or insert of subscribers, the cache gets cleared.
	 *
	 * @return
	 */
	public function clearAllEventListCache() {

				$select = 'DISTINCT pid';
				$table = 'tt_content';
				$query = 'list_type = \'slubevents_eventlist\' AND hidden = 0 AND deleted = 0';

				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select, $table ,$query);

				while (($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))) {
					$pluginPageIds[] = $row['pid'];
				};

				$GLOBALS['TSFE']->clearPageCacheContent_pidList(implode(',', $pluginPageIds));

				return;
	}

	/**
	 * action delete
	 *
	 * @param Tx_SlubEvents_Domain_Model_Event $event
	 * @param string $subscriber
	 * @ignorevalidation $event
	 * @ignorevalidation $editcode
	 * @return void
	 */
	public function deleteAction(Tx_SlubEvents_Domain_Model_Event $event = NULL, $editcode) {

		// somebody is calling the action without giving an event --> useless
		if ($event === NULL)
			$this->redirect('eventNotFound');

		// delete for which subscriber?
		$subscriber = $this->subscriberRepository->findAllByEditcode($editcode)->getFirst();

		$event->removeSubscriber($subscriber);

		// some helper timestamps for ics-file
		$helper['now'] = time();
		$helper['isdelete'] = 1;
		$helper['description'] = $this->foldline($event->getDescription());
		// location may be empty...
		if (is_object($event->getLocation())) {
			$helper['location'] = $event->getLocation()->getName();
			$helper['locationics'] = $this->foldline($event->getLocation()->getName());
		}
		$helper['nameto'] = strtolower(str_replace(array(',', ' '), array('', '-'), $subscriber->getName()));

		$helper['start'] = $event->getStartDateTime()->getTimestamp();
		// endDate may be empty
		if (is_object($event->getEndDateTime()) || ($event->getStartDateTime() != $event->getEndDateTime()))
			$helper['end'] = $event->getEndDateTime()->getTimestamp();

		if ($event->isAllDay()) {
			$helper['allDay'] = 1;
		}

		$this->sendTemplateEmail(
			array($subscriber->getEmail() => $subscriber->getName()),
			array($event->getContact()->getEmail() => $event->getContact()->getName()),
			'Ihre Abmeldung: ' . $event->getTitle(),
			'Unsubscribe',
			array(	'event' => $event,
					'subscriber' => $subscriber,
					'helper' => $helper,
					'settings' => $this->settings,
			)
		);

		$this->clearAllEventListCache();
		$this->view->assign('event', $event);
		$this->view->assign('subscriber', $subscriber);
	}

	/**
	 * action beIcsInvitation
	 *
	 * --> see ics template in Resources/Private/Backend/Templates/Email/
	 *
	 * @param Tx_SlubEvents_Domain_Model_Event $event
	 * @ignorevalidation $event
	 * @return void
	 */
	public function beIcsInvitationAction(Tx_SlubEvents_Domain_Model_Event $event) {

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
		$helper['description'] = $this->foldline($event->getDescription());
		// location may be empty...
		if (is_object($event->getLocation())) {
			$helper['location'] = $event->getLocation()->getName();
			$helper['locationics'] = $this->foldline($event->getLocation()->getName());
		}
		$helper['nameto'] = strtolower(str_replace(array(',', ' '), array('', '-'), $event->getContact()->getName()));

		$this->sendTemplateEmail(
			array($event->getContact()->getEmail() => $event->getContact()->getName()),
			array($this->settings['senderEmailAddress'] => Tx_Extbase_Utility_Localization::translate('tx_slubevents.be.eventmanagement', 'slub_events') . ' - noreply'),
			'Termineinladung: ' . $event->getTitle(),
			'Invitation',
			array(	'event' => $event,
					'subscribers' => $event->getSubscribers(),
					'attachSubscriberAsCsv' => TRUE,
					'helper' => $helper,
					'settings' => $this->settings,
			)
		);

		$this->view->assign('event', $event);
	}

	/**
	 * action beList
	 *
	 * @return void
	 */
	public function beListAction() {

						// get data from BE session
						$sessionData = $GLOBALS['BE_USER']->getSessionData('tx_slubevents');
						// get search parameters from BE user configuration
						$ucData = $GLOBALS['BE_USER']->uc['moduleData']['slubevents'];

						// get search parameters from POST variables
						$searchParameter = $this->getParametersSafely('searchParameter');
						if (is_array($searchParameter)) {
							$ucData['searchParameter'] = $searchParameter;
							$sessionData['selectedStartDateStamp'] = $searchParameter['selectedStartDateStamp'];
							//~ $GLOBALS['BE_USER']->setAndSaveSessionData('tx_slubevents', $sessionData);
							$GLOBALS['BE_USER']->uc['moduleData']['slubevents'] = $ucData;
							$GLOBALS['BE_USER']->writeUC($GLOBALS['BE_USER']->uc);
							// save session data
							$GLOBALS['BE_USER']->setAndSaveSessionData('tx_slubevents', $sessionData);
						} else {
							// no POST vars --> take BE user configuration
							$searchParameter = $ucData['searchParameter'];
						}

						// set the startDateStamp
						// startDateStamp is saved in session data NOT user data
						if (empty($selectedStartDateStamp)) {
							if (!empty($sessionData['selectedStartDateStamp']))
								$selectedStartDateStamp = $sessionData['selectedStartDateStamp'];
							else
								$selectedStartDateStamp = date('d-m-Y');
						}

						$categories = $this->categoryRepository->findAllTree();

						if (is_array($searchParameter['selectedCategories'])) {
							$this->view->assign('selectedCategories', $searchParameter['selectedCategories']);
						}
						else {
							// if no category selection in user settings present --> look for the root categories
							if (! is_array($searchParameter['category']))
								foreach ($categories as $uid => $category)
									$searchParameter['category'][$uid] = $uid;
							$this->view->assign('categoriesSelected', $searchParameter['category']);
						}
						$this->view->assign('selectedStartDateStamp', $selectedStartDateStamp);
						if (is_array($searchParameter['category']))
							$events = $this->eventRepository->findAllByCategoriesAndDate($searchParameter['category'], strtotime($selectedStartDateStamp));

						$this->view->assign('categories', $categories);
						$this->view->assign('events', $events);

						$subscribers = $this->subscriberRepository->findAllByEvents($events);

						$this->view->assign('subscribers', $subscribers);
	}

}

?>
