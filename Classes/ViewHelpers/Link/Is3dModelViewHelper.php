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
 * Check if given link is from 3d model
 *
 * = Examples =
 *
 * <code title="Defaults">
 * <f:if condition="<se:link.is3dModel link='{event.location.link}' />">
 * </code>
 * <output>
 * 1
 * </output>
 *
 *
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 * @api
 */

class Tx_SlubEvents_ViewHelpers_Link_Is3dModelViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {

	/**
	 * check if string "3d.slub-dresden.de" is part of the l ink
	 *
	 * @param string $link the given link as integer uid or string
	 * @return boolean
	 * @author Alexander Bigga <alexander.bigga@slub-dresden.de>
	 * @api
	 */
	public function render($link) {

		if ($link === NULL) {
			return FALSE;
		}

		return strpos($link, '3d.slub-dresden.de') ? TRUE : FALSE;

	}
}
?>
