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
 * strip all html tags
 *

 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 * @api
 */

class Tx_SlubEvents_ViewHelpers_Format_StripTagsViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {

	/**
	 * Render the supplied DateTime object as a formatted date.
	 *
	 * @param string $htmlString
	 * @return string
 	 * @author Alexander Bigga <alexander.bigga@slub-dresden.de>
	 * @api
	 */
	public function render($htmlString) {

		$text = strip_tags( html_entity_decode($htmlString, ENT_COMPAT, 'UTF-8'), '<br>,<p>');
		// get away paragraphs including class, title etc.
		$text = preg_replace('/<p[\ \w\=\"]{0,}>/', '', $text);
		$text = str_replace('</p>', "\n", $text);
		$text = str_replace('<br />', "\n", $text);
		// remove more than one empty line
		$text = preg_replace('/[\n]{3,}/', "\n\n", $text);
		// Remove separator characters (like non-breaking spaces...)
		$text = preg_replace( '/\p{Z}/u', ' ', $text );
		// remove more than one space
		$text = preg_replace('/ +/', ' ', $text);

		// remove everything else...
		$text = strip_tags( $text );
		return $text; 
		
	}
}
?>
