<?php
namespace Slub\SlubEvents\Domain\Repository;

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
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * @package slub_events
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */

class EventRepository extends Repository
{

    /**
     * Finds all datasets by MM relation categories
     *
     * @param \Slub\SlubEvents\Domain\Model\Category $category
     *
     * @return array The found Event Objects
     */
    public function findAllGbByCategory(Category $category)
    {
        $query = $this->createQuery();

        $constraints = [];
        $constraints[] = $query->equals('categories.uid', $category);
        $constraints[] = $query->equals('genius_bar', 1);
        $constraints[] = $query->greaterThan('start_date_time', strtotime('today'));

        if (count($constraints)) {
            $query->matching($query->logicalAnd($constraints));
        }

        // order by start_date -> start_time...
        $query->setOrderings(
            array('start_date_time' => QueryInterface::ORDER_ASCENDING)
        );

        return $query->execute();
    }

    /**
     * Finds all datasets by MM relation contact
     *
     * @param \Slub\SlubEvents\Domain\Model\Contact $contact
     * @param integer $category
     * @param boolean $bExcludeCategory
     *
     * @return array The found Event Objects
     */
    public function findWibaByContact($contact, $category = 0, $bExcludeCategory)
    {
        $query = $this->createQuery();

        $constraints = [];
        $constraints[] = $query->equals('contact', $contact);
        $constraints[] = $query->equals('genius_bar', 1);
        $constraints[] = $query->equals('cancelled', 0);
        if ($category > 0) {
            if ($bExcludeCategory) {
                $constraints[] = $query->logicalNot($query->equals('categories.uid', $category));
            } else {
                $constraints[] = $query->equals('categories.uid', $category);
            }
        }
        $constraints[] = $query->lessThan('subscribers', '1');
        $constraints[] = $query->greaterThan('start_date_time', strtotime('today'));

        if (count($constraints)) {
            $query->matching($query->logicalAnd($constraints));
        }

        // order by start_date -> start_time...
        $query->setOrderings(
            ['start_date_time' => QueryInterface::ORDER_ASCENDING]
        );

        return $query->execute();
    }

    /**
     * Finds all datasets by MM relation contact
     *
     * @param \Slub\SlubEvents\Domain\Model\Contact $contact
     * @param string $category
     *
     * @return array The found Event Objects
     */
    public function findEventByContact($contact, $category = null)
    {
        $query = $this->createQuery();

        $constraints = [];
        $constraints[] = $query->equals('contact', $contact);
        $constraints[] = $query->equals('genius_bar', 0);
        $constraints[] = $query->equals('cancelled', 0);
        if ($category != null) {
            $constraints[] = $query->in('categories.uid', explode(',', $category));
        }
        $constraints[] = $query->greaterThan('max_subscriber', 'subscribers');
        $constraints[] = $query->greaterThan('start_date_time', strtotime('today'));

        if (count($constraints)) {
            $query->matching($query->logicalAnd($constraints));
        }

        // order by start_date -> start_time...
        $query->setOrderings(
            array('start_date_time' => QueryInterface::ORDER_ASCENDING)
        );

        return $query->execute();
    }

    /**
     * Finds all datasets by MM relation categories
     *
     * @param array $categories separated by comma
     * @param bool  $fromNow    separated by comma
     *
     * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface The found Event Objects
     */
    public function findAllByCategories($categories, $fromNow = true)
    {
        $query = $this->createQuery();

        // order by start_date -> start_time...
        $query->setOrderings(
            ['start_date_time' => QueryInterface::ORDER_ASCENDING]
        );

        $constraints = [];
        $constraints[] = $query->in('categories.uid', $categories);
        if ($fromNow === true) {
            $constraints[] = $query->greaterThan('start_date_time', strtotime('today'));
        }

        $query->matching($query->logicalAnd($constraints));

        return $query->execute();
    }

