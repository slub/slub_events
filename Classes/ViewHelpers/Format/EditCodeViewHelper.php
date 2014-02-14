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
 * Adds the Editcode to the form and to the user session
 *

 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 * @api
 */

class Tx_SlubEvents_ViewHelpers_Format_EditCodeViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {

	/**
	 * Set session data
	 *
	 * @param $key
	 * @param $data
	 * @return
	 */
	public function setSessionData($key, $data) {

	    $GLOBALS['TSFE']->fe_user->setKey('ses', $key, $data);

	    return;
	}

	/**
	 * Render the supplied DateTime object as a formatted date.
	 *
	 * @param Tx_SlubEvents_Domain_Model_Event $event
	 * @return int
 	 * @author Alexander Bigga <alexander.bigga@slub-dresden.de>
	 * @api
	 */
	public function render($event) {

		// set editcode-dummy for Spam/Form-double-sent protection
		$editCodeDummy = hash('sha256', rand().$event->getTitle().time().'dummy');
		$this->setSessionData('editcode', $editCodeDummy);

	    return $editCodeDummy;

	}
}
?>
