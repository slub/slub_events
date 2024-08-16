<?php

namespace Slub\SlubEvents\Service;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 3
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use Slub\SlubEvents\Domain\Model\Event;
use Slub\SlubEvents\Domain\Repository\EventRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * @package slub_events
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class EventService
{
    /**
     * @var CategoryService
     */
    protected $categoryService;

    /**
     * @var SubscriberService
     */
    protected $subscriberService;

    /**
     * @var EventRepository
     */
    protected $eventRepository;

    /**
     * EventService constructor.
     */
    public function __construct()
    {
        /** @var ObjectManager $objectManager */
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);

        $this->categoryService = $objectManager->get(CategoryService::class);
        $this->subscriberService = $objectManager->get(SubscriberService::class);
        $this->eventRepository = $objectManager->get(EventRepository::class);
    }

    /**
     * @param array $arguments
     * @return array
     */
    public function findAllBySettings(array $arguments): array
    {
        $events = $this->eventRepository->findAllBySettings($arguments)->toArray();

        return $this->addRootCategoriesToEvents($events);
    }

    /**
     * @param int $user
     * @param array $events
     * @param array $settings
     * @return array
     */
    public function prepareForUser(int $user, $events = [], $settings = []): array
    {
        if ($user === 0 || count($events) === 0) {
            return [];
        }

        return $this->subscriberService->addUnsubscribeUrl(
            $user,
            $events,
            (int)$settings['unsubscribePid']
        );
    }

    /**
     * @param array $events
     * @return array
     */
    protected function addRootCategoriesToEvents(array $events): array
    {
        $withRootCategory = [];

        if (count($events) > 0) {
            /** @var Event $event */
            foreach ($events as $event) {
                $withRootCategory[] = $this->addRootCategoriesToEvent($event);
            }
        }

        return $withRootCategory;
    }

    /**
     * @param Event $event
     * @return Event
     */
    protected function addRootCategoriesToEvent(Event $event): Event
    {
        $categories = $event->getCategories()->toArray();
        $rootCategories = $this->categoryService->getRoots($categories);

        $event->setRootCategories($rootCategories);

        return $event;
    }
}
