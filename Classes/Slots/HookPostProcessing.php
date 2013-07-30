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
 * This hook extends the tcemain class.
 * It preselects the author field with the current be_user id.
 *
 * @author	Alexander Bigga <alexander.bigga@slub-dresden.de>
 */
class Tx_SlubEvents_Slots_HookPostProcessing {

	/**
	 * Clear cache of all pages with slubevents_eventlist plugin
	 * This way the plugin may stay cached but on every delete or insert of subscribers, the cache gets cleared.
	 *
	 * @return
	 */
	function clearAllEventListCache() {

		global $GLOBALS;

		$select = 'DISTINCT pages.uid';
		$table = 'tt_content, pages';
		$query = 'list_type IN(\'slubevents_eventlist\', \'slubevents_eventgeniusbar\') AND pages.uid = tt_content.pid';
		$query .= ' AND tt_content.hidden = 0 AND pages.hidden = 0';
		$query .= ' AND tt_content.deleted = 0 AND pages.deleted = 0';

		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select, $table ,$query);

		$tcemain = t3lib_div::makeInstance('t3lib_TCEmain');

		// next two lines are necessary... don't know why.
		$tcemain->stripslashes_values = 0;
		$tcemain->start(array(), array());

		while (($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))) {
			$tcemain->clear_cacheCmd($row['uid']);
		};

		return;
	}

	/**
	 * TCEmain hook function
	 *
	 * @param       string          Status "new" or "update"
	 * @param       string          Table name
	 * @param       string          Record ID. If new record its a string pointing to index inside t3lib_tcemain::substNEWwithIDs
	 * @param       array           Field array of updated fields in the operation
	 * @param       object          Reference to tcemain calling object
	 * @return      void
	 */
	function processDatamap_afterDatabaseOperations($status, $table, $idElement, &$fieldArray, &$pObj) {

		// we are only interested in tx_slubevents_domain_model_event
		if ($table == 'tx_slubevents_domain_model_event' &&
				$pObj->checkValue_currentRecord['hidden'] == '0') {

			$this->clearAllEventListCache();


		}
	}

}
