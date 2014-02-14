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
 * returns one single line
 *

 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 * @api
 */

class Tx_SlubEvents_ViewHelpers_Format_OneLineViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {

	/**
	 * Render
	 *
	 * @param string $htmlString
	 * @return string
 	 * @author Alexander Bigga <alexander.bigga@slub-dresden.de>
	 * @api
	 */
	public function render($htmlString) {

		$text = str_replace("\t", ' ', $htmlString);
		$text = str_replace('<br />', ' ', $text);
		// remove more than one empty line
		$text = preg_replace('/[\n]{1,}/', ' ', $text);
		// remove more than one space
		$text = preg_replace('/[\ ]{2,}/', ' ', $text);

		return trim($text);

	}
}
?>
