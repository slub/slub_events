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
class Tx_SlubEvents_Domain_Model_Event extends Tx_Extbase_DomainObject_AbstractEntity {

	/**
	 * title
	 *
	 * @var boolean
	 */
	protected $hidden;

	/**
	 * title
	 *
	 * @var string
	 */
	protected $title;

	/**
	 * startDateTime
	 *
	 * @var DateTime
	 * @validate NotEmpty
	 */
	protected $startDateTime;

	/**
	 * This is an Allday-Event (Time disabled)
	 *
	 * @var boolean
	 */
	protected $allDay = FALSE;

	/**
	 * End Date of Event
	 *
	 * @var DateTime
	 */
	protected $endDateTime;

	/**
	 * End Date of Subscription
	 *
	 * @var DateTime
	 */
	protected $subEndDateTime;

	/**
	 * teaser
	 *
	 * @var string
	 */
	protected $teaser;

	/**
	 * description
	 *
	 * @var string
	 */
	protected $description;

	/**
	 * Minimum of Subscribers
	 *
	 * @var integer
	 */
	protected $minSubscriber;

	/**
	 * Maximum of Subscribers
	 *
	 * @var integer
	 */
	protected $maxSubscriber;

	/**
	 * Target Audience
	 *
	 * @var integer
	 * @validate NotEmpty
	 */
	protected $audience;

	/**
	 * Sent Information about SubEndTime reached
	 *
	 * @var boolean
	 */
	protected $subEndDateInfoSent = FALSE;

	/**
	 * This is a genius bar event
	 *
	 * @var boolean
	 */
	protected $geniusBar = FALSE;

	/**
	 * The event  has been canceld
	 *
	 * @var boolean
	 */
	protected $cancelled = FALSE;

	/**
	 * Category Id
	 *
	 * @var Tx_Extbase_Persistence_ObjectStorage<Tx_SlubEvents_Domain_Model_Category>
	 */
	protected $categories;

	/**
	 * Subscriber Ids
	 *
	 * @var Tx_Extbase_Persistence_ObjectStorage<Tx_SlubEvents_Domain_Model_Subscriber>
	 * @lazy
	 */
	protected $subscribers;

	/**
	 * Location Ids
	 *
	 * @var Tx_SlubEvents_Domain_Model_Location
	 */
	protected $location;

	/**
	 * Discipline ID
	 *
	 * @var Tx_SlubEvents_Domain_Model_Discipline
	 */
	protected $discipline;

	/**
	 * Contact ID
	 *
	 * @var Tx_SlubEvents_Domain_Model_Contact
	 */
	protected $contact;

	/**
	 * onlinesurvey
	 *
	 * @var string
	 */
	protected $onlinesurvey;

	/**
	 * Returns hidden
	 *
	 * @return boolean $hidden
	 */
	public function getHidden() {
		return $this->hidden;
	}

	/**
	 * Sets hidden
	 *
	 * @param boolean $hidden
	 * @return void
	 */
	public function setHidden($hidden) {
		$this->hidden = $hidden;
	}

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
	 * Returns the teaser
	 *
	 * @return string $teaser
	 */
	public function getTeaser() {
		return $this->teaser;
	}

	/**
	 * Sets the teaser
	 *
	 * @param string $teaser
	 * @return void
	 */
	public function setTeaser($teaser) {
		$this->teaser = $teaser;
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
		$this->categories = new Tx_Extbase_Persistence_ObjectStorage();

		$this->subscribers = new Tx_Extbase_Persistence_ObjectStorage();
	}

	/**
	 * Returns the minSubscriber
	 *
	 * @return integer $minSubscriber
	 */
	public function getMinSubscriber() {
		return $this->minSubscriber;
	}

	/**
	 * Sets the minSubscriber
	 *
	 * @param integer $minSubscriber
	 * @return void
	 */
	public function setMinSubscriber($minSubscriber) {
		$this->minSubscriber = $minSubscriber;
	}

