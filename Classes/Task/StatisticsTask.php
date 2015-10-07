<?php
	namespace Slub\SlubEvents\Task;
/***************************************************************
*  Copyright notice
*
*  (c) 2015 Alexander Bigga <alexander.bigga@slub-dresden.de>
*  All rights reserved
*
*  This script is part of the Typo3 project. The Typo3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
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
* Scheduler Task for Statistics
*
*
*
* @author	Alexander Bigga <alexander.bigga@slub-dresden.de>
*/
	use Slub\SlubEvents\Helper\EmailHelper;
	use TYPO3\CMS\Core\Utility\MathUtility;
	use TYPO3\CMS\Core\Utility\GeneralUtility;

class StatisticsTask extends \TYPO3\CMS\Scheduler\Task\AbstractTask {

	/**
	 * PID of storage folder to work with
	 *
	 * @var integer
	 */
	protected $storagePid;

	/**
	 * Email address of receiver(s)
	 *
	 * @var array
	 */
	protected $receiverEmailAddress;

	/**
	 * Email address of sender
	 *
	 * @var string
	 */
	protected $senderEmailAddress;


	/**
	 * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
	 */
	protected $configurationManager;

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
	 * injectConfigurationManager
	 *
	 * @param \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManager
	 * @return void
	 */
	public function injectConfigurationManager(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManager) {
		$this->configurationManager = $configurationManager;

		$this->contentObj = $this->configurationManager->getContentObject();
		$this->settings = $this->configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS);
	}

	/**
	 * Set the value of the storage pid
	 *
	 * @param integer $page UID of the start page for this task.
	 * @return void
	 */
	public function setStoragePid($storagePid) {
		$this->storagePid = $storagePid;
	}

	/**
	 * Get the value of the storage pid
	 *
	 * @return integer $page UID of the start page for this task.
	 */
	public function getStoragePid() {
		return $this->storagePid;
	}

	/**
	 * Set the value of the private property receiverEmailAddress
	 *
	 * @param string $receiverEmailAddress The receiver email address
	 * @return void
	 */
	public function setReceiverEmailAddress($receiverEmailAddress) {
		$this->receiverEmailAddress = $receiverEmailAddress;
	}

	/**
	 * Get the value of the storage pid
	 *
	 * @return string $receiverEmailAddress The receiver email address
	 */
	public function getReceiverEmailAddress() {
		return $this->receiverEmailAddress;
	}

	/**
	 * Set the value of the private property senderEmailAddress
	 *
	 * @param string $senderEmailAddress The sender email address
	 * @return void
	 */
	public function setSenderEmailAddress($senderEmailAddress) {
		$this->senderEmailAddress = $senderEmailAddress;
	}

	/**
	 * Get the value of the storage pid
	 *
	 * @return string $senderEmailAddress The sender email address
	 */
	public function getSenderEmailAddress() {
		return $this->senderEmailAddress;
	}

	/**
	 * initializeAction
	 *
	 * @return
	 */
	protected function initializeAction() {

		// TYPO3 doesn't set locales for backend-users --> so do it manually like this...
		// is needed with strftime
		setlocale(LC_ALL, 'de_DE.utf8');

		// simulate BE_USER setting to force fluid using the proper translation
		$GLOBALS['BE_USER']->uc['lang'] = 'de';

		$objectManager = GeneralUtility::makeInstance('\TYPO3\CMS\Extbase\Object\ObjectManager');

		$this->eventRepository= $objectManager->get('\Slub\SlubEvents\Domain\Repository\EventRepository');
		$this->categoryRepository= $objectManager->get('\Slub\SlubEvents\Domain\Repository\CategoryRepository');
		$this->configurationManager = $objectManager->get('\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface');

	}

	/**
	 * Function execute from the Scheduler
	 *
	 * @return boolean TRUE on successful execution, FALSE on error
	 * @throws \InvalidArgumentException if the email template file can not be read
	 */
	public function execute() {

		$successfullyExecuted = TRUE;

		// do some init work...
		$this->initializeAction();

		// abort if no storagePid is found
		if (!MathUtility::canBeInterpretedAsInteger($this->storagePid)) {
			echo 'NO storagePid given. Please enter the storagePid in the scheduler task.';
			$successfullyExecuted = FALSE;
		}

		// abort if no senderEmailAddress is found
		if (empty($this->senderEmailAddress)) {
			echo 'NO senderEmailAddress given. Please enter the senderEmailAddress in the scheduler task.';
			$successfullyExecuted = FALSE;
		}

		// abort if no senderEmailAddress is found
		if (empty($this->receiverEmailAddress)) {
			echo 'NO receiverEmailAddress given. Please enter the receiverEmailAddress in the scheduler task.';
			$successfullyExecuted = FALSE;
		}
//		else {
//			$this->receiverEmailAddress = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(', ', $this->receiverEmailAddress);
//		}

		// set storagePid to point extbase to the right repositories
		$configurationArray = array(
			'persistence' => array(
				'storagePid' => $this->storagePid
			)
		);
		$this->configurationManager->setConfiguration($configurationArray);



		// start the work...

		// 1. get the categories
		$categories = $this->categoryRepository->findAll();

		foreach ($categories as $uid => $category) {
			$searchParameter['category'][$uid] = $uid;
		}

		// 2. get events of last month
		$startDateTime = strtotime('first day of last month 00:00:00');
		$endDateTime = strtotime('last day of last month 23:59:59');

		$allevents = $this->eventRepository->findAllByCategoriesAndDateInterval($searchParameter['category'], $startDateTime, $endDateTime);
		// used to name the csv file...
		$helper['nameto'] = strftime('%Y%m', $startDateTime);

		// email to all receivers...
		$successfullyExecuted = EmailHelper::sendTemplateEmail(
			$this->receiverEmailAddress,
			array($this->senderEmailAddress => 'SLUB Veranstaltungen - noreply'),
			'Statistik Report Veranstaltungen: ' . ': ' . strftime('%x', $startDateTime) . ' - ' . strftime('%x', $endDateTime),
			'Statistics',
			array('events' => $allevents,
				'categories' => $categories,
				'helper' => $helper,
				'attachCsv' => TRUE,
				'attachIcs' => FALSE,
			)
		);

		return $successfullyExecuted;
	}
}
