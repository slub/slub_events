<?php

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
 *
 *
 * @package slub_events
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */

class Tx_SlubEvents_Slots_ValidationController extends Tx_Extbase_MVC_Controller_ActionController {


	/**
	 * @var Tx_SlubEvents_Domain_Repository_EventRepository
	 */
	protected $subscriberRepository;

	/**
	 * formsRepository
	 *
	 * @var Tx_Powermail_Domain_Repository_FormsRepository
	 */
	 protected $formsRepository;

	/**
	 * @var Tx_SlubEvents_Domain_Repository_SubscriberRepository
	 */
	protected $eventRepository;

	/**
	 * @var Tx_Extbase_Configuration_ConfigurationManagerInterface
	 */
     protected $configurationManager;

	/**
	 * @param Tx_Extbase_Configuration_ConfigurationManagerInterface $configurationManager
	 * @return void
	 */
	public function injectConfigurationManager(Tx_Extbase_Configuration_ConfigurationManagerInterface $configurationManager) {
		$this->configurationManager = $configurationManager;
	}

	/**
	 * Initializes the current action
	 *
	 * @return void
	 */
	public function initializeAction() {
		$this->subscriberRepository = t3lib_div::makeInstance('Tx_SlubEvents_Domain_Repository_SubscriberRepository');
		$this->formsRepository = t3lib_div::makeInstance('Tx_Powermail_Domain_Repository_FormsRepository');
		$this->eventRepository = t3lib_div::makeInstance('Tx_SlubEvents_Domain_Repository_EventRepository');
	}
	
	public function checkEvent($params, $obj) {

		$this->initializeAction();
		$frameworkConfiguration = $this->configurationManager->getConfiguration(Tx_Extbase_Configuration_ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK, 'slubEvents', 'Eventlist');

		//~ t3lib_utility_Debug::debug($params, 'addInformation: params: ');
		$gp = t3lib_div::_GP('tx_powermail_pi1');
		$formUid = $gp['form'];
		$form = $this->formsRepository->findByUid($formUid);
		if (!method_exists($form, 'getPages')) {
			return $this->isValid = FALSE;
		}

		foreach ($form->getPages() as $page) { // every page in current form

		
			foreach ($page->getFields() as $field) { // every field in current page
				switch ($field->getMarker()) {
					case 'action':	$action = $params[$field->getUid()];
								break;
					case 'editcode':	$editcode = $params[$field->getUid()];
								break;
					case 'event':	$formIdEvent = $field->getUid();
									$eventId = $params[$formIdEvent];
								break;
					case 'eventtitle':	$formIdEventtitle = $field->getUid();
								break;
				}
			}
			
		}
		
		//~ t3lib_utility_Debug::debug($editcode, 'checkEvent: editcode... ');
		switch ($action) {
				case 'delete':
					//~ $subscriber = $this->subscriberRepository->findAllByFeuser($frameworkConfiguration['persistence']['storagePid'])->getFirst();
					$subscriber = $this->subscriberRepository->findAllByEditcode($editcode, $frameworkConfiguration['persistence']['storagePid'])->getFirst();
					if (empty($subscriber)) {
						t3lib_utility_Debug::debug($subscriber, 'checkEvent: NO subscriber found... ');
						$obj->isValid = FALSE;
						// we have to use the language labels of powermail :-(
						// --> for validationerror_validation: 
						$obj->setError('validation', $idEventTitle);
						break;
					}
					//~ t3lib_utility_Debug::debug($subscriber, 'saveSubscription: delete... ');
					$event = $this->eventRepository->findByUid($eventId);
					if (empty($event)) {
						$obj->isValid = FALSE;
						// we have to use the language labels of powermail :-(
						// --> for validationerror_validation: 
						$obj->setError('validation', $idEventTitle);
						break;
					}
					$event->removeSubscriber($subscriber);
					break;
				case 'new':
					if ($eventId > 0) {
						$event = $this->eventRepository->findByUid($eventId);

						// limit reached already --> overbooked
						if (count($event->getSubscribers()) >= $event->getMaxSubscriber()) {
							$obj->isValid = FALSE;
							// we have to use the language labels of powermail :-(
							// --> for validationerror_validation: 
							$obj->setError('validation', $idEventTitle);
						}
					}
					break;
				default:
					return;
		}

		return;
		//~ t3lib_utility_Debug::debug($eventId  , 'checkEvent: eventId');

		if ($eventId > 0) {
			$event = $this->eventRepository->findByUid($eventId);

			// limit reached already --> overbooked
			if (count($event->getSubscribers()) >= $event->getMaxSubscriber()) {
				$obj->isValid = FALSE;
				// we have to use the language labels of powermail :-(
				// --> for validationerror_validation: 
				$obj->setError('validation', $idEventTitle);
			}
		}

		return;
	}
}
?>
