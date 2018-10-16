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
use TYPO3\CMS\Core\Utility\GeneralUtility;

class HookPostProcessing
{

    /**
     * Clear cache of all pages with slubevents_eventlist plugin
     * This way the plugin may stay cached but on every delete or insert of subscribers, the cache gets cleared.
     *
     * @param       int     $pid         the PID of the storage folder
     * @param       boolean $isGeniusBar set TRUE if this is a genius bar event
     *
     * @return void
     */
    public function clearAllEventListCache($pid = 0, $isGeniusBar = false)
    {
        /** @var \TYPO3\CMS\Core\DataHandling\DataHandler $tcemain */
        $tcemain = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\DataHandling\\DataHandler');

        // next two lines are necessary... don't know why.
        $tcemain->stripslashes_values = 0;
        $tcemain->start([], []);

        if ($isGeniusBar) {
            $tcemain->clear_cacheCmd('cachetag:tx_slubevents_cat_' . $pid);
        } else {
            $tcemain->clear_cacheCmd('cachetag:tx_slubevents_' . $pid);
        }

        return;
    }

    /**
     * Clear ajax cache files for fullcalendar
     *
     * @param timestamp $startDate the startDate as unix timestamp
     *
     * @return void
     */
    public function clearAjaxCacheFiles($startDate = null)
    {
        $dir = PATH_site . 'typo3temp/tx_slubevents/';
        if (!file_exists($dir)) {
            mkdir($dir);
        }
        if ($startDate === null) {
            system('rm ' . $dir . 'calfile*');
        } else {
            $files = scandir($dir);
            foreach ($files as $file) {
                // example filename: calfile_571ea50f5d4f02ca0151c8bd2b1e23a5_1536098400_1536184800
                $fileDetails = preg_split('/_/', $file);
                if ($fileDetails[0] == 'calfile') {
                    if ($startDate > $fileDetails[2] && $startDate < $fileDetails[3]) {
                        system('rm ' . $dir . $file);
                    }
                }
            }
        }

        return;
    }

    /**
     * TCEmain hook function
     *
     * This hook is used to unset some TCA-helper fields which are not
     * part of the database table.
     *
     * We have to unset these helper fields here because with
     * status="NEW" it doesn't work in the preProcessFieldArray-hook
     *
     * @param       string $status     Status "new" or "update"
     * @param       string $table      Table name
     * @param       string $id         Record ID. If new record its a string pointing to index inside
     *                                 \TYPO3\CMS\Core\DataHandling\DataHandler::substNEWwithIDs
     * @param       array  $fieldArray Field array of updated fields in the operation
     * @param       object $pObj       Reference to tcemain calling object
     *
     * @return      void
     *
     */
    public function processDatamap_postProcessFieldArray($status, $table, $id, &$fieldArray, &$pObj)
    {
        if ($table == 'tx_slubevents_domain_model_event') {
            // should be already unset in HookPreProcessing
            unset($fieldArray['end_date_time_select']);
            unset($fieldArray['sub_end_date_time_select']);

        }
    }

    /**
     * TCEmain hook function
     *
     * @param       string $status     "new" or "update"
     * @param       string $table      name
     * @param       string $idElement  record ID. If new record its a string pointing to index inside
     *                                 end_date_time_select::substNEWwithIDs
     * @param       array  $fieldArray of updated fields in the operation
     * @param       object $pObj       Reference to tcemain calling object
     *
     * @return      void
     */
    public function processDatamap_afterDatabaseOperations($status, $table, $idElement, &$fieldArray, &$pObj)
    {
        // we are only interested in tx_slubevents_domain_model_event
        if ($table == 'tx_slubevents_domain_model_event') {
            // we need to update/create or delete all child events
            if ($status == "update") {

                $objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\ObjectManager::class);
                $eventController = $objectManager->get(\Slub\SlubEvents\Controller\EventController::class);

                if ($pObj->checkValue_currentRecord['recurring'] == 1) {
                    $eventController->createChildsAction($idElement);
                } else if ($pObj->checkValue_currentRecord['recurring'] == 0) {
                    $eventController->deleteChildsAction($idElement);
                }


            }

            if ($status == "new") {

                // debug($id, 'id new');

            }

            // clear cache only if event is not hidden
            if ($pObj->checkValue_currentRecord['hidden'] == '0') {
                $this->clearAllEventListCache(
                    $pObj->checkValue_currentRecord['pid'],
                    $pObj->checkValue_currentRecord['genius_bar']
                );

                // unfortunately I cannot access the category IDs only the amount of categories
                // but at least I get the start_date_time so I will delete all cached files around this
                // start_date_time
                $this->clearAjaxCacheFiles($pObj->checkValue_currentRecord['start_date_time']);
            }
        }
    }

    /**
     *
     * @param    string $table      : The table TCEmain is currently processing
     * @param    string $id         : The records id (if any)
     * @param    string $recordToDelete         : The records id (if any)
     * @param    string $recordWasDeleted         : The records id (if any)
     * @param    array  $fieldArray : The field names and their values to be processed (passed by reference)
     *
     * @return    void
     * @access public
     */
    public function processCmdmap_deleteAction($table, $id, $recordToDelete, &$recordWasDeleted, $fieldArray)
    {
      if ($table == 'tx_slubevents_domain_model_event') {

          if ($recordToDelete['parent'] == 0) {
              //in case of a parent (recurring) event, delete all children, too
              $objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\ObjectManager::class);
              $configurationManager = $objectManager->get(\TYPO3\CMS\Extbase\Configuration\ConfigurationManager::class);

              $configurationArray = [
                  'persistence' => [
                      'storagePid' => $recordToDelete['pid'],
                  ],
              ];
              $configurationManager->setConfiguration($configurationArray);

              $eventRepository = $objectManager->get(\Slub\SlubEvents\Domain\Repository\EventRepository::class);

              $eventRepository->deleteAllNotAllowedChildren(array(), $id);

              $persistenceManager = $objectManager->get(\TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager::class);
              $persistenceManager->persistAll();

          }
      }
    }

}
