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
class Tx_SlubEvents_Domain_Model_Subscriber extends Tx_Extbase_DomainObject_AbstractEntity {

	/**
	 * name
	 *
	 * @var string
	 * @validate NotEmpty
	 */
	protected $name;

	/**
	 * email
	 *
	 * @var string
	 * @validate NotEmpty
	 */
	protected $email;

	/**
	 * telephone
	 *
	 * @var string
	 */
	protected $telephone;

	/**
	 * customerid
	 *
	 * @var string
	 */
	protected $customerid;

	/**
	 * Number of Subscribers
	 *
	 * @var integer
	 */
	protected $number = 2;

	/**
	 * Message by the Customer
	 *
	 * @var string
	 */
	protected $message;

	/**
	 * Edit Code
	 *
	 * @var string
	 * @validate NotEmpty
	 */
	protected $editcode;

	/**
	 * Creation Date
	 *
	 * @var DateTime
	 */
	protected $crdate;

	/**
	 * event
	 *
	 * @var Tx_SlubEvents_Domain_Model_Event
	 */
	protected $event;

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
	 * Returns the event
	 *
	 * @return string $event
	 */
	public function getEvent() {
		return $this->event;
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
	 * Returns the customerid
	 *
	 * @return string $customerid
	 */
	public function getCustomerid() {
		return $this->customerid;
	}

	/**
	 * Sets the customerid
	 *
	 * @param string $customerid
	 * @return void
	 */
	public function setCustomerid($customerid) {
		$this->customerid = $customerid;
	}

	/**
	 * Returns the number
	 *
	 * @return integer number
	 */
	public function getNumber() {
		return $this->number;
	}

	/**
	 * Sets the number
	 *
	 * @param integer $number
	 * @return integer number
	 */
	public function setNumber($number) {
		$this->number = $number;
	}

	/**
	 * Returns the editcode
	 *
	 * @return string $editcode
	 */
	public function getEditcode() {
		return $this->editcode;
	}

	/**
	 * Sets the editcode
	 *
	 * @param string $editcode
	 * @return void
	 */
	public function setEditcode($editcode) {
		$this->editcode = $editcode;
	}

	/**
	 * Returns the message
	 *
	 * @return string $message
	 */
	public function getMessage() {
		return $this->message;
	}

	/**
	 * Sets the message
	 *
	 * @param string $message
	 * @return void
	 */
	public function setMessage($message) {
		$this->message = $message;
	}

	/**
	 * Returns the crdate
	 *
	 * @return DateTime $crdate
	 */
	public function getCrdate() {
		return $this->crdate;
	}

	/**
	 * Sets the crdate
	 *
	 * @param DateTime $crdate
	 * @return void
	 */
	public function setCrdate($crdate) {
		$this->crdate = $crdate;
	}

	/**
	 * Sets the event
	 *
	 * @param Tx_SlubEvents_Domain_Model_Event $event
	 * @return void
	 */
	public function setEvent(Tx_SlubEvents_Domain_Model_Event $event) {
		$this->event = $event;
	}

}

?>