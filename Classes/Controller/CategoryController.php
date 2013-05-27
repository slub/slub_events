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
class Tx_SlubEvents_Controller_CategoryController extends Tx_SlubEvents_Controller_AbstractController {

	/**
	 * categoryRepository
	 *
	 * @var Tx_SlubEvents_Domain_Repository_CategoryRepository
	 */
	protected $categoryRepository;

	/**
	 * action list
	 *
	 * @param Tx_SlubEvents_Domain_Model_Category $category
	 * @dontvalidate
	 * @return void
	 */
	public function listAction(Tx_SlubEvents_Domain_Model_Category $category = NULL) {
			
				//~ if ($category != NULL) {
//~ 
					//~ // get rootline
					//~ $rootline = $category;
					//~ $categoriesRootline[] = $category;
					//~ while ($rootline->getParent()->current()) {
						//~ $rootline = $rootline->getParent()->current();
						//~ $categoriesRootline[] = $rootline;
					//~ }
//~ 
				//~ } else {
					// take the flexform settings by default
					$category = $this->categoryRepository->findAllByUids(t3lib_div::intExplode(',', $this->settings['categorySelection'], TRUE))->getFirst();
					$categoriesRootline[] = $category;
				//~ }

				$categories = $this->categoryRepository->findCurrentBranch($category);
				//~ $categories = $this->categoryRepository->findAllTree();
//~ print_r("anzahl der categorien". count($categories));
//~ print_r($categories);
				if (count($categories) == 0) {
					// there are no further child categories --> show events

					$this->forward('gbList');
				} else {
					$this->view->assign('categoriesRootline', $categoriesRootline);
					$this->view->assign('category', $category);
					//~ $this->view->assign('parentcategory', $category->getParent()->current());
					$this->view->assign('categories', $categories);

				}
	}

	/**
	 * injectCategoryRepository
	 *
	 * @param Tx_SlubEvents_Domain_Repository_CategoryRepository $categoryRepository
	 * @return void
	 */
	public function injectCategoryRepository(Tx_SlubEvents_Domain_Repository_CategoryRepository $categoryRepository) {
		$this->categoryRepository = $categoryRepository;
	}

	/**
	 * action gbList
	 * 
	 * List of genius bar events with category description, contact photo and calendar link
	 *
	 * @param Tx_SlubEvents_Domain_Model_Category $category
	 * @dontvalidate
	 * @return void
	 */
	public function gbListAction(Tx_SlubEvents_Domain_Model_Category $category = NULL) {
		
				if ($category != NULL) {
					$events = $this->eventRepository->findAllGbByCategory($category);
		
							// get rootline
							//~ $rootline = $category;
							//~ $categoriesRootline[] = $category;
							//~ while ($rootline->getParent()->current()) {
								//~ $rootline = $rootline->getParent()->current();
								//~ $categoriesRootline[] = $rootline;
							//~ }
		
				}
		
				$this->view->assign('events', $events);
				$this->view->assign('category', $category);
				//~ $this->view->assign('categoriesRootline', $categoriesRootline);
				$this->view->assign('parentcategory', $category->getParent()->current());
	}

	/**
	 * action show
	 *
	 * @param Tx_SlubEvents_Domain_Model_Category $category
	 * @return void
	 */
	public function showAction(Tx_SlubEvents_Domain_Model_Category $category) {
		$this->view->assign('category', $category);
	}

}

?>