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
class Tx_SlubEvents_Domain_Repository_SubscriberRepository extends Tx_Extbase_Persistence_Repository {

	/**
	 * Finds subscriber by fe_user data
	 *
	 * @param int pid
	 * @return array The found Subscriber Objects
	 */
	public function findAllByFeuser($pid = 0) {
		
								$query = $this->createQuery();
		
								$constraints = array();
								$constraints[] = $query->equals('customerid', $GLOBALS['TSFE']->fe_user->user['username']);
								if ($pid) {
									$query->getQuerySettings()->setRespectStoragePage(FALSE);
									$constraints[] = $query->equals('pid', $pid);
								}
		
								//~ $constraints[] = $query->greaterThan('start_date',  strtotime('today') );
		
								if (count($constraints)) {
									$query->matching($query->logicalAnd($constraints));
								}
		
								return $query->execute();
	}

	/**
	 * Finds subscriber by fe_user data
	 *
	 * @param string editcode
	 * @param int pid
	 * @return array The found Subscriber Objects
	 */
	public function findAllByEditcode($editcode, $pid = 0) {
		
								$query = $this->createQuery();
		
								$constraints = array();
								$constraints[] = $query->equals('editcode', $editcode);
								if ($pid) {
									$query->getQuerySettings()->setRespectStoragePage(FALSE);
									$constraints[] = $query->equals('pid', $pid);
								}
		
								if (count($constraints)) {
									$query->matching($query->logicalAnd($constraints));
								}
		
								return $query->execute();
	}

	/**
	 * Count all Subscribers by number for a given event
	 *
	 * @param Tx_SlubEvents_Domain_Model_Event event
	 * @return array The found Subscriber Objects
	 */
	public function countAllByEvent($event) {
		
								$query = $this->createQuery();
		
								$constraints = array();
								$constraints[] = $query->equals('event', $event->getUid());
		
								if (count($constraints)) {
									$query->matching($query->logicalAnd($constraints));
								}
		
								// extbase doesn't know Mysql SUM() :-(
								$allSubscribers = $query->execute();
		
								$count = 0;
								foreach ($allSubscribers as $subscriber) {
									$count += $subscriber->getNumber();
								}
		
								return $count;
	}

	/**
	 * Finds subscriber by fe_user data
	 *
	 * @param Tx_Extbase_Persistence_ObjectStorage<Tx_SlubEvents_Domain_Model_Event> $events
	 * @return array The found Subscriber Objects
	 */
	public function findAllByEvents($events) {
		
						$query = $this->createQuery();
		
						$constraints = array();
						$constraints[] = $query->in('event', $events);
		
						if (count($constraints)) {
							$query->matching($query->logicalAnd($constraints));
						}
		
						// order by start_date -> start_time...
						$query->setOrderings(
							array('crdate' => Tx_Extbase_Persistence_QueryInterface::ORDER_DESCENDING)
						);
		
						return $query->execute();
	}

}

?>