<?php

namespace Slub\SlubEvents\Tests\Unit\Domain\Model;

use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
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
 * Test case for class Tx_SlubEvents_Domain_Model_Subscriber.
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
class SubscriberTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * @var Subscriber
     */
    protected $subject;

    public function setUp()
    {
        $this->subject = new Subscriber();
    }

    public function tearDown()
    {
        unset($this->subject);
    }

    /**
     * @test
     */
    public function getNameReturnsInitialValueForString()
    {
    }

    /**
     * @test
     */
    public function setNameForStringSetsName()
    {
        $this->subject->setName('Conceived at T3CON10');

        $this->assertSame(
            'Conceived at T3CON10',
            $this->subject->getName()
        );
    }

    /**
     * @test
     */
    public function getEmailReturnsInitialValueForString()
    {
    }

    /**
     * @test
     */
    public function setEmailForStringSetsEmail()
    {
        $this->subject->setEmail('Conceived at T3CON10');

        $this->assertSame(
            'Conceived at T3CON10',
            $this->subject->getEmail()
        );
    }

    /**
     * @test
     */
    public function getTelephoneReturnsInitialValueForString()
    {
    }

    /**
     * @test
     */
    public function setTelephoneForStringSetsTelephone()
    {
        $this->subject->setTelephone('Conceived at T3CON10');

        $this->assertSame(
            'Conceived at T3CON10',
            $this->subject->getTelephone()
        );
    }

    /**
     * @test
     */
    public function getCustomeridReturnsInitialValueForString()
    {
    }

    /**
     * @test
     */
    public function setCustomeridForStringSetsCustomerid()
    {
        $this->subject->setCustomerid('Conceived at T3CON10');

        $this->assertSame(
            'Conceived at T3CON10',
            $this->subject->getCustomerid()
        );
    }

    /**
     * @test
     */
    public function getNumberReturnsInitialValueForInteger()
    {
        $this->assertSame(
            1,
            $this->subject->getNumber()
        );
    }

    /**
     * @test
     */
    public function setNumberForIntegerSetsNumber()
    {
        $this->subject->setNumber(12);

        $this->assertSame(
            12,
            $this->subject->getNumber()
        );
    }

    /**
     * @test
     */
    public function getMessageReturnsInitialValueForString()
    {
    }

    /**
     * @test
     */
    public function setMessageForStringSetsMessage()
    {
        $this->subject->setMessage('Conceived at T3CON10');

        $this->assertSame(
            'Conceived at T3CON10',
            $this->subject->getMessage()
        );
    }

    /**
     * @test
     */
    public function getEditcodeReturnsInitialValueForString()
    {
    }

    /**
     * @test
     */
    public function setEditcodeForStringSetsEditcode()
    {
        $this->subject->setEditcode('Conceived at T3CON10');

        $this->assertSame(
            'Conceived at T3CON10',
            $this->subject->getEditcode()
        );
    }

    /**
     * @test
     */
    public function getCrdateReturnsInitialValueForDateTime()
    {
    }

    /**
     * @test
     */
    public function setCrdateForDateTimeSetsCrdate()
    {
    }

    /**
     * @test
     */
    public function getEventReturnsInitialValueForEvent()
    {
    }

    /**
     * @test
     */
    public function setEventForEventSetsEvent()
    {
    }
}
