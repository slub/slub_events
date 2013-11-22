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
class Tx_SlubEvents_Domain_Repository_CategoryRepository extends Tx_Extbase_Persistence_Repository {


	/**
	 * Finds all datasets by MM relation categories
	 *
	 * @param string categories separated by comma
	 * @return array The found Category Objects
	 */
	public function findAllByUids($categories) {

		$query = $this->createQuery();

		$constraints = array();
		$constraints[] = $query->in('uid', $categories);

		if (count($constraints)) {
			$query->matching($query->logicalAnd($constraints));
		}

		return $query->execute();
	}

	/**
	 * Finds all datasets and return in tree order
	 *
	 * @return array The found Category Objects
	 */
	public function findAllTree() {

		$query = $this->createQuery();

		$categories = $query->execute();

		$flatCategories = array();
		foreach ($categories as $category) {
			$flatCategories[$category->getUid()] = Array(
				'item' =>  $category,
				'parent' => ($category->getParent()->current()) ? $category->getParent()->current()->getUid() : NULL
			);
		}

		$tree = array();
		foreach ($flatCategories as $id => &$node) {
			if ($node['parent'] === NULL) {
				$tree[$id] = &$node;
			} else {
				$flatCategories[$node['parent']]['children'][$id] = &$node;
			}
		}

		return $tree;
	}

	/**
	 * Finds all datasets and return in tree order
	 *
	 * @param string categories separated by comma
	 * @return array The found Category Objects
	 */
	public function findAllByUidsTree($categories) {

		$query = $this->createQuery();

		$constraints = array();
		$constraints[] = $query->in('uid', $categories);

		if (count($constraints)) {
			$query->matching($query->logicalAnd($constraints));
		}

		$categories = $query->execute();

		$flatCategories = array();
		foreach ($categories as $category) {
			$flatCategories[$category->getUid()] = Array(
				'item' =>  $category,
				'parent' => ($category->getParent()->current()) ? $category->getParent()->current()->getUid() : NULL
			);
		}

		$tree = array();
		foreach ($flatCategories as $id => &$node) {
			if ($node['parent'] === NULL) {
				$tree[$id] = &$node;
			} else {
				$flatCategories[$node['parent']]['children'][$id] = &$node;
			}
		}

		return $tree;
	}

	/**
	 *
	 * @param Tx_SlubEvents_Domain_Model_Category $category
	 *
	 * @return int uid
	 */
	    public function getParentUidLocalized($category) {

		// is a localized entry?
		if ($category->getL10nParent()) {

			// create own query because of buggy localization handling in extbase 4.7 with relations
			$query = $this->createQuery();

			$query->getQuerySettings()->setReturnRawQueryResult(TRUE);

			$buildquery = 'SELECT parent ';
			$buildquery .= 'FROM tx_slubevents_domain_model_category ';
			$buildquery .= 'WHERE uid = \''. $category->getL10nParent() .'\' ';
			$buildquery .= 'AND deleted = \'0\' ';
			$buildquery .= 'AND hidden = \'0\' ';

			$query->statement($buildquery);

			$parentCategory = $query->execute();

			$parentUid = $parentCategory[0]['parent'];

		} else {
		    $parentUid = ($category->getParent()->current()) ? $category->getParent()->current()->getUid() : NULL;
		}

		return $parentUid;

	    }

	/**
	 * Finds all datasets of current branch and return in tree order
	 *
	 * @param Tx_SlubEvents_Domain_Model_Category $startCategory
	 * @ignorevalidation $startCategory
	 * @return array The found Category Objects as Tree
	 */
	public function findCurrentBranch($startCategory = NULL) {

		$query = $this->createQuery();

		// order by start_date -> start_time...
		$query->setOrderings(
			array('sorting' => Tx_Extbase_Persistence_QueryInterface::ORDER_ASCENDING)
		);

		$categories = $query->execute();

		$flatCategories = array();
		foreach ($categories as $category) {
			$flatCategories[$category->getUid()] = Array(
				'item' =>  $category,
				'parent' => $this->getParentUidLocalized($category),
			);
		}


		$tree = array();
		foreach ($flatCategories as $id => &$node) {
			if ($node['parent'] === NULL) {
				$tree[$id] = &$node;
			} else {
				$flatCategories[$node['parent']]['children'][$id] = &$node;
			}
		}

		return $tree[$startCategory->getUid()]['children'];
	}

	/**
	 * Finds all datasets of current level and return in tree order
	 *
	 * @param Tx_SlubEvents_Domain_Model_Category $startCategory
	 * @ignorevalidation $startCategory
	 * @return array The found Category Objects as Tree
	 */
	public function findCurrentLevel($startCategory = NULL) {

		$query = $this->createQuery();

		$constraints = array();

		if ($startCategory !== NULL)
			$constraints[] = $query->equals('parent', $startCategory->getUid());
		else
			$constraints[] = $query->equals('parent', 0);

		if (count($constraints)) {
			$query->matching($query->logicalAnd($constraints));
		}
		$categories = $query->execute();

		$flatCategories = array();
		foreach ($categories as $category) {
			$flatCategories[$category->getUid()] = Array(
				'item' =>  $category,
				'parent' => ($category->getParent()->current()) ? $category->getParent()->current()->getUid() : NULL
			);
		}

		$tree = array();

		// if only one categorie exists the foreach-solution below
		// doesn't work as expected --> take the one and give it back as tree-array()
		if (count($flatCategories) == 1) {
			$tree[0] = array_shift($flatCategories);
			return $tree;
		}

		foreach ($flatCategories as $id => &$node) {
			if ($node['parent'] === NULL) {
				$tree[$id] = &$node;
			} else {
				$tree[$node['parent']]['children'][$id] = &$node;
			}
		}

		return $tree;
	}

	/**
	 * Finds all datasets of current branch and return in tree order
	 *
	 * @param Tx_SlubEvents_Domain_Model_Category $startCategory
	 * @ignorevalidation $startCategory
	 * @return array The found Category Objects
	 */
	public function findCategoryRootline($startCategory = NULL) {

		$query = $this->createQuery();

		$constraints = array();

		if ($startCategory !== NULL)
			$constraints[] = $query->equals('parent', $startCategory->getUid());
		else
			$constraints[] = $query->equals('parent', 0);

		if (count($constraints)) {
			$query->matching($query->logicalAnd($constraints));
		}
		$categories = $query->execute();

		$flatCategories = array();
		foreach ($categories as $category) {
			$flatCategories[$category->getUid()] = Array(
				'item' =>  $category,
				'parent' => ($category->getParent()->current()) ? $category->getParent()->current()->getUid() : NULL
			);
		}

		$tree = array();

		// if only one categorie exists the foreach-solution below
		// doesn't work as expected --> take the one and give it back as tree-array()
		if (count($flatCategories) == 1) {
			$tree[0] = array_shift($flatCategories);
			return $tree;
		}

		foreach ($flatCategories as $id => &$node) {
			if ($node['parent'] === NULL) {
				$tree[$id] = &$node;
			} else {
				$flatCategories[$node['parent']]['children'][$id] = &$node;
			}
		}
		return $tree;
	}

}

?>