	/**
	 * Returns the maxSubscriber
	 *
	 * @return integer $maxSubscriber
	 */
	public function getMaxSubscriber() {
		return $this->maxSubscriber;
	}

	/**
	 * Sets the maxSubscriber
	 *
	 * @param integer $maxSubscriber
	 * @return void
	 */
	public function setMaxSubscriber($maxSubscriber) {
		$this->maxSubscriber = $maxSubscriber;
	}

	/**
	 * Adds a Subscriber
	 *
	 * @param Tx_SlubEvents_Domain_Model_Subscriber $subscriber
	 * @return void
	 */
	public function addSubscriber(Tx_SlubEvents_Domain_Model_Subscriber $subscriber) {
		$this->subscribers->attach($subscriber);
	}

	/**
	 * Removes a Subscriber
	 *
	 * @param Tx_SlubEvents_Domain_Model_Subscriber $subscriberToRemove The Subscriber to be removed
	 * @return void
	 */
	public function removeSubscriber(Tx_SlubEvents_Domain_Model_Subscriber $subscriberToRemove) {
		$this->subscribers->detach($subscriberToRemove);
	}

	/**
	 * Returns the subscribers
	 *
	 * @return Tx_Extbase_Persistence_ObjectStorage<Tx_SlubEvents_Domain_Model_Subscriber> $subscribers
	 */
	public function getSubscribers() {
		return $this->subscribers;
	}

	/**
	 * Sets the subscribers
	 *
	 * @param Tx_Extbase_Persistence_ObjectStorage<Tx_SlubEvents_Domain_Model_Subscriber> $subscribers
	 * @return void
	 */
	public function setSubscribers(Tx_Extbase_Persistence_ObjectStorage $subscribers) {
		$this->subscribers = $subscribers;
	}

	/**
	 * Returns the audience
	 *
	 * @return integer $audience
	 */
	public function getAudience() {
		return $this->audience;
	}

	/**
	 * Sets the audience
	 *
	 * @param integer $audience
	 * @return void
	 */
	public function setAudience($audience) {
		$this->audience = $audience;
	}

	/**
	 * Returns the discipline
	 *
	 * @return Tx_SlubEvents_Domain_Model_Discipline $discipline
	 */
	public function getDiscipline() {
		return $this->discipline;
	}

	/**
	 * Sets the discipline
	 *
	 * @param Tx_SlubEvents_Domain_Model_Discipline $discipline
	 * @return void
	 */
	public function setDiscipline(Tx_SlubEvents_Domain_Model_Discipline $discipline) {
		$this->discipline = $discipline;
	}

	/**
	 * Returns the location
	 *
	 * @return Tx_SlubEvents_Domain_Model_Location $location
	 */
	public function getLocation() {
		return $this->location;
	}

	/**
	 * Sets the location
	 *
	 * @param Tx_SlubEvents_Domain_Model_Location $location
	 * @return void
	 */
	public function setLocation(Tx_SlubEvents_Domain_Model_Location $location) {
		$this->location = $location;
	}

	/**
	 * Returns the contact
	 *
	 * @return Tx_SlubEvents_Domain_Model_Event $event
	 */
	public function getEvent() {
		return $this->event;
	}

	/**
	 * Returns the contact
	 *
	 * @return Tx_SlubEvents_Domain_Model_Contact $contact
	 */
	public function getContact() {
		return $this->contact;
	}

	/**
	 * Sets the contact
	 *
	 * @param Tx_SlubEvents_Domain_Model_Contact $contact
	 * @return void
	 */
	public function setContact(Tx_SlubEvents_Domain_Model_contact $contact) {
		$this->contact = $contact;
	}

	/**
	 * Returns the allDay
	 *
	 * @return boolean allDay
	 */
	public function getAllDay() {
		return $this->allDay;
	}

	/**
	 * Sets the allDay
	 *
	 * @param boolean $allDay
	 * @return boolean allDay
	 */
	public function setAllDay($allDay) {
		$this->allDay = $allDay;
	}

