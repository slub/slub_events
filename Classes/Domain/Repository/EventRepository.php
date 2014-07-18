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
class Tx_SlubEvents_Domain_Repository_EventRepository extends Tx_Extbase_Persistence_Repository {

	/**
	 * Finds all datasets by MM relation categories
	 *
	 * @param Tx_SlubEvents_Domain_Model_Category $category
	 * @return array The found Event Objects
	 */
	public function findAllGbByCategory($category) {

		$query = $this->createQuery();

		$constraints = array();
		//~ $constraints[] = $query->in('tx_slubevents_domain_model_category.uid', $categories);
		$constraints[] = $query->equals('categories.uid', $category);
		$constraints[] = $query->equals('genius_bar', 1);
		$constraints[] = $query->greaterThan('start_date_time', strtotime('today'));

		if (count($constraints)) {
			$query->matching($query->logicalAnd($constraints));
		}

		// order by start_date -> start_time...
		$query->setOrderings(
			array('start_date_time' => Tx_Extbase_Persistence_QueryInterface::ORDER_ASCENDING)
		);

		return $query->execute();
	}

	/**
	 * Finds all datasets by MM relation categories
	 *
	 * @param string $categories separated by comma
	 * @return array The found Event Objects
	 */
	public function findAllByCategories($categories) {

		$query = $this->createQuery();

		$constraints = array();

		$constraints[] = $query->in('categories.uid', $categories);
		$constraints[] = $query->greaterThan('start_date_time', strtotime('today'));

		if (count($constraints)) {
			$query->matching($query->logicalAnd($constraints));
		}

		// order by start_date -> start_time...
		$query->setOrderings(
			array('start_date_time' => Tx_Extbase_Persistence_QueryInterface::ORDER_ASCENDING)
		);

		return $query->execute();
	}

	/**
	 * Finds all datasets using the flexform settings
	 *
	 * @param array $settings
	 * @return array The found Event Objects
	 */
	public function findAllBySettings($settings) {

		$query = $this->createQuery();
		$constraints = array();

		// we don't want genius_bar events here
		$constraints[] = $query->equals('genius_bar', 0);

		// are categories selected?
		if (!empty($settings['categoryList'])) {
			$constraints[] = $query->in('categories.uid', $settings['categoryList']);
		}

		// are disciplines selected?
		if (!empty($settings['disciplineList'])) {
			$constraints[] = $query->in('discipline.uid', $settings['disciplineList']);
		}

		// are contacts selected?
		if (!empty($settings['contactsSelection'])) {
			$constraints[] = $query->in('contact.uid', t3lib_div::intExplode(',', $settings['contactsSelection'], TRUE));
		}

		// default is to show only future events
		if ($settings['showPastEvents'] !== TRUE) {
			$constraints[] = $query->greaterThan('start_date_time', strtotime('today'));
		}

		// AND all constraints together
		if (count($constraints)) {
			$query->matching($query->logicalAnd($constraints));
		}

		// order by start_date ascending or descending
		if ($settings['eventOrdering'] === 'DESC') {
			$query->setOrderings(
				array('start_date_time' => Tx_Extbase_Persistence_QueryInterface::ORDER_DESCENDING)
			);
		} else {
			$query->setOrderings(
				array('start_date_time' => Tx_Extbase_Persistence_QueryInterface::ORDER_ASCENDING)
			);
		}

		return $query->execute();
	}


	/**
	 * Finds all datasets by disciplines
	 *
	 * @param int $discipline
	 * @param int $category
	 * @return array The found Event Objects
	 */
	public function findAllByDisciplineAndCategory($discipline, $category) {

		$query = $this->createQuery();

		$constraints = array();
		//~ $constraints[] = $query->in('tx_slubevents_domain_model_category.uid', $categories);
		$constraints[] = $query->equals('discipline.uid', $discipline);
		$constraints[] = $query->equals('categories.uid', $category);
		$constraints[] = $query->greaterThan('start_date_time', strtotime('today'));

		if (count($constraints)) {
			$query->matching($query->logicalAnd($constraints));
		}

		// order by start_date -> start_time...
		$query->setOrderings(
			array('start_date_time' => Tx_Extbase_Persistence_QueryInterface::ORDER_ASCENDING)
		);

		return $query->execute();
	}


