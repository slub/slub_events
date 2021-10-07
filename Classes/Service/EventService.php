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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * @package slub_events
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class EventService
{
    /**
     * @var SubscriberService
     */
    protected $subscriberService;

    /**
     * EventService constructor.
     */
    public function __construct()
    {
        /** @var ObjectManager $objectManager */
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);

        $this->subscriberService = $objectManager->get(SubscriberService::class);
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
            $settings['unsubscribePid']
        );
    }
}
