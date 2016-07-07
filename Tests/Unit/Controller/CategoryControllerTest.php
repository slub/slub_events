<?php

namespace Slub\SlubEvents\Tests\Unit\Domain\Model;

use Slub\SlubEvents\Domain\Model\Category;
use Slub\SlubEvents\Controller\CategoryController;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

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
 * Test case for class CategoryController.
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
class CategoryControllerTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * @var Category
     */
    protected $subject = null;

    /**
     * categoryRepository
     *
     * @var \Slub\SlubEvents\Domain\Repository\CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @var ViewInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $view = null;

    public function setUp()
    {
        $this->subject = $this->getMock('Slub\\SlubEvents\\Controller\\CategoryController', array('redirect', 'forward', 'addFlashMessage'), array(), '', FALSE);

        $this->categoryRepository = $this->getMock('Slub\\SlubEvents\\Domain\\Repository\\CategoryRepository', array(), array(), '', FALSE);
        $this->inject($this->subject, 'categoryRepository', $this->categoryRepository);

        $this->view = $this->getMock('TYPO3\\CMS\\Extbase\\Mvc\\View\\ViewInterface');
        $this->inject($this->subject, 'view', $this->view);

    }

    public function tearDown()
    {
        unset($this->subject);
    }

    /**
     * @test
     */
    public function showActionAssignsTheGivenCategoryToView() {
        $category = new Category();

        $view = $this->getMock('TYPO3\\CMS\\Extbase\\Mvc\\View\\ViewInterface');
        $this->inject($this->subject, 'view', $view);
        $view->expects($this->once())->method('assign')->with('category', $category);

        $this->subject->showAction($category);
    }

    /**
     * @test
     */
    public function listActionPassOneCategoryAsCategorytreeToView()
    {
        $mockedQueryResult = $this->getMock('TYPO3\\CMS\\Extbase\\Persistence\\QueryResultInterface');

        $allCategories = array();

        $settings = array('categorySelection' => '1');

        $this->inject($this->subject, 'settings', $settings);

        $categoryRepository = $this->getMock('Slub\\SlubEvents\\Domain\\Repository\\CategoryRepository', array('findCurrentBranch', 'findAllByUids'), array(), '', FALSE);
        $categoryRepository->expects($this->once())->method('findAllByUids')
            ->will($this->returnValue($mockedQueryResult));
        $categoryRepository->expects($this->once())->method('findCurrentBranch')
            ->will($this->returnValue($allCategories));
        $this->inject($this->subject, 'categoryRepository', $categoryRepository);

        $this->subject->listAction();
    }
    /**
     * @test
     */
    public function dummyMethod()
    {
        $this->markTestIncomplete();
    }
}
