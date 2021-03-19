<?php
namespace Slub\SlubEvents\Task;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2020 Alexander Bigga <alexander.bigga@slub-dresden.de>, SLUB Dresden
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
 * Scheduler Task to check for events with subscription end reached.
 *
 * @package slub_events
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
use Slub\SlubEvents\Helper\EmailHelper;
use Slub\SlubEvents\Domain\Repository\EventRepository;
use Slub\SlubEvents\Domain\Repository\SubscriberRepository;
use Slub\SlubEvents\Helper\EventHelper;
use Slub\SlubEvents\Utility\TextUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

class CheckeventsTask extends \TYPO3\CMS\Scheduler\Task\AbstractTask
{
    /**
     * eventRepository
     *
     * @var \Slub\SlubEvents\Domain\Repository\EventRepository
     */
    protected $eventRepository;

    /**
     * subscriberRepository
     *
     * @var \Slub\SlubEvents\Domain\Repository\SubscriberRepository
     */
    protected $subscriberRepository;

    /**
     * PID of storage folder to work with
     *
     * @var integer
     */
    public $storagePid;

    /**
     * Email address of sender
     *
     * @var string
     */
    public $senderEmailAddress;

    /**
     * Language of email text
     *
     * @var string
     */
    public $language;

    /**
     * @var ConfigurationManagerInterface
     */
    protected $configurationManager;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager
     */
    protected $persistenceManager;

    /**
     * initializeAction
     *
     * @return void
     */
    protected function initializeAction()
    {

        $objectManager = GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\ObjectManager::class);

        $this->subscriberRepository = $objectManager->get(
            \Slub\SlubEvents\Domain\Repository\SubscriberRepository::class
        );

        $this->eventRepository = $objectManager->get(
            \Slub\SlubEvents\Domain\Repository\EventRepository::class
        );

        $this->configurationManager = $objectManager->get(
            \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::class
        );

        $this->persistenceManager = $objectManager->get(
            \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager::class
        );

        switch ($this->language) {
            case 'de':  setlocale(LC_ALL, 'de_DE.utf8');
                        $GLOBALS['LANG']->init('de');
                        break;
            case 'en':
            default:
                        setlocale(LC_ALL, 'en_US.utf8');
                        $GLOBALS['LANG']->init('en');
                        break;
        }
    }

    /**
     * Function execute from the Scheduler
     *
     * @return boolean TRUE on successful execution, FALSE on error
     * @throws \InvalidArgumentException if the email template file can not be read
     */
    public function execute()
    {
        $successfullyExecuted = false;

        // do some init work...
        $this->initializeAction();

        // abort if no storagePid is found
        if (MathUtility::canBeInterpretedAsInteger($this->storagePid)) {
            $successfullyExecuted = true;

            // set storagePid to point extbase to the right repositories
            $configurationArray = [
                'persistence' => [
                    'storagePid' => $this->storagePid,
                ],
            ];
            $this->configurationManager->setConfiguration($configurationArray);

            // start the work...
            // 1. get events in future which reached the subscription deadline
            $allevents = $this->eventRepository->findAllSubscriptionEnded();

            foreach ($allevents as $event) {

                // startDateTime may never be empty
                $helper['start'] = $event->getStartDateTime()->getTimestamp();
                // endDateTime may be empty
                if (($event->getEndDateTime() instanceof \DateTime) && ($event->getStartDateTime() != $event->getEndDateTime())) {
                    $helper['end'] = $event->getEndDateTime()->getTimestamp();
                } else {
                    $helper['end'] = $helper['start'];
                }

                if ($event->isAllDay()) {
                    $helper['allDay'] = 1;
                }
                $helper['now'] = time();
                // used to name the csv file...
                $nameTo = EmailHelper::prepareNameTo($event->getContact()->getName());
                $helper['description'] = TextUtility::foldline($event->getDescription());
                $helper['location'] = EventHelper::getLocationNameWithParent($event);
                $helper['locationics'] = TextUtility::foldline($helper['location']);

                // check if we have to cancel the event
                if ($this->subscriberRepository->countAllByEvent($event) < $event->getMinSubscriber()) {
                    // --> ok, we have to cancel the event because not enough subscriber were found

                    // email to all subscribers
                    foreach ($event->getSubscribers() as $subscriber) {
                        $cronLog .= 'Absage an Teilnehmer: ' . $event->getTitle() . ': ' . strftime('%x %H:%M',
                                $event->getStartDateTime()->getTimestamp()) . ' --> ' . $subscriber->getEmail() . "\n";
                        $out = EmailHelper::sendTemplateEmail(
                            [$subscriber->getEmail() => $subscriber->getName()],
                            [$event->getContact()->getEmail() => $event->getContact()->getName()],
                            'Absage der Veranstaltung: ' . $event->getTitle(),
                            'CancellEvent',
                            [
                                'event' => $event,
                                'subscribers' => '',
                                'helper' => $helper,
                                'attachIcs' => true,
                            ]
                        );
                    }

                    $cronLog .= 'Absage an Veranstalter: ' . $event->getTitle() . ': ' . strftime('%x %H:%M',
                            $event->getStartDateTime()->getTimestamp()) . ' --> ' . $event->getContact()->getEmail() . "\n";
                    // email to event owner
                    $out = EmailHelper::sendTemplateEmail(
                        [$event->getContact()->getEmail() => $event->getContact()->getName()],
                        [$this->senderEmailAddress => 'SLUB Veranstaltungen - noreply'],
                        'Absage der Veranstaltung: ' . $event->getTitle(),
                        'CancellEvent',
                        [
                            'event' => $event,
                            'subscribers' => $event->getSubscribers(),
                            'helper' => $helper,
                            'attachCsv' => true,
                            'attachIcs' => true,
                        ]
                    );
                    if ($out === true) {
                        $event->setSubEndDateInfoSent(true);
                        $event->setCancelled(true);
                        $this->eventRepository->update($event);
                    }
                } else {
                    // event takes place but subscription is not possible anymore...
                    // email to event owner
                    $cronLog .= 'Anmeldefrist abgelaufen an Veranstalter: ' . $event->getTitle() . ': ' . strftime('%x %H:%M',
                            $event->getStartDateTime()->getTimestamp()) . ' --> ' . $event->getContact()->getEmail() . "\n";
                    $out = EmailHelper::sendTemplateEmail(
                        [$event->getContact()->getEmail() => $event->getContact()->getName()],
                        [$this->senderEmailAddress => 'SLUB Veranstaltungen - noreply'],
                        'Veranstaltung Anmeldefrist abgelaufen: ' . $event->getTitle(),
                        'DeadlineReached',
                        [
                            'event' => $event,
                            'subscribers' => $event->getSubscribers(),
                            'nameTo' => $nameTo,
                            'helper' => $helper,
                            'attachCsv' => true,
                            'attachIcs' => true,
                        ]
                    );
                    if ($out === true) {
                        $event->setSubEndDateInfoSent(true);
                        $this->eventRepository->update($event);
                    }
                }
            }
            // persist the repository
            $this->persistenceManager->persistAll();
        }

        return $successfullyExecuted;
    }
}
