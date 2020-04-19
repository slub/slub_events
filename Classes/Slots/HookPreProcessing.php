<?php
namespace Slub\SlubEvents\Slots;
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
 * @author    Alexander Bigga <alexander.bigga@slub-dresden.de>
 */
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageQueue;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class HookPreProcessing
{

    /**
     * initializeAction
     *
     * @return
     */
    protected function initialize()
    {

        // TYPO3 doesn't set locales for backend-users --> so do it manually like this...
        // is needed especially with gmstrftime
        switch ($GLOBALS['BE_USER']->uc['lang']) {
            case 'en':
                setlocale(LC_ALL, 'en_GB.utf8');
                break;
            case 'de':
                setlocale(LC_ALL, 'de_DE.utf8');
                break;
        }
    }

    /**
     * This method is called by a hook in the TYPO3 Core Engine (TCEmain)
     * when a record is saved.
     * We use it to disable saving of the current record if it has
     * categories assigned that are not allowed for the BE user.
     *
     * @param    array  $fieldArray : The field names and their values to be processed (passed by reference)
     * @param    string $table      : The table TCEmain is currently processing
     * @param    string $id         : The records id (if any)
     * @param    object $pObj       : Reference to the parent object (TCEmain)
     *
     * @return    void
     * @access public
     */
    public function processDatamap_preProcessFieldArray(&$fieldArray, $table, $id, &$pObj)
    {
        if ($table == 'tx_slubevents_domain_model_event') { // prevent moving of categories into their rootline

            // fieldArray only contains the hidden field, if you click on the lamp
            // fieldArray is complete, if you edit the tceform
            // as start_date_time is a required field, we take it to compare these two cases:
            if (empty($fieldArray['start_date_time'])) {
                return;
            }

            $this->initialize();

            /** @var \TYPO3\CMS\Core\Messaging\FlashMessageService $flashMessageService */
            $flashMessageService = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Messaging\\FlashMessageService');
            /** @var $defaultFlashMessageQueue \TYPO3\CMS\Core\Messaging\FlashMessageQueue */
            $defaultFlashMessageQueue = $flashMessageService->getMessageQueueByIdentifier();

            if (empty($fieldArray['genius_bar'])) {
                $message = GeneralUtility::makeInstance(
                    'TYPO3\CMS\Core\Messaging\FlashMessage',
                    'Veranstaltung gespeichert: "' . $fieldArray['title'] . '" am ' . $this->gmstrftime(
                        $fieldArray['start_date_time']) . '.',
                    'OK',
                    FlashMessage::OK,
                    true);
            } else {
                $message_text = 'Wissensbar-Veranstaltung gespeichert: ';
                // most time the category field is something like
                // 5|Literatur%20finden%3A%20Recherchestr...,11|Spezielle%20Datenbanken%3A%20Normen,12|Thematische%20Recherche
                // but in some cases it's:
                // 5,11,12
                foreach (explode(',', $fieldArray['categories']) as $category) {
                    $catarray = explode('|', $category);
                    if ($catarray && count($catarray) > 1) {
                        $category_text .= urldecode($catarray[1]) . ', ';
                    }
                }
                if (!empty($category_text)) {
                    // get away last ', ' and add formating:
                    $category_text = '"' . substr($category_text, 0, strlen($category_text) - 2) . '"';
                }
                $message_text .= $category_text . ' am ' . $this->gmstrftime(
                        $fieldArray['start_date_time']) . '.';
                $message = GeneralUtility::makeInstance(
                    'TYPO3\CMS\Core\Messaging\FlashMessage',
                    $message_text,
                    'OK',
                    FlashMessage::OK,
                    true);
            }
            $defaultFlashMessageQueue->enqueue($message);

            if ($fieldArray['start_date_time'] > $fieldArray['end_date_time'] && $fieldArray['end_date_time'] > 0) {
                $message = GeneralUtility::makeInstance(
                    'TYPO3\CMS\Core\Messaging\FlashMessage',
                    'Ende (' . $this->gmstrftime(
                        $fieldArray['end_date_time']) . ') liegt vor dem Start (' . $this->gmstrftime(
                        $fieldArray['start_date_time']) . ')',
                    'Fehler: Ende der Veranstaltung',
                    FlashMessage::ERROR,
                    true);
                $defaultFlashMessageQueue->enqueue($message);
            }

            // use the select box value to calculate the end_date_time relative to start_date_time
            if (!empty($fieldArray['end_date_time_select'])) {
                $fieldArray['end_date_time'] = $this->calculateEndDateTime($fieldArray['start_date_time'], $fieldArray['end_date_time_select']);
                unset($fieldArray['end_date_time_select']);
                $message = GeneralUtility::makeInstance(
                    'TYPO3\CMS\Core\Messaging\FlashMessage',
                    'Ende der Veranstaltung gesetzt auf ' . $this->gmstrftime($fieldArray['end_date_time']),
                    'Bitte prüfen:',
                    FlashMessage::INFO,
                    true);
                $defaultFlashMessageQueue->enqueue($message);
            }

            // touch the subscribtion end only if minimum subscribers are set
            if ($fieldArray['min_subscriber'] > 0 || $fieldArray['max_subscriber'] > 0) {
                if ($fieldArray['start_date_time'] < $fieldArray['sub_end_date_time'] ||
                    ($fieldArray['min_subscriber'] > 0 && empty($fieldArray['sub_end_date_time']))
                ) {
                    if ($fieldArray['sub_end_date_time_select'] >= 0) {
                        $fieldArray['sub_end_date_time'] = $this->calculateEndDateTime($fieldArray['start_date_time'], $fieldArray['sub_end_date_time_select'], FALSE);

                        $message = GeneralUtility::makeInstance(
                            'TYPO3\CMS\Core\Messaging\FlashMessage',
                            'Ende der Anmeldungsfrist wurde gesetzt auf ' . $this->gmstrftime(
                                $fieldArray['sub_end_date_time']),
                            'Bitte prüfen:',
                            FlashMessage::INFO,
                            true);
                        $defaultFlashMessageQueue->enqueue($message);
                    }
                }
                unset($fieldArray['sub_end_date_time_select']);

                // warn if subscription deadline is more than 3 days before the event.
                if ($fieldArray['sub_end_date_time'] > 0 && ($fieldArray['start_date_time'] > $fieldArray['sub_end_date_time'] + (3 * 86400))) {
                    $message = GeneralUtility::makeInstance(
                        'TYPO3\CMS\Core\Messaging\FlashMessage',
                        'Ende der Anmeldungsfrist ist aktuell gesetzt auf ' . $this->gmstrftime(
                            $fieldArray['sub_end_date_time']) . ' ==> ' . (int)(($fieldArray['start_date_time'] - $fieldArray['sub_end_date_time']) / 86400) . ' Tage vorher!',
                        'Bitte prüfen:',
                        FlashMessage::WARNING,
                        true);
                    $defaultFlashMessageQueue->enqueue($message);
                }
            } else {
                unset($fieldArray['sub_end_date_time_select']);
                $fieldArray['sub_end_date_time'] = '';
            }

            if ($fieldArray['genius_bar'] == false && count(explode(',', $fieldArray['categories'])) > 1) {
                $message = GeneralUtility::makeInstance(
                    'TYPO3\CMS\Core\Messaging\FlashMessage',
                    'Sie haben ' . count(explode(',', $fieldArray['categories'])) . ' Kategorien ausgewählt. ',
                    'Bitte prüfen: ',
                    FlashMessage::INFO,
                    true);
                $defaultFlashMessageQueue->enqueue($message);
            }

            // force genius bar events with min_ and max_subscriber == 1
            if ($fieldArray['genius_bar'] == true && ($fieldArray['min_subscriber'] != 1 || $fieldArray['max_subscriber'] != 1)) {
                $fieldArray['min_subscriber'] = 1;
                $fieldArray['max_subscriber'] = 1;
                $message = GeneralUtility::makeInstance(
                    'TYPO3\CMS\Core\Messaging\FlashMessage',
                    'Die Mindest- und Maximalteilnehmerzahl beträgt in der Wissensbar immer 1. Dies wurde automatisch korrigiert. ',
                    'Bitte prüfen: ',
                    FlashMessage::INFO,
                    true);
                $defaultFlashMessageQueue->enqueue($message);
            }

            if ($fieldArray['max_subscriber'] > 0 && $fieldArray['max_number'] == 0) {
                $fieldArray['max_number'] = $fieldArray['max_subscriber'];
            }

            // save recurring options as serialized Array
            if (!empty($fieldArray['recurring_options'])) {
              $fieldArray['recurring_options'] = serialize($fieldArray['recurring_options']);
            }
        }
    }

    /**
     * calculate end_date_time from selected time interval
     *
     * @param mixed $startDateTime
     * @param mixed $selectedInterval
     * @param boolean $add
     *
     * @return end_date_time
     */
    protected function calculateEndDateTime($startDateTime, $selectedInterval, $add = TRUE)
    {
        // TYPO3 is working with dateTime values instead of unix timestamps in fieldArray
        $sdt = new \DateTime($startDateTime);
        $edt = new \DateTime();
        if ($add === TRUE) {
            $edt = $sdt->add(new \DateInterval("PT" . trim($selectedInterval) . "M"));
        } else {
            $edt = $sdt->sub(new \DateInterval("PT" . trim($selectedInterval) . "M"));
        }
        $endDateTime = $edt->format(\DateTime::ATOM);

        return $endDateTime;
    }

    /**
     * return formated timestring
     *
     * @param mixed $time
     *
     * @return string $formatedTimeString
     */
    protected function gmstrftime($time)
    {
      // TYPO3 is working with dateTime values instead of unix timestamps in fieldArray
        $dt = new \DateTime($time);
        $formatedTimeString = gmstrftime('%a, %x %H:%M:%S', $dt->format('U'));

        return $formatedTimeString;
    }
}
