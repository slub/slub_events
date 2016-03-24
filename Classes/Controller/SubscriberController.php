<?php
	namespace Slub\SlubEvents\Controller;

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
	use TYPO3\CMS\Core\Utility\GeneralUtility;
	use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
	use Slub\SlubEvents\Helper\EmailHelper;

class SubscriberController extends AbstractController {

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
	 * @param \Slub\SlubEvents\Domain\Model\Subscriber $subscriber
	 * @return void
	 */
	public function showAction(\Slub\SlubEvents\Domain\Model\Subscriber $subscriber) {
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
	 * action SubscriberNotfound
	 *
	 * @return void
	 */
	public function subscriberNotFoundAction() {

	}

	/**
	 * action initializeNew
	 *
	 * This is necessary to precheck the given event id. If the event
	 * is not found the object is NULL and the newAction is not called.
	 * Otherwise the newAction will use the propertyMapper to convert
	 * the event id to and object. If this object doesn't exist or is
	 * hidden, an exception is thrown (TYPO3 4.7.x)
	 *
	 * @return void
	 */
//	public function initializeNewAction() {
//
//		$eventId = $this->getParametersSafely('event');
//		$event = NULL;
//
//		if ($eventId != NULL) {
//			$event = $this->eventRepository->findByUid($eventId);
//		}
//
//		if ($event === NULL) {
//			$this->redirect('eventNotFound');
//		}
//
//	}


	/**
	 * action initializeDelete
	 *
	 * This is necessary to precheck the given event id. If the event
	 * is not found the object is NULL and the newAction is not called.
	 * Otherwise the newAction will use the propertyMapper to convert
	 * the event id to and object. If this object doesn't exist or is
	 * hidden, an exception is thrown (TYPO3 4.7.x)
	 *
	 * @return void
	 */
//	public function initializeDeleteAction() {
//
//		$eventId = $this->getParametersSafely('event');
//		$event = NULL;
//
//		if ($eventId != NULL) {
//			$event = $this->eventRepository->findByUid($eventId);
//		}
//
//		if ($event === NULL) {
//			$this->redirect('eventNotFound');
//		}
//
//	}

	/**
	 * action new
	 *
	 * @param \Slub\SlubEvents\Domain\Model\Subscriber $newSubscriber
	 * @param \Slub\SlubEvents\Domain\Model\Event $event
	 * @param \Slub\SlubEvents\Domain\Model\Category $category
	 * @ignorevalidation $newSubscriber
	 * @ignorevalidation $event
	 * @ignorevalidation $category
	 * @return void
	 */
	public function newAction(\Slub\SlubEvents\Domain\Model\Subscriber $newSubscriber = NULL, \Slub\SlubEvents\Domain\Model\Event $event = NULL, \Slub\SlubEvents\Domain\Model\Category $category = NULL) {

		// somebody is calling the action without giving an event --> useless
		if ($event === NULL)
			$this->redirect('eventNotFound');

		// this is a little stupid with the rewritten property mapper from
		// extbase 1.4, because the object is never NULL!
		// anyway we can set default values here which are overwritten if
		// already POST values exists. extbase vodoo ;-)
		if($newSubscriber === NULL) {

			$newSubscriber = GeneralUtility::makeInstance('\Slub\SlubEvents\Domain\Model\Subscriber');
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
	 * @param \Slub\SlubEvents\Domain\Model\Subscriber $newSubscriber
	 * @param \Slub\SlubEvents\Domain\Model\Event $event
	 * @param \Slub\SlubEvents\Domain\Model\Category $category
	 * @validate $event \Slub\SlubEvents\Domain\Validator\EventSubscriptionAllowedValidator
	 * @ignorevalidation $category
	 * @return void
	 */
	public function createAction(\Slub\SlubEvents\Domain\Model\Subscriber $newSubscriber, \Slub\SlubEvents\Domain\Model\Event $event, \Slub\SlubEvents\Domain\Model\Category $category = NULL) {

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
		$helper['description'] = $this->foldline($this->html2rest($event->getDescription()));
		// location may be empty...
		if (is_object($event->getLocation())) {
			if (is_object($event->getLocation()->getParent()->current())){
				$helper['location'] = $event->getLocation()->getParent()->current()->getName() . ', ';
				$helper['locationics'] = $this->foldline($event->getLocation()->getParent()->current()->getName()) . ', ';;
			}
			$helper['location'] .= $event->getLocation()->getName();
			$helper['locationics'] .= $this->foldline($event->getLocation()->getName());
		}
		$helper['nameto'] = strtolower(str_replace(array(',', ' '), array('', '-'), $newSubscriber->getName()));

		// startDateTime may never be empty
		$helper['start'] = $event->getStartDateTime()->getTimestamp();

		// endDate may be empty
		if (($event->getEndDateTime() instanceof \DateTime) && ($event->getStartDateTime() != $event->getEndDateTime())) {
			$helper['end'] = $event->getEndDateTime()->getTimestamp();
		}

		if ($event->isAllDay()) {
			$helper['allDay'] = 1;
		}

		// email to customer
		EmailHelper::sendTemplateEmail(
			array($newSubscriber->getEmail() => $newSubscriber->getName()),
			array($event->getContact()->getEmail() => $event->getContact()->getName()),
			'Ihre Anmeldung: ' . $event->getTitle(),
			'Subscribe',
			array(	'event' => $event,
					'subscriber' => $newSubscriber,
					'helper' => $helper,
					'settings' => $this->settings,
					'attachCsv' => FALSE,
					'attachIcs' => TRUE,
			),
			$this->configurationManager
		);

		// send to contact, if maximum is reached and TS setting is present:
		if ($this->settings['emailToContact']['sendEmailOnMaximumReached'] &&
			($this->subscriberRepository->countAllByEvent($event) + $newSubscriber->getNumber()) == $event->getMaxSubscriber()) {
			$helper['nameto'] = strtolower(str_replace(array(',', ' '), array('', '-'), $event->getContact()->getName()));

			// email to event owner
			EmailHelper::sendTemplateEmail(
				array($event->getContact()->getEmail() => $event->getContact()->getName()),
				array($this->settings['senderEmailAddress'] => LocalizationUtility::translate('tx_slubevents.be.eventmanagement', 'slub_events') . ' - noreply'),
				'Veranstaltung ausgebucht: ' . $event->getTitle(),
				'Maximumreached',
				array(	'event' => $event,
						'subscribers' => $event->getSubscribers(),
						'helper' => $helper,
						'settings' => $this->settings,
						'attachCsv' => TRUE,
						'attachIcs' => TRUE,
				),
				$this->configurationManager
			);
		} // send to contact, on every booking if TS setting is present:
		else if ($this->settings['emailToContact']['sendEmailOnEveryBooking']) {
			$helper['nameto'] = strtolower(str_replace(array(',', ' '), array('', '-'), $event->getContact()->getName()));

			// email to event owner
			EmailHelper::sendTemplateEmail(
				array($event->getContact()->getEmail() => $event->getContact()->getName()),
				array($this->settings['senderEmailAddress'] => LocalizationUtility::translate('tx_slubevents.be.eventmanagement', 'slub_events') . ' - noreply'),
				'Veranstaltung gebucht: ' . $event->getTitle(),
				'Newsubscriber',
				array(	'event' => $event,
						'newsubscriber' => $newSubscriber,
						'subscribers' => $event->getSubscribers(),
						'helper' => $helper,
						'settings' => $this->settings,
						'attachCsv' => FALSE,
						'attachIcs' => FALSE,
				),
				$this->configurationManager
			);
		}

		// reset session data
		$this->setSessionData('editcode', '');

		// we changed the event inside the repository and have to
		// update the repo manually as of TYPO3 6.1
		$this->eventRepository->update($event);

		// clear cache on all cached list pages
		$this->clearAllEventListCache($event->getGeniusBar());
		$this->view->assign('event', $event);
		$this->view->assign('category', $category);
		$this->view->assign('newSubscriber', $newSubscriber);
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

		if (strlen($restofline) > 0) {
			$foldedline = $firstline . "\n ". $restofline;
		} else {
			$foldedline = $firstline;
		}

		return $foldedline;
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


	/**
	 * Clear cache of all pages with cached slubevents content.
	 * This way the plugin may stay cached but on every delete or insert
	 * of subscribers, the cache gets cleared.

	 * @param integer $isGeniusBar
	 *
	 * @return
	 */
	public function clearAllEventListCache($isGeniusBar = 0) {

		$tcemain = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\DataHandling\\DataHandler');

		// next two lines are necessary... don't know why.
		$tcemain->stripslashes_values = 0;
		$tcemain->start(array(), array());

		if ($isGeniusBar) {

			$tcemain->clear_cacheCmd('cachetag:tx_slubevents_cat_'.$this->settings['storagePid']);

		} else {

			$tcemain->clear_cacheCmd('cachetag:tx_slubevents_'.$this->settings['storagePid']);

		}

		return;

	}

	/**
	 * action delete
	 *
	 * @param \Slub\SlubEvents\Domain\Model\Event $event
	 * @param string $editcode
	 * @ignorevalidation $event
	 * @return void
	 */
	public function deleteAction(\Slub\SlubEvents\Domain\Model\Event $event = NULL, $editcode) {

		// somebody is calling the action without giving an event --> useless
		if ($event === NULL || empty($editcode)) {
			$this->redirect('eventNotFound');
		}

		// delete for which subscriber?
		$subscriber = $this->subscriberRepository->findAllByEditcode($editcode)->getFirst();

		if (! is_object($subscriber)) {
			$this->redirect('subscriberNotFound');
		}
		// get all subscribers of event
		$allsubscribers = $event->getSubscribers();

		// check if subscriber has really subscribed to this event
		if ($allsubscribers->offsetExists($subscriber)) {
			$event->removeSubscriber($subscriber);
		} else {
			// ohh, someone tries to unsubscribe but has not subscribed or is already unsubscribed.
			$this->redirect('subscriberNotFound');
		}

		// some helper timestamps for ics-file
		$helper['now'] = time();
		$helper['isdelete'] = 1;
		$helper['description'] = $this->foldline($this->html2rest($event->getDescription()));
		// location may be empty...
		if (is_object($event->getLocation())) {
			if (is_object($event->getLocation()->getParent()->current())){
				$helper['location'] = $event->getLocation()->getParent()->current()->getName() . ', ';
				$helper['locationics'] = $this->foldline($event->getLocation()->getParent()->current()->getName()) . ', ';;
			}
			$helper['location'] = $event->getLocation()->getName();
			$helper['locationics'] = $this->foldline($event->getLocation()->getName());
		}
		$helper['nameto'] = strtolower(str_replace(array(',', ' '), array('', '-'), $subscriber->getName()));

		$helper['start'] = $event->getStartDateTime()->getTimestamp();

		// endDate may be empty
		if (($event->getEndDateTime() instanceof \DateTime) && ($event->getStartDateTime() != $event->getEndDateTime())) {
			$helper['end'] = $event->getEndDateTime()->getTimestamp();
		}

		if ($event->isAllDay()) {
			$helper['allDay'] = 1;
		}

		// send to contact, if
		//  1. new subscriber count is below minSubscriber
		//     AND
		//  2. before subscriber count was above minSubscriber -> event was guaranteed
		//     AND
		//  3. settings sendEmailOnFreeAgain
		if ($this->settings['emailToContact']['sendEmailOnFreeAgain'] &&
			($this->subscriberRepository->countAllByEvent($event) >= $event->getMinSubscriber()) &&
			($this->subscriberRepository->countAllByEvent($event) - $subscriber->getNumber()) < $event->getMinSubscriber()
			) {
			$helper['nameto'] = strtolower(str_replace(array(',', ' '), array('', '-'), $event->getContact()->getName()));

			// email to event owner
			EmailHelper::sendTemplateEmail(
				array($event->getContact()->getEmail() => $event->getContact()->getName()),
				array($this->settings['senderEmailAddress'] => LocalizationUtility::translate('tx_slubevents.be.eventmanagement', 'slub_events')),
				'Veranstaltung wegen Abmeldung nicht mehr gesichert: ' . $event->getTitle(),
				'Minimumreachedagain',
				array(	'event' => $event,
						'subscribers' => $event->getSubscribers(),
						'subscriberCount' => $this->subscriberRepository->countAllByEvent($event) - $subscriber->getNumber(),
						'helper' => $helper,
						'settings' => $this->settings,
						'attachCsv' => FALSE,
						'attachIcs' => TRUE,
				),
				$this->configurationManager
			);
		}

		EmailHelper::sendTemplateEmail(
			array($subscriber->getEmail() => $subscriber->getName()),
			array($event->getContact()->getEmail() => $event->getContact()->getName()),
			'Ihre Abmeldung: ' . $event->getTitle(),
			'Unsubscribe',
			array(	'event' => $event,
					'subscriber' => $subscriber,
					'helper' => $helper,
					'settings' => $this->settings,
					'attachCsv' => FALSE,
					'attachIcs' => TRUE,
			),
			$this->configurationManager
		);

		// we changed the event inside the repository and have to
		// update the repo manually as of TYPO3 6.1
		$this->eventRepository->update($event);

		$this->clearAllEventListCache($event->getGeniusBar());
		$this->view->assign('event', $event);
		$this->view->assign('subscriber', $subscriber);
	}

	/**
	 * action beIcsInvitation
	 *
	 * --> see ics template in Resources/Private/Backend/Templates/Email/
	 *
	 * @param \Slub\SlubEvents\Domain\Model\Event $event
	 * @ignorevalidation $event
	 * @return void
	 */
	public function beIcsInvitationAction(\Slub\SlubEvents\Domain\Model\Event $event) {

		// startDateTime may never be empty
		$helper['start'] = $event->getStartDateTime()->getTimestamp();
		// endDateTime may be empty
		if (($event->getEndDateTime() instanceof \DateTime) && ($event->getStartDateTime() != $event->getEndDateTime()))
			$helper['end'] = $event->getEndDateTime()->getTimestamp();
		else
			$helper['end'] = $helper['start'];

		if ($event->isAllDay()) {
			$helper['allDay'] = 1;
		}

		$helper['now'] = time();
		$helper['description'] = $this->foldline($this->html2rest($event->getDescription()));
		// location may be empty...
		if (is_object($event->getLocation())) {
			if (is_object($event->getLocation()->getParent()->current())){
				$helper['location'] = $event->getLocation()->getParent()->current()->getName() . ', ';
				$helper['locationics'] = $this->foldline($event->getLocation()->getParent()->current()->getName()) . ', ';;
			}
			$helper['location'] = $event->getLocation()->getName();
			$helper['locationics'] = $this->foldline($event->getLocation()->getName());
		}
		$helper['nameto'] = strtolower(str_replace(array(',', ' '), array('', '-'), $event->getContact()->getName()));

		EmailHelper::sendTemplateEmail(
			array($event->getContact()->getEmail() => $event->getContact()->getName()),
			array($this->settings['senderEmailAddress'] => LocalizationUtility::translate('tx_slubevents.be.eventmanagement', 'slub_events')),
			'Termineinladung: ' . $event->getTitle(),
			'Invitation',
			array(	'event' => $event,
					'subscribers' => $event->getSubscribers(),
					'helper' => $helper,
					'settings' => $this->settings,
					'attachCsv' => TRUE,
					'attachIcs' => TRUE,
			),
			$this->configurationManager
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

		if (is_array($searchParameter['category'])) {

			$events = $this->eventRepository->findAllByCategoriesAndDate($searchParameter['category'], strtotime($selectedStartDateStamp));

		}

		$this->view->assign('categories', $categories);
		$this->view->assign('events', $events);

		$subscribers = $this->subscriberRepository->findAllByEvents($events);

		$this->view->assign('subscribers', $subscribers);
	}

	/**
	 * action beOnlineSurveyAction
	 *
	 * --> see ics template in Resources/Private/Backend/Templates/Email/
	 *
	 * @param \Slub\SlubEvents\Domain\Model\Event $event
	 * @param integer $step
	 * @ignorevalidation $event
	 * @return void
	 */
	public function beOnlineSurveyAction(\Slub\SlubEvents\Domain\Model\Event $event, $step = 0) {

		// get the onlineSurveyLink and potential timestamp of last sent
		$onlineSurveyLink = GeneralUtility::trimExplode('|', $event->getOnlinesurvey(), TRUE);

		// set the link to the current object to get access inside the email
		$event->setOnlinesurvey($onlineSurveyLink[0]);

		if ($step == 0) {
			/** @var \TYPO3\CMS\Fluid\View\StandaloneView $emailViewHTML */
			$emailViewHTML = $this->objectManager->get('TYPO3\\CMS\\Fluid\\View\\StandaloneView');

			$emailViewHTML->getRequest()->setControllerExtensionName($this->extensionName);
			$emailViewHTML->setFormat('html');
			//~ $emailText->assignMultiple($variables);
			$emailViewHTML->assign('onlineSurveyLink', $onlineSurveyLink[0]);
			$emailViewHTML->assign('event', $event);
			$emailViewHTML->assign('subscriber', array('name' => '###Name wird automatisch ausgefüllt###'));

			$extbaseFrameworkConfiguration = $this->configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
			$templateRootPath = GeneralUtility::getFileAbsFileName($extbaseFrameworkConfiguration['view']['templateRootPath']);
			$partialRootPath = GeneralUtility::getFileAbsFileName($extbaseFrameworkConfiguration['view']['partialRootPath']);

			$emailViewHTML->setTemplatePathAndFilename($templateRootPath . 'Email/' . 'OnlineSurvey.html');
			$emailViewHTML->setPartialRootPath($partialRootPath);

			$emailTextHTML = $emailViewHTML->render();

		}

		if ($step == 1) {
			$helper['now'] = time();
			$helper['description'] = $this->foldline($this->html2rest($event->getDescription()));
			// location may be empty...
			if (is_object($event->getLocation())) {
				if (is_object($event->getLocation()->getParent()->current())){
					$helper['location'] = $event->getLocation()->getParent()->current()->getName() . ', ';
					$helper['locationics'] = $this->foldline($event->getLocation()->getParent()->current()->getName()) . ', ';;
				}
				$helper['location'] = $event->getLocation()->getName();
				$helper['locationics'] = $this->foldline($event->getLocation()->getName());
			}

			$allSubscribers = $event->getSubscribers();
			foreach ($allSubscribers as $uid => $subscriber) {
				EmailHelper::sendTemplateEmail(
					array($subscriber->getEmail() => $subscriber->getName()),
					array($event->getContact()->getEmail() => $event->getContact()->getName()),
					'Online-Umfrage zu ' . $event->getTitle(),
					'OnlineSurvey',
					array(	'event' => $event,
							'subscriber' => $subscriber,
							'helper' => $helper,
							'settings' => $this->settings,
							'attachCsv' => FALSE,
							'attachIcs' => FALSE,
					),
					$this->configurationManager
				);
			}

			// change the onlineSurvey link to see, that we sent it already
			$event->setOnlinesurvey($onlineSurveyLink[0] . '|' .time());
			// we changed the event inside the repository and have to
			// update the repo manually as of TYPO3 6.1
			$this->eventRepository->update($event);

		}

		$this->view->assign('event', $event);

		if (isset($onlineSurveyLink[1])) {

			$this->view->assign('onlineSurveyLastSent', $onlineSurveyLink[1]);

		}

		$this->view->assign('subscribers', $event->getSubscribers());
		$this->view->assign('step', $step);
		$this->view->assign('emailText', $emailTextHTML);

	}

}

?>
