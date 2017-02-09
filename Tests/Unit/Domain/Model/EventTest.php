<?php

namespace Slub\SlubEvents\Tests\Unit\Domain\Model;

use Slub\SlubEvents\Domain\Model\Contact;
use Slub\SlubEvents\Domain\Model\Discipline;
use Slub\SlubEvents\Domain\Model\Location;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

use Slub\SlubEvents\Domain\Model\Event;
use Slub\SlubEvents\Domain\Model\Category;
use Slub\SlubEvents\Domain\Model\Subscriber;

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
 * Test case for Event.
 *
 * @version    $Id$
 * @copyright  Copyright belongs to the respective authors
 * @license    http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 * @package    TYPO3
 * @subpackage SLUB: Event Registration
 *
 * @author     Alexander Bigga <alexander.bigga@slub-dresden.de>
 */
class EventTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * @var Event
     */
    protected $subject;

    public function setUp()
    {
        $this->subject = new Event();
    }

    public function tearDown()
    {
        unset($this->subject);
    }

    /**
     * @test
     */
    public function getTitleInitiallyReturnsNull()
    {
        self::assertSame(
            null,
            $this->subject->getTitle()
        );
    }

    /**
     * @test
     */
    public function setTitleForStringSetsTitle()
    {
        $this->subject->setTitle('Conceived at T3CON10');

        self::assertSame(
            'Conceived at T3CON10',
            $this->subject->getTitle()
        );
    }

    /**
     * @test
     */
    public function getStartDateTimeInitiallyReturnsNull()
    {
        self::assertSame(
            null,
            $this->subject->getStartDateTime()
        );
    }

    /**
     * @test
     */
    public function setStartDateTimeForDateTimeSetsStartDateTime()
    {
        $now = new \DateTime('2016-05-13');
        $this->subject->setStartDateTime($now);

        self::assertSame(
            $now,
            $this->subject->getStartDateTime()
        );
    }

    /**
     * @test
     */
    public function getAllDayReturnsInitialValueForBoolean()
    {
        self::assertSame(
            false,
            $this->subject->getAllDay()
        );
    }

    /**
     * @test
     */
    public function setAllDayForBooleanSetsAllDay()
    {
        $this->subject->setAllDay(true);

        self::assertSame(
            true,
            $this->subject->getAllDay()
        );
    }

    /**
     * @test
     */
    public function getEndDateTimeInitiallyReturnsNull()
    {
        self::assertSame(
            null,
            $this->subject->getEndDateTime()
        );
    }

    /**
     * @test
     */
    public function setEndDateTimeForDateTimeSetsEndDateTime()
    {
        $now = new \DateTime('2016-05-13');
        $this->subject->setEndDateTime($now);

        self::assertSame(
            $now,
            $this->subject->getEndDateTime()
        );

    }

    /**
     * @test
     */
    public function getSubEndDateTimeInitiallyReturnsNull()
    {
        self::assertSame(
            null,
            $this->subject->getSubEndDateTime()
        );
    }

    /**
     * @test
     */
    public function setSubEndDateTimeForDateTimeSetsSubEndDateTime()
    {
        $now = new \DateTime('2016-05-13');
        $this->subject->setSubEndDateTime($now);

        self::assertSame(
            $now,
            $this->subject->getSubEndDateTime()
        );
    }

    /**
     * @test
     */
    public function getTeaserInitiallyReturnsNull()
    {
        self::assertSame(
            null,
            $this->subject->getTeaser()
        );
    }

    /**
     * @test
     */
    public function setTeaserForStringSetsTeaser()
    {
        $this->subject->setTeaser('Conceived at T3CON10');

        self::assertSame(
            'Conceived at T3CON10',
            $this->subject->getTeaser()
        );
    }

    /**
     * @test
     */
    public function getDescriptionReturnsInitialValueForString()
    {
    }

    /**
     * @test
     */
    public function setDescriptionForStringSetsDescription()
    {
        $this->subject->setDescription('Conceived at T3CON10');

        self::assertSame(
            'Conceived at T3CON10',
            $this->subject->getDescription()
        );
    }

    /**
     * @test
     */
    public function getMinSubscriberReturnsInitialValueForInteger()
    {
        self::assertSame(
            0,
            $this->subject->getMinSubscriber()
        );
    }

    /**
     * @test
     */
    public function setMinSubscriberForIntegerSetsMinSubscriber()
    {
        self::assertSame(
            0,
            $this->subject->getMinSubscriber()
        );
    }

    /**
     * @test
     */
    public function getMaxSubscriberReturnsInitialValueForInteger()
    {
        self::assertSame(
            0,
            $this->subject->getMaxSubscriber()
        );
    }

    /**
     * @test
     */
    public function setMaxSubscriberForIntegerSetsMaxSubscriber()
    {
        $this->subject->setMaxSubscriber(12);

        self::assertSame(
            12,
            $this->subject->getMaxSubscriber()
        );
    }

    /**
     * @test
     */
    public function getAudienceReturnsInitialValueForInteger()
    {
        self::assertSame(
            0,
            $this->subject->getAudience()
        );
    }

    /**
     * @test
     */
    public function setAudienceForIntegerSetsAudience()
    {
        $this->subject->setAudience(12);

        self::assertSame(
            12,
            $this->subject->getAudience()
        );
    }

    /**
     * @test
     */
    public function getSubEndDateInfoSentReturnsInitialValueForBoolean()
    {
        self::assertSame(
            false,
            $this->subject->getSubEndDateInfoSent()
        );
    }

    /**
     * @test
     */
    public function setSubEndDateInfoSentForBooleanSetsSubEndDateInfoSent()
    {
        $this->subject->setSubEndDateInfoSent(true);

        self::assertSame(
            true,
            $this->subject->getSubEndDateInfoSent()
        );
    }

    /**
     * @test
     */
    public function getGeniusBarReturnsInitialValueForBoolean()
    {
        self::assertSame(
            false,
            $this->subject->getGeniusBar()
        );
    }

    /**
     * @test
     */
    public function setGeniusBarForBooleanSetsGeniusBar()
    {
        $this->subject->setGeniusBar(true);

        self::assertSame(
            true,
            $this->subject->getGeniusBar()
        );
    }

    /**
     * @test
     */
    public function getCancelledReturnsInitialValueForBoolean()
    {
        self::assertSame(
            false,
            $this->subject->getCancelled()
        );
    }

    /**
     * @test
     */
    public function setCancelledForBooleanSetsCancelled()
    {
        $this->subject->setCancelled(true);

        self::assertSame(
            true,
            $this->subject->getCancelled()
        );
    }

    /**
     * @test
     */
    public function getCategoriesReturnsInitialValueForObjectStorageContainingCategory()
    {
        $newObjectStorage = new ObjectStorage();
        self::assertEquals(
            $newObjectStorage,
            $this->subject->getCategories()
        );
    }

    /**
     * @test
     */
    public function setCategoriesForObjectStorageContainingCategorySetsCategories()
    {
        $category = new Category();
        $objectStorageHoldingExactlyOneCategories = new ObjectStorage();
        $objectStorageHoldingExactlyOneCategories->attach($category);
        $this->subject->setCategories($objectStorageHoldingExactlyOneCategories);

        self::assertSame(
            $objectStorageHoldingExactlyOneCategories,
            $this->subject->getCategories()
        );
    }

    /**
     * @test
     */
    public function addCategoryToObjectStorageHoldingCategories()
    {
        $category = new Category();
        $objectStorageHoldingExactlyOneCategory = new ObjectStorage();
        $objectStorageHoldingExactlyOneCategory->attach($category);
        $this->subject->addCategory($category);

        self::assertEquals(
            $objectStorageHoldingExactlyOneCategory,
            $this->subject->getCategories()
        );
    }

    /**
     * @test
     */
    public function removeCategoryFromObjectStorageHoldingCategories()
    {
        $category = new Category();
        $localObjectStorage = new ObjectStorage();
        $localObjectStorage->attach($category);
        $localObjectStorage->detach($category);
        $this->subject->addCategory($category);
        $this->subject->removeCategory($category);

        self::assertEquals(
            $localObjectStorage,
            $this->subject->getCategories()
        );
    }

    /**
     * @test
     */
    public function getSubscribersReturnsInitialValueForObjectStorageContainingSubscriber()
    {
        $newObjectStorage = new ObjectStorage();
        self::assertEquals(
            $newObjectStorage,
            $this->subject->getSubscribers()
        );
    }

    /**
     * @test
     */
    public function setSubscribersForObjectStorageContainingSubscriberSetsSubscribers()
    {
        $subscriber = new Subscriber();
        $objectStorageHoldingExactlyOneSubscribers = new ObjectStorage();
        $objectStorageHoldingExactlyOneSubscribers->attach($subscriber);
        $this->subject->setSubscribers($objectStorageHoldingExactlyOneSubscribers);

        self::assertSame(
            $objectStorageHoldingExactlyOneSubscribers,
            $this->subject->getSubscribers()
        );
    }

    /**
     * @test
     */
    public function addSubscriberToObjectStorageHoldingSubscribers()
    {
        $subscriber = new Subscriber();
        $objectStorageHoldingExactlyOneSubscriber = new ObjectStorage();
        $objectStorageHoldingExactlyOneSubscriber->attach($subscriber);
        $this->subject->addSubscriber($subscriber);

        self::assertEquals(
            $objectStorageHoldingExactlyOneSubscriber,
            $this->subject->getSubscribers()
        );
    }

    /**
     * @test
     */
    public function removeSubscriberFromObjectStorageHoldingSubscribers()
    {
        $subscriber = new Subscriber();
        $localObjectStorage = new ObjectStorage();
        $localObjectStorage->attach($subscriber);
        $localObjectStorage->detach($subscriber);
        $this->subject->addSubscriber($subscriber);
        $this->subject->removeSubscriber($subscriber);

        self::assertEquals(
            $localObjectStorage,
            $this->subject->getSubscribers()
        );
    }

    /**
     * @test
     */
    public function getLocationReturnsInitialValueForLocation()
    {
        self::assertEquals(
            NULL,
            $this->subject->getLocation()
        );
    }

    /**
     * @test
     */
    public function setLocationForLocationSetsLocation()
    {
        $location = new Location();
        $this->subject->setLocation($location);

        self::assertSame(
            $location,
            $this->subject->getLocation()
        );
    }

    /**
     * @test
     */
    public function getDisciplineReturnsInitialValueForDiscipline()
    {
        $newObjectStorage = new ObjectStorage();
        self::assertEquals(
            $newObjectStorage,
            $this->subject->getDiscipline()
        );
    }

    /**
     * @test
     */
    public function setDisciplineForDisciplineSetsDiscipline()
    {
        $discipline = new Discipline();
        $objectStorageHoldingExactlyOneDiscipline = new ObjectStorage();
        $objectStorageHoldingExactlyOneDiscipline->attach($discipline);
        $this->subject->setDiscipline($objectStorageHoldingExactlyOneDiscipline);

        self::assertSame(
            $objectStorageHoldingExactlyOneDiscipline,
            $this->subject->getDiscipline()
        );
    }

    /**
     * @test
     */
    public function addDisciplineToObjectStorageHoldingDiscipline()
    {
        $discipline = new Discipline();
        $objectStorageHoldingExactlyOneDiscipline = new ObjectStorage();
        $objectStorageHoldingExactlyOneDiscipline->attach($discipline);
        $this->subject->addDiscipline($discipline);

        self::assertEquals(
            $objectStorageHoldingExactlyOneDiscipline,
            $this->subject->getDiscipline()
        );
    }

    /**
     * @test
     */
    public function removeDisciplineFromObjectStorageHoldingDiscipline()
    {
        $discipline = new Discipline();
        $localObjectStorage = new ObjectStorage();
        $localObjectStorage->attach($discipline);
        $localObjectStorage->detach($discipline);
        $this->subject->addDiscipline($discipline);
        $this->subject->removeDiscipline($discipline);

        self::assertEquals(
            $localObjectStorage,
            $this->subject->getDiscipline()
        );
    }

    /**
     * @test
     */
    public function getContactReturnsInitialValueForContact()
    {
        self::assertEquals(
            NULL,
            $this->subject->getContact()
        );
    }

    /**
     * @test
     */
    public function setContactForContactSetsContact()
    {
        $contact = new Contact();
        $this->subject->setContact($contact);

        self::assertSame(
            $contact,
            $this->subject->getContact()
        );
    }
}