    /**
     * Finds all datasets using the flexform settings
     *
     * @param array   $settings
     * @param integer $geniusBar
     *
     * @return array The found Event Objects
     */
    public function findAllBySettings($settings, $geniusBar = 0)
    {
        $query = $this->createQuery();
        $constraints = [];

        // we don't want genius_bar events here
        $constraints[] = $query->equals('genius_bar', $geniusBar);

        // is user / subscriber given
        if ((int)$settings['user'] > 0) {
            $constraints[] = $query->logicalAnd(
                [
                    $query->equals('subscribers.customerid', $settings['user']),
                    $query->logicalNot(
                        $query->equals('subscribers.editcode', '')
                    )
                ]
            );
        }

        // are categories selected?
        if (is_array($settings['categoryList']) && count($settings['categoryList']) > 0) {
            $constraints[] = $query->in('categories.uid', $settings['categoryList']);
        }

        // are disciplines selected?
        if (is_array($settings['disciplineList']) && count($settings['disciplineList']) > 0) {
            $constraints[] = $query->in('discipline.uid', $settings['disciplineList']);
        }

        // are contacts selected?
        if (!empty($settings['contactsSelection'])) {
            $constraints[] = $query->in(
                'contact.uid',
                GeneralUtility::intExplode(',', $settings['contactsSelection'], true)
            );
        }

        // if its the homepage view hide past events exactly to the minute
        if ($settings['exactlyToTheMinute']) {
            $constraints[] = $query->greaterThan('start_date_time', strtotime('now'));
        }

        // default is to show events beginning with today
        if ($settings['showPastEvents'] != true) {
            if ($settings['showEventsFromNow']) {
                $constraints[] = $query->greaterThan('end_date_time', strtotime('now'));
            } else {
                $constraints[] = $query->greaterThan('start_date_time', strtotime('today'));
            }
        }

        // limit to next weeks
        if ($settings['limitByNextWeeks'] >= 1) {
            $now = new \DateTime();
            $dateTimeInterval = new \DateInterval("P" . (int)$settings['limitByNextWeeks'] . "W");
            $constraints[] = $query->lessThanOrEqual('start_date_time', $now->add($dateTimeInterval));
        }

        // default is to show only future events
        if (!empty($settings['startTimestamp']) && !empty($settings['stopTimestamp'])) {
            $constraints[] = $query->greaterThanOrEqual('start_date_time', $settings['startTimestamp']);
            $constraints[] = $query->lessThanOrEqual('start_date_time', $settings['stopTimestamp']);
        }

        // AND all constraints together
        if (count($constraints)) {
            $query->matching($query->logicalAnd($constraints));
        }

        // order by start_date ascending or descending
        if ($settings['eventOrdering'] === 'DESC') {
            $query->setOrderings(
                ['start_date_time' => QueryInterface::ORDER_DESCENDING]
            );
        } else {
            $query->setOrderings(
                ['start_date_time' => QueryInterface::ORDER_ASCENDING]
            );
        }

        if (!empty($settings['limit']) && (int)$settings['limit'] > 0) {
            $query->setLimit((int)$settings['limit']);
        }

        return $query->execute();
    }

    /**
     * Finds all datasets by disciplines
     *
     * @param int $discipline
     * @param int $category
     *
     * @return array The found Event Objects
     */
    public function findAllByDisciplineAndCategory($discipline, $category)
    {
        $query = $this->createQuery();

        $constraints = [];
        $constraints[] = $query->equals('discipline.uid', $discipline);
        $constraints[] = $query->equals('categories.uid', $category);
        $constraints[] = $query->greaterThan('start_date_time', strtotime('today'));

        if (count($constraints)) {
            $query->matching($query->logicalAnd($constraints));
        }

        // order by start_date -> start_time...
        $query->setOrderings(
            ['start_date_time' => QueryInterface::ORDER_ASCENDING]
        );

        return $query->execute();
    }

    /**
     * Finds all datasets by MM relation categories
     *
     * INCLUDE hidden events for backend usage only!
     *
     * @param string $categories separated by comma
     * @param string $searchString
     * @param int    $startDateStamp
     * @param array  $contacts   separated by comma
     * @param int    $recurring   is recurring event
     *
     * @return array The found Event Objects
     */
    public function findAllByCategoriesAndDate($categories, $startDateStamp, $searchString = '', $contacts = [], $recurring = 0)
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setIgnoreEnableFields(true);
        $query->getQuerySettings()->setEnableFieldsToBeIgnored('hidden');