	/**
	 * Returns the boolean state of allDay
	 *
	 * @return boolean allDay
	 */
	public function isAllDay() {
		return $this->getAllDay();
	}

	/**
	 * Returns the startDateTime
	 *
	 * @return DateTime startDateTime
	 */
	public function getStartDateTime() {
		return $this->startDateTime;
	}

	/**
	 * Sets the startDateTime
	 *
	 * @param DateTime $startDateTime
	 * @return DateTime startDateTime
	 */
	public function setStartDateTime($startDateTime) {
		$this->startDateTime = $startDateTime;
	}

	/**
	 * Returns the endDateTime
	 *
	 * @return DateTime endDateTime
	 */
	public function getEndDateTime() {
		return $this->endDateTime;
	}

	/**
	 * Sets the endDateTime
	 *
	 * @param DateTime $endDateTime
	 * @return DateTime endDateTime
	 */
	public function setEndDateTime($endDateTime) {
		$this->endDateTime = $endDateTime;
	}

	/**
	 * Returns the subEndDateTime
	 *
	 * @return DateTime subEndDateTime
	 */
	public function getSubEndDateTime() {
		return $this->subEndDateTime;
	}

	/**
	 * Sets the subEndDateTime
	 *
	 * @param DateTime $subEndDateTime
	 * @return DateTime subEndDateTime
	 */
	public function setSubEndDateTime($subEndDateTime) {
		$this->subEndDateTime = $subEndDateTime;
	}

	/**
	 * Returns the subEndDateInfoSent
	 *
	 * @return DateTime subEndDateTime
	 */
	public function getSubEndDateInfoSent() {
		return $this->subEndDateInfoSent;
	}

	/**
	 * Sets the subEndDateInfoSent
	 *
	 * @param DateTime $subEndDateTime
	 * @return DateTime subEndDateTime
	 */
	public function setSubEndDateInfoSent($subEndDateInfoSent) {
		$this->subEndDateInfoSent = $subEndDateInfoSent;
	}

	/**
	 * Returns geniusBar
	 *
	 * @return boolean $geniusBar
	 */
	public function getGeniusBar() {
		return $this->geniusBar;
	}

	/**
	 * Sets geniusBar
	 *
	 * @param boolean $geniusBar
	 * @return void
	 */
	public function setGeniusBar($geniusBar) {
		$this->geniusBar = $geniusBar;
	}

	/**
	 * Returns the categories
	 *
	 * @return Tx_Extbase_Persistence_ObjectStorage<Tx_SlubEvents_Domain_Model_Category> $categories
	 */
	public function getCategories() {
		return $this->categories;
	}

	/**
	 * Adds a categories
	 *
	 * @param Tx_SlubEvents_Domain_Model_Category $category
	 * @return Tx_Extbase_Persistence_ObjectStorage<Tx_SlubEvents_Domain_Model_Category> categories
	 */
	public function addCategory(Tx_SlubEvents_Domain_Model_Category $category) {
		$this->categories->attach($category);
	}

	/**
	 * Returns the cancelled
	 *
	 * @return boolean $cancelled
	 */
	public function getCancelled() {
		return $this->cancelled;
	}

	/**
	 * Sets the cancelled
	 *
	 * @param boolean $cancelled
	 * @return void
	 */
	public function setCancelled($cancelled) {
		$this->cancelled = $cancelled;
	}

	/**
	 * Returns the boolean state of cancelled
	 *
	 * @return boolean
	 */
	public function isCancelled() {
		return $this->getCancelled();
	}

	/**
	 * Returns the onlinesurvey
	 *
	 * @return string $onlinesurvey
	 */
	public function getOnlinesurvey() {
		return $this->onlinesurvey;
	}

	/**
	 * Sets the onlinesurvey
	 *
	 * @param string $onlinesurvey
	 * @return void
	 */
	public function setOnlinesurvey($onlinesurvey) {
		$this->onlinesurvey = $onlinesurvey;
	}

}

?>
