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

use Slub\SlubEvents\Domain\Model\Category;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class CategoryController extends AbstractController
{
    /**
     * @var \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController
     */
    protected $typoScriptFrontendController;

    /**
     * Initializes the current action
     *
     * idea from tx_news extension
     *
     * @return void
     */
    public function initializeAction()
    {

        // Only do this in Frontend Context
        if (!empty($GLOBALS['TSFE']) && is_object($GLOBALS['TSFE'])) {
            // We only want to set the tag once in one request, so we have to cache that statically if it has been done
            static $cacheTagsSet = false;

            /** @var $typoScriptFrontendController \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController */
            $typoScriptFrontendController = $GLOBALS['TSFE'];
            if (!$cacheTagsSet) {
                $typoScriptFrontendController->addCacheTags(
                    [0 => 'tx_slubevents_cat_' . $this->settings['persistence']['storagePid']]
                );
                $cacheTagsSet = true;
            }
            $this->typoScriptFrontendController = $typoScriptFrontendController;
        }
    }

    /**
     * action list
     *
     * @param Category $category
     *
     * @ignorevalidation
     * @return void
     */
    public function listAction(Category $category = null)
    {
        // take the root category of the flexform
        $category = $this->categoryRepository->findAllByUids(
            GeneralUtility::intExplode(',', $this->settings['categorySelection'], true)
        )->getFirst();

        $categories = $this->categoryRepository->findCurrentBranch($category);

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
     *
     * @ignorevalidation
     * @return void
     */
    public function contactListAction(\Slub\SlubEvents\Domain\Model\Category $category = null)
    {
        if (!($this->settings['contactSelection'] > 0)) {
            $this->view->assign('contactSelectionWarning', 1);
        } else {
            $this->view->assign('contacts', $this->contactRepository->findById($this->settings['contactSelection']));
            if ($this->settings['showWiba']) {
                $wibas = $this->eventRepository->findWibaByContact($this->settings['contactSelection'], 0);
            }
            if ($this->settings['showEvent']) {
                $events = $this->eventRepository->findEventByContact($this->settings['contactSelection'], 0);
            }
            if ($this->settings['showConsultation'] && $this->settings['consultationSelection'] > 0) {
                $consultation = $this->eventRepository->findWibaByContact($this->settings['contactSelection'], $this->settings['consultationSelection']);
            }
        }


        #if ($category != NULL) {
        #	$events = $this->eventRepository->findAllGbByCategory($category);
        #}

        // get Default Category
        if (is_null($category)) {
            $category = $this->categoryRepository->findDefaultGeniusbarCategory();
        }

        $this->view->assign('category', $category);

        $this->view->assign('wibas', $wibas);
        $this->view->assign('events', $events);
        $this->view->assign('consultation', $consultation);
        $this->view->assign('showWiba', $this->settings['showWiba']);
        $this->view->assign('showEvent', $this->settings['showEvent']);
        $this->view->assign('showConsultation', $this->settings['showConsultation']);
    }

    /**
     * action gbList
     *
     * List of genius bar events with category description, contact photo and calendar link
     *
     * @param Category $category
     *
     * @ignorevalidation
     * @return void
     */
    public function gbListAction(Category $category = null)
    {
        $events = [];
        if ($category != null) {
            $events = $this->eventRepository->findAllGbByCategory($category);
        }

        $this->view->assign('events', $events);
        $this->view->assign('category', $category);
        $this->view->assign('parentcategory', $category->getParent()->current());
    }

    /**
     * action show
     *
     * @param Category $category
     *
     * @return void
     */
    public function showAction(Category $category)
    {
        $this->view->assign('category', $category);
    }
}
