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
 *  the Free Software Foundation; either version 2 of the License, or
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
 * Test case for class Tx_SlubEvents_Domain_Model_Event.
 *
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 * @package TYPO3
 * @subpackage SLUB: Event Registration
 *
 * @author Alexander Bigga <alexander.bigga@slub-dresden.de>
 */
class Tx_SlubEvents_Domain_Model_EventTest extends Tx_Extbase_Tests_Unit_BaseTestCase {
	/**
	 * @var Tx_SlubEvents_Domain_Model_Event
	 */
	protected $fixture;

	public function setUp() {
		$this->fixture = new Tx_SlubEvents_Domain_Model_Event();
	}

	public function tearDown() {
		unset($this->fixture);
	}

	/**
	 * @test
	 */
	public function getTitleReturnsInitialValueForString() { }

	/**
	 * @test
	 */
	public function setTitleForStringSetsTitle() { 
		$this->fixture->setTitle('Conceived at T3CON10');

		$this->assertSame(
			'Conceived at T3CON10',
			$this->fixture->getTitle()
		);
	}
	
	/**
	 * @test
	 */
	public function getStartDateTimeReturnsInitialValueForDateTime() { }

	/**
	 * @test
	 */
	public function setStartDateTimeForDateTimeSetsStartDateTime() { }
	
	/**
	 * @test
	 */
	public function getAllDayReturnsInitialValueForBoolean() { 
		$this->assertSame(
			TRUE,
			$this->fixture->getAllDay()
		);
	}

	/**
	 * @test
	 */
	public function setAllDayForBooleanSetsAllDay() { 
		$this->fixture->setAllDay(TRUE);

		$this->assertSame(
			TRUE,
			$this->fixture->getAllDay()
		);
	}
	
	/**
	 * @test
	 */
	public function getEndDateTimeReturnsInitialValueForDateTime() { }

	/**
	 * @test
	 */
	public function setEndDateTimeForDateTimeSetsEndDateTime() { }
	
	/**
	 * @test
	 */
	public function getSubEndDateTimeReturnsInitialValueForDateTime() { }

	/**
	 * @test
	 */
	public function setSubEndDateTimeForDateTimeSetsSubEndDateTime() { }
	
	/**
	 * @test
	 */
	public function getTeaserReturnsInitialValueForString() { }

	/**
	 * @test
	 */
	public function setTeaserForStringSetsTeaser() { 
		$this->fixture->setTeaser('Conceived at T3CON10');

		$this->assertSame(
			'Conceived at T3CON10',
			$this->fixture->getTeaser()
		);
	}
	
	/**
	 * @test
	 */
	public function getDescriptionReturnsInitialValueForString() { }

	/**
	 * @test
	 */
	public function setDescriptionForStringSetsDescription() { 
		$this->fixture->setDescription('Conceived at T3CON10');

		$this->assertSame(
			'Conceived at T3CON10',
			$this->fixture->getDescription()
		);
	}
	
	/**
	 * @test
	 */
	public function getMinSubscriberReturnsInitialValueForInteger() { 
		$this->assertSame(
			0,
			$this->fixture->getMinSubscriber()
		);
	}

	/**
	 * @test
	 */
	public function setMinSubscriberForIntegerSetsMinSubscriber() { 
		$this->fixture->setMinSubscriber(12);

		$this->assertSame(
			12,
			$this->fixture->getMinSubscriber()
		);
	}
	
	/**
	 * @test
	 */
	public function getMaxSubscriberReturnsInitialValueForInteger() { 
		$this->assertSame(
			0,
			$this->fixture->getMaxSubscriber()
		);
	}

	/**
	 * @test
	 */
	public function setMaxSubscriberForIntegerSetsMaxSubscriber() { 
		$this->fixture->setMaxSubscriber(12);

		$this->assertSame(
			12,
			$this->fixture->getMaxSubscriber()
		);
	}
	
	/**
	 * @test
	 */
	public function getAudienceReturnsInitialValueForInteger() { 
		$this->assertSame(
			0,
			$this->fixture->getAudience()
		);
	}

	/**
	 * @test
	 */
	public function setAudienceForIntegerSetsAudience() { 
		$this->fixture->setAudience(12);

		$this->assertSame(
			12,
			$this->fixture->getAudience()
		);
	}
	
	/**
	 * @test
	 */
	public function getSubEndDateInfoSentReturnsInitialValueForBoolean() { 
		$this->assertSame(
			TRUE,
			$this->fixture->getSubEndDateInfoSent()
		);
	}

	/**
	 * @test
	 */
	public function setSubEndDateInfoSentForBooleanSetsSubEndDateInfoSent() { 
		$this->fixture->setSubEndDateInfoSent(TRUE);

		$this->assertSame(
			TRUE,
			$this->fixture->getSubEndDateInfoSent()
		);
	}
	
	/**
	 * @test
	 */
	public function getGeniusBarReturnsInitialValueForBoolean() { 
		$this->assertSame(
			TRUE,
			$this->fixture->getGeniusBar()
		);
	}

	/**
	 * @test
	 */
	public function setGeniusBarForBooleanSetsGeniusBar() { 
		$this->fixture->setGeniusBar(TRUE);

		$this->assertSame(
			TRUE,
			$this->fixture->getGeniusBar()
		);
	}
	
	/**
	 * @test
	 */
	public function getCancelledReturnsInitialValueForBoolean() { 
		$this->assertSame(
			TRUE,
			$this->fixture->getCancelled()
		);
	}

