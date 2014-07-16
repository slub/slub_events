<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Alexander Bigga <alexander.bigga@slub-dresden.de>, SLUB Dresden
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
class Tx_SlubEvents_Domain_Repository_DisciplineRepository extends Tx_Extbase_Persistence_Repository {

	/**
	 * Finds all datasets of current level and return in tree order
	 *
	 * @param integer $startCategory
	 * @return array The found Category Ids
	 */
	public function findAllChildDisciplines($startCategory = 0) {

		$childCategoriesIds = self::findChildDisciplines($startCategory);

		foreach ($categories as $category) {
			$childCategoriesIds = array_merge($this->findChildDisciplines($category), $childCategoriesIds);
		}

		return $childCategoriesIds;
	}

	/**
	 * Finds all categories recursive from given startCategory
	 *
	 * @param integer $startCategory
	 * @return array The found Category Ids
	 */
	private function findChildDisciplines($startCategory = 0) {

		$query = $this->createQuery();

		$constraints = array();

		$constraints[] = $query->equals('parent', $startCategory);

		if (count($constraints)) {
			$query->matching($query->logicalAnd($constraints));
		}
		$categories = $query->execute();

		foreach ($categories as $category) {
			$childCategoriesIds[] = $category->getUid();

			$recursiveCategoriesIds = self::findChildDisciplines($category->getUid());
			if (count($recursiveCategoriesIds) > 0) {
				$childCategoriesIds = array_merge($recursiveCategoriesIds, $childCategoriesIds );
			}
		}

		return $childCategoriesIds;
	}

}
?>
