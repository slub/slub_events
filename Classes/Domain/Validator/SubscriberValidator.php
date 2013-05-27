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
	 * Return variable
	 *
	 * @var bool
	 */
	private $isValid = true;

	/**
	 * Get session data
	 *
	 * @return
	 */
	public function getSessionData($key) {
		
		return $GLOBALS["TSFE"]->fe_user->getKey("ses", $key);

	}

	/**
	 * Validation of given Params
	 *
	 * @param $subscriber
	 * @return bool
	 */
	public function isValid($subscriber) {

		//~ t3lib_utility_Debug::debug($subscriber->getNumber(), 'Tx_Powermail_Domain_Validator_SubscriberValidator: number... ');

		if (strlen($subscriber->getName())<3) {
			$this->addError('val_name', 1000);
			$this->isValid = false;
		}
		if (!t3lib_div::validEmail($subscriber->getEmail())) {
			$this->addError('val_email', 1100);
			$this->isValid = false;
		}
		if (strlen($subscriber->getCustomerid()) >0 &&
			filter_var($subscriber->getCustomerid(), FILTER_VALIDATE_INT) === FALSE) {
			$this->addError('val_customerid', 1110);
			$this->isValid = false;
		}
		if (strlen($subscriber->getNumber()) == 0 &&
			filter_var($subscriber->getNumber(), FILTER_VALIDATE_INT) === FALSE) {
			$this->addError('val_number', 1120);
			$this->isValid = false;
		}
		if ($subscriber->getEditcode() != $this->getSessionData('editcode')) {
			$this->addError('val_editcode', 1130);
			$this->isValid = false;
		}

		return $this->isValid;
		
  	}

}
?>
