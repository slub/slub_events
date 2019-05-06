<?php

namespace Slub\SlubEvents\ViewHelpers\Condition;

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
 * Counts events of given category
 *
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 * @api
 */
class GbEventsOfCategoryViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
{
    /**
     * eventRepository
     *
     * @var \Slub\SlubEvents\Domain\Repository\EventRepository
     * @inject
     */
    protected $eventRepository;

    /**
     * categoryRepository
     *
     * @var \Slub\SlubEvents\Domain\Repository\CategoryRepository
     * @inject
     */
    protected $categoryRepository;

    /**
     * subscriberRepository
     *
     * @var \Slub\SlubEvents\Domain\Repository\SubscriberRepository
     * @inject
     */
    protected $subscriberRepository;

    /**
     * check if any events of categories below are present and free for booking
     *
     * @param \Slub\SlubEvents\Domain\Model\Category $category
     *
     * @return boolean
     * @api
     */
    public function render(\Slub\SlubEvents\Domain\Model\Category $category)
    {
        $events = $this->eventRepository->findAllGbByCategory($category);
        $categories = $this->categoryRepository->findCurrentBranch($category);

        $showLink = false;
        if (($categories && count($categories) == 0) || ($events && count($events) == 0)) {
            /** @var \Slub\SlubEvents\Domain\Model\Event $event */
            foreach ($events as $event) {
                $showLink = true;
                if ($this->subscriberRepository->countAllByEvent($event) >= $event->getMaxSubscriber()) {
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
}
