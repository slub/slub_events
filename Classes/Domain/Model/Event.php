<?php

namespace Slub\SlubEvents\Domain\Model;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Alexander Bigga <typo3@slub-dresden.de>, SLUB Dresden
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

use TYPO3\CMS\Extbase\Annotation as Extbase;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * @package slub_events
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class Event extends AbstractEntity {

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
     * Parent Event (in case of recurring event)
     *
     * @var \Slub\SlubEvents\Domain\Model\Event
     */
    protected $parent;

    /**
     * @var array
     */
    protected $rootCategories = [];

    /**
     * startDateTime
     *
     * @Extbase\Validate("NotEmpty")
     * @var \DateTime
     */
    protected $startDateTime;

    /**
     * This is an Allday-Event (Time disabled)
     *
     * @var boolean
     */
    protected $allDay = false;

    /**
     * End Date of Event
     *
     * @var \DateTime
     */
    protected $endDateTime;

    /**
     * End Date of Subscription
     *
     * @var \DateTime
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
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Slub\SlubEvents\Domain\Model\TtContent>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     */
    protected $contentElements;

    /**
     * Fal media items
     *
     * @var \TYPO3\CMS\Extbase\Domain\Model\FileReference
     */
    protected $image;


    /**
     * Minimum of Subscribers
     *
     * @var int
     */
    protected $minSubscriber = 0;

    /**
     * Maximum of Subscribers
     *
     * @var int
     */
    protected $maxSubscriber = 0;

    /**
     * Maximum amount of Persons per Subscription
     *
     * @var int
     */
    protected $maxNumber = 0;

    /**
     * Target Audience
     *
     * @Extbase\Validate("NotEmpty")
     * @var int
     */
    protected $audience = 0;

    /**
     * Sent Information about SubEndTime reached
     *
     * @var boolean
     */
    protected $subEndDateInfoSent = false;

    /**
     * Should this Event been indexed by search engine e.g. solr
     *
     * @var boolean
     */
    protected $noSearch = false;

    /**
     * This is a genius bar event
     *
     * @var boolean
     */
    protected $geniusBar = false;

    /**
     * The event  has been canceld
     *
     * @var boolean
     */
    protected $cancelled = false;

    /**
     * Category Id
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Slub\SlubEvents\Domain\Model\Category>
     */
    protected $categories;

    /**
     * Category Stats ID
     *
     * @var \Slub\SlubEvents\Domain\Model\Category
     */
    protected $categoryStats;

    /**
     * Subscriber Ids
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Slub\SlubEvents\Domain\Model\Subscriber>
     * @Extbase\ORM\Lazy
     * @Extbase\ORM\Cascade("remove")
     */
    protected $subscribers;

    /**
     * Location Ids
     *
     * @var \Slub\SlubEvents\Domain\Model\Location
     */
    protected $location = null;

    /**
     * Discipline IDs
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Slub\SlubEvents\Domain\Model\Discipline>
     */
    protected $discipline;

    /**
     * Discipline Stats ID
     *
     * @var \Slub\SlubEvents\Domain\Model\Discipline
     */
    protected $disciplineStats;

    /**
     * Contact ID
     *
     * @var \Slub\SlubEvents\Domain\Model\Contact
     */
    protected $contact = null;

    /**
     * onlinesurvey
     *
     * @var string
     */
    protected $onlinesurvey;

    /**
     * external registration link
     *
     * @var string
     */
    protected $externalRegistration;

    /**
     * This is a recurring event
     *
     * @var boolean
     */
    protected $recurring = false;

    /**
     * The recurring options
     *
     * @var string
     */
    protected $recurringOptions;

    /**
     * The unsubscribe url
     *
     * @var string
     */
    protected $unsubscribeUrl;

    /**
     * The recurring end dateTime
     *
     * @var \DateTime
     */
    protected $recurringEndDateTime;

    /**
     * Event constructor.
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
        $this->categories = new ObjectStorage();

        $this->contentElements = new ObjectStorage();

        $this->discipline = new ObjectStorage();

        $this->subscribers = new ObjectStorage();
    }

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
     */
    public function setHidden( $hidden ) {
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
     */
    public function setTitle( $title ) {
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
     */
    public function setTeaser( $teaser ) {
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
     */
    public function setDescription( $description ) {
        $this->description = $description;
    }

    /**
     * Get content elements
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage
     */
    public function getContentElements() {
        return $this->contentElements;
    }

    /**
     * Set content element list
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage $contentElements content elements
     */
    public function setContentElements( $contentElements ) {
        $this->contentElements = $contentElements;
    }

    /**
     * Get id list of content elements
     *
     * @return string
     */
    public function getContentElementIdList() {
        return $this->getIdOfContentElements();
    }

    /**
     * Get translated id list of content elements
     *
     * @return string
     */
    public function getTranslatedContentElementIdList() {
        return $this->getIdOfContentElements( false );
    }

    /**
     * Collect id list
     *
     * @param bool $original
     *
     * @return string
     */
    protected function getIdOfContentElements( $original = true ) {
        $idList          = [];
        $contentElements = $this->getContentElements();
        if ( $contentElements ) {
            foreach ( $this->getContentElements() as $contentElement ) {
                if ( $contentElement->getColPos() >= 0 ) {
                    $idList[] = $original ? $contentElement->getUid() : $contentElement->_getProperty( '_localizedUid' );
                }
            }
        }

        return implode( ',', $idList );
    }

    /**
     * Returns the image
     *
     * @return \TYPO3\CMS\Extbase\Domain\Model\FileReference $image
     */
    public function getImage() {
        return $this->image;
    }

    /**
     * Sets the image
     *
     * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $image
     */
    public function setImage( $image ) {
        $this->image = $image;
    }

    /**
     * Returns the parent
     *
     * @return \Slub\SlubEvents\Domain\Model\Event $parent
     */
    public function getParent() {
        return $this->parent;
    }

    /**
     * Sets the parent
     *
     * @param \Slub\SlubEvents\Domain\Model\Event $parent
     *
     * @return void
     */
    public function setParent( \Slub\SlubEvents\Domain\Model\Event $parent ) {
        $this->parent = $parent;
    }

    /**
     * @return array $rootCategories
     */
    public function getRootCategories(): array
    {
        return $this->rootCategories;
    }

    /**
     * @param array $rootCategories
     */
    public function setRootCategories(array $rootCategories): void
    {
        $this->rootCategories = $rootCategories;
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
     *
     * @return void
     */
    public function setMinSubscriber( $minSubscriber ) {
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
     *
     * @return void
     */
    public function setMaxSubscriber( $maxSubscriber ) {
        $this->maxSubscriber = $maxSubscriber;
    }

    /**
     * Returns maxNumber
     *
     * @return integer $maxNumber
     */
    public function getMaxNumber() {
        return $this->maxNumber;
    }

    /**
     * Sets the maxSubscriber
     *
     * @param integer $maxNumber
     *
     * @return void
     */
    public function setMaxNumber( $maxNumber ) {
        $this->maxNumber = $maxNumber;
    }

    /**
     * Adds a Subscriber
     *
     * @param \Slub\SlubEvents\Domain\Model\Subscriber $subscriber
     *
     * @return void
     */
    public function addSubscriber( Subscriber $subscriber ) {
        $this->subscribers->attach( $subscriber );
    }

    /**
     * Removes a Subscriber
     *
     * @param \Slub\SlubEvents\Domain\Model\Subscriber $subscriberToRemove The Subscriber to be removed
     *
     * @return void
     */
    public function removeSubscriber( Subscriber $subscriberToRemove ) {
        $this->subscribers->detach( $subscriberToRemove );
    }

    /**
     * Returns the subscribers
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Slub\SlubEvents\Domain\Model\Subscriber> $subscribers
     */
    public function getSubscribers() {
        return $this->subscribers;
    }

    /**
     * Sets the subscribers
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Slub\SlubEvents\Domain\Model\Subscriber> $subscribers
     *
     * @return void
     */
    public function setSubscribers( ObjectStorage $subscribers ) {
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
     *
     * @return void
     */
    public function setAudience( $audience ) {
        $this->audience = $audience;
    }

    /**
     * Sets the discipline
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Slub\SlubEvents\Domain\Model\Discipline> $discipline
     *
     * @return void
     */
    public function setDiscipline( ObjectStorage $discipline ) {
        $this->discipline = $discipline;
    }

    /**
     * Returns the discipline
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Slub\SlubEvents\Domain\Model\Discipline> $discipline
     */
    public function getDiscipline() {
        return $this->discipline;
    }

    /**
     * Adds a discipline
     *
     * @param \Slub\SlubEvents\Domain\Model\Discipline $discipline
     *
     * @return void
     */
    public function addDiscipline( Discipline $discipline ) {
        $this->discipline->attach( $discipline );
    }

    /**
     * Removes a Discipline
     *
     * @param \Slub\SlubEvents\Domain\Model\Discipline $disciplineToRemove The Discipline to be removed
     *
     * @return void
     */
    public function removeDiscipline( Discipline $disciplineToRemove ) {
        $this->discipline->detach( $disciplineToRemove );
    }

    /**
     * Returns the location
     *
     * @return \Slub\SlubEvents\Domain\Model\Location $location
     */
    public function getLocation() {
        return $this->location;
    }

    /**
     * Sets the location
     *
     * @param \Slub\SlubEvents\Domain\Model\Location $location
     *
     * @return void
     */
    public function setLocation( Location $location ) {
        $this->location = $location;
    }

    /**
     * Returns the Event
     *
     * @return \Slub\SlubEvents\Domain\Model\Event $event
     */
    public function getEvent() {
        return $this->event;
    }

    /**
     * Returns the contact
     *
     * @return \Slub\SlubEvents\Domain\Model\Contact $contact
     */
    public function getContact() {
        return $this->contact;
    }

    /**
     * Sets the contact
     *
     * @param \Slub\SlubEvents\Domain\Model\Contact $contact
     *
     * @return void
     */
    public function setContact( Contact $contact ) {
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
     *
     * @return boolean allDay
     */
    public function setAllDay( $allDay ) {
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
     * @return \DateTime startDateTime
     */
    public function getStartDateTime() {
        return $this->startDateTime;
    }

    /**
     * Sets the startDateTime
     *
     * @param \DateTime $startDateTime
     */
    public function setStartDateTime( $startDateTime ) {
        $this->startDateTime = $startDateTime;
    }

    /**
     * Returns the endDateTime
     *
     * @return \DateTime endDateTime
     */
    public function getEndDateTime() {
        return $this->endDateTime;
    }

    /**
     * Sets the endDateTime
     *
     * @param \DateTime $endDateTime
     */
    public function setEndDateTime( $endDateTime ) {
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
     * @param \DateTime $subEndDateTime
     */
    public function setSubEndDateTime( $subEndDateTime ) {
        $this->subEndDateTime = $subEndDateTime;
    }

    /**
     * Returns the subEndDateInfoSent
     *
     * @return \DateTime subEndDateTime
     */
    public function getSubEndDateInfoSent() {
        return $this->subEndDateInfoSent;
    }

    /**
     * Sets the subEndDateInfoSent
     *
     * @param \DateTime $subEndDateInfoSent
     */
    public function setSubEndDateInfoSent( $subEndDateInfoSent ) {
        $this->subEndDateInfoSent = $subEndDateInfoSent;
    }

    /**
     * Returns the noSearch
     *
     * @return boolean $noSearch
     */
    public function getNoSearch() {
        return $this->noSearch;
    }

    /**
     * Sets the noSearch
     *
     * @param boolean $noSearch
     */
    public function setNoSearch( $noSearch ) {
        $this->noSearch = $noSearch;
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
     *
     * @return void
     */
    public function setGeniusBar( $geniusBar ) {
        $this->geniusBar = $geniusBar;
    }

    /**
     * Adds a category
     *
     * @param \Slub\SlubEvents\Domain\Model\Category $category
     *
     * @return void
     */
    public function addCategory( Category $category ) {
        $this->categories->attach( $category );
    }

    /**
     * Removes a category
     *
     * @param \Slub\SlubEvents\Domain\Model\Category $category
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Slub\SlubEvents\Domain\Model\Category> categories
     */
    public function removeCategory( Category $categoryToBeRemoved ) {
        $this->categories->detach( $categoryToBeRemoved );
    }

    /**
     * Returns the categories
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Slub\SlubEvents\Domain\Model\Category> $categories
     */
    public function getCategories() {
        return $this->categories;
    }

    /**
     * Sets the categories
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Slub\SlubEvents\Domain\Model\Category> $categories
     *
     * @return void
     */
    public function setCategories( ObjectStorage $categories ) {
        $this->categories = $categories;
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
     *
     * @return void
     */
    public function setCancelled( $cancelled ) {
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
     *
     * @return void
     */
    public function setOnlinesurvey( $onlinesurvey ) {
        $this->onlinesurvey = $onlinesurvey;
    }

    /**
     * Returns the external registration link
     *
     * @return string $externalRegistration
     */
    public function getExternalRegistration() {
        return $this->externalRegistration;
    }

    /**
     * Sets the external registration link
     *
     * @param string $externalRegistration
     *
     * @return void
     */
    public function setExternalRegistration( $externalRegistration ) {
        $this->externalRegistration = $externalRegistration;
    }


    /**
     * Returns the recurring value
     *
     * @return boolean $recurring
     */
    public function getRecurring() {
        return $this->recurring;
    }

    /**
     * Sets the recurring state
     *
     * @param boolean $recurring
     *
     * @return void
     */
    public function setRecurring( $recurring ) {
        $this->recurring = $recurring;
    }

    /**
     * Returns the boolean state of recurring
     *
     * @return boolean recurring
     */
    public function isRecurring() {
        return $this->getRecurring();
    }

    /**
     * Returns the recurring options
     *
     * @return array $recurringOptions
     */
    public function getRecurringOptions() {
        return unserialize( $this->recurringOptions );
    }

    /**
     * Sets the recurring options
     *
     * @param array $recurringOptions
     *
     * @return void
     */
    public function setRecurringOptions( $recurringOptions ) {
        $this->recurringOptions = serialize( $recurringOptions );
    }

    /**
     * Returns the recurring end dateTime
     *
     * @return \DateTime recurringEndDateTime
     */
    public function getRecurringEndDateTime() {
        return $this->recurringEndDateTime;
    }

    /**
     * Sets the recurring end dateTime
     *
     * @param \DateTime $recurringEndDateTime
     */
    public function setRecurringEndDateTime( $recurringEndDateTime ) {
        $this->recurringEndDateTime = $recurringEndDateTime;
    }

    /**
     * Returns the unsubscribe url value
     *
     * @return string $unsubscribeUrl
     */
    public function getUnsubscribeUrl()
    {
        return $this->unsubscribeUrl;
    }

    /**
     * Sets the unsubscribe url state
     *
     * @param string $unsubscribeUrl
     *
     * @return void
     */
    public function setUnsubscribeUrl($unsubscribeUrl)
    {
        $this->unsubscribeUrl = $unsubscribeUrl;
    }
  
    /**
     * Get CategoryStats
     *
     * @return \Slub\SlubEvents\Domain\Model\Category $categoryStats
     */
    public function getCategoryStats() {
        return $this->categoryStats;
    }

    /**
     * Set CategoryStats
     *
     * @return \Slub\SlubEvents\Domain\Model\Category $categoryStats
     */
    public function setCategoryStats( $categoryStats ) {
        $this->categoryStats = $categoryStats;
    }

    /**
     * Get DisciplineStats
     *
     * @return \Slub\SlubEvents\Domain\Model\Discipline $disciplineStats
     */
    public function getDisciplineStats() {
        return $this->disciplineStats;
    }

    /**
     * Set DisciplineStats
     *
     * @return \Slub\SlubEvents\Domain\Model\Discipline $disciplineStats
     */
    public function setDisciplineStats( $disciplineStats ) {
        $this->disciplineStats = $disciplineStats;
    }

}
