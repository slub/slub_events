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

use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

use Slub\SlubEvents\Helper\IconsHelper;

/**
 * This hook extends the tcemain class.
 * It preselects the author field with the current be_user id.
 *
 * @author    Alexander Bigga <alexander.bigga@slub-dresden.de>
 */
class Tceforms
{

    public function getMainFields_preProcess($table, &$row, $tceform)
    {
        if ($table == 'tx_slubevents_domain_model_event') {

            if ($row['author'] == 0 || empty($row['author'])) {
                $row['author'] = $GLOBALS['BE_USER']->user['uid'];
            }

            if (empty($row['contact_name'])) {
                $row['contact_name'] = $GLOBALS['BE_USER']->user['realName'];
            }

            if (empty($row['contact_email'])) {
                $row['contact_email'] = $GLOBALS['BE_USER']->user['email'];
            }

            if (empty($row['end_date_time_select']) && empty($row['end_date_time'])) {
                $row['end_date_time_select'] = 60;
            }

            if (empty($row['sub_end_date_time_select']) && empty($row['sub_end_date_time'])) {
                $row['sub_end_date_time_select'] = 1440;
            }
        }
    }

    public function getSingleField_preProcess($table, $field, &$row, $altName, $palette, $extra, $pal, &$pObj)
    {
        if ($table == 'tx_slubevents_domain_model_event') {

        }
    }

    public function recurring_options($PA, $fObj)
    {
      $recurring_options = unserialize($PA['itemFormElValue']);

      $startDateTime = $PA['row']['start_date_time'];

      $week = [
        1 => strftime("%A", strtotime('last Monday')),
        2 => strftime("%A", strtotime('last Tuesday')),
        3 => strftime("%A", strtotime('last Wednesday')),
        4 => strftime("%A", strtotime('last Thursday')),
        5 => strftime("%A", strtotime('last Friday')),
        6 => strftime("%A", strtotime('last Saturday')),
        7 => strftime("%A", strtotime('last Sunday')),
      ];

      // Weekday Settings ------
      if (!is_array($recurring_options['weekday'])) {
          // initialize empty array if new recurring settings
          $recurring_options['weekday'] = [];
      }
      $formField .= '<h4>'. LocalizationUtility::translate(
          'tx_slubevents_domain_model_event.recurring_options.interval.days',
          'slub_events').'</h4>';
      $formField .= '<div class="btn-group" data-toggle="buttons">';

      for ($i=1; $i<8; $i++) {
        $disabled = FALSE;
        if (strftime("%u", $startDateTime) == $i) {
            $active = 'active';
            $checked = 'checked="checked"';
            $disabled = TRUE;
        } else if (in_array($i, $recurring_options['weekday'])) {
          $active = 'active';
          $checked = 'checked="checked"';
        } else {
          $active = '';
          $checked = '';
        }
        $formField .= '<label for="weekday-'.$i.'" class="btn btn-primary '.$active.' '.($disabled ? 'disabled' : '').'">';

        if ($disabled) {
            // send the current value as hidden field and show the checkbox as disabled to the user
           $formField .= '<input type="hidden" name="' . $PA['itemFormElName'] . '[weekday][]" value="' . $i . '" />';
        }
        $formField .= '<input type="checkbox" name="' . $PA['itemFormElName'] . '[weekday][]"';
        $formField .= ' value="' . $i . '" ' . $checked;
        if ($disabled) {
            $formField .= ' readonly disabled';
        }
        $formField .= ' onchange="' . htmlspecialchars(implode('', $PA['fieldChangeFunc'])) . '"';
        $formField .= $PA['onFocus'];
        $formField .= ' />';
        $formField .= $week[$i] . '</label>';
      }
      $formField .= '</div>';

      // Interval Settings ------
      if (empty($recurring_options['interval'])) {
          // initialize empty array if new recurring settings
          $recurring_options['interval'] = 'weekly';
      }
      $formField .= '<h4>'. LocalizationUtility::translate(
          'tx_slubevents_domain_model_event.recurring_options.interval',
          'slub_events').'</h4>';
      $formField .= '<div class="btn-group" data-toggle="buttons">';

      // ---weekly
      if ($recurring_options['interval'] == 'weekly') {
        $active = 'active';
        $checked = 'checked="checked"';
      } else {
        $active = '';
        $checked = '';
      }
      $formField .= '<label for="interval-weekly" class="btn btn-primary '.$active.'">';
      $formField .= '<input type="radio" id="interval-weekly" name="' . $PA['itemFormElName'] . '[interval]"';
      $formField .= ' value="weekly" '.$checked;
      $formField .= ' onchange="' . htmlspecialchars(implode('', $PA['fieldChangeFunc'])) . '"';
      $formField .= $PA['onFocus'];
      $formField .= ' />';
      $formField .= LocalizationUtility::translate(
          'tx_slubevents_domain_model_event.recurring_options.interval.weekly',
          'slub_events') . '</label>';

      // --- 2weekly
      if ($recurring_options['interval'] == '2weekly') {
        $active = 'active';
        $checked = 'checked="checked"';
      } else {
        $active = '';
        $checked = '';
      }
      $formField .= '<label for="interval-2weekly" class="btn btn-primary '.$active.'">';
      $formField .= '<input type="radio" id="interval-2weekly" name="' . $PA['itemFormElName'] . '[interval]"';
      $formField .= ' value="2weekly" '.$checked;
      $formField .= ' onchange="' . htmlspecialchars(implode('', $PA['fieldChangeFunc'])) . '"';
      $formField .= $PA['onFocus'];
      $formField .= ' />';
      $formField .= LocalizationUtility::translate(
          'tx_slubevents_domain_model_event.recurring_options.interval.2weekly',
          'slub_events') . '</label>';

      // --- 4weekly
      if ($recurring_options['interval'] == '4weekly') {
        $active = 'active';
        $checked = 'checked="checked"';
      } else {
        $active = '';
        $checked = '';
      }
      $formField .= '<label for="interval-4weekly" class="btn btn-primary '.$active.'">';
      $formField .= '<input type="radio" id="interval-4weekly" name="' . $PA['itemFormElName'] . '[interval]"';
      $formField .= ' value="4weekly" '.$checked;
      $formField .= ' onchange="' . htmlspecialchars(implode('', $PA['fieldChangeFunc'])) . '"';
      $formField .= $PA['onFocus'];
      $formField .= ' />';
      $formField .= LocalizationUtility::translate(
          'tx_slubevents_domain_model_event.recurring_options.interval.4weekly',
          'slub_events') . '</label>';

      // --- monthly
      if ($recurring_options['interval'] == 'monthly') {
        $active = 'active';
        $checked = 'checked="checked"';
      } else {
        $active = '';
        $checked = '';
      }
      $formField .= '<label for="interval-monthly" class="btn btn-primary '.$active.'">';
      $formField .= '<input type="radio" id="interval-monthly" name="' . $PA['itemFormElName'] . '[interval]"';
      $formField .= ' value="monthly" '.$checked;
      $formField .= ' onchange="' . htmlspecialchars(implode('', $PA['fieldChangeFunc'])) . '"';
      $formField .= $PA['onFocus'];
      $formField .= ' />';
      $formField .= LocalizationUtility::translate(
          'tx_slubevents_domain_model_event.recurring_options.interval.monthly',
          'slub_events') . '</label>';

      // --- yearly
      if ($recurring_options['interval'] == 'yearly') {
        $active = 'active';
        $checked = 'checked="checked"';
      } else {
        $active = '';
        $checked = '';
      }
      $formField .= '<label for="interval-yearly" class="btn btn-primary '.$active.'">';
      $formField .= '<input type="radio" id="interval-yearly" name="' . $PA['itemFormElName'] . '[interval]"';
      $formField .= ' value="yearly" '.$checked;
      $formField .= ' onchange="' . htmlspecialchars(implode('', $PA['fieldChangeFunc'])) . '"';
      $formField .= $PA['onFocus'];
      $formField .= ' />';
      $formField .= LocalizationUtility::translate(
          'tx_slubevents_domain_model_event.recurring_options.interval.yearly',
          'slub_events') . '</label>';
      $formField .= '</div>';

      return $formField;
    }

