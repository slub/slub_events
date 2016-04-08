<?php
	namespace Slub\SlubEvents\Controller;

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
	use TYPO3\CMS\Core\Utility\GeneralUtility;

class CategoryController extends AbstractController {

	/**
	 * Initializes the current action
	 *
	 * idea from tx_news extension
	 *
	 * @return void
	 */
	public function initializeAction() {

		// Only do this in Frontend Context
		if (!empty($GLOBALS['TSFE']) && is_object($GLOBALS['TSFE'])) {
			// We only want to set the tag once in one request, so we have to cache that statically if it has been done
			static $cacheTagsSet = FALSE;

			/** @var $typoScriptFrontendController \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController  */
			$typoScriptFrontendController = $GLOBALS['TSFE'];
			if (!$cacheTagsSet) {
				$typoScriptFrontendController->addCacheTags(array(0 => 'tx_slubevents_cat_' . $this->settings['storagePid']));
				$cacheTagsSet = TRUE;
			}
			$this->typoScriptFrontendController = $typoScriptFrontendController;
		}
	}

	/**
	 * action list
	 *
	 * @param \Slub\SlubEvents\Domain\Model\Category $category
	 * @ignorevalidation
	 * @return void
	 */
	public function listAction(\Slub\SlubEvents\Domain\Model\Category $category = NULL) {

		// take the root category of the flexform
		$category = $this->categoryRepository->findAllByUids(GeneralUtility::intExplode(',', $this->settings['categorySelection'], TRUE))->getFirst();

		$categories = $this->categoryRepository->findCurrentBranch($category);
		//~ $categories = $this->categoryRepository->findCurrentLevel($category);

		if (count($categories) == 0) {
			// there are no further child categories --> show events
			$this->forward('gbList');
		} else {
			$this->view->assign('categories', $categories);
		}
	}

	/**
	 * action contactList
	 *
	 * List of genius bar events with category description, contact photo and calendar link
	 *
	 * @param \Slub\SlubEvents\Domain\Model\Category $category
	 * @ignorevalidation
	 * @return void
	 */
	public function contactListAction(\Slub\SlubEvents\Domain\Model\Category $category = NULL) {

// Array ( [hidePagination] => 0 [senderEmailAddress] => webmaster@slub-dresden.de [emailToContact] => Array ( [sendEmailOnMaximumReached] => 1 [sendEmailOnFreeAgain] => 1 [sendEmailOnEveryBooking] => 0 ) [list] => Array ( [paginate] => Array ( [itemsPerPage] => 10 [insertAbove] => TRUE [insertBelow] => TRUE [lessPages] => TRUE [forcedNumberOfLinks] => 5 [pagesBefore] => 3 [pagesAfter] => 3 ) ) [categorySelection] => 7 [pidListing] => 26 [pidDetails] => 28 [pidSubscribeForm] => 29 [categorySelectionRecursive] => 0 [disciplineSelection] => [disciplineSelectionRecursive] => 0 [showPastEvents] => 0 [contactsSelection] => [pidUnsubscribeForm] => [pidListOwn] => [pidUnSubscribeForm] => [testval] => ewqrwqr [contactSelection] => 1 [fullCalendarJS] => [storagePid] => 25 )


		if(!($this->settings['contactSelection'] > 0)) {
			$this->view->assign('contactSelectionWarning', 1);
		} else {
			$this->view->assign('ansprech', $this->settings['contactSelection']);
			$events = $this->eventRepository->findAllGbByContact($this->settings['contactSelection']);
		}


		#if ($category != NULL) {
		#	$events = $this->eventRepository->findAllGbByCategory($category);
		#}

		$this->view->assign('events', $events);
		#$this->view->assign('category', $category);
		#$this->view->assign('parentcategory', $category->getParent()->current());
	}

	/**
	 * action gbList
	 *
	 * List of genius bar events with category description, contact photo and calendar link
	 *
	 * @param \Slub\SlubEvents\Domain\Model\Category $category
	 * @ignorevalidation
	 * @return void
	 */
	public function gbListAction(\Slub\SlubEvents\Domain\Model\Category $category = NULL) {

		if ($category != NULL) {
			$events = $this->eventRepository->findAllGbByCategory($category);
		}

		$this->view->assign('events', $events);
		$this->view->assign('category', $category);
		$this->view->assign('parentcategory', $category->getParent()->current());
	}

	/**
	 * action show
	 *
	 * @param \Slub\SlubEvents\Domain\Model\Category $category
	 * @return void
	 */
	public function showAction(\Slub\SlubEvents\Domain\Model\Category $category) {
		$this->view->assign('category', $category);
	}

}

?>