	/**
	 * @test
	 */
	public function setCancelledForBooleanSetsCancelled() { 
		$this->fixture->setCancelled(TRUE);

		$this->assertSame(
			TRUE,
			$this->fixture->getCancelled()
		);
	}
	
	/**
	 * @test
	 */
	public function getCategoriesReturnsInitialValueForObjectStorageContainingTx_SlubEvents_Domain_Model_Category() { 
		$newObjectStorage = new Tx_Extbase_Persistence_ObjectStorage();
		$this->assertEquals(
			$newObjectStorage,
			$this->fixture->getCategories()
		);
	}

	/**
	 * @test
	 */
	public function setCategoriesForObjectStorageContainingTx_SlubEvents_Domain_Model_CategorySetsCategories() { 
		$category = new Tx_SlubEvents_Domain_Model_Category();
		$objectStorageHoldingExactlyOneCategories = new Tx_Extbase_Persistence_ObjectStorage();
		$objectStorageHoldingExactlyOneCategories->attach($category);
		$this->fixture->setCategories($objectStorageHoldingExactlyOneCategories);

		$this->assertSame(
			$objectStorageHoldingExactlyOneCategories,
			$this->fixture->getCategories()
		);
	}
	
	/**
	 * @test
	 */
	public function addCategoryToObjectStorageHoldingCategories() {
		$category = new Tx_SlubEvents_Domain_Model_Category();
		$objectStorageHoldingExactlyOneCategory = new Tx_Extbase_Persistence_ObjectStorage();
		$objectStorageHoldingExactlyOneCategory->attach($category);
		$this->fixture->addCategory($category);

		$this->assertEquals(
			$objectStorageHoldingExactlyOneCategory,
			$this->fixture->getCategories()
		);
	}

	/**
	 * @test
	 */
	public function removeCategoryFromObjectStorageHoldingCategories() {
		$category = new Tx_SlubEvents_Domain_Model_Category();
		$localObjectStorage = new Tx_Extbase_Persistence_ObjectStorage();
		$localObjectStorage->attach($category);
		$localObjectStorage->detach($category);
		$this->fixture->addCategory($category);
		$this->fixture->removeCategory($category);

		$this->assertEquals(
			$localObjectStorage,
			$this->fixture->getCategories()
		);
	}
	
	/**
	 * @test
	 */
	public function getSubscribersReturnsInitialValueForObjectStorageContainingTx_SlubEvents_Domain_Model_Subscriber() { 
		$newObjectStorage = new Tx_Extbase_Persistence_ObjectStorage();
		$this->assertEquals(
			$newObjectStorage,
			$this->fixture->getSubscribers()
		);
	}

	/**
	 * @test
	 */
	public function setSubscribersForObjectStorageContainingTx_SlubEvents_Domain_Model_SubscriberSetsSubscribers() { 
		$subscriber = new Tx_SlubEvents_Domain_Model_Subscriber();
		$objectStorageHoldingExactlyOneSubscribers = new Tx_Extbase_Persistence_ObjectStorage();
		$objectStorageHoldingExactlyOneSubscribers->attach($subscriber);
		$this->fixture->setSubscribers($objectStorageHoldingExactlyOneSubscribers);

		$this->assertSame(
			$objectStorageHoldingExactlyOneSubscribers,
			$this->fixture->getSubscribers()
		);
	}
	
	/**
	 * @test
	 */
	public function addSubscriberToObjectStorageHoldingSubscribers() {
		$subscriber = new Tx_SlubEvents_Domain_Model_Subscriber();
		$objectStorageHoldingExactlyOneSubscriber = new Tx_Extbase_Persistence_ObjectStorage();
		$objectStorageHoldingExactlyOneSubscriber->attach($subscriber);
		$this->fixture->addSubscriber($subscriber);

		$this->assertEquals(
			$objectStorageHoldingExactlyOneSubscriber,
			$this->fixture->getSubscribers()
		);
	}

	/**
	 * @test
	 */
	public function removeSubscriberFromObjectStorageHoldingSubscribers() {
		$subscriber = new Tx_SlubEvents_Domain_Model_Subscriber();
		$localObjectStorage = new Tx_Extbase_Persistence_ObjectStorage();
		$localObjectStorage->attach($subscriber);
		$localObjectStorage->detach($subscriber);
		$this->fixture->addSubscriber($subscriber);
		$this->fixture->removeSubscriber($subscriber);

		$this->assertEquals(
			$localObjectStorage,
			$this->fixture->getSubscribers()
		);
	}
	
	/**
	 * @test
	 */
	public function getLocationReturnsInitialValueForTx_SlubEvents_Domain_Model_Location() { }

	/**
	 * @test
	 */
	public function setLocationForTx_SlubEvents_Domain_Model_LocationSetsLocation() { }
	
	/**
	 * @test
	 */
	public function getDisciplineReturnsInitialValueForTx_SlubEvents_Domain_Model_Discipline() { }

	/**
	 * @test
	 */
	public function setDisciplineForTx_SlubEvents_Domain_Model_DisciplineSetsDiscipline() { }
	
	/**
	 * @test
	 */
	public function getContactReturnsInitialValueForTx_SlubEvents_Domain_Model_Contact() { }

	/**
	 * @test
	 */
	public function setContactForTx_SlubEvents_Domain_Model_ContactSetsContact() { }
	
}
?>