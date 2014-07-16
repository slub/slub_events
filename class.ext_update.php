<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2014 Alexander Bigga <alexander.bigga@slub-dresden.de>
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
 * Update class for the extension manager.
 *
 * @package slub_events
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */

class ext_update {

	protected $messageArray = array();

	/**
	 * Main update function called by the extension manager.
	 *
	 * @return string
	 */
	public function main() {
		$this->processUpdates();
		return $this->generateOutput();
	}

	/**
	 * Called by the extension manager to determine if the update menu entry
	 * should by showed.
	 *
	 * @return bool
	 */
	public function access() {

		// we act only, if mm-table is still empty
		$countMmTable = $GLOBALS['TYPO3_DB']->exec_SELECTcountRows('*', 'tx_slubevents_event_discipline_mm', '1=1');

		$countGeniusBarEvent = $GLOBALS['TYPO3_DB']->exec_SELECTcountRows('*', 'tx_slubevents_domain_model_event', 'genius_bar=1');
		$countGeniusBarCategory = $GLOBALS['TYPO3_DB']->exec_SELECTcountRows('*', 'tx_slubevents_domain_model_category', 'genius_bar=1');

		if ($countMmTable > 0 && ($countGeniusBarEvent > 0 &&  $countGeniusBarCategory > 0))
			return FALSE;
		else
			return TRUE;
	}

	/**
	 * The actual update function. Add your update task in here.
	 *
	 * @return void
	 */
	protected function processUpdates() {

		$this->updateContentRelationToMm();
		$this->updateCategoriesGeniusBar();

	}

	/**
	 * We changed the event.discipline field to be MM related
	 *
	 * This takes the old discipline value and enters it in the MM-table
	 * in the right way.
	 *
	 * @return void
	 */
	protected function updateContentRelationToMm() {

		$title = 'Update discipline relation';

		// we act only, if mm-table is still empty
		$countMmTable = $GLOBALS['TYPO3_DB']->exec_SELECTcountRows('*', 'tx_slubevents_event_discipline_mm', '1=1');

		if ($countMmTable === 0) {

			$eventCount = 0;

			// Insert mm relation, sorting and sorting_foreign is 0 because it will be only one item
			$fields = array('uid_local', 'uid_foreign');

			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid,discipline', 'tx_slubevents_domain_model_event', 'deleted=0');
			while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				$eventCount++;

				// Insert mm relation, sorting is 0 because it will be only one item
				$inserts[] = array($row['uid'], $row['discipline']);

				// Update event record
				$update = array('discipline' => 1);
				$GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_slubevents_domain_model_event', 'uid=' . $row['uid'], $update);
			}
			$GLOBALS['TYPO3_DB']->exec_INSERTmultipleRows('tx_slubevents_event_discipline_mm', $fields, $inserts);
			$GLOBALS['TYPO3_DB']->sql_free_result($res);

			$this->messageArray[] = array(t3lib_FlashMessage::OK, $title, $eventCount . ' event records have been updated!');

		} else if ($countMmTable === FALSE) {

			$this->messageArray[] = array(t3lib_FlashMessage::ERROR, $title, 'ERROR: table tx_slubevents_event_discipline_mm. Did you run db compare after udpate?');

		} else {

			$this->messageArray[] = array(t3lib_FlashMessage::NOTICE, $title, 'Not needed/possible anymore as the mm table is already filled!');

		}

	}

	/**
	 * We added a genius_bar flag to the categories
	 *
	 * This updatescript sets the genius_bar flag to all categories,
	 * used from genius_bar events.
	 *
	 *
	 * @return void
	 */
	protected function updateCategoriesGeniusBar() {

		$title = 'Update categories genius_bar flag';

		// we act only, if any genius_bar event is present
		$countGeniusBarEvent = $GLOBALS['TYPO3_DB']->exec_SELECTcountRows('*', 'tx_slubevents_domain_model_event', 'genius_bar=1');
		$countGeniusBarCategory = $GLOBALS['TYPO3_DB']->exec_SELECTcountRows('*', 'tx_slubevents_domain_model_category', 'genius_bar=1');

		if ($countGeniusBarEvent > 0 &&  $countGeniusBarCategory === 0) {

			$categoryCount = 0;
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid_foreign', 'tx_slubevents_domain_model_event, tx_slubevents_event_category_mm', 'genius_bar=1 AND uid_local=uid');
			while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				$categoryCount++;

				// Update event record
				$update = array('genius_bar' => 1);
				$GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_slubevents_domain_model_category', 'uid=' . $row['uid_foreign'], $update);
			}
			$GLOBALS['TYPO3_DB']->sql_free_result($res);

			$this->messageArray[] = array(t3lib_FlashMessage::OK, $title, $categoryCount . ' category records have been updated!');
		} else if ($countGeniusBarEvent === FALSE || $countGeniusBarCategory === FALSE) {

			$this->messageArray[] = array(t3lib_FlashMessage::ERROR, $title, 'ERROR: table tx_slubevents_domain_model_category. Did you run db compare after update?');

		} else {

			$this->messageArray[] = array(t3lib_FlashMessage::NOTICE, $title, 'Not needed/possible anymore as the genius_bar flag is already set.');

		}

	}


	/**
	 * Generates output by using flash messages
	 *
	 * @return string
	 */
	protected function generateOutput() {
		$output = '';
		foreach ($this->messageArray as $messageItem) {
			$flashMessage = t3lib_div::makeInstance(
					't3lib_FlashMessage',
					$messageItem[2],
					$messageItem[1],
					$messageItem[0]);
			$output .= $flashMessage->render();
		}

		return $output;
	}

}
