<?php
namespace Slub\SlubEvents\Task;

/***************************************************************
     *  Copyright notice
     *
     *  (c) 2015 Alexander Bigga <alexander.bigga@slub-dresden.de>
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
 * Scheduler Task for Statistics, Additional Field Provider
 *
 *
 *
 * @author    Alexander Bigga <alexander.bigga@slub-dresden.de>
 */
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class StatisticsTaskAdditionalFieldProvider implements \TYPO3\CMS\Scheduler\AdditionalFieldProviderInterface
{

    /**
     * Render additional information fields within the scheduler backend.
     *
     * @param array                                                     $taskInfo        Array information of task to return
     * @param StatisticsTask                                            $task            Task object
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

        if (empty($taskInfo['storagePid'])) {
            if ($schedulerModule->CMD == 'add') {
                $taskInfo['storagePid'] = '';
            } elseif ($schedulerModule->CMD == 'edit') {
                $taskInfo['storagePid'] = $task->getStoragePid();
            } else {
                $taskInfo['storagePid'] = $task->getStoragePid();
            }
        }

        if (empty($taskInfo['senderEmailAddress'])) {
            if ($schedulerModule->CMD == 'add') {
                $taskInfo['senderEmailAddress'] = '';
            } elseif ($schedulerModule->CMD == 'edit') {
                $taskInfo['senderEmailAddress'] = $task->getSenderEmailAddress();
            } else {
                $taskInfo['senderEmailAddress'] = $task->getSenderEmailAddress();
            }
        }

        if (empty($taskInfo['receiverEmailAddress'])) {
            if ($schedulerModule->CMD == 'add') {
                $taskInfo['receiverEmailAddress'] = '';
            } elseif ($schedulerModule->CMD == 'edit') {
                $taskInfo['receiverEmailAddress'] = $task->getReceiverEmailAddress();
            } else {
                $taskInfo['receiverEmailAddress'] = $task->getReceiverEmailAddress();
            }
        }

        $fieldId = 'task_storagePid';
        $fieldCode = '<input type="text" name="tx_scheduler[slub_events][storagePid]" id="' . $fieldId . '" value="' . htmlspecialchars($taskInfo['storagePid']) . '"/>';
        $label = $GLOBALS['LANG']->sL('LLL:EXT:slub_events/Resources/Private/Language/locallang.xlf:tasks.statistics.storagePid');
        $label = BackendUtility::wrapInHelp('slub_events', $fieldId, $label);
        $additionalFields[$fieldId] = [
            'code'  => $fieldCode,
            'label' => $label,
        ];

        $fieldId = 'task_senderEmailAddress';
        $fieldCode = '<input type="text" name="tx_scheduler[slub_events][senderEmailAddress]" id="' . $fieldId . '" value="' . htmlspecialchars($taskInfo['senderEmailAddress']) . '"/>';
        $label = $GLOBALS['LANG']->sL('LLL:EXT:slub_events/Resources/Private/Language/locallang.xlf:tasks.statistics.senderEmailAddress');
        $label = BackendUtility::wrapInHelp('slub_events', $fieldId, $label);
        $additionalFields[$fieldId] = [
            'code'  => $fieldCode,
            'label' => $label,
        ];

        $fieldId = 'task_receiverEmailAddress';
        $fieldCode = '<textarea cols="30" rows="5" name="tx_scheduler[slub_events][receiverEmailAddress]" id="' . $fieldId . '" >';
        if (is_array($taskInfo['receiverEmailAddress'])) {
            foreach ($taskInfo['receiverEmailAddress'] as $id => $emailAdd) {
                if (GeneralUtility::validEmail($emailAdd)) {
                    $fieldCode .= htmlspecialchars($emailAdd) . "\n";
                }
            }
            // remove last newline:
            $fieldCode = trim($fieldCode);
        }
        $fieldCode .= '</textarea>';
        $label = $GLOBALS['LANG']->sL('LLL:EXT:slub_events/Resources/Private/Language/locallang.xlf:tasks.statistics.receiverEmailAddress');
        $label = BackendUtility::wrapInHelp('slub_events', $fieldId, $label);
        $additionalFields[$fieldId] = [
            'code'  => $fieldCode,
            'label' => $label,
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
            $schedulerModule->addMessage(
                $GLOBALS['LANG']->sL('LLL:EXT:slub_events/Resources/Private/Language/locallang.xlf:tasks.statistics.invalidStoragePid') . ': ' . $submittedData['slub_events']['storagePid'],
                \TYPO3\CMS\Core\Messaging\FlashMessage::ERROR
            );
        }

        if (!\TYPO3\CMS\Core\Utility\GeneralUtility::validEmail($submittedData['slub_events']['senderEmailAddress'])) {
            $isValid = false;
            $schedulerModule->addMessage($GLOBALS['LANG']->sL('LLL:EXT:slub_events/Resources/Private/Language/locallang.xlf:tasks.statistics.invalidEmail'),
                \TYPO3\CMS\Core\Messaging\FlashMessage::ERROR);
        }

        if (!empty($submittedData['slub_events']['receiverEmailAddress'])) {
            $emailList = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode('|',
                preg_replace('/[\n,\s]+/', '|', $submittedData['slub_events']['receiverEmailAddress']));
            foreach ($emailList as $emailAdd) {
                if (!\TYPO3\CMS\Core\Utility\GeneralUtility::validEmail($emailAdd)) {
                    $isValid = false;
                    $schedulerModule->addMessage($GLOBALS['LANG']->sL('LLL:EXT:slub_events/Resources/Private/Language/locallang.xlf:tasks.statistics.invalidEmail') . ': ' . $emailAdd,
                        \TYPO3\CMS\Core\Messaging\FlashMessage::ERROR);
                }
            }
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
        /** @var $task StatisticsTask */
        $task->setStoragePid($submittedData['slub_events']['storagePid']);
        $task->setReceiverEmailAddress(\TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',',
            preg_replace('/[\n\s]+/', ',', $submittedData['slub_events']['receiverEmailAddress'])));
        $task->setSenderEmailAddress($submittedData['slub_events']['senderEmailAddress']);
    }
}
