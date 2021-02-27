<?php
namespace Slub\SlubEvents\Domain\Validator;

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

use Slub\SlubEvents\Domain\Repository\SubscriberRepository;

/**
 *
 *
 * @package slub_events
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
use TYPO3\CMS\Core\Utility\GeneralUtility;

class SubscriberValidator extends \TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator
{

    /**
     * subscriberRepository
     *
     * @var \Slub\SlubEvents\Domain\Repository\SubscriberRepository
     */
    protected $subscriberRepository;

	/**
     * @param \Slub\SlubEvents\Domain\Repository\SubscriberRepository $subscriberRepository
     */
    public function injectSubscriberRepository(SubscriberRepository $subscriberRepository)
    {
        $this->subscriberRepository = $subscriberRepository;
    }

    /**
     * Object Manager
     *
     * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
     */
    protected $objectManager;

	/**
     * @param \TYPO3\CMS\Extbase\Object\ObjectManagerInterface $objectManager
     */
    public function injectObjectManager(\TYPO3\CMS\Extbase\Object\ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Return variable
     *
     * @var bool
     */
    private $isValid = true;

    /**
     * Get session data
     *
     * @param string $key
     * @return string
     */
    protected static function getSessionData($key)
    {
        return $GLOBALS['TSFE']->fe_user->getKey('ses', $key);
    }

    /**
     * Validation of given Params
     *
     * @param \Slub\SlubEvents\Domain\Model\Subscriber $newSubscriber
     *
     * @return bool
     */
    public function isValid($newSubscriber)
    {
        if (strlen($newSubscriber->getName()) < 3) {
			$this->addError('val_name', 1000);
            $this->isValid = false;
        }
        if (!GeneralUtility::validEmail($newSubscriber->getEmail())) {
			$this->addError('val_email', 1100);
            $this->isValid = false;
        }
        if (strlen($newSubscriber->getCustomerid()) > 0 &&
            filter_var($newSubscriber->getCustomerid(), FILTER_VALIDATE_INT) === false
        ) {
			$this->addError('val_customerid', 1110);
            $this->isValid = false;
        }
        if (strlen($newSubscriber->getNumber()) == 0 ||
            filter_var($newSubscriber->getNumber(), FILTER_VALIDATE_INT) === false ||
            $newSubscriber->getNumber() < 1
        ) {
			$this->addError('val_number', 1120);
            $this->isValid = false;
        } else {
            $event = $newSubscriber->getEvent();
            // limit reached already --> overbooked
            if ($this->subscriberRepository->countAllByEvent($event) + $newSubscriber->getNumber() > $event->getMaxSubscriber()) {
			    $this->addError('val_number', 1130);
                $this->isValid = false;
            }
        }
        $currentSessionData = $this->getSessionData('editcode');
        if ($newSubscriber->getEditcode() != $this->getSessionData('editcode')) {
			$this->addError('val_editcode', 1140);
            $this->isValid = false;
        }
        if ($newSubscriber->getAcceptpp() !== null && $newSubscriber->getAcceptpp() === false) {
            $this->addError('val_acceptpp', 1140);
            $this->isValid = false;
        }

        return $this->isValid;
    }
}
