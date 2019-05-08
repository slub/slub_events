<?php
namespace Slub\SlubEvents\Controller;

    /***************************************************************
     *  Copyright notice
     *
     *  (c) 2012-2014 Alexander Bigga <alexander.bigga@slub-dresden.de>, SLUB Dresden
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

use Slub\SlubEvents\Domain\Model\Event;
use Slub\SlubEvents\Helper\EmailHelper;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

class EventController extends AbstractController
{

    /**
     * Initializes the current action
     *
     * idea from tx_news extension
     *
     * @return void
     */
    public function initializeAction()
    {

        // Only do this in Frontend Context
        if (!empty($GLOBALS['TSFE']) && is_object($GLOBALS['TSFE'])) {
            // We only want to set the tag once in one request, so we have to cache that statically if it has been done
            static $cacheTagsSet = false;

            /** @var $typoScriptFrontendController \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController */
            $typoScriptFrontendController = $GLOBALS['TSFE'];
            if (!$cacheTagsSet) {
                $typoScriptFrontendController->addCacheTags(
                    [1 => 'tx_slubevents_' . $this->settings['persistence']['storagePid']]
                );
                $cacheTagsSet = true;
            }
            $this->typoScriptFrontendController = $typoScriptFrontendController;
        }
    }

    /**
     * action list
     *
     * @return void
     */
    public function listAction()
    {
        if (!empty($this->settings['categorySelection'])) {
            $categoriesIds = GeneralUtility::intExplode(',', $this->settings['categorySelection'], true);

            if ($this->settings['categorySelectionRecursive']) {
                // add somehow the other categories...
                foreach ($categoriesIds as $category) {
                    $foundRecusiveCategories = $this->categoryRepository->findAllChildCategories($category);
                    if (count($foundRecusiveCategories) > 0) {
                        $categoriesIds = array_merge($foundRecusiveCategories, $categoriesIds);
                    }
                }
            }
            $this->settings['categoryList'] = $categoriesIds;
        }

        if (!empty($this->settings['disciplineSelection'])) {
            $disciplineIds = GeneralUtility::intExplode(',', $this->settings['disciplineSelection'], true);

            if ($this->settings['disciplineSelectionRecursive']) {
                // add somehow the other categories...
                foreach ($disciplineIds as $discipline) {
                    $foundRecusiveDisciplines = $this->disciplineRepository->findAllChildDisciplines($discipline);
                    if (count($foundRecusiveDisciplines) > 0) {
                        $disciplineIds = array_merge($foundRecusiveDisciplines, $disciplineIds);
                    }
                }
            }
            $this->settings['disciplineList'] = $disciplineIds;
        }

        $events = $this->eventRepository->findAllBySettings($this->settings);

        $this->view->assign('events', $events);
    }

    /**
     * action listUpcomming
     *
     * @return void
     */
    public function listUpcomingAction()
    {
        if (!empty($this->settings['categorySelection'])) {
            $categoriesIds = GeneralUtility::intExplode(',', $this->settings['categorySelection'], true);

            if ($this->settings['categorySelectionRecursive']) {
                // add somehow the other categories...
                foreach ($categoriesIds as $category) {
                    $foundRecusiveCategories = $this->categoryRepository->findAllChildCategories($category);
                    if (count($foundRecusiveCategories) > 0) {
                        $categoriesIds = array_merge($foundRecusiveCategories, $categoriesIds);
                    }
                }
            }
            $this->settings['categoryList'] = $categoriesIds;
        }

        if (!empty($this->settings['disciplineSelection'])) {
            $disciplineIds = GeneralUtility::intExplode(',', $this->settings['disciplineSelection'], true);

            if ($this->settings['disciplineSelectionRecursive']) {
                // add somehow the other categories...
                foreach ($disciplineIds as $discipline) {
                    $foundRecusiveDisciplines = $this->disciplineRepository->findAllChildDisciplines($discipline);
                    if (count($foundRecusiveDisciplines) > 0) {
                        $disciplineIds = array_merge($foundRecusiveDisciplines, $disciplineIds);
                    }
                }
            }
            $this->settings['disciplineList'] = $disciplineIds;
        }

        $this->settings['exactlyToTheMinute'] = true;

        $events = $this->eventRepository->findAllBySettings($this->settings);

        $this->view->assign('events', $events);
    }

    /**
     * action initializeShow
     *
     * @return void
     */
