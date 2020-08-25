<?php
namespace Slub\SlubEvents\Task;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2020 Alexander Bigga <alexander.bigga@slub-dresden.de>, SLUB Dresden
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
 *
 *
 * @package slub_events
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Scheduler\AbstractAdditionalFieldProvider;
use TYPO3\CMS\Scheduler\Task\Enumeration\Action;

class CheckeventsTaskAdditionalFieldProvider extends AbstractAdditionalFieldProvider
{

    /**
     * Render additional information fields within the scheduler backend.
     *
     * @param array $taskInfo Array information of task to return
     * @param AbstractTask|null $task When editing, reference to the current task. NULL when adding.
     * @param \TYPO3\CMS\Scheduler\Controller\SchedulerModuleController $schedulerModule Reference to the BE module of the Scheduler
     *
     * @return array Additional fields
     * @see \TYPO3\CMS\Scheduler\AdditionalFieldProviderInterface->getAdditionalFields($taskInfo, $task, $schedulerModule)
     */
    public function getAdditionalFields(
        array &$taskInfo,
        $task,
        \TYPO3\CMS\Scheduler\Controller\SchedulerModuleController $schedulerModule
    ) {
        $additionalFields = [];
        $currentSchedulerModuleAction = $schedulerModule->getCurrentAction();

        if (empty($taskInfo['storagePid'])) {
            if ($currentSchedulerModuleAction->equals(Action::ADD)) {
                $taskInfo['storagePid'] = '';
            } elseif ($currentSchedulerModuleAction->equals(Action::EDIT)) {
                $taskInfo['storagePid'] = $task->storagePid;
            } else {
                $taskInfo['storagePid'] = $task->storagePid;
            }
        }

        if (empty($taskInfo['senderEmailAddress'])) {
            if ($currentSchedulerModuleAction->equals(Action::ADD)) {
                $taskInfo['senderEmailAddress'] = '';
            } elseif ($currentSchedulerModuleAction->equals(Action::EDIT)) {
                $taskInfo['senderEmailAddress'] = $task->senderEmailAddress;
            } else {
                $taskInfo['senderEmailAddress'] = $task->senderEmailAddress;
            }
        }

        if (empty($taskInfo['language'])) {
            if ($currentSchedulerModuleAction->equals(Action::ADD)) {
                $taskInfo['language'] = 'en';
            } elseif ($currentSchedulerModuleAction->equals(Action::EDIT)) {
                $taskInfo['language'] = $task->language;
            } else {
                $taskInfo['language'] = $task->language;
            }
        }

        $fieldId = 'task_storagePid';
        $fieldCode = '<input class="form-control" type="text" name="tx_scheduler[slub_events][storagePid]" id="' . $fieldId . '" value="' . htmlspecialchars($taskInfo['storagePid']) . '"/>';
        $label = $GLOBALS['LANG']->sL('LLL:EXT:slub_events/Resources/Private/Language/locallang.xlf:tasks.cleanup.storagePid');
        $additionalFields[$fieldId] = [
            'code'  => $fieldCode,
            'label' => $label
        ];

        $fieldId = 'task_senderEmailAddress';
        $fieldCode = '<input class="form-control" type="text" name="tx_scheduler[slub_events][senderEmailAddress]" id="' . $fieldId . '" value="' . htmlspecialchars($taskInfo['senderEmailAddress']) . '"/>';
        $label = $GLOBALS['LANG']->sL('LLL:EXT:slub_events/Resources/Private/Language/locallang.xlf:tasks.statistics.senderEmailAddress');
        $label = BackendUtility::wrapInHelp('slub_events', $fieldId, $label);
        $additionalFields[$fieldId] = [
            'code'  => $fieldCode,
            'label' => $label,
        ];

        $fieldId = 'task_language';
        $fieldCode = '<select class="form-control" name="tx_scheduler[slub_events][language]" id="' . $fieldId . '">
                        <option value="en" '.($taskInfo['language'] === 'en' ? 'selected' : '').' />English</option>
                        <option value="de" '.($taskInfo['language'] === 'de' ? 'selected' : '').' />Deutsch</option>
                      </select>
                        ';
        $label = $GLOBALS['LANG']->sL('LLL:EXT:slub_events/Resources/Private/Language/locallang.xlf:tasks.checkevents.language');
        $additionalFields[$fieldId] = [
            'code'  => $fieldCode,
            'label' => $label
        ];

        return $additionalFields;
    }

    /**
     * This method checks any additional data that is relevant to the specific task.
     * If the task class is not relevant, the method is expected to return TRUE.
     *
     * @param array                                                     $submittedData   Reference to the array containing the data submitted by the user
     * @param \TYPO3\CMS\Scheduler\Controller\SchedulerModuleController $schedulerModule Reference to the BE module of the Scheduler
     *
     * @return boolean TRUE if validation was ok (or selected class is not relevant), FALSE otherwise
     */
    public function validateAdditionalFields(
        array &$submittedData,
        \TYPO3\CMS\Scheduler\Controller\SchedulerModuleController $schedulerModule
    ) {
        $isValid = true;

        if (!MathUtility::canBeInterpretedAsInteger($submittedData['slub_events']['storagePid'])) {
            $isValid = false;
            $this->addMessage(
                $GLOBALS['LANG']->sL('LLL:EXT:slub_events/Resources/Private/Language/locallang.xlf:tasks.cleanup.invalidStoragePid') . ': ' . $submittedData['slub_events']['cleanupDays'],
                \TYPO3\CMS\Core\Messaging\FlashMessage::ERROR
            );
        }

        if (!\TYPO3\CMS\Core\Utility\GeneralUtility::validEmail($submittedData['slub_events']['senderEmailAddress'])) {
            $isValid = false;
            $this->addMessage($GLOBALS['LANG']->sL('LLL:EXT:slub_events/Resources/Private/Language/locallang.xlf:tasks.statistics.invalidEmail'),
                FlashMessage::ERROR);
        }

        return $isValid;
    }

    /**
     * This method is used to save any additional input into the current task object
     * if the task class matches.
     *
     * @param array                                  $submittedData Array containing the data submitted by the user
     * @param \TYPO3\CMS\Scheduler\Task\AbstractTask $task          Reference to the current task object
     *
     * @return void
     */
    public function saveAdditionalFields(array $submittedData, \TYPO3\CMS\Scheduler\Task\AbstractTask $task)
    {
        /** @var $task CheckeventTask */
        $task->storagePid = $submittedData['slub_events']['storagePid'];
        $task->senderEmailAddress = $submittedData['slub_events']['senderEmailAddress'];
        $task->language = $submittedData['slub_events']['language'];
    }
}
