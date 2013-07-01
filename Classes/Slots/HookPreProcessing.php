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
class Tx_SlubEvents_Slots_HookPreProcessing {

	/**
	 * initializeAction
	 *
	 * @return
	 */
	protected function initialize() {

		global $BE_USER;

		// TYPO3 doesn't set locales for backend-users --> so do it manually like this...
		// is needed especially with gmstrftime
		switch ($BE_USER->uc['lang']) {
			case 'en': setlocale(LC_ALL, 'en_GB.utf8');
				break;
			case 'de': setlocale(LC_ALL, 'de_DE.utf8');
				break;
		}
	}

	/**
	 * This method is called by a hook in the TYPO3 Core Engine (TCEmain) when a record is saved. We use it to disable saving of the current record if it has categories assigned that are not allowed for the BE user.
	 *
	 * @param	array		$fieldArray: The field names and their values to be processed (passed by reference)
	 * @param	string		$table: The table TCEmain is currently processing
	 * @param	string		$id: The records id (if any)
	 * @param	object		$pObj: Reference to the parent object (TCEmain)
	 * @return	void
	 * @access public
	 */
	function processDatamap_preProcessFieldArray(&$fieldArray, $table, $id, &$pObj) {

		if ($table == 'tx_slubevents_domain_model_event') { // prevent moving of categories into their rootline

			// fieldArray only contains the hidden field, if you click on the lamp
			// fieldArray is complete, if you edit the tceform
			// as start_date_time is a required field, we take it to compare these two cases:
			if (empty($fieldArray['start_date_time']))
				return;

			$this->initialize();

			if ($fieldArray['genius_bar'] == FALSE)
				$message = t3lib_div::makeInstance('t3lib_FlashMessage', 'Veranstaltung gespeichert: <b>"'.$fieldArray['title'] . '"</b> am '. gmstrftime('%a, %x %H:%M:%S', $fieldArray['start_date_time']) .'.', 'OK', t3lib_FlashMessage::OK, TRUE);
			else {
				$message_text = 'Wissensbar-Veranstaltung gespeichert: ';
				// most time the category field is something like
				// 5|Literatur%20finden%3A%20Recherchestr...,11|Spezielle%20Datenbanken%3A%20Normen,12|Thematische%20Recherche
				// but in some cases it's:
				// 5,11,12
				foreach (explode(',', $fieldArray['categories']) as $category) {
					$catarray = explode('|', $category);
					if (count($catarray)>1) {
						$category_text .= urldecode($catarray[1]) . ', ';
					}
				}
				if (!empty($category_text)) {
					// get away last ', ' and add formating:
					$category_text = '<b>"'.substr($category_text, 0, strlen($category_text)-2).'"</b>';
				}
				$message_text .= $category_text . ' am '. gmstrftime('%a, %x %H:%M:%S', $fieldArray['start_date_time']) .'.';
				$message = t3lib_div::makeInstance('t3lib_FlashMessage', $message_text, 'OK', t3lib_FlashMessage::OK, TRUE);
			}

			t3lib_FlashMessageQueue::addMessage($message);

			if ($fieldArray['start_date_time'] > $fieldArray['end_date_time'] && $fieldArray['end_date_time'] > 0) {
				$message = t3lib_div::makeInstance('t3lib_FlashMessage', 'Ende (' . gmstrftime('%a, %x %H:%M:%S', $fieldArray['end_date_time']) .') liegt vor dem Start (' . gmstrftime('%a, %x %H:%M:%S', $fieldArray['start_date_time']) .')', 'Fehler: Ende der Veranstaltung', t3lib_FlashMessage::ERROR, TRUE);
				t3lib_FlashMessageQueue::addMessage($message);
			}

			if ($fieldArray['min_subscriber'] > 0 || $fieldArray['max_subscriber'] > 0) {
				if ($fieldArray['start_date_time'] < $fieldArray['sub_end_date_time'] ||
					($fieldArray['min_subscriber'] > 0 && empty($fieldArray['sub_end_date_time'])) ) {
					$fieldArray['sub_end_date_time'] = $fieldArray['start_date_time'] - 86400;
					$message = t3lib_div::makeInstance('t3lib_FlashMessage', 'Ende der Anmeldungsfrist wurde automatisch gesetzt auf ' . gmstrftime('%a, %x %H:%M:%S', $fieldArray['sub_end_date_time']), 'Bitte prüfen:', t3lib_FlashMessage::INFO, TRUE);
					t3lib_FlashMessageQueue::addMessage($message);
				}

				// warn if subscription deadline is more than 3 days before the event.
				if ($fieldArray['sub_end_date_time'] > 0 && ($fieldArray['start_date_time']  > $fieldArray['sub_end_date_time'] + (3*86400))) {
					$message = t3lib_div::makeInstance('t3lib_FlashMessage', 'Ende der Anmeldungsfrist ist aktuell gesetzt auf ' . gmstrftime('%a, %x %H:%M:%S', $fieldArray['sub_end_date_time']) . ' ==> <b>' . (int)(($fieldArray['start_date_time'] - $fieldArray['sub_end_date_time']) / 86400). ' Tage</b> vorher!', 'Bitte prüfen:', t3lib_FlashMessage::WARNING, TRUE);
					t3lib_FlashMessageQueue::addMessage($message);
				}
			} else {
					$fieldArray['sub_end_date_time'] = '';
			}

			if ($fieldArray['genius_bar'] == FALSE && count(explode(',', $fieldArray['categories'])) > 1) {
					$message = t3lib_div::makeInstance('t3lib_FlashMessage', 'Sie haben ' . count(explode(',', $fieldArray['categories'])). ' Kategorien ausgewählt. Mehrere Kategorien sind nur bei Wissensbar-Veranstaltungen erlaubt!', 'Fehler: ', t3lib_FlashMessage::ERROR, TRUE);
					t3lib_FlashMessageQueue::addMessage($message);
			}
		}
	}
}