//	public function initializeShowAction() {
//
//		$eventId = $this->getParametersSafely('event');
//		$event = NULL;
//
//		if ($eventId != NULL)
//			$event = $this->eventRepository->findByUid($eventId);
//
//		if ($event === NULL)
//			$this->redirect('showNotFound');
//	}

    /**
     * action show
     *
     * @param Event $event
     * @ignorevalidation $event
     *
     * @return void
     */
    public function showAction(Event $event = null)
    {
        if ($event !== null) {
            // fill registers to be used in ts
            $cObj = GeneralUtility::makeInstance(\TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer::class);
            $cObj->cObjGetSingle('LOAD_REGISTER',
                [
                    'eventPageTitle' =>
                        LocalizationUtility::translate(
                            'tx_slubevents_domain_model_event',
                            'slub_events'
                        )
                        . ': "' . $event->getTitle() . '" - ' . strftime(
                            '%a, %x %H:%M',
                            $event->getStartDateTime()->getTimeStamp()
                        ),
                ],
                'LOAD_REGISTER'
            );
        }

        $this->view->assign('event', $event);
    }

    /**
     * action showNotfound
     *
     * @return void
     */
    public function showNotFoundAction()
    {
    }

    /**
     * action new
     *
     * @param Event $newEvent
     * @ignorevalidation $newEvent
     *
     * @return void
     */
    public function newAction(Event $newEvent = null)
    {
        $this->view->assign('newEvent', $newEvent);
    }

    /**
     * action create
     *
     * @param Event $newEvent
     *
     * @return void
     */
    public function createAction(Event $newEvent)
    {
        $this->eventRepository->add($newEvent);
        $this->addFlashMessage('Your new Event was created.');
        $this->redirect('list');
    }

    /**
     * action edit
     *
     * @param Event $event
     * @ignorevalidation $event
     *
     * @return void
     */
    public function editAction(Event $event)
    {
        $this->view->assign('event', $event);
    }

    /**
     * action update
     *
     * @param Event $event
     *
     * @return void
     */
    public function updateAction(Event $event)
    {
        $this->eventRepository->update($event);
        $this->addFlashMessage('Your Event was updated.');
        $this->redirect('list');
    }

    /**
     * action delete
     *
     * @param Event $event
     *
     * @return void
     */
    public function deleteAction(Event $event)
    {
        $this->eventRepository->remove($event);
        $this->addFlashMessage('Your Event was removed.');
        $this->redirect('list');
    }

    /**
     * action listOwn
     *
     * @return void
     */
    public function listOwnAction()
    {

        // + the user is logged in
        // + the username == customerid
        $subscribers = $this->subscriberRepository->findAllByFeuser();

        $events = $this->eventRepository->findAllBySubscriber($subscribers);

        $this->view->assign('subscribers', $subscribers);
        $this->view->assign('events', $events);
    }

    /**
     * action beList
     */
    public function beListAction()
    {
        // get current event of last beIcsInvitationAction
        $currentActiveEvent = $this->getParametersSafely('currentActiveEvent');

        $searchParameter = array();

        // if search was triggered, get search parameters from POST variables
        $submittedSearchParams = $this->getParametersSafely('searchParameter');

        if (is_array($submittedSearchParams)) {

            $searchParameter = $submittedSearchParams;

            // save session data
            $this->setSessionData('tx_slubevents', $searchParameter, true);
        } else {
            // no POST vars --> take BE user configuration
            $searchParameter = $this->getSessionData('tx_slubevents');
        }

        // set the startDateStamp
        if (empty($searchParameter['selectedStartDateStamp'])) {
            $searchParameter['selectedStartDateStamp'] = date('d-m-Y');
        }

        // Categories
        // ------------------------------------------------------------------------------------

        // get the categories
        $categories = $this->categoryRepository->findAllTree();

        // check which categories have been selected
        if (!is_array($searchParameter['category'])) {
            $allCategories = $this->categoryRepository->findAll()->toArray();
            foreach ($allCategories as $category) {
                $searchParameter['category'][$category->getUid()] = $category->getUid();
            }
        }
        $this->view->assign('categoriesSelected', $searchParameter['category']);

        // Contacts
        // ------------------------------------------------------------------------------------
        // check which contacts have been selected
        // get all contacts
        $contacts = $this->contactRepository->findAllSorted();

        // if no contacts selection in user settings present --> look for the root categories
        if (!is_array($searchParameter['contacts'])) {
            $searchParameter['contacts'] = [];
            foreach ($contacts as $uid => $contact) {
                $searchParameter['contacts'][$uid] = $contact->getUid();
            }
        }
        $this->view->assign('contactsSelected', $searchParameter['contacts']);
        // Events
        // ------------------------------------------------------------------------------------
        // get the events to show
        $events = $this->eventRepository->findAllByCategoriesAndDate(
            $searchParameter['category'],
            strtotime($searchParameter['selectedStartDateStamp']),
            $searchParameter['searchString'],
            $searchParameter['contacts'],
            $searchParameter['recurring']
        );

        $pageRenderer = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Page\PageRenderer::class);
        $pageRenderer->loadRequireJsModule('TYPO3/CMS/Backend/DateTimePicker');

        $this->view->assign('selectedStartDateStamp', $searchParameter['selectedStartDateStamp']);
        $this->view->assign('searchString', $searchParameter['searchString']);
        $this->view->assign('categories', $categories);
        $this->view->assign('events', $events);
        $this->view->assign('contacts', $contacts);
        $this->view->assign('currentActiveEvent', $currentActiveEvent);
        $this->view->assign('recurring', $searchParameter['recurring']);
    }

    /**
     * action beCopy
     *
     * @param Event $event
     *
     * @return void
     */
    public function beCopyAction(Event $event)
    {
        $availableProperties = \TYPO3\CMS\Extbase\Reflection\ObjectAccess::getGettablePropertyNames($event);
        /** @var Event $newEvent */
        $newEvent = $this->objectManager->get(Event::class);

        foreach ($availableProperties as $propertyName) {
            if (\TYPO3\CMS\Extbase\Reflection\ObjectAccess::isPropertySettable($newEvent, $propertyName)
                && !in_array($propertyName, [
                    'uid',
                    'pid',
                    'subscribers',
                    'cancelled',
                    'subEndDateTime',
                    'subEndDateInfoSent',
                    'categories',
                    'discipline',
                    'parent'
                ])
            ) {
                $propertyValue = \TYPO3\CMS\Extbase\Reflection\ObjectAccess::getProperty($event, $propertyName);
                // special handling for onlinesurvey field to remove trailing timestamp with sent date
                if ($propertyName == 'onlinesurvey' && (strpos($propertyValue, '|') > 0)) {
                    $propertyValue = substr($propertyValue, 0, strpos($propertyValue, '|'));
                }
                \TYPO3\CMS\Extbase\Reflection\ObjectAccess::setProperty($newEvent, $propertyName, $propertyValue);
            }
        }

        foreach ($event->getCategories() as $cat) {
            $newEvent->addCategory($cat);
        }

        foreach ($event->getDiscipline() as $discipline) {
            $newEvent->addDiscipline($discipline);
        }

        if ($event->getGeniusBar()) {
            $newEvent->setTitle('Wissensbar ' . $newEvent->getContact()->getName());
        } else {
            $newEvent->setTitle($newEvent->getTitle());
        }

        $newEvent->setHidden(true);

        $this->eventRepository->add($newEvent);
        $this->addFlashMessage('Die Veranstaltung ' . $newEvent->getTitle() . ' wurde kopiert.');

        $currentWidgetPage = $this->getParametersSafely('@widget_0');
        if (!$currentWidgetPage) {
          $currentWidgetPage = [ 'currentPage' => 0 ];
        }
        $this->redirect('beList', NULL, NULL, [
          'currentActiveEvent' => $event->getUid(),
          '@widget_0' => $currentWidgetPage
        ]);

    }

    /**
     * action beIcsInvitation
     *
     * --> see ics template in Resources/Private/Backend/Templates/Email/
     *
     * @param Event $event
     * @ignorevalidation $event
     *
     * @return void
     */
    public function beIcsInvitationAction(Event $event)
    {
        $allEvents = [];

        // add all child events if this is a parent recurring event
        if ($event->isRecurring()) {
            $allEvents = $this->eventRepository->findByParent($event)->toArray();
        }
        // put (parent) event to top of array
        array_unshift($allEvents, $event);

        foreach ($allEvents as $singleEvent) {
            // startDateTime may never be empty
            $helper['start'] = $singleEvent->getStartDateTime()->getTimestamp();
            // endDateTime may be empty
            if (($singleEvent->getEndDateTime() instanceof \DateTime)
                && ($singleEvent->getStartDateTime() != $singleEvent->getEndDateTime())
            ) {
                $helper['end'] = $singleEvent->getEndDateTime()->getTimestamp();
            } else {
                $helper['end'] = $helper['start'];
            }

            if ($event->isAllDay) {
                $helper['allDay'] = 1;
            }

            $helper['now'] = time();
            $helper['description'] = $this->foldline($this->html2rest($singleEvent->getDescription()));
            // location may be empty...
            if (is_object($singleEvent->getLocation())) {
                if (is_object($singleEvent->getLocation()->getParent()->current())) {
                    $helper['location'] = $singleEvent->getLocation()->getParent()->current()->getName() . ', ';

                    $helper['locationics'] =
                        $this->foldline($singleEvent->getLocation()->getParent()->current()->getName()) . ', ';
                }
                $helper['location'] = $singleEvent->getLocation()->getName();
                $helper['locationics'] = $this->foldline($singleEvent->getLocation()->getName());
            }
            $helper['eventuid'] = $singleEvent->getUid();

            $icsHelpers[] = $helper;
        }

        $nameTo = strtolower(str_replace([',', ' '], ['', '-'], $event->getContact()->getName()));

        EmailHelper::sendTemplateEmail(
            [$event->getContact()->getEmail() => $event->getContact()->getName()],
            [
                $this->settings['senderEmailAddress'] => LocalizationUtility::translate(
                    'tx_slubevents.be.eventmanagement',
                    'slub_events'
                ),
            ],
            LocalizationUtility::translate(
                'be.icsInvitation',
                'slub_events'
            ) . ':' . $event->getTitle(),
            'Invitation',
            [
                'event'       => $event,
                'subscribers' => $event->getSubscribers(),
                'nameTo'      => $nameTo,
                'helpers'     => $icsHelpers,
                'settings'    => $this->settings,
                'attachCsv'   => true,
                'attachIcs'   => true,
            ],
            $this->configurationManager
        );

        $this->addFlashMessage(LocalizationUtility::translate(
            'be.icsInvitation',
            'slub_events'
        ) . ' "'.$event->getTitle().'" '. LocalizationUtility::translate(
            'be.sentTo',
            'slub_events'
        ) .' ' . $event->getContact()->getEmail() . '.');

        $currentWidgetPage = $this->getParametersSafely('@widget_0');
        if (!$currentWidgetPage) {
          $currentWidgetPage = [ 'currentPage' => 0 ];
        }

        $this->redirect('beList', NULL, NULL, [
          'currentActiveEvent' => $event->getUid(),
          '@widget_0' => $currentWidgetPage
        ]);

    }

    /**
     * action listMonth
     *
     * @return void
     */
    public function listMonthAction()
    {
        if (!empty($this->settings['categorySelection'])) {
            $categoriesIds = GeneralUtility::intExplode(',', $this->settings['categorySelection'], true);

            if ($this->settings['categorySelectionRecursive']) {
                // add somehow the other categories...
                foreach ($categoriesIds as $category) {
                    $foundRecusiveCategories = $this->categoryRepository->findAllChildCategories($category);
                    if (count($foundRecusiveCategories) > 0) {
                        $categoriesIds = array_merge($foundRecusiveCategories, $categoriesIds);
                    }
                }
            }
            $this->settings['categoryList'] = $categoriesIds;
            $categories = $this->categoryRepository->findAllByUidsTree($this->settings['categoryList']);
        }

        if (!empty($this->settings['disciplineSelection'])) {
            $disciplineIds = GeneralUtility::intExplode(',', $this->settings['disciplineSelection'], true);

            if ($this->settings['disciplineSelectionRecursive']) {
                // add somehow the other categories...
                foreach ($disciplineIds as $discipline) {
                    $foundRecusiveDisciplines = $this->disciplineRepository->findAllChildDisciplines($discipline);
                    if (count($foundRecusiveDisciplines) > 0) {
                        $disciplineIds = array_merge($foundRecusiveDisciplines, $disciplineIds);
                    }
                }
            }
            $this->settings['disciplineList'] = $disciplineIds;
            $disciplines = $this->disciplineRepository->findAllByUidsTree($this->settings['disciplineList']);
        }

        $this->view->assign('categories', $categories);
        $this->view->assign('disciplines', $disciplines);
        $this->view->assign('categoriesIds', explode(',', $this->settings['categorySelection']));
        $this->view->assign('disciplinesIds', explode(',', $this->settings['disciplineSelection']));
    }

    /**
     * Initializes the create childs action
     * @param integer $id
     *
     * @return void
     */
    public function initializeCreateChildsAction($id)
    {
        // this does not work reliable in this context (maybe a bug in TYPO3 7.6?)
        // as the childs must be on the same storage pid as the parent, we take
        // the pid and set is as storagePid
        $parentEventRow = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid, pid', 'tx_slubevents_domain_model_event', 'uid=' . (int)$id)->fetch_assoc();
        $this->settings['storagePid'] = $parentEventRow['pid'];
        // set storagePid to point extbase to the right repositories
        $configurationArray = [
            'persistence' => [
                'storagePid' => $parentEventRow['pid'],
            ],
        ];
        $this->configurationManager->setConfiguration($configurationArray);
    }

    /**
     * create or update child events for given event id
     *
     * @param integer $id
     *
     * @return void
     */
    public function createChildsAction($id)
    {
        $this->initializeCreateChildsAction($id);

        $parentEvent = $this->eventRepository->findOneByUidIncludeHidden($id);

        if ($parentEvent) {

            $allChildren = $this->eventRepository->findByParent($parentEvent);

            $childDateTimes = $this->getChildDateTimes($parentEvent);

            $availableProperties = \TYPO3\CMS\Extbase\Reflection\ObjectAccess::getGettablePropertyNames($parentEvent);

            // delete all present child events which are not requested (e.g. from former settings)
            $this->eventRepository->deleteAllNotAllowedChildren($childDateTimes, $parentEvent);

            foreach ($childDateTimes as $childDateTime) {

                $isUpdate = FALSE;

                $childEvent = $this->eventRepository->findOneByStartDateTimeAndParent($childDateTime['startDateTime'], $parentEvent);

                // a childevent for the given startDateTime already exists
                if ($childEvent) {
                    $isUpdate = TRUE;
                } else {
                    // no child event found - create a new one
                    /** @var Event $childEvent */
                    $childEvent = $this->objectManager->get(Event::class);
                }

                foreach ($availableProperties as $propertyName) {
                    if (\TYPO3\CMS\Extbase\Reflection\ObjectAccess::isPropertySettable($childEvent, $propertyName)
                        && !in_array($propertyName, [
                            'uid',
                            'pid',
                            'hidden',
                            'parent',
                            'recurring',
                            'recurring_options',
                            'recurring_end_date_time',
                            'startDateTime',
                            'endDateTime',
                            'subscribers',
                            'cancelled',
                            'subEndDateTime',
                            'subEndDateInfoSent',
                            'categories',
                            'discipline',
                        ])
                    ) {
                        $propertyValue = \TYPO3\CMS\Extbase\Reflection\ObjectAccess::getProperty($parentEvent, $propertyName);
                        // special handling for onlinesurvey field to remove trailing timestamp with sent date
                        if ($propertyName == 'onlinesurvey' && (strpos($propertyValue, '|') > 0)) {
                            $propertyValue = substr($propertyValue, 0, strpos($propertyValue, '|'));
                        }
                        \TYPO3\CMS\Extbase\Reflection\ObjectAccess::setProperty($childEvent, $propertyName, $propertyValue);
                    }
                }

                $childEvent->setParent($parentEvent);

                $childEvent->setStartDateTime($childDateTime['startDateTime']);

                $childEvent->setEndDateTime($childDateTime['endDateTime']);

                if ($childDateTime['subEndDateTime']) {
                    $childEvent->setSubEndDateTime($childDateTime['subEndDateTime']);
                }

                foreach ($parentEvent->getCategories() as $cat) {
                    $childEvent->addCategory($cat);
                }

                foreach ($parentEvent->getDiscipline() as $discipline) {
                    $childEvent->addDiscipline($discipline);
                }

                if ($parentEvent->getGeniusBar()) {
                    $childEvent->setTitle('Wissensbar ' . $childEvent->getContact()->getName());
                } else {
                    $childEvent->setTitle($childEvent->getTitle());
                }

                if ($isUpdate === TRUE) {
                    $this->eventRepository->update($childEvent);
                } else {
                    $this->eventRepository->add($childEvent);
                }

            }

            $persistenceManager = $this->objectManager->get(\TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager::class);
            $persistenceManager->persistAll();
        }

    }

    /**
     * delete child events for given event id
     *
     * @param integer $id
     *
     * @return void
     */
    public function deleteChildsAction($id)
    {
        $this->initializeCreateChildsAction($id);

        $parentEvent = $this->eventRepository->findOneByUid($id);

        if ($parentEvent) {

            $allChildren = $this->eventRepository->findByParent($parentEvent);

            // delete all present child events
            $this->eventRepository->deleteAllNotAllowedChildren(array(), $parentEvent);

            $persistenceManager = $this->objectManager->get(\TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager::class);
            $persistenceManager->persistAll();

        }

    }

    /**
     * action errorAction
     *
     * @return void
     */
    public function errorAction()
    {
    }


    /**
     * action ajax
     *
     * EXPERIMENTAL!!
     *
     * @return string
     */
    public function ajaxAction()
    {
        $jsonevent = [];

        $events = $this->eventRepository->findAllBySettings([
            'categoryList'   => GeneralUtility::intExplode(',', $_GET['categories'], true),
            'disciplineList' => GeneralUtility::intExplode(',', $_GET['disciplines'], true),
            'startTimestamp' => $_GET['start'],
            'stopTimestamp'  => $_GET['stop'],
            'showPastEvents' => true,
        ]);

        $cObj = $this->configurationManager->getContentObject();
        /** @var Event $event */
        foreach ($events as $event) {
            $foundevent = [];

            $foundevent['id'] = $event->getUid();
            $foundevent['title'] = $event->getTitle();
            $foundevent['teaser'] = $event->getTeaser();
            $foundevent['start'] = $event->getStartDateTime()->format('Y-m-d H:i:s');
            foreach ($event->getCategories() as $cat) {
                $foundevent['className'] .= ' slubevents-category-' . $cat->getUid();
            }

            if ($event->getEndDateTime() instanceof \DateTime) {
                $foundevent['end'] = $event->getEndDateTime()->format('Y-m-d H:i:s');
            }

            $conf = [
                // Link to current page
                'parameter'        => $_GET['detailPid'],
                // Set additional parameters
                'additionalParams' => '&type=0&tx_slubevents_eventlist%5Bevent%5D=' . $event->getUid() . '&tx_slubevents_eventlist%5Baction%5D=show',
                // We must add cHash because we use parameters
                'useCacheHash'     => 1,
                // We want link only
                'returnLast'       => 'url',
            ];
            $url = $cObj->typoLink('', $conf);
            $foundevent['url'] = $url;

            if ($event->getAllDay()) {
                $foundevent['allDay'] = true;
            } else {
                $foundevent['allDay'] = false;
            }

            // how many free places are available?
            $freePlaces = ($event->getMaxSubscriber() - $this->subscriberRepository->countAllByEvent($event));
            if ($freePlaces <= 0) {
                $foundevent['freePlaces'] = 0;
            } else {
                if ($freePlaces == 1) {
                    $foundevent['freePlaces'] = LocalizationUtility::translate(
                        'tx_slubevents_domain_model_event.oneFreePlace',
                        'slub_events'
                    );
                } else {
                    $foundevent['freePlaces'] =
                        ($event->getMaxSubscriber() - $this->subscriberRepository->countAllByEvent($event));

                    $foundevent['freePlaces'] .= ' ' .
                        LocalizationUtility::translate(
                            'tx_slubevents_domain_model_event.freeplaces',
                            'slub_events'
                        );
                }
            }

            // set special css class if subscription is NOT possible
            $noSubscription = false;
            // limit reached already --> overbooked
            if ($this->subscriberRepository->countAllByEvent($event) >= $event->getMaxSubscriber()) {
                $noSubscription = true;
            }
            // event is cancelled
            if ($event->getCancelled()) {
                $noSubscription = true;
            }
            // deadline reached....
            if (is_object($event->getSubEndDateTime())) {
                if ($event->getSubEndDateTime()->getTimestamp() < time()) {
                    $noSubscription = true;
                }
            }
            if ($noSubscription) {
                $foundevent['className'] .= ' no_subscription';
            }
            $jsonevent[] = $foundevent;
        }

        return json_encode($jsonevent);
    }

    /**
     * action printCal
     *
     * @param Event $event
     *
     * @return void
     */
    public function printCalAction(Event $event = null)
    {
        $helper['now'] = time();
        $helper['start'] = $event->getStartDateTime()->getTimestamp();
        // endDate may be empty
        if ($event->getEndDateTime() instanceof \DateTime && $event->getStartDateTime() != $event->getEndDateTime()) {
            $helper['end'] = $event->getEndDateTime()->getTimestamp();
        } else {
            $helper['allDay'] = 1;
            $helper['end'] = $helper['start'];
        }
        $helper['description'] = $this->foldline($this->html2rest($event->getDescription()));
        // location may be empty...
        if (is_object($event->getLocation())) {
            if (is_object($event->getLocation()->getParent()->current())) {
                $helper['location'] = $event->getLocation()->getParent()->current()->getName() . ', ';
                $helper['locationics'] =
                    $this->foldline($event->getLocation()->getParent()->current()->getName()) . ', ';
            }
            $helper['location'] = $event->getLocation()->getName();
            $helper['locationics'] = $this->foldline($event->getLocation()->getName());
        }
        $this->view->assign('helper', $helper);
        $this->view->assign('event', $event);
    }

    /**
     * calculate all child dateTime fields (start_date_time, end_date_time, sub_end_date_time ...)
     *
     * @param Event $parentEvent
     *
     * @return void
     */
    public function getChildDateTimes($parentEvent)
    {
        $recurring_options = $parentEvent->getRecurringOptions();
        $recurringEndDateTime = $parentEvent->getRecurringEndDateTime();

        $parentStartDateTime = $parentEvent->getStartDateTime();

        $sumDiffDays = 0;
        foreach($recurring_options['weekday'] as $id => $weekday) {
            if ((int)$weekday != $parentStartDateTime->format('N')) {
                $nextEventWeekday = (int)$weekday - $parentStartDateTime->format('N') - $sumDiffDays;
                if ($nextEventWeekday < 0) {
                    $nextEventWeekday += 7;
                }
                $sumDiffDays += $nextEventWeekday;
                $diffDays[] = new \DateInterval("P" . $nextEventWeekday . "D");
            }
        }

        $parentEndDateTime = $parentEvent->getEndDateTime();
        $parentSubEndDateTime = $parentEvent->getSubEndDateTime();

        if (!$recurringEndDateTime) {
          // if no recurringEndDateTime is given, set it to ... 3 months for now
          $recurringEndDateTime = clone $parentStartDateTime;
          $recurringEndDateTime->add(new \DateInterval("P3M"));
        }

        $childDateTimes = array();

        $eventStartDateTime = clone $parentStartDateTime;
        $eventEndDateTime = clone $parentEndDateTime;
        if ($parentSubEndDateTime) {
            $eventSubEndDateTime = clone $parentSubEndDateTime;
        }
        switch ($recurring_options['interval']) {
            case 'weekly':
                  $dateTimeInterval = new \DateInterval("P1W");
                  break;
            case '2weekly':
                  $dateTimeInterval = new \DateInterval("P2W");
                  break;
            case '4weekly':
                  $dateTimeInterval = new \DateInterval("P4W");
                  break;
            case 'monthly':
                  $dateTimeInterval = new \DateInterval("P1M");
                  break;
            case 'yearly':
                  $dateTimeInterval = new \DateInterval("P1Y");
                  break;
        }
        $adjustDlstRun = 1;
        do {
            $eventStartDateTime->add($dateTimeInterval);
            $daylightOffset = 0;

            //  we need to calculate the transitions out of a new DateTimeZone object
            $timeZone = new \DateTimeZone(date_default_timezone_get());
            $transitions = $timeZone->getTransitions($parentStartDateTime->getTimestamp(), $eventStartDateTime->getTimestamp());

            if ($transitions && count($transitions) > 1 && $adjustDlstRun < count($transitions)) {
                // only adjust offset once
                $adjustDlstRun++;
                // there seems to be a dailight saving switch
                $last_transition = array_pop($transitions);
                $previous_transition = array_pop($transitions);
                $daylightOffset = $previous_transition['offset'] - $last_transition['offset'];
            }
            $this->daylightOffset($eventStartDateTime, $daylightOffset);

            $eventEndDateTime->add($dateTimeInterval);
            $this->daylightOffset($eventEndDateTime, $daylightOffset);
            if ($parentSubEndDateTime) {
                $eventSubEndDateTime->add($dateTimeInterval);
                $this->daylightOffset($eventSubEndDateTime, $daylightOffset);
            }
            $childDateTime = array();
            $childDateTime['endDateTime'] = clone $eventEndDateTime;
            $childDateTime['startDateTime'] = clone $eventStartDateTime;
            if ($eventSubEndDateTime) {
                $childDateTime['subEndDateTime'] = clone $eventSubEndDateTime;
            }
            if ($childDateTime['startDateTime'] < $recurringEndDateTime){
                $childDateTimes[] = $childDateTime;
            }

            // create the child days within a week
            if (!empty($diffDays)) {
                $diffDayEventStartDateTime = clone $eventStartDateTime;
                $diffDayEventEndDateTime = clone $eventEndDateTime;
                if ($eventSubEndDateTime) {
                    $diffDayEventSubEndDateTime = clone $eventSubEndDateTime;
                }
                $adjustDlstDone = FALSE;
                foreach ($diffDays as $weekDayInterval) {
                    $diffDayEventStartDateTime->add($weekDayInterval);
                    $transitions = $timeZone->getTransitions($eventStartDateTime->getTimestamp(), $diffDayEventStartDateTime->getTimestamp());
                    // if there is a transition between startDateStamp and
                    // following weekday in series adjust only once the offset.
                    if ($transitions && count($transitions) > 1 && $adjustDlstDone === FALSE) {
                        $adjustDlstDone = TRUE;
                        // there seems to be a dailight saving switch
                        $last_transition = array_pop($transitions);
                        $previous_transition = array_pop($transitions);
                        $daylightOffset = $previous_transition['offset'] - $last_transition['offset'];
                    } else {
                      $daylightOffset = 0;
                    }

                    $this->daylightOffset($diffDayEventStartDateTime, $daylightOffset);
                    $diffDayEventEndDateTime->add($weekDayInterval);
                    $this->daylightOffset($diffDayEventEndDateTime, $daylightOffset);
                    if ($diffDayEventSubEndDateTime) {
                        $diffDayEventSubEndDateTime->add($weekDayInterval);
                        $this->daylightOffset($diffDayEventSubEndDateTime, $daylightOffset);
                    }

                    $childDateTime = array();
                    $childDateTime['endDateTime'] = clone $diffDayEventEndDateTime;
                    $childDateTime['startDateTime'] = clone $diffDayEventStartDateTime;
                    if ($eventSubEndDateTime) {
                        $childDateTime['subEndDateTime'] = clone $diffDayEventSubEndDateTime;
                    }
                    if ($childDateTime['startDateTime'] < $recurringEndDateTime){
                      $childDateTimes[] = $childDateTime;
                    }
                }
            }
        } while ($eventStartDateTime < $recurringEndDateTime);

        return $childDateTimes;
    }

    /**
     * add offset to given DateTime
     *
     * @param \DateTime $dateTimeValue
     * @param integer $offset in seconds
     *
     * @return void
     */
    private function daylightOffset($dateTimeValue, $offset)
    {
      if ($offset > 0) {
          $dateTimeValue->add(new \DateInterval('PT'.$offset.'S'));
      } else if ($offset < 0) {
          $dateTimeValue->sub(new \DateInterval('PT'.(-1) * $offset.'S'));
      }
    }
}
