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
class Tx_SlubEvents_Domain_Validator_SubscriberValidator extends Tx_Extbase_Validation_Validator_AbstractValidator {

	/**
	 * subscriberRepository
	 *
	 * @var Tx_SlubEvents_Domain_Repository_SubscriberRepository
	 * @inject
	 */
	protected $subscriberRepository;

	/**
	 * Object Manager
	 *
	 * @var Tx_Extbase_Object_ObjectManagerInterface
	 * @inject
	 */
	protected $objectManager;


	/**
	 * Return variable
	 *
	 * @var bool
	 */
	private $isValid = TRUE;

	/**
	 * Get session data
	 *
	 * @return
	 */
	public function getSessionData($key) {

		return $GLOBALS['TSFE']->fe_user->getKey('ses', $key);

	}

        /**
	 * Validation of given Params
	 *
	 * @param Tx_SlubEvents_Domain_Model_Subscriber $newSubscriber
	 * @return bool
	 */
	public function isValid($newSubscriber) {

		if (strlen($newSubscriber->getName())<3) {
			$error = $this->objectManager->get('Tx_Extbase_Error_Error', 'val_name', 1000);
			$this->result->forProperty('name')->addError($error);
			// usually $this->addError is enough but this doesn't set the CSS errorClass in the form-viewhelper :-(
//			$this->addError('val_name', 1000);

			$this->isValid = FALSE;
		}
		if (!t3lib_div::validEmail($newSubscriber->getEmail())) {
			$error = $this->objectManager->get('Tx_Extbase_Error_Error', 'val_email', 1100);
			$this->result->forProperty('email')->addError($error);
//			$this->addError('val_email', 1100);

			$this->isValid = FALSE;
		}
		if (strlen($newSubscriber->getCustomerid()) > 0 &&
			filter_var($newSubscriber->getCustomerid(), FILTER_VALIDATE_INT) === FALSE) {
			$error = $this->objectManager->get('Tx_Extbase_Error_Error', 'val_customerid', 1110);
			$this->result->forProperty('customerid')->addError($error);
//			$this->addError('val_customerid', 1110);

			$this->isValid = FALSE;
		}
		if (strlen($newSubscriber->getNumber()) == 0 ||
			filter_var($newSubscriber->getNumber(), FILTER_VALIDATE_INT) === FALSE ||
			$newSubscriber->getNumber() < 1) {

			$error = $this->objectManager->get('Tx_Extbase_Error_Error', 'val_number', 1120);
			$this->result->forProperty('number')->addError($error);
//			$this->addError('val_number', 1120);

			$this->isValid = FALSE;
		} else {
			$event = $newSubscriber->getEvent();
			// limit reached already --> overbooked
			if ($this->subscriberRepository->countAllByEvent($event) + $newSubscriber->getNumber() > $event->getMaxSubscriber()) {
			    $error = $this->objectManager->get('Tx_Extbase_Error_Error', 'val_number', 1130);
			    $this->result->forProperty('number')->addError($error);
//			    $this->addError('val_number', 1130);

			    $this->isValid = FALSE;
			}
		}
		if ($newSubscriber->getEditcode() != $this->getSessionData('editcode')) {
			$error = $this->objectManager->get('Tx_Extbase_Error_Error', 'val_editcode', 1140);
			$this->result->forProperty('editcode')->addError($error);
//			$this->addError('val_editcode', 1140);
			$this->isValid = FALSE;
		}

		return $this->isValid;
  	}
}
?>
