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
class Tx_SlubEvents_Slots_PowermailSlots extends Tx_Powermail_Controller_FormsController {

	/**
	 * @var Tx_SlubEvents_Domain_Repository_EventRepository
	 */
	protected $subscriberRepository;

	/**
	 * @var Tx_SlubEvents_Domain_Repository_SubscriberRepository
	 */
	protected $eventRepository;

	/**
	 * formsRepository
	 *
	 * @var Tx_Powermail_Domain_Repository_FormsRepository
	 */
	protected $formsRepository;

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
		$this->eventRepository = t3lib_div::makeInstance('Tx_SlubEvents_Domain_Repository_EventRepository');
		$this->formsRepository = t3lib_div::makeInstance('Tx_Powermail_Domain_Repository_FormsRepository');
	}

	/**
	 * @param array Field Values
	 * @param integer Form UID
	 * @param object Mail object (normally empty, filled when mail already exists via double-optin)
	 * @param Tx_Powermail_Controller_FormsController $controller
	 */
	public function saveSubscription(&$fieldIn = array(), $formId, $mail = NULL, $controller) {

		$this->initializeAction();
		$frameworkConfiguration = $this->configurationManager->getConfiguration(Tx_Extbase_Configuration_ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK, 'slubEvents', 'Eventlist');

		// toDo:
		// - match fields more flexible
		// - check for vacancies before saving
		// - do not send mail if any problem occures
		$form = $this->formsRepository->findByUids($formId)->getFirst();



		// either new or delete
		
		foreach ($form->getPages() as $page) { // every page in current form
			foreach ($page->getFields() as $field) { // every field in current page

				switch ($field->getMarker()) {
					case 'action':	$action = $fieldIn[$field->getUid()];
								break;
					case 'editcode':	$editcode = $fieldIn[$field->getUid()];
								break;
					case 'event':	$event = $this->eventRepository->findByUid($fieldIn[$field->getUid()]);
								break;
				}
			}
		}


		switch ($action) {
				case 'delete':
					//~ $subscriber = $this->subscriberRepository->findAllByFeuser($frameworkConfiguration['persistence']['storagePid'])->getFirst();
					$subscriber = $this->subscriberRepository->findAllByEditcode($editcode, $frameworkConfiguration['persistence']['storagePid'])->getFirst();
					//~ t3lib_utility_Debug::debug($subscriber, 'saveSubscription: delete... ');
					$event->removeSubscriber($subscriber);
					return;
					break;
				case 'new':
					break;
				default:
					return;
		}







		
		$newsubscriber = t3lib_div::makeInstance('Tx_SlubEvents_Domain_Model_Subscriber');
		$newsubscriber->setNumber(1);
		$newsubscriber->setPid($frameworkConfiguration['persistence']['storagePid']);
		$editcode = hash('sha256', rand().$newsubscriber->getEmail().time());
		$newsubscriber->setEditcode($editcode);

		if (!method_exists($form, 'getPages')) {
			return false;
		}
		foreach ($form->getPages() as $page) { // every page in current form
			foreach ($page->getFields() as $field) { // every field in current page

				
				if (! array_key_exists($field->getUid(), $fieldIn))
					continue;

				//~ t3lib_utility_Debug::debug($field->getMarker() .':' .$field->getUid(), 'saveSubscription: getMarker');
				switch ($field->getMarker()) {
					case 'vor_undzuname':	$newsubscriber->setName($fieldIn[$field->getUid()]);
								break;
					case 'event':	$event = $this->eventRepository->findByUid($fieldIn[$field->getUid()]);
								break;
					case 'email':	$newsubscriber->setEmail($fieldIn[$field->getUid()]);
								break;
					case 'benutzernummer':	$newsubscriber->setCustomerid($fieldIn[$field->getUid()]);
								break;
					case 'editcode':	$fieldIn[$field->getUid()] = '<a href="http://www.slub-dresden.de/?editcode='.$editcode.'">Link zur Abmeldung</a>';
								break;
					case 'anzahl':	$newsubscriber->setNumber(1);
								break;
								
				}
			}
		}

		//~ $fieldIn[29] = $editcode;
		
		// only add, if event is found.
		if (is_object($event))
			$event->addSubscriber($newsubscriber);


		return;
		
		//~ t3lib_utility_Debug::debug($this->settings, 'saveSubscription: settings');
		t3lib_utility_Debug::debug($frameworkConfiguration, 'frameworkConfiguration: settings');
		t3lib_utility_Debug::debug($fieldIn, 'saveSubscription: field');
		t3lib_utility_Debug::debug($mail, 'saveSubscription: mail');

	}
}
?>
