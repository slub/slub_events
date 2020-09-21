<?php
namespace Slub\SlubEvents\Helper\Form\Element;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2020 Alexander Bigga <alexander.bigga@slub-dresden.de>
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

use TYPO3\CMS\Backend\Form\Element\AbstractFormElement;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class RecurringEventsElement extends AbstractFormElement
{
    public function render()
    {
        // Custom TCA properties and other data can be found in $this->data, for example the above
        // parameters are available in $this->data['parameterArray']['fieldConf']['config']['parameters']
        $result = $this->initializeResultArray();

        $objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\ObjectManager::class);
        $configurationManager = $objectManager->get(\TYPO3\CMS\Extbase\Configuration\ConfigurationManager::class);

        $configurationArray = [
            'persistence' => [
                'storagePid' => $this->data['databaseRow']['pid'],
            ],
        ];
        $configurationManager->setConfiguration($configurationArray);

        $eventRepository = $objectManager->get(\Slub\SlubEvents\Domain\Repository\EventRepository::class);

        $childEvents = $eventRepository->findFutureByParent($this->data['databaseRow']['uid']);

        $parentEvent = $eventRepository->findOneByUidIncludeHidden($this->data['databaseRow']['uid']);

        $output .= '<script>require(["TYPO3/CMS/Recordlist/Tooltip"]);</script>
        ';
        $output .= '<h4>'. LocalizationUtility::translate(
            'tx_slubevents_domain_model_event.recurring',
            'slub_events').'</h4>';

        if ($this->data['databaseRow']['hidden'] == 1) {
            $output .= '<div class="alert alert-warning">'.LocalizationUtility::translate(
                'tx_slubevents_domain_model_event.recurring.event_hidden',
                'slub_events');
        } else {
            $output .= '<div class="alert alert-success">'.LocalizationUtility::translate(
                'tx_slubevents_domain_model_event.recurring_parent',
                'slub_events');
        }
        $output .= ' <br /><strong>' . strftime('%A, %d.%m.%Y %H:%M', $parentEvent->getStartDateTime()->getTimestamp())
        .'</strong></div>';

        if ($childEvents && count($childEvents)>0) {

            $output .= '<p class="alert alert-info">'.LocalizationUtility::translate(
              'tx_slubevents_domain_model_event.only_future_events',
              'slub_events').'</p>';
            $output .= '<div class="table-fit">';
            $output .= '<table data-table="'.$table.'" class="table table-striped table-hover">';

            $iconHelper = $objectManager->get(\Slub\SlubEvents\Helper\IconsHelper::class);
            foreach ($childEvents as $childEvent) {
              $output .= '<tr class="t3js-entity" data-table="tx_slubevents_domain_model_event" title="id='.$childEvent->getUid().'" data-uid="'.$childEvent->getUid().'" style="opacity: 1;">';
              $output .= $iconHelper->getHiddenRecordIcon('tx_slubevents_domain_model_event', $childEvent->getUid(), $childEvent->getHidden());
              $output .= '<td class="col-title col-responsive nowrap">'.strftime('%A, %d.%m.%Y %H:%M', $childEvent->getStartDateTime()->getTimestamp()).'</td>';
              $output .= $iconHelper->getHiddenCheckbox('tx_slubevents_domain_model_event', $childEvent->getUid(), $childEvent->getHidden());
              $output .= '</tr>';
            }
            $output .= '</table>';
            $output .= '</div>';
        } else {
            $output .= '<div class="alert alert-warning">'.LocalizationUtility::translate(
                'tx_slubevents_domain_model_event.recurring.no_future_children',
                'slub_events').'</div>'
                ;
        }

        $result['html'] = $output;

        return $result;
    }
}