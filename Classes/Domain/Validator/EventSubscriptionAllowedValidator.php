<?php
namespace Slub\SlubEvents\Domain\Validator;

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

use Slub\SlubEvents\Domain\Repository\SubscriberRepository;
use TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @package slub_events
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class EventSubscriptionAllowedValidator extends AbstractValidator
{

    /**
     * subscriberRepository
     *
     * @var \Slub\SlubEvents\Domain\Repository\SubscriberRepository
     */
    protected $subscriberRepository;

    public function __construct()
    {
        $this->subscriberRepository = GeneralUtility::makeInstance(subscriberRepository::class);
    }

    /**
     * Return variable
     *
     * @var bool
     */
    private $isValid = true;

    /**
     * Validation of given Params
     *
     * @param \Slub\SlubEvents\Domain\Model\Event $event
     *
     * @return bool
     */
    public function isValid($event)
    {

        // limit reached already --> overbooked
        if ($this->subscriberRepository->countAllByEvent($event) + 1 > $event->getMaxSubscriber()) {
            $this->isValid = false;
            $this->addError('val_event_overbooked', 1200);
        }

        // event is cancelled
        if ($event->getCancelled()) {
            $this->isValid = false;
            $this->addError('val_event_cancelled', 1300);
        }

        // deadline reached....
        if (is_object($event->getSubEndDateTime())) {
            if ($event->getSubEndDateTime()->getTimestamp() < time()) {
                $this->isValid = false;
                $this->addError('val_event_reacheddeadline', 1400);
            }
        }

        return $this->isValid;
    }
}
