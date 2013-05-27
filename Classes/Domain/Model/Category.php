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
class Tx_SlubEvents_Domain_Model_Category extends Tx_Extbase_DomainObject_AbstractEntity {

	/**
	 * Name of the Category
	 *
	 * @var string
	 * @validate NotEmpty
	 */
	protected $title;

	/**
	 * Description of the Category
	 *
	 * @var string
	 */
	protected $description;

	/**
	 * Parent Category
	 *
	 * @var Tx_Extbase_Persistence_ObjectStorage<Tx_SlubEvents_Domain_Model_Category>
	 */
	protected $parent;

	/**
	 * Returns the title
	 *
	 * @return string $title
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * Sets the title
	 *
	 * @param string $title
	 * @return void
	 */
	public function setTitle($title) {
		$this->title = $title;
	}

	/**
	 * Returns the description
	 *
	 * @return string $description
	 */
	public function getDescription() {
		return $this->description;
	}

	/**
	 * Sets the description
	 *
	 * @param string $description
	 * @return void
	 */
	public function setDescription($description) {
		$this->description = $description;
	}

	/**
	 * __construct
	 *
	 * @return void
	 */
	public function __construct() {
		//Do not remove the next line: It would break the functionality
		$this->initStorageObjects();
	}

	/**
	 * Initializes all Tx_Extbase_Persistence_ObjectStorage properties.
	 *
	 * @return void
	 */
	protected function initStorageObjects() {
		/**
		 * Do not modify this method!
		 * It will be rewritten on each save in the extension builder
		 * You may modify the constructor of this class instead
		 */
		$this->parent = new Tx_Extbase_Persistence_ObjectStorage();
	}

	/**
	 * Adds a Category
	 *
	 * @param Tx_SlubEvents_Domain_Model_Category $parent
	 * @return void
	 */
	public function addParent(Tx_SlubEvents_Domain_Model_Category $parent) {
		$this->parent->attach($parent);
	}

	/**
	 * Removes a Category
	 *
	 * @param Tx_SlubEvents_Domain_Model_Category $parentToRemove The Category to be removed
	 * @return void
	 */
	public function removeParent(Tx_SlubEvents_Domain_Model_Category $parentToRemove) {
		$this->parent->detach($parentToRemove);
	}

	/**
	 * Returns the parent
	 *
	 * @return Tx_Extbase_Persistence_ObjectStorage<Tx_SlubEvents_Domain_Model_Category> $parent
	 */
	public function getParent() {
		return $this->parent;
	}

	/**
	 * Sets the parent
	 *
	 * @param Tx_Extbase_Persistence_ObjectStorage<Tx_SlubEvents_Domain_Model_Category> $parent
	 * @return void
	 */
	public function setParent(Tx_Extbase_Persistence_ObjectStorage $parent) {
		$this->parent = $parent;
	}

}

?>