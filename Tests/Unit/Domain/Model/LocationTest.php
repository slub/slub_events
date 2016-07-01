<?php

namespace Slub\SlubEvents\Tests\Unit\Domain\Model;

use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use Slub\SlubEvents\Domain\Model\Location;

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
 * Test case for class Location.
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
class LocationTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * @var Location
     */
    protected $subject;

    public function setUp()
    {
        $this->subject = new Location();
    }

    public function tearDown()
    {
        unset($this->subject);
    }

    /**
     * @test
     */
    public function getNameInitiallyReturnsNull()
    {
        self::assertSame(
            null,
            $this->subject->getName()
        );
    }

    /**
     * @test
     */
    public function setNameForStringSetsName()
    {
        $this->subject->setName('Conceived at T3CON10');

        self::assertSame(
            'Conceived at T3CON10',
            $this->subject->getName()
        );
    }

    /**
     * @test
     */
    public function getDescriptionInitiallyReturnsNull()
    {
        self::assertSame(
            null,
            $this->subject->getDescription()
        );
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
    public function getLinkReturnsInitialValueForString()
    {
    }

    /**
     * @test
     */
    public function setLinkForStringSetsLink()
    {
        $this->subject->setLink('Conceived at T3CON10');

        self::assertSame(
            'Conceived at T3CON10',
            $this->subject->getLink()
        );
    }

    /**
     * @test
     */
    public function getParentReturnsInitialValueForObjectStorageContainingLocation()
    {
        $newObjectStorage = new ObjectStorage();
        self::assertEquals(
            $newObjectStorage,
            $this->subject->getParent()
        );
    }

    /**
     * @test
     */
    public function setParentForObjectStorageContainingLocationSetsParent()
    {
        $parent = new Location();
        $objectStorageHoldingExactlyOneParent = new ObjectStorage();
        $objectStorageHoldingExactlyOneParent->attach($parent);
        $this->subject->setParent($objectStorageHoldingExactlyOneParent);

        self::assertSame(
            $objectStorageHoldingExactlyOneParent,
            $this->subject->getParent()
        );
    }

    /**
     * @test
     */
    public function addParentToObjectStorageHoldingParent()
    {
        $parent = new Location();
        $objectStorageHoldingExactlyOneParent = new ObjectStorage();
        $objectStorageHoldingExactlyOneParent->attach($parent);
        $this->subject->addParent($parent);

        self::assertEquals(
            $objectStorageHoldingExactlyOneParent,
            $this->subject->getParent()
        );
    }

    /**
     * @test
     */
    public function removeParentFromObjectStorageHoldingParent()
    {
        $parent = new Location();
        $localObjectStorage = new ObjectStorage();
        $localObjectStorage->attach($parent);
        $localObjectStorage->detach($parent);
        $this->subject->addParent($parent);
        $this->subject->removeParent($parent);

        self::assertEquals(
            $localObjectStorage,
            $this->subject->getParent()
        );
    }
}
