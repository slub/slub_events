<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Alexander Bigga <alexander.bigga@slub-dresden.de>, SLUB Dresden
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
 * Counts events of given category
 *

 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 * @api
 */

class Tx_SlubEvents_ViewHelpers_Condition_EventsOfCategoryViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {

	/**
	 * eventRepository
	 *
	 * @var Tx_SlubEvents_Domain_Repository_EventRepository
	 * @inject
	 */
	protected $eventRepository;

	/**
	 * categoryRepository
	 *
	 * @var Tx_SlubEvents_Domain_Repository_CategoryRepository
	 * @inject
	 */
	protected $categoryRepository;

	/**
	 * subscriberRepository
	 *
	 * @var Tx_SlubEvents_Domain_Repository_SubscriberRepository
	 * @inject
	 */
	protected $subscriberRepository;

	/**
	 * check if any events of categories below are present and free for booking
	 *
	 * @param Tx_SlubEvents_Domain_Model_Category $category
	 * @return boolean
	 * @api
	 */
	public function render(Tx_SlubEvents_Domain_Model_Category $category) {

		$events = $this->eventRepository->findAllGbByCategory($category);

		$categories = $this->categoryRepository->findCurrentBranch($category);

		$showLink = FALSE;

		if (count($categories) == 0 || count($events) == 0) {
			foreach ($events as $event) {
				$showLink = TRUE;
				if ($this->subscriberRepository->countAllByEvent($event) >= $event->getMaxSubscriber()) {
					$showLink = FALSE;
				}
				// event is cancelled
				if ($event->getCancelled()) {
					$showLink = FALSE;
				}
				// deadline reached....
				if (is_object($event->getSubEndDateTime())) {
					if ($event->getSubEndDateTime()->getTimestamp() < time()) {
						$showLink = FALSE;
					}
				}
				// if any event exists and is valid, break here and return TRUE
				if ($showLink)
					break;
			}
		}
		//~ else
			//~ return TRUE;

		return $showLink;
	}
}
?>
