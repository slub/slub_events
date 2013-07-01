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
class Tx_SlubEvents_Controller_EventController extends Tx_SlubEvents_Controller_AbstractController {

	/**
	 * eventRepository
	 *
	 * @var Tx_SlubEvents_Domain_Repository_EventRepository
	 * @inject
	 */
	protected $eventRepository;

	/**
	 * action list
	 *
	 * @return void
	 */
	public function listAction() {

t3lib_utility_Debug::debug($this->settings['categorySelection'], 'listAction: ... ');

		if (!empty($this->settings['categorySelection']))
			$events = $this->eventRepository->findAllByCategories(t3lib_div::intExplode(',', $this->settings['categorySelection'], TRUE));
		else
			$events = $this->eventRepository->findAll();

		$this->view->assign('events', $events);
	}

	/**
	 * action show
	 *
	 * @param Tx_SlubEvents_Domain_Model_Event $event
	 * @ignorevalidation $event
	 * @return void
	 */
	public function showAction(Tx_SlubEvents_Domain_Model_Event $event = NULL) {

		// fill registers to be used in ts
		$cObj = t3lib_div::makeInstance('tslib_cObj');
		$cObj->LOAD_REGISTER(
			array(
			    'eventPageTitle' => Tx_Extbase_Utility_Localization::translate('tx_slubevents_domain_model_event', 'slub_events') . ': "' . $event->getTitle() . '" - ' . strftime('%a, %x %H:%M', $event->getStartDateTime()->getTimeStamp()),
			), 'LOAD_REGISTER');

		$this->view->assign('event', $event);
	}

	/**
	 * action new
	 *
	 * @param Tx_SlubEvents_Domain_Model_Event $newEvent
	 * @ignorevalidation $newEvent
	 * @return void
	 */
	public function newAction(Tx_SlubEvents_Domain_Model_Event $newEvent = NULL) {
			    $this->view->assign('newEvent', $newEvent);
	}

	/**
	 * action create
	 *
	 * @param Tx_SlubEvents_Domain_Model_Event $newEvent
	 * @return void
	 */
	public function createAction(Tx_SlubEvents_Domain_Model_Event $newEvent) {
		$this->eventRepository->add($newEvent);
		$this->flashMessageContainer->add('Your new Event was created.');
		$this->redirect('list');
	}

	/**
	 * action edit
	 *
	 * @param Tx_SlubEvents_Domain_Model_Event $event
	 * @ignorevalidation $event
	 * @return void
	 */
	public function editAction(Tx_SlubEvents_Domain_Model_Event $event) {
		$this->view->assign('event', $event);
	}

	/**
	 * action update
	 *
	 * @param Tx_SlubEvents_Domain_Model_Event $event
	 * @return void
	 */
	public function updateAction(Tx_SlubEvents_Domain_Model_Event $event) {
		$this->eventRepository->update($event);
		$this->flashMessageContainer->add('Your Event was updated.');
		$this->redirect('list');
	}

	/**
	 * action delete
	 *
	 * @param Tx_SlubEvents_Domain_Model_Event $event
	 * @return void
	 */
	public function deleteAction(Tx_SlubEvents_Domain_Model_Event $event) {
		$this->eventRepository->remove($event);
		$this->flashMessageContainer->add('Your Event was removed.');
		$this->redirect('list');
	}

	/**
	 * action listOwn
	 *
	 * @return void
	 */
	public function listOwnAction() {

		// two ways:
		// 1. either an editcode is given --> look for this - and only this event
		// 2. a) an editcode is given AND the user is logged in
		// 2. b) the user is logged in

		$subscribers = $this->subscriberRepository->findAllByFeuser();
		$events = $this->eventRepository->findAllBySubscriber($subscribers);

		//~ t3lib_utility_Debug::debug(count($subscribers), 'listOwnAction: sizeof(subscribers)... ');

		$this->view->assign('subscribers', $subscribers);
		$this->view->assign('events', $events);
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
	}

