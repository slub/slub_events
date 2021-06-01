<?php
namespace Slub\SlubEvents\Controller\Backend;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Extbase\Annotation as Extbase;
use Slub\SlubEvents\Domain\Model\Event;
use Slub\SlubEvents\Helper\EmailHelper;
use Slub\SlubEvents\Helper\EventHelper;
use Slub\SlubEvents\Utility\TextUtility;

/**
 * @package slub_events
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class EventController extends BaseController
{
    /**
     * action beList
     */
    public function beListAction()
    {
        // get current event of last beIcsInvitationAction
        $currentActiveEvent = $this->getParametersSafely('currentActiveEvent');

        $searchParameter = [];

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

        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
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
        $availableProperties = ObjectAccess::getGettablePropertyNames($event);
        /** @var Event $newEvent */
        $newEvent = $this->objectManager->get(Event::class);

        foreach ($availableProperties as $propertyName) {
            if (ObjectAccess::isPropertySettable($newEvent, $propertyName)
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
                $propertyValue = ObjectAccess::getProperty($event, $propertyName);
                // special handling for onlinesurvey field to remove trailing timestamp with sent date
                if ($propertyName == 'onlinesurvey' && (strpos($propertyValue, '|') > 0)) {
                    $propertyValue = substr($propertyValue, 0, strpos($propertyValue, '|'));
                }
                ObjectAccess::setProperty($newEvent, $propertyName, $propertyValue);
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
     * @Extbase\IgnoreValidation("event")
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
            $helper['description'] = TextUtility::foldline(EmailHelper::html2rest($singleEvent->getDescription()));
            $helper['location'] = EventHelper::getLocationNameWithParent($singleEvent);
            $helper['locationics'] = TextUtility::foldline($helper['location']);
            $helper['eventuid'] = $singleEvent->getUid();

            $icsHelpers[] = $helper;
        }

        $nameTo = EmailHelper::prepareNameTo($event->getContact()->getName());

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
}