        $constraints = [];

        if (!empty($categories)) {
            $constraints[] = $query->in('categories.uid', $categories);
        }

        if (!empty($contacts) && is_array($contacts)) {
            $constraints[] = $query->in('contact', $contacts);
        }

        $constraints[] = $query->greaterThan('start_date_time', $startDateStamp);

        if (!empty($searchString)) {
            $constraints[] = $query->like('title', '%' . $searchString . '%');
        }

        if (!empty($recurring)) {
            $constraints[] = $query->equals('recurring', $recurring);
        }

        if (count($constraints)) {
            $query->matching($query->logicalAnd($constraints));
        }

        // order by start_date -> start_time...
        $query->setOrderings(
            ['start_date_time' => QueryInterface::ORDER_ASCENDING]
        );

        return $query->execute();
    }

    /**
     * Finds all datasets by MM relation categories
     *
     * @param int $startDateStamp
     * @param int $stopDateStamp
     *
     * @return array The found Event Objects
     */
    public function findAllByDateInterval($startDateStamp, $stopDateStamp)
    {
        $query = $this->createQuery();

        $constraints = [];

        $constraints[] = $query->greaterThanOrEqual('start_date_time', $startDateStamp);
        $constraints[] = $query->lessThanOrEqual('start_date_time', $stopDateStamp);

        if (count($constraints)) {
            $query->matching($query->logicalAnd($constraints));
        }

        // order by start_date -> start_time...
        $query->setOrderings(
            ['start_date_time' => QueryInterface::ORDER_ASCENDING]
        );

        return $query->execute();
    }

    /**
     * Finds all datasets by MM relation categories
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Slub\SlubEvents\Domain\Model\Subscriber> $subscribers
     *
     * @return array The found Event Objects
     */
    public function findAllBySubscriber($subscribers)
    {
        $query = $this->createQuery();

        $constraints = [];
        foreach ($subscribers as $subscriber) {
            $editCode = $subscriber->getEditcode();
            if (!empty($editCode)) {
                $constraints[] = $query->equals('subscribers.editcode', $editCode);
            }
        }

        if (count($constraints)) {
            $query->matching(
                $query->logicalAND(
                    $query->greaterThan('start_date_time', strtotime('today')),
                    $query->logicalOr($constraints)
                )
            );
        } else {
            return;
        }

        // order by start_date -> start_time...
        $query->setOrderings(
            ['start_date_time' => QueryInterface::ORDER_ASCENDING]
        );

        return $query->execute();
    }

    /**
     * Finds all events in future where subscription time ended (deadline)
     * and ignore events with external registration
     *
     * @param string categories separated by comma
     *
     * @return array The found Event Objects
     */
    public function findAllSubscriptionEnded()
    {
        $query = $this->createQuery();

        $constraints = [];
        $constraints[] = $query->greaterThan('start_date_time', strtotime('today'));
        $constraints[] = $query->lessThan('sub_end_date_time', time());
        $constraints[] = $query->greaterThan('sub_end_date_time', 0);
        $constraints[] = $query->equals('sub_end_date_info_sent', '0');
        $constraints[] = $query->equals('external_registration', '');

        if (count($constraints)) {
            $query->matching($query->logicalAnd($constraints));
        }

        // order by start_date -> start_time...
        $query->setOrderings(
            ['start_date_time' => QueryInterface::ORDER_ASCENDING]
        );

        return $query->execute();
    }


    /**
     * Finds all events with given startDateTime and parent
     *
     * @param \DateTime $startDateStamp
     * @param Event $parent
     *
     * @return array The found Event Objects
     */
    public function findByStartDateTimeAndParent($startDateStamp, $parent)
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setIgnoreEnableFields(true);
        $query->getQuerySettings()->setEnableFieldsToBeIgnored('hidden');

        $constraints = [];

        $constraints[] = $query->equals('start_date_time', $startDateStamp);
        $constraints[] = $query->equals('parent', $parent);

        if (count($constraints)) {
            $query->matching($query->logicalAnd($constraints));
        }

        // order by start_date -> start_time...
        $query->setOrderings(
            ['start_date_time' => QueryInterface::ORDER_ASCENDING]
        );

        return $query->execute();
    }

    /**
     * Finds one events with given startDateTime and parent
     *
     * @param \DateTime $startDateStamp
     * @param Event $parent
     *
     * @return array The found Event Objects
     */
    public function findOneByStartDateTimeAndParent($startDateStamp, $parent)
    {
        return $this->findByStartDateTimeAndParent($startDateStamp, $parent)->getFirst();
    }

    /**
     * Finds one events - even if hidden
     *
     * @param int $uid
     *
     * @return array The found Event Objects
     */
    public function findOneByUidIncludeHidden($uid)
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setIgnoreEnableFields(true);
        $query->getQuerySettings()->setEnableFieldsToBeIgnored('hidden');

        $constraints = [];
        $constraints[] = $query->equals('uid', $uid);

        if (count($constraints)) {
            $query->matching($query->logicalAnd($constraints));
        }

        return $query->execute()->getFirst();

    }

    /**
     * Delete all child events which are not in the list of allowed startDateTimes
     *
     * @param array $childDateTimes
     * @param Event $parent
     *
     * @return void
     */
    public function deleteAllNotAllowedChildren($childDateTimes, $parent)
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setIgnoreEnableFields(true);
        $query->getQuerySettings()->setEnableFieldsToBeIgnored('hidden');

        $constraints = [];

        $uidsAllowed = [];

        foreach ($childDateTimes as $childDateTime) {
            foreach ($this->findByStartDateTimeAndParent($childDateTime['startDateTime'], $parent) as $childEvent) {
                $uidsAllowed[] = $childEvent->getUid();
            };
        }

        if (!empty($uidsAllowed)) {
            $constraints[] = $query->logicalNot($query->in('uid', $uidsAllowed));
        }
        $constraints[] = $query->equals('parent', $parent);

        if (count($constraints)) {
            $query->matching($query->logicalAnd($constraints));
        }

        $eventsToBeRemoved = $query->execute();

        foreach($eventsToBeRemoved as $eventRemove) {
            $this->remove($eventRemove);
        }

    }

    /**
     * Find all future childevent of given parent
     *
     * @param Event $parent
     *
     * @return array The found Event Objects
     */
    public function findFutureByParent($parent)
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setIgnoreEnableFields(true);
        $query->getQuerySettings()->setEnableFieldsToBeIgnored('hidden');

        $constraints = [];
        $constraints[] = $query->equals('parent', $parent);
        $constraints[] = $query->greaterThan('start_date_time', strtotime('today'));

        if (count($constraints)) {
            $query->matching($query->logicalAnd($constraints));
        }

        // order by start_date -> start_time...
        $query->setOrderings(
            ['start_date_time' => QueryInterface::ORDER_ASCENDING]
        );

        return $query->execute();
    }

    /**
	 * Find all events older than given days
	 *
	 * @param integer $days
	 * @return objects found old events
	 */
	public function findOlderThan($days) {

        $query = $this->createQuery();
        $query->getQuerySettings()->setIgnoreEnableFields(true);
        $query->getQuerySettings()->setEnableFieldsToBeIgnored('hidden');

        $constraints = [];

        $constraints[] = $query->lessThanOrEqual('end_date_time', strtotime(' - ' . $days . ' days'));

        if (count($constraints)) {
            $query->matching($query->logicalAnd($constraints));
        }

        return $query->execute();

    }

    /**
     * Returns the name of the Event-Table
     * @return string
     */
    protected static function getTableName()
    {
        /**
         * @var \TYPO3\CMS\Extbase\Object\ObjectManager                  $objectManager
         * @var \TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper $dataMapper
         */
        $objectManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
        $dataMapper = $objectManager->get('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\Mapper\\DataMapper');

        return $dataMapper
            ->getDataMap('Slub\\SlubEvents\\Domain\\Model\\Event')
            ->getTableName();
    }
}
