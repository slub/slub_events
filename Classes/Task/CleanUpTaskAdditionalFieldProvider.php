<?php
namespace Slub\SlubEvents\Task;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2020 Alexander Bigga <typo3@slub-dresden.de>, SLUB Dresden
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

use TYPO3\CMS\Scheduler\Controller\SchedulerModuleController;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Scheduler\Task\AbstractTask;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Scheduler\AbstractAdditionalFieldProvider;
use TYPO3\CMS\Scheduler\Task\Enumeration\Action;

/**
 * @package slub_events
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class CleanUpTaskAdditionalFieldProvider extends AbstractAdditionalFieldProvider
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
        SchedulerModuleController $schedulerModule
    ) {
        $additionalFields = [];
        $currentSchedulerModuleAction = $schedulerModule->getCurrentAction();

        if (empty($taskInfo['storagePid'])) {
            if ($currentSchedulerModuleAction->equals(Action::ADD)) {
                $taskInfo['storagePid'] = '';
            } elseif ($currentSchedulerModuleAction->equals(Action::EDIT)) {
                $taskInfo['storagePid'] = $task->getStoragePid();
            } else {
                $taskInfo['storagePid'] = $task->getStoragePid();
            }
        }

        if (empty($taskInfo['cleanupDays'])) {
            if ($currentSchedulerModuleAction->equals(Action::ADD)) {
                $taskInfo['cleanupDays'] = '';
            } elseif ($currentSchedulerModuleAction->equals(Action::EDIT)) {
                $taskInfo['cleanupDays'] = $task->getCleanupDays();
            } else {
                $taskInfo['cleanupDays'] = $task->getCleanupDays();
            }
        }

        if (empty($taskInfo['cleanupDaysEvents'])) {
            if ($currentSchedulerModuleAction->equals(Action::ADD)) {
                $taskInfo['cleanupDaysEvents'] = '';
            } elseif ($currentSchedulerModuleAction->equals(Action::EDIT)) {
                $taskInfo['cleanupDaysEvents'] = $task->getCleanupDaysEvents();
            } else {
                $taskInfo['cleanupDaysEvents'] = $task->getCleanupDaysEvents();
            }
        }

        $fieldId = 'task_storagePid';
        $fieldCode = '<input class="form-control" type="text" name="tx_scheduler[slub_events][storagePid]" id="' . $fieldId . '" value="' . htmlspecialchars($taskInfo['storagePid']) . '"/>';
        $label = $GLOBALS['LANG']->sL('LLL:EXT:slub_events/Resources/Private/Language/locallang.xlf:tasks.cleanup.storagePid');
        $additionalFields[$fieldId] = [
            'code'  => $fieldCode,
            'label' => $label
        ];

        $fieldId = 'task_cleanupDays';
        $fieldCode = '<input class="form-control" type="text" name="tx_scheduler[slub_events][cleanupDays]" id="' . $fieldId . '" value="' . htmlspecialchars($taskInfo['cleanupDays']) . '"/>';
        $label = $GLOBALS['LANG']->sL('LLL:EXT:slub_events/Resources/Private/Language/locallang.xlf:tasks.cleanup.cleanupDays');
        $additionalFields[$fieldId] = [
            'code'  => $fieldCode,
            'label' => $label
        ];

        $fieldId = 'task_cleanupDaysEvents';
        $fieldCode = '<input class="form-control" type="text" name="tx_scheduler[slub_events][cleanupDaysEvents]" id="' . $fieldId . '" value="' . htmlspecialchars($taskInfo['cleanupDaysEvents']) . '"/>';
        $label = $GLOBALS['LANG']->sL('LLL:EXT:slub_events/Resources/Private/Language/locallang.xlf:tasks.cleanup.cleanupDaysEvents');
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
        SchedulerModuleController $schedulerModule
    ) {
        $isValid = true;

        if (!MathUtility::canBeInterpretedAsInteger($submittedData['slub_events']['storagePid'])) {
            $isValid = false;
            $this->addMessage(
                $GLOBALS['LANG']->sL('LLL:EXT:slub_events/Resources/Private/Language/locallang.xlf:tasks.cleanup.invalidStoragePid') . ': ' . $submittedData['slub_events']['cleanupDays'],
                FlashMessage::ERROR
            );
        }

        if (!MathUtility::canBeInterpretedAsInteger($submittedData['slub_events']['cleanupDays'])) {
            $isValid = false;
            $this->addMessage(
                $GLOBALS['LANG']->sL('LLL:EXT:slub_events/Resources/Private/Language/locallang.xlf:tasks.cleanup.invalidCleanupDays') . ': ' . $submittedData['slub_events']['cleanupDays'],
                FlashMessage::ERROR
            );
        }

        if (!MathUtility::canBeInterpretedAsInteger($submittedData['slub_events']['cleanupDaysEvents'])) {
            $isValid = false;
            $this->addMessage(
                $GLOBALS['LANG']->sL('LLL:EXT:slub_events/Resources/Private/Language/locallang.xlf:tasks.cleanup.invalidCleanupDaysEvents') . ': ' . $submittedData['slub_events']['cleanupDaysEvents'],
                FlashMessage::ERROR
            );
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
    public function saveAdditionalFields(array $submittedData, AbstractTask $task)
    {
        /** @var $task CleanUpTask */
        $task->setStoragePid($submittedData['slub_events']['storagePid']);
        $task->setCleanupDays($submittedData['slub_events']['cleanupDays']);
        $task->setCleanupDaysEvents($submittedData['slub_events']['cleanupDaysEvents']);
    }
}
