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
 * Ajax Dispatcher Class
 *
 * based on http://daniel.lienert.cc/blog/blog-post/2011/04/23/typo3-extbase-und-ajax/
 *
 * @author	Alexander Bigga <alexander.bigga@slub-dresden.de>
 */

// EXPERIMENTAL DOESN'T WORK AT ALL!
// EXPERIMENTAL DOESN'T WORK AT ALL!
// EXPERIMENTAL DOESN'T WORK AT ALL!
// EXPERIMENTAL DOESN'T WORK AT ALL!

require_once(PATH_tslib.'class.tslib_pibase.php');
//~ require_once t3lib_extMgm::extPath('pt_extbase') . 'Classes/Utility/AjaxDispatcher.php';


class Tx_SlubEvents_Utility_Ajax_Dispatcher extends tslib_pibase {

  public function main(){

//~ require_once(PATH_tslib.'class.tslib_pibase.php');

//~ class mydispatch extends tslib_pibase {

	//~ public function Tx_SlubEvents_Utility_Ajax_Dispatcher {
//~ extends tslib_pibase {

//~ 
    $feUserObj = tslib_eidtools::initFeUser(); // Initialize FE user object     
	//Connect to database
	tslib_eidtools::connectDB();
	 
	// Init TSFE for database access
	$GLOBALS['TSFE'] = t3lib_div::makeInstance('tslib_fe', $TYPO3_CONF_VARS, 0, 0, true);
	$GLOBALS['TSFE']->sys_page = t3lib_div::makeInstance('t3lib_pageSelect');
	$GLOBALS['TSFE']->initFEuser();
	$dispatcher = t3lib_div::makeInstance('Tx_PtExtbase_Utility_AjaxDispatcher'); /** @var $dispatcher Tx_PtExtbase_Utility_AjaxDispatcher */
	 //~ 
	//~ // ATTENTION! Dispatcher first needs to be initialized here!!!
	echo $dispatcher->initCallArguments()->dispatch();
	
	}
}

$output = t3lib_div::makeInstance('Tx_SlubEvents_Utility_Ajax_Dispatcher');
$output->main();

?>
