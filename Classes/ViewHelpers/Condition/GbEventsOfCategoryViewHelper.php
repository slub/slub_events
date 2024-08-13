<?php
namespace Slub\SlubEvents\ViewHelpers\Condition;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Alexander Bigga <typo3@slub-dresden.de>, SLUB Dresden
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

use \Slub\SlubEvents\Domain\Model\Category;
use \Slub\SlubEvents\Domain\Repository\CategoryRepository;

use \Slub\SlubEvents\Domain\Repository\EventRepository;
use \Slub\SlubEvents\Domain\Repository\SubscriberRepository;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

/**
 * Counts events of given category
 *
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 * @api
 */
class GbEventsOfCategoryViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    /**
     * Initialize arguments.
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('category', Category::class, 'Category', true);
    }
    /**
     * eventRepository
     *
     * @var EventRepository
     */
    protected static $eventRepository = null;

    /**
     * categoryRepository
     *
     * @var CategoryRepository
     */
    protected static $categoryRepository = null;

    /**
     * subscriberRepository
     *
     * @var SubscriberRepository
     */
    protected static $subscriberRepository = null;

    /**
     * check if any events of categories below are present and free for booking
     *
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        $category = $arguments['category'];
        $events = self::getEventRepository()->findAllGbByCategory($category);
        $categories = self::getCategoryRepository()->findCurrentBranch($category);

        $showLink = false;
        if (empty($categories) || empty($events)) {
            /** @var \Slub\SlubEvents\Domain\Model\Event $event */
            foreach ($events as $event) {
                $showLink = true;
                if (self::getSubscriberRepository()->countAllByEvent($event) >= $event->getMaxSubscriber()) {
                    $showLink = false;
                }
                // event is cancelled
                if ($event->getCancelled()) {
                    $showLink = false;
                }
                // deadline reached....
                if (is_object($event->getSubEndDateTime())) {
                    if ($event->getSubEndDateTime()->getTimestamp() < time()) {
                        $showLink = false;
                    }
                }
                // if any event exists and is valid, break here and return TRUE
                if ($showLink) {
                    break;
                }
            }
        }

        return $showLink;
    }

    /**
     * Initialize the eventRepository
     *
     * return eventRepository
     */
    private static function getEventRepository()
    {
        if (null === static::$eventRepository) {
            $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
            static::$eventRepository = $objectManager->get(eventRepository::class);
        }

        return static::$eventRepository;
    }

    /**
     * Initialize the categoryRepository
     *
     * return categoryRepository
     */
    private static function getCategoryRepository()
    {
        if (null === static::$categoryRepository) {
            $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
            static::$categoryRepository = $objectManager->get(categoryRepository::class);
        }

        return static::$categoryRepository;
    }

    /**
     * Initialize the subscriberRepository
     *
     * return subscriberRepository
     */
    private static function getSubscriberRepository()
    {
        if (null === static::$subscriberRepository) {
            $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
            static::$subscriberRepository = $objectManager->get(subscriberRepository::class);
        }

        return static::$subscriberRepository;
    }
}
