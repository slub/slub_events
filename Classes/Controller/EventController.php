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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

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
     * @param \Slub\SlubEvents\Domain\Model\Event $event
     * @ignorevalidation $event
     *
     * @return void
     */
    public function showAction(\Slub\SlubEvents\Domain\Model\Event $event = null)
    {
        if ($event !== null) {
            // fill registers to be used in ts
            $cObj = GeneralUtility::makeInstance('TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer');
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
     * @param \Slub\SlubEvents\Domain\Model\Event $newEvent
     * @ignorevalidation $newEvent
     *
     * @return void
     */
    public function newAction(\Slub\SlubEvents\Domain\Model\Event $newEvent = null)
    {
        $this->view->assign('newEvent', $newEvent);
    }

    /**
     * action create
     *
     * @param \Slub\SlubEvents\Domain\Model\Event $newEvent
     *
     * @return void
     */
    public function createAction(\Slub\SlubEvents\Domain\Model\Event $newEvent)
    {
        $this->eventRepository->add($newEvent);
        $this->addFlashMessage('Your new Event was created.');
        $this->redirect('list');
    }

    /**
     * action edit
     *
     * @param \Slub\SlubEvents\Domain\Model\Event $event
     * @ignorevalidation $event
     *
     * @return void
     */
    public function editAction(\Slub\SlubEvents\Domain\Model\Event $event)
    {
        $this->view->assign('event', $event);
    }

    /**
     * action update
     *
     * @param \Slub\SlubEvents\Domain\Model\Event $event
     *
     * @return void
     */
    public function updateAction(\Slub\SlubEvents\Domain\Model\Event $event)
    {
        $this->eventRepository->update($event);
        $this->addFlashMessage('Your Event was updated.');
        $this->redirect('list');
    }

    /**
     * action delete
     *
     * @param \Slub\SlubEvents\Domain\Model\Event $event
     *
     * @return void
     */
    public function deleteAction(\Slub\SlubEvents\Domain\Model\Event $event)
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
        $searchParameter = array();

        // if search was triggered, get search parameters from POST variables
        $submittedSearchParams = $this->getParametersSafely('searchParameter');

        if (is_array($submittedSearchParams)) {
            // clean category array to prevent errors
            $searchParameter['category'] = $this->cleanArray($submittedSearchParams['category']);

            // merge search parameter
            $searchParameter = array_merge($searchParameter, $submittedSearchParams);

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
            $searchParameter['contacts']
        );

        $this->view->assign('selectedStartDateStamp', $searchParameter['selectedStartDateStamp']);
        $this->view->assign('searchString', $searchParameter['searchString']);
        $this->view->assign('categories', $categories);
        $this->view->assign('events', $events);
        $this->view->assign('contacts', $contacts);
    }

    /**
     * action beCopy
     *
     * @param \Slub\SlubEvents\Domain\Model\Event $event
     *
     * @return void
     */
    public function beCopyAction(\Slub\SlubEvents\Domain\Model\Event $event)
    {
        $availableProperties = \TYPO3\CMS\Extbase\Reflection\ObjectAccess::getGettablePropertyNames($event);
        /** @var \Slub\SlubEvents\Domain\Model\Event $newEvent */
        $newEvent = $this->objectManager->get('Slub\SlubEvents\Domain\Model\Event');

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
        $this->redirect('beList');
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
        /** @var \Slub\SlubEvents\Domain\Model\Event $event */
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
     * @param @param \Slub\SlubEvents\Domain\Model\Event $event

     * @return void
     */
    public function printCalAction(\Slub\SlubEvents\Domain\Model\Event $event = null)
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
     * remove empty entries in array
     *
     * @param array $array
     *
     * @return array
     */
    protected function cleanArray(array $array)
    {
        return array_filter(array_map('trim', $array));
    }
}