	/**
	 * Finds all datasets by MM relation categories
	 *
	 * INCLUDE hidden events for backend usage only!
	 *
	 * @param string categories separated by comma
	 * @param int startdatestamp
	 * @param string searchString
	 * @param array contacts separated by comma
	 * @return array The found Event Objects
	 */
	public function findAllByCategoriesAndDate($categories, $startDateStamp, $searchString = '', $contacts = array()) {

		$query = $this->createQuery();

		$constraints = array();

		if (t3lib_utility_VersionNumber::convertVersionNumberToInteger(TYPO3_version) <  '6000000') {
			// TYPO3 4.7
			// include hidden and deleted records
			$query->getQuerySettings()->setRespectEnableFields(FALSE);
			// get rid of deleted records
			$constraints[] = $query->equals('deleted', 0 );
		} else {
			// TYPO3 6.x
			$query->getQuerySettings()->setIgnoreEnableFields(TRUE);
			$query->getQuerySettings()->setEnableFieldsToBeIgnored('hidden');
		}

			$constraints[] = $query->in('categories.uid', $categories );
			if (!empty($contacts))
				$constraints[] = $query->in('contact', $contacts);
			$constraints[] = $query->greaterThan('start_date_time', $startDateStamp);
			if (!empty($searchString))
				$constraints[] = $query->like('title', '%'.$searchString.'%');

		if (count($constraints)) {
			$query->matching($query->logicalAnd($constraints));
		}

		// order by start_date -> start_time...
		$query->setOrderings(
			array('start_date_time' => Tx_Extbase_Persistence_QueryInterface::ORDER_ASCENDING)
		);

		return $query->execute();
	}

	/**
	 * Finds all datasets by MM relation categories
	 *
	 * @param string categories separated by comma
	 * @param int startdatestamp
	 * @param int stopDateStamp
	 * @return array The found Event Objects
	 */
	public function findAllByCategoriesAndDateInterval($categories, $startDateStamp, $stopDateStamp) {

		$query = $this->createQuery();

		$constraints = array();

		$constraints[] = $query->in('categories.uid', $categories);
		$constraints[] = $query->greaterThanOrEqual('start_date_time', $startDateStamp);
		$constraints[] = $query->lessThanOrEqual('start_date_time', $stopDateStamp);

		if (count($constraints)) {
			$query->matching($query->logicalAnd($constraints));
		}

		// order by start_date -> start_time...
		$query->setOrderings(
			array('start_date_time' => Tx_Extbase_Persistence_QueryInterface::ORDER_ASCENDING)
		);

		return $query->execute();
	}

	/**
	 * Finds all datasets by MM relation categories
	 *
	 * @param Tx_Extbase_Persistence_ObjectStorage<Tx_SlubEvents_Domain_Model_Subscriber> $subscribers
	 * @return array The found Event Objects
	 */
	public function findAllBySubscriber($subscribers) {

		$query = $this->createQuery();

		$constraints = array();
		foreach ($subscribers as $subscriber)
			$constraints[] = $query->equals('subscribers.editcode', $subscriber->getEditcode());

		if (count($constraints)) {
			$query->matching(
				$query->logicalAND($query->greaterThan('start_date_time',  strtotime('today')),
				$query->logicalOr($constraints)
				)
			);
		} else
			return;

		// order by start_date -> start_time...
		$query->setOrderings(
			array('start_date_time' => Tx_Extbase_Persistence_QueryInterface::ORDER_ASCENDING)
		);

		return $query->execute();
	}

	/**
	 * Finds all start dates for past and future events
	 *
	 * @return array The found Event Objects
	 */
	public function findAllStartMonths() {

		global $BE_USER;

		// we don't want to get an extbase object but an ordinary PHP array:
		$query = $this->createQuery();
		$query->getQuerySettings()->setReturnRawQueryResult(TRUE);
		// order by start_date -> start_time...
		$query->setOrderings(
			array('start_date_time' => Tx_Extbase_Persistence_QueryInterface::ORDER_ASCENDING)
		);
		$query->setLimit(1);

		$oldest = $query->execute();

		$query = $this->createQuery();
		$query->getQuerySettings()->setReturnRawQueryResult(TRUE);
		// order by start_date -> start_time...
		$query->setOrderings(
			array('start_date_time' => Tx_Extbase_Persistence_QueryInterface::ORDER_DESCENDING)
		);
		$query->setLimit(1);

		$newest = $query->execute();

		$startDate = strtotime('first day of this month 00:00:00', $oldest['0']['start_date_time']);

		for ($date = $startDate; $date <= $newest['0']['start_date_time']; $date = strtotime('+1 month', $date)) {
				$dateShow[$date] = strftime('%d %B %Y', $date);
		}

		return $dateShow;
	}

	/**
	 * Finds all events in future where subscription time ended (deadline)
	 *
	 * @param string categories separated by comma
	 * @return array The found Event Objects
	 */
	public function findAllSubscriptionEnded() {

		$query = $this->createQuery();

		$constraints = array();
		$constraints[] = $query->greaterThan('start_date_time', strtotime('today') );
		$constraints[] = $query->lessThan('sub_end_date_time', time() );
		$constraints[] = $query->greaterThan('sub_end_date_time', 0);
		$constraints[] = $query->equals('sub_end_date_info_sent', '0');

		if (count($constraints)) {
			$query->matching($query->logicalAnd($constraints));
		}

		// order by start_date -> start_time...
		$query->setOrderings(
			array('start_date_time' => Tx_Extbase_Persistence_QueryInterface::ORDER_ASCENDING)
		);

		return $query->execute();
	}

}

?>
