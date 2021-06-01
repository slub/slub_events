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

use Slub\SlubEvents\Domain\Model\Category;
use Slub\SlubEvents\Domain\Model\Event;
use Slub\SlubEvents\Domain\Model\Subscriber;
use Slub\SlubEvents\Helper\EmailHelper;
use Slub\SlubEvents\Helper\EventHelper;
use Slub\SlubEvents\Utility\TextUtility;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Extbase\Annotation as Extbase;

/**
 * @package slub_events
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class SubscriberController extends AbstractController
{

    /**
     * action list
     *
     * @return void
     */
    public function listAction()
    {
        $subscribers = $this->subscriberRepository->findAll();
        $this->view->assign('subscribers', $subscribers);
    }

    /**
     * action show
     *
     * @param Subscriber $subscriber
     *
     * @return void
     */
    public function showAction(Subscriber $subscriber)
    {
        $this->view->assign('subscriber', $subscriber);
    }

    /**
     * action EventNotfound
     *
     * @return void
     */
    public function eventNotFoundAction()
    {
    }

    /**
     * action SubscriberNotfound
     *
     * @return void
     */
    public function subscriberNotFoundAction()
    {
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
     * @param Subscriber $newSubscriber
     * @param Event      $event
     * @param Category   $category
     * @Extbase\IgnoreValidation("newSubscriber")
     * @Extbase\IgnoreValidation("event")
     * @Extbase\IgnoreValidation("category")
     *
     * @return void
     */
    public function newAction(
        Subscriber $newSubscriber = null,
        Event $event = null,
        Category $category = null
    ) {

        // somebody is calling the action without giving an event --> useless
        if ($event === null) {
            $this->redirect('eventNotFound');
        }

        // this is a little stupid with the rewritten property mapper from
        // extbase 1.4, because the object is never NULL!
        // anyway we can set default values here which are overwritten if
        // already POST values exists. extbase vodoo ;-)
        if ($newSubscriber === null) {

            /** @var \Slub\SlubEvents\Domain\Model\Subscriber $newSubscriber */
            $newSubscriber = GeneralUtility::makeInstance('Slub\\SlubEvents\\Domain\\Model\\Subscriber');
            $newSubscriber->setNumber(1);

            if (!empty($GLOBALS['TSFE']->fe_user->user['username'])) {
                $newSubscriber->setCustomerid($GLOBALS['TSFE']->fe_user->user['username']);
                $loggedIn = 'readonly'; // css class for form
            } else {
                $loggedIn = '';
            } // css class for form

            if (!empty($GLOBALS['TSFE']->fe_user->user['name'])) {
                $newSubscriber->setName($GLOBALS['TSFE']->fe_user->user['name']);
            }

            if (!empty($GLOBALS['TSFE']->fe_user->user['email'])) {
                $newSubscriber->setEmail($GLOBALS['TSFE']->fe_user->user['email']);
            }
        }

        $this->view->assign('event', $event);
        $this->view->assign('category', $category);
        $this->view->assign('newSubscriber', $newSubscriber);
        $this->view->assign('loggedIn', $loggedIn);
    }

    /**
     * action create
     *
     * note (from docs): Up until version 10, Extbase “magically” applied validators based on a naming convention.
     * Starting with TYPO3 v10, all validators need to be explicitly registered.
     *
     * @param Subscriber $newSubscriber
     * @param Event      $event
     * @param Category   $category
     * @Extbase\Validate("Slub\SlubEvents\Domain\Validator\SubscriberValidator", param="newSubscriber")
     * @Extbase\Validate("Slub\SlubEvents\Domain\Validator\EventSubscriptionAllowedValidator", param="event")
     * @Extbase\IgnoreValidation("category")
     *
     * @return void
     */
    public function createAction(
        Subscriber $newSubscriber,
        Event $event,
        Category $category = null
    ) {

        // add subscriber to event
        $editcode = hash('sha256', rand() . $newSubscriber->getEmail() . time());
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
        $helper['description'] = TextUtility::foldline(EmailHelper::html2rest($event->getDescription()));
        $helper['location'] = EventHelper::getLocationNameWithParent($event);
        $helper['locationics'] = TextUtility::foldline($helper['location']);
        $nameTo = EmailHelper::prepareNameTo($newSubscriber->getName());


        // startDateTime may never be empty
        $helper['start'] = $event->getStartDateTime()->getTimestamp();

        // endDate may be empty
        if (($event->getEndDateTime() instanceof \DateTime)
            && ($event->getStartDateTime() != $event->getEndDateTime())
        ) {
            $helper['end'] = $event->getEndDateTime()->getTimestamp();
        }

        if ($event->isAllDay()) {
            $helper['allDay'] = 1;
        }

        // email to customer
        EmailHelper::sendTemplateEmail(
            [$newSubscriber->getEmail() => $newSubscriber->getName()],
            [$event->getContact()->getEmail() => $event->getContact()->getName()],
            'Ihre Anmeldung: ' . $event->getTitle(),
            'Subscribe',
            [
                'event'      => $event,
                'subscriber' => $newSubscriber,
                'nameTo'     => $nameTo,
                'helper'     => $helper,
                'settings'   => $this->settings,
                'attachCsv'  => false,
                'attachIcs'  => true,
            ],
            $this->configurationManager
        );

        // send to contact, if maximum is reached and TS setting is present:
        if ($this->settings['emailToContact']['sendEmailOnMaximumReached'] &&
            ($this->subscriberRepository->countAllByEvent($event) + $newSubscriber->getNumber()) == $event->getMaxSubscriber()
        ) {
            $nameTo = EmailHelper::prepareNameTo($event->getContact()->getName());

            // email to event owner
            EmailHelper::sendTemplateEmail(
                [$event->getContact()->getEmail() => $event->getContact()->getName()],
                [
                    $this->settings['senderEmailAddress'] =>
                        LocalizationUtility::translate(
                            'tx_slubevents.be.eventmanagement',
                            'slub_events'
                        )
                        . ' - noreply',
                ],
                'Veranstaltung ausgebucht: ' . $event->getTitle(),
                'Maximumreached',
                [
                    'event'       => $event,
                    'subscribers' => $event->getSubscribers(),
                    'nameTo'      => $nameTo,
                    'helper'      => $helper,
                    'settings'    => $this->settings,
                    'attachCsv'   => true,
                    'attachIcs'   => true,
                ],
                $this->configurationManager
            );
        } // send to contact, on every booking if TS setting is present:
        else {
            if ($this->settings['emailToContact']['sendEmailOnEveryBooking']) {
                $nameTo = EmailHelper::prepareNameTo($event->getContact()->getName());

                // email to event owner
                EmailHelper::sendTemplateEmail(
                    [$event->getContact()->getEmail() => $event->getContact()->getName()],
                    [
                        $this->settings['senderEmailAddress'] =>
                            LocalizationUtility::translate(
                                'tx_slubevents.be.eventmanagement',
                                'slub_events'
                            )
                            . ' - noreply',
                    ],
                    'Veranstaltung gebucht: ' . $event->getTitle(),
                    'Newsubscriber',
                    [
                        'event'         => $event,
                        'newsubscriber' => $newSubscriber,
                        'subscribers'   => $event->getSubscribers(),
                        'nameTo'        => $nameTo,
                        'helper'        => $helper,
                        'settings'      => $this->settings,
                        'attachCsv'     => false,
                        'attachIcs'     => false,
                    ],
                    $this->configurationManager
                );
            }
        }

        // reset session data
        $this->setSessionData('editcode', '', false);

        // we changed the event inside the repository and have to
        // update the repo manually as of TYPO3 6.1
        $this->eventRepository->update($event);

        // clear cache on all cached list pages
        if (!$this->settings['dontClearCachedEvents']) {
            $this->clearAllEventListCache($event->getGeniusBar());
        }
        $this->view->assign('event', $event);
        $this->view->assign('category', $category);
        $this->view->assign('newSubscriber', $newSubscriber);
    }

    /**
     * Clear cache of all pages with cached slubevents content.
     * This way the plugin may stay cached but on every delete or insert
     * of subscribers, the cache gets cleared.
     *
     * @param bool $isGeniusBar
     */
    public function clearAllEventListCache($isGeniusBar = false)
    {
        if ($isGeniusBar) {
            $cacheTag = 'tx_slubevents_cat_' . $this->settings['storagePid'];
        } else {
            $cacheTag = 'tx_slubevents_' . $this->settings['storagePid'];
        }
        $this->getCacheManager()->flushCachesInGroupByTags('pages', [$cacheTag]);
    }

    /**
     * action delete
     *
     * @param Event  $event
     * @param string $editcode
     * @Extbase\IgnoreValidation("event")
     *
     * @return void
     */
    public function deleteAction(Event $event = null, $editcode = null)
    {
        // somebody is calling the action without giving an event --> useless
        if ($event === null || $editcode === null) {
            $this->redirect('eventNotFound');
        }

        // delete for which subscriber?
        $subscriber = $this->subscriberRepository->findAllByEditcode($editcode)->getFirst();

        if (!is_object($subscriber)) {
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
        $helper['description'] = TextUtility::foldline(EmailHelper::html2rest($event->getDescription()));
        $helper['location'] = EventHelper::getLocationNameWithParent($event);
        $helper['locationics'] = TextUtility::foldline($helper['location']);
        $nameTo = EmailHelper::prepareNameTo($subscriber->getName());

        $helper['start'] = $event->getStartDateTime()->getTimestamp();

        // endDate may be empty
        if (($event->getEndDateTime() instanceof \DateTime)
            && ($event->getStartDateTime() != $event->getEndDateTime())
        ) {
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
            $nameTo = EmailHelper::prepareNameTo($event->getContact()->getName());

            // email to event owner
            EmailHelper::sendTemplateEmail(
                [$event->getContact()->getEmail() => $event->getContact()->getName()],
                [
                    $this->settings['senderEmailAddress'] => LocalizationUtility::translate(
                        'tx_slubevents.be.eventmanagement',
                        'slub_events'
                    ),
                ],
                'Veranstaltung wegen Abmeldung nicht mehr gesichert: ' . $event->getTitle(),
                'Minimumreachedagain',
                [
                    'event'           => $event,
                    'subscribers'     => $event->getSubscribers(),
                    'subscriberCount' => $this->subscriberRepository->countAllByEvent($event) - $subscriber->getNumber(),
                    'nameTo'          => $nameTo,
                    'helper'          => $helper,
                    'settings'        => $this->settings,
                    'attachCsv'       => false,
                    'attachIcs'       => true,
                ],
                $this->configurationManager
            );
        }

        EmailHelper::sendTemplateEmail(
            [$subscriber->getEmail() => $subscriber->getName()],
            [$event->getContact()->getEmail() => $event->getContact()->getName()],
            'Ihre Abmeldung: ' . $event->getTitle(),
            'Unsubscribe',
            [
                'event'      => $event,
                'subscriber' => $subscriber,
                'nameTo'     => $nameTo,
                'helper'     => $helper,
                'settings'   => $this->settings,
                'attachCsv'  => false,
                'attachIcs'  => true,
            ],
            $this->configurationManager
        );

        // we changed the event inside the repository and have to
        // update the repo manually as of TYPO3 6.1
        $this->eventRepository->update($event);

        if (!$this->settings['dontClearCachedEvents']) {
            $this->clearAllEventListCache($event->getGeniusBar());
        }
        $this->view->assign('event', $event);
        $this->view->assign('subscriber', $subscriber);
    }

    /**
     * Create and returns an instance of the CacheManager
     *
     * @return CacheManager
     */
    protected function getCacheManager()
    {
        return GeneralUtility::makeInstance(CacheManager::class);
    }
}
