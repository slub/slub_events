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
use Slub\SlubEvents\Domain\Model\Subscriber;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * @package slub_events
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class SubscriberService
{
    /**
     * @var string
     */
    public $argumentPrefix = 'tx_slubevents_eventsubscribe';

    /**
     * @var UriBuilder
     */
    protected $uriBuilder;

    /**
     * SubscriberService constructor.
     */
    public function __construct()
    {
        /** @var ObjectManager $objectManager */
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);

        $this->uriBuilder = $objectManager->get(UriBuilder::class);
    }

    /**
     * @param int $user
     * @param array $events
     * @param int $unsubscribePid
     * @return array
     */
    public function addUnsubscribeUrl(int $user, array $events, int $unsubscribePid): array
    {
        $preparedEvents = [];
        $unsubscribeUrl = $this->getUnsubscribeUrl($unsubscribePid);

        /** @var Event $event */
        foreach ($events as $event) {
            $preparedUnsubscribeParameter = $this->prepareUnsubscribeParameter($user, $event);

            if ($preparedUnsubscribeParameter === null) {
                continue;
            }

            $event->setUnsubscribeUrl($unsubscribeUrl . '&' . $preparedUnsubscribeParameter);

            $preparedEvents[] = $event;
        }

        return $preparedEvents;
    }

    /**
     * @param int $unsubscribePid
     * @return string
     */
    protected function getUnsubscribeUrl(int $unsubscribePid): string
    {
        return $this->uriBuilder
            ->reset()
            ->setTargetPageUid($unsubscribePid)
            ->setCreateAbsoluteUri(true)
            ->setArguments([
                $this->argumentPrefix => [
                    'action' => 'delete'
                ]
            ])
            ->build();
    }

    /**
     * @param int $user
     * @param Event $event
     * @return string|null
     */
    protected function prepareUnsubscribeParameter(int $user, Event $event): ?string
    {
        $editCode = $this->getEditCode($user, $event);

        if ($editCode === null) {
            return null;
        }

        return GeneralUtility::implodeArrayForUrl(
            $this->argumentPrefix,
            [
                'editcode' => $editCode,
                'event' => $event->getUid()
            ]
        );
    }

    /**
     * @param int $user
     * @param Event $event
     * @return string|null
     */
    protected function getEditCode(int $user, Event $event): ?string
    {
        $subscribers = $event->getSubscribers();

        if (count($subscribers) === 0) {
            return null;
        }

        /** @var Subscriber $subscriber */
        foreach ($subscribers as $subscriber) {
            if (!empty($subscriber->getEditcode()) && (int)$subscriber->getCustomerid() === $user) {
                return $subscriber->getEditcode();
            }
        }

        return null;
    }
}