    /*
     * format the parent event string in TCA form
     */
    public function eventParentString($PA, $fObj)
    {
      $parentEventRow = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid, title', 'tx_slubevents_domain_model_event', 'uid=' . (int)$PA['row']['parent'])->fetch_assoc();
      return $this->getEditLink('tx_slubevents_domain_model_event', $parentEventRow);
    }

    /*
     * list all recurring childevents
     */
    public function recurring_events($PA, $fObj)
    {
        if ($PA['table'] == 'tx_slubevents_domain_model_event') {

            $objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\ObjectManager::class);
            $configurationManager = $objectManager->get(\TYPO3\CMS\Extbase\Configuration\ConfigurationManager::class);

            $configurationArray = [
                'persistence' => [
                    'storagePid' => $PA['row']['pid'],
                ],
            ];
            $configurationManager->setConfiguration($configurationArray);

            $eventRepository = $objectManager->get(\Slub\SlubEvents\Domain\Repository\EventRepository::class);

            $childEvents = $eventRepository->findFutureByParent($PA['row']['uid']);

            $output = '<h4>'. LocalizationUtility::translate(
                'tx_slubevents_domain_model_event.recurring',
                'slub_events').'</h4>';
            $output .= '<ul>';

            $iconHelper = $objectManager->get(\Slub\SlubEvents\Helper\IconsHelper::class);
            foreach ($childEvents as $childEvent) {
              if ($childEvent->getHidden() === TRUE) {
                  $checked = '';
              } else {
                  $checked = 'checked="checked"';
              }

              if ($childEvent->getHidden() == TRUE) {
                  $classHidden = ' class="list-group-item list-group-item-warning" style="text-decoration: line-through;"';
              } else {
                  $classHidden = ' class="list-group-item list-group-item-success"';
              }
              $output .= '<li' . $classHidden . '>';
              $output .= $iconHelper->getHideIcon('tx_slubevents_domain_model_event', $childEvent->getUid(), $childEvent->getHidden());
              $output .= ' ' . strftime('%A, %d.%m.%Y %H:%M', $childEvent->getStartDateTime()->getTimestamp());
              $output .= '</li>';

            }
            $output .= '</ul>';

            return $output;
        }
    }

    /**
     * /**
     * Returns the Edit Icon with link
     *
     * @param string $table Table name
     * @param array $row Data row
     * @return string html output
     */
    protected function getEditLink($table, array $row)
    {
        $params .= '&edit[' . $table . '][' . $row['uid'] . ']=edit';
        $title = LocalizationUtility::translate('be.editEvent', 'slub_events',
                $arguments = null) . ' ' .
                LocalizationUtility::translate('tx_slubevents_domain_model_event.recurring', 'slub_events',
                $arguments = null) . ' `' . $row['title'] . '`';
        $link = '<a href="#" class="btn btn-info" onclick="' . htmlspecialchars(BackendUtility::editOnClick($params, $this->backPath)) . '" title="' . $title . '">' .
                '[' . $row['uid'] . '] ' . $row['title'] .
            '</a>';

        return $link;
    }

}
