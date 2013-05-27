<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2013 Alexander Bigga <alexander.bigga@slub-dresden.de>
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
 * Adds a new eval possibility to TCA of TYPO3
*
 * @author	Alexander Bigga <alexander.bigga@slub-dresden.de>
 */
class Tx_SlubEvents_Slots_TceformsEval {

	/**
	 * Adds new JavaScript function for evaluation of the TCA fields in backend
	 *
	 * @return	string	JavaScript
	 */
	public function returnFieldJS() {
		return '
                        //Convert the date to a timstamp using standard TYPO3 methods
                        value = evalFunc.input("datetime", value);
                        //Convert the timestamp back to human readable using standard TYPO3 methods
                        value = evalFunc.output("datetime", value, null);
                        return value;
                ';
	}

	/**
	 * Server valuation
	 *
	 * @param       $value          The field value to be evaluated.
	 * @param       $is_in          The "is_in" value of the field configuration from TCA
	 * @param       $set            Boolean defining if the value is written to the database or not. Must be passed by reference and changed if needed.
	 * @return      string          Value
	 */
	public function evaluateFieldValue($value, $is_in, &$set) {
		//~ return $value . ' [added by PHP]';
		print_r($is_in);
		//~ return "hallo - preis eingeben";
		//~ $value = 100;
		$set = 0;
		return $value;
	}
	
}
?>