	/**
	 * action beCopy
	 *
	 * @param Tx_SlubEvents_Domain_Model_Event $event
	 * @ignorevalidation $event
	 * @return void
	 */
	public function beCopyAction($event) {

				$availableProperties = Tx_Extbase_Reflection_ObjectAccess::getGettablePropertyNames($event);
				$newEvent =  $this->objectManager->create('Tx_SlubEvents_Domain_Model_Event');

				foreach ($availableProperties as $propertyName) {
					if (Tx_Extbase_Reflection_ObjectAccess::isPropertySettable($newEvent, $propertyName)
						&& !in_array($propertyName, array('uid','pid','subscribers','sub_end_date_time','sub_end_date_info_sent','categories'))) {
						$propertyValue = Tx_Extbase_Reflection_ObjectAccess::getProperty($event, $propertyName);
						Tx_Extbase_Reflection_ObjectAccess::setProperty($newEvent, $propertyName, $propertyValue);
					}
				}

				foreach ($event->getCategories() as $cat) {
					//~ $this->flashMessageContainer->add('Kategorie: '.$cat->getTitle().' wurde kopiert.');
					$newEvent->addCategory($cat);
				}

				if ($event->getGeniusBar())
					$newEvent->setTitle('Wissensbar ' . $newEvent->getContact()->getName());
				else
					$newEvent->setTitle($newEvent->getTitle());

				$newEvent->setHidden(TRUE);

				$this->eventRepository->add($newEvent);

				$this->flashMessageContainer->add('Die Veranstaltung '.$newEvent->getTitle().' wurde kopiert.');
				$this->redirect('beList');
	}

	/**
	 * action listMiniMonth
	 *
	 * @return void
	 */
	public function listMiniMonthAction() {

							    if (!empty($this->settings['categorySelection']))
								    $events = $this->eventRepository->findAllByCategories(t3lib_div::intExplode(',', $this->settings['categorySelection'], TRUE));
							    else
								    $events = $this->eventRepository->findAll();

							    // prepare event array with timestamps for days:
							    foreach ($events as $event) {
								    $eventsPerDay[strtotime('today 00:00:00', $event->getStartDateTime()->getTimestamp())][] = $event->getUid();
							    }

							    $startDate = time();

							    $firstDayOfMonth = strtotime('first day of this month', $startDate);
							    if (date('w', $firstDayOfMonth) == 1) // last day is Monday
								    $firstDay = $firstDayOfMonth;
							    else
								    $firstDay = strtotime('last monday', $firstDayOfMonth);

							    $lastDayOfMonth = strtotime('last day of this month', $startDate);
							    if (date('w', $lastDayOfMonth) == 0) // last day is Sunday
								    $lastDay = $lastDayOfMonth;
							    else
								    $lastDay = strtotime('next sunday', $lastDayOfMonth);

							    for ($d = $firstDay; $d<=$lastDay; $d = strtotime('+1 day', $d)) {
								    unset($day);
								    $day['timestamp'] = $d;
								    if ($d < $firstDayOfMonth || $d > $lastDayOfMonth)
									    $day['active'] = 0;
								    else
									    $day['active'] = 1;

								    if ($eventsPerDay[$d])
									    $day['events'][] = $eventsPerDay[$d];

								    $weeks[date('W', $d)][] = $day;
							    }

							    $this->view->assign('firstDayOfMonth', $firstDayOfMonth);
							    $this->view->assign('events', $events);
							    $this->view->assign('weeks', $weeks);
	}

	/**
	 * action errorAction
	 *
	 * @return void
	 */
	public function errorAction() {

	}

	/**
	 * action ajax
	 *
	 * EXPERIMENTAL!!
	 *
	 * @return void
	 */
	public function ajaxAction() {

							    $events = $this->eventRepository->findAllByCategoriesAndDateInterval(t3lib_div::intExplode(',', $_GET['categories'], TRUE), $_GET['start'], $_GET['stop']);

							    $cObj = $this->configurationManager->getContentObject();
							    foreach ($events as $event) {

								    $foundevent = array();

								    $foundevent['id'] = $event->getUid();
								    $foundevent['title'] = $event->getTitle();
								    $foundevent['start'] = $event->getStartDateTime()->getTimestamp();
								    if ($event->getEndDateTime() instanceof DateTime)
									    $foundevent['end'] = $event->getEndDateTime()->getTimestamp();

								    $conf = array(
									    // Link to current page
									    'parameter' => $_GET['detailPid'],
									    // Set additional parameters
									    'additionalParams' => '&type=0&tx_slubevents_eventlist%5Bevent%5D='.$event->getUid().'&tx_slubevents_eventlist%5Baction%5D=show&tx_slubevents_eventlist%5Bcontroller%5D=Event',
									    // We must add cHash because we use parameters
									    'useCachHash' => true,
									    // We want link only
									    'returnLast' => 'url',
								    );
								    $url = $cObj->typoLink('', $conf);
								    //~
								    $foundevent['url'] = $url;
								    if ($event->getAllDay())
									    $foundevent['allDay'] = true;
								    else
									    $foundevent['allDay'] = false;

								    $jsonevent[] = $foundevent;
							    }
							    return json_encode($jsonevent);
	}

	/**
	 * injectEventRepository
	 *
	 * @param Tx_SlubEvents_Domain_Repository_EventRepository $eventRepository
	 * @return void
	 */
	public function injectEventRepository(Tx_SlubEvents_Domain_Repository_EventRepository $eventRepository) {
		$this->eventRepository = $eventRepository;
	}

}

?>
