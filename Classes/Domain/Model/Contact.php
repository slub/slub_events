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
 *
 *
 * @package slub_events
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class Tx_SlubEvents_Domain_Model_Contact extends Tx_Extbase_DomainObject_AbstractEntity {

	/**
	 * Name of Contact
	 *
	 * @var string
	 * @validate NotEmpty
	 */
	protected $name;

	/**
	 * Email
	 *
	 * @var string
	 * @validate NotEmpty
	 */
	protected $email;

	/**
	 * Telephone
	 *
	 * @var string
	 */
	protected $telephone;

	/**
	 * Description Field
	 *
	 * @var string
	 */
	protected $description;

	/**
	 * Photo of Contact Person
	 *
	 * @var string
	 */
	protected $photo;

	/**
	 * Returns the name
	 *
	 * @return string $name
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Sets the name
	 *
	 * @param string $name
	 * @return void
	 */
	public function setName($name) {
		$this->name = $name;
	}

	/**
	 * Returns the email
	 *
	 * @return string $email
	 */
	public function getEmail() {
		return $this->email;
	}

	/**
	 * Sets the email
	 *
	 * @param string $email
	 * @return void
	 */
	public function setEmail($email) {
		$this->email = $email;
	}

	/**
	 * Returns the telephone
	 *
	 * @return string $telephone
	 */
	public function getTelephone() {
		return $this->telephone;
	}

	/**
	 * Sets the telephone
	 *
	 * @param string $telephone
	 * @return void
	 */
	public function setTelephone($telephone) {
		$this->telephone = $telephone;
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
	 * Returns the photo
	 *
	 * @return string $photo
	 */
	public function getPhoto() {
		return $this->photo;
	}

	/**
	 * Sets the photo
	 *
	 * @param string $photo
	 * @return void
	 */
	public function setPhoto($photo) {
		$this->photo = $photo;
	}

}
?>