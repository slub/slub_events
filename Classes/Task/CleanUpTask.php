<?php
namespace Slub\SlubEvents\Task;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2017 Alexander Bigga <alexander.bigga@slub-dresden.de>, SLUB Dresden
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
use TYPO3\CMS\Core\Utility\MathUtility;

class CleanUpTask extends \TYPO3\CMS\Scheduler\Task\AbstractTask
{

    /**
     * PID of storage folder to work with
     *
     * @var integer
     */
    protected $storagePid;

    /**
     * Age (in days) of emails allowed.
     *
     * @var integer
     */
    protected $cleanupDays;

    /**
     * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
     */
    protected $configurationManager;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager
     */
    protected $persistenceManager;

    /**
     * injectConfigurationManager
     *
     * @param \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManager
     *
     * @return void
     */
    public function injectConfigurationManager(
        \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManager
    ) {
        $this->configurationManager = $configurationManager;

        $this->settings = $this->configurationManager->getConfiguration(
            \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS
        );
    }

    /**
     * Set the value of the storage pid
     *
     * @param integer $storagePid UID of the storage folder for this task.
     *
     * @return void
     */
    public function setStoragePid($storagePid)
    {
        $this->storagePid = $storagePid;
    }

    /**
     * Get the value of the storage pid
     *
     * @return integer $storagePid UID of the storage folder for this task.
     */
    public function getStoragePid()
    {
        return $this->storagePid;
    }

    /**
     * Set the maximum days, emails are stored
     *
     * @param integer $cleanupDays days
     *
     * @return void
     */
    public function setCleanupDays($cleanupDays)
    {
        $this->cleanupDays = $cleanupDays;
    }

    /**
     * Get the value of maximum days
     *
     * @return integer $cleanupDays days
     */
    public function getCleanupDays()
    {
        return $this->cleanupDays;
    }

    /**
     * Set the maximum days, events are stored
     *
     * @param integer $cleanupDaysEvents days
     *
     * @return void
     */
    public function setCleanupDaysEvents($cleanupDaysEvents)
    {
        $this->cleanupDaysEvents = $cleanupDaysEvents;
    }

    /**
     * Get the value of maximum storage days for events
     *
     * @return integer $cleanupDaysEvents days
     */
    public function getCleanupDaysEvents()
    {
        return $this->cleanupDaysEvents;
    }

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

        // if a valid storagePid is given, only delete in this repository
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

            // 1. Get old events to be deleted
            $oldEvents = $this->eventRepository->findOlderThan($this->cleanupDaysEvents);
            foreach($oldEvents AS $event) {
                if ($event->getRecurring() === FALSE) {
                    // it's a normal event or a child event of a recurring event
                    $this->eventRepository->remove($event);
                } else if ($event->getParent() == 0
                    && $this->eventRepository->findByParent($event->getUid())->count() == 0
                ) {
                    // it's a recurring parent event without children
                    $this->eventRepository->remove($event);
                }
            }
            // persist the repository
            $this->persistenceManager->persistAll();

            // 2. Get subscribers to be anonymized of events which still exists
            $anonymizeEvents = $this->eventRepository->findOlderThan($this->cleanupDays);
            if ($anonymizeEvents->count() > 0) {
                $anonymizeSubscribers = $this->subscriberRepository->findAllByEvents($anonymizeEvents);
                foreach ($anonymizeSubscribers AS $subscriber) {
                    // anonymize one by one
                    $subscriber->setEmail('anonymous@example.com');
                    $subscriber->setName('anonymous');
                    $subscriber->setCustomerid('1234567');
                    $subscriber->setMessage('');
                    $this->subscriberRepository->update($subscriber);
                }
            }

            // 3. Get zombie subscribers to be deleted of events which does not exist anymore
            $zombieSubscribers = $this->subscriberRepository->findOlderThan($this->cleanupDays);
            foreach ($zombieSubscribers AS $subscriber) {
                // anonymize or remove one by one
                if ($subscriber->getEvent()) {
                    // this should never be necessary as already done in step 2.
                    $subscriber->setEmail('anonymous@example.com');
                    $subscriber->setName('anonymous');
                    $subscriber->setCustomerid('1234567');
                    $subscriber->setMessage('');
                    $this->subscriberRepository->update($subscriber);
                } else {
                    $this->subscriberRepository->remove($subscriber);
                }
            }

            // persist the repository
            $this->persistenceManager->persistAll();
        }

        return $successfullyExecuted;
    }
}
