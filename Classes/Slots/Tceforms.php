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

      $week = [
        1 => strftime("%a", strtotime('last Monday')),
        2 => strftime("%a", strtotime('last Tuesday')),
        3 => strftime("%a", strtotime('last Wednesday')),
        4 => strftime("%a", strtotime('last Thursday')),
        5 => strftime("%a", strtotime('last Friday')),
        6 => strftime("%a", strtotime('last Saturday')),
        7 => strftime("%a", strtotime('last Sunday')),
      ];
      $formField .= '<h4>'. LocalizationUtility::translate(
          'tx_slubevents_domain_model_event.recurring_options.interval.days',
          'slub_events').'</h4>';
      $formField .= '<div class="btn-group" data-toggle="buttons">';

      for ($i=1; $i<8; $i++) {
        if (is_array($recurring_options['weekday']) && in_array($i, $recurring_options['weekday'])) {
          $active = 'active';
          $checked = 'checked="checked"';
        } else {
          $active = '';
          $checked = '';
        }
        $formField .= '<label for="weekday-'.$i.'" class="btn btn-primary '.$active.'">';
        $formField .= '<input type="checkbox"  name="' . $PA['itemFormElName'] . '[weekday][]"';
        $formField .= ' value="'.$i.'" '.$checked;
        $formField .= ' onchange="' . htmlspecialchars(implode('', $PA['fieldChangeFunc'])) . '"';
        $formField .= $PA['onFocus'];
        $formField .= ' />';
        $formField .= $week[$i] . '</label>';
        //$formField .= '<label for="weekday-'.$i.'" class="btn btn-primary">' . $week[$i] . '</label>';
      }

      $formField .= '</div>';
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
      $formField .= '<label for="interval-weekly" class="btn btn-primary '.$active.'">'.LocalizationUtility::translate(
          'tx_slubevents_domain_model_event.recurring_options.interval.weekly',
          'slub_events');
      $formField .= '<input type="radio" id="interval-weekly" name="' . $PA['itemFormElName'] . '[interval]"';
      $formField .= ' value="weekly" '.$checked;
      $formField .= ' onchange="' . htmlspecialchars(implode('', $PA['fieldChangeFunc'])) . '"';
      $formField .= $PA['onFocus'];
      $formField .= ' />';
      $formField .= '</label>';

      // --- 2weekly
      if ($recurring_options['interval'] == '2weekly') {
        $active = 'active';
        $checked = 'checked="checked"';
      } else {
        $active = '';
        $checked = '';
      }
      $formField .= '<label for="interval-2weekly" class="btn btn-primary '.$active.'">'.LocalizationUtility::translate(
          'tx_slubevents_domain_model_event.recurring_options.interval.2weekly',
          'slub_events'
      );
      $formField .= '<input type="radio" id="interval-2weekly" name="' . $PA['itemFormElName'] . '[interval]"';
      $formField .= ' value="2weekly" '.$checked;
      $formField .= ' onchange="' . htmlspecialchars(implode('', $PA['fieldChangeFunc'])) . '"';
      $formField .= $PA['onFocus'];
      $formField .= ' />';
      $formField .= '</label>';


      // --- 4weekly
      if ($recurring_options['interval'] == '4weekly') {
        $active = 'active';
        $checked = 'checked="checked"';
      } else {
        $active = '';
        $checked = '';
      }
      $formField .= '<label for="interval-4weekly" class="btn btn-primary '.$active.'">'.LocalizationUtility::translate(
          'tx_slubevents_domain_model_event.recurring_options.interval.4weekly',
          'slub_events'
      );
      $formField .= '<input type="radio" id="interval-4weekly" name="' . $PA['itemFormElName'] . '[interval]"';
      $formField .= ' value="4weekly" '.$checked;
      $formField .= ' onchange="' . htmlspecialchars(implode('', $PA['fieldChangeFunc'])) . '"';
      $formField .= $PA['onFocus'];
      $formField .= ' />';
      $formField .= '</label>';

      // --- monthly
      if ($recurring_options['interval'] == 'monthly') {
        $active = 'active';
        $checked = 'checked="checked"';
      } else {
        $active = '';
        $checked = '';
      }
      $formField .= '<label for="interval-monthly" class="btn btn-primary '.$active.'">'.LocalizationUtility::translate(
          'tx_slubevents_domain_model_event.recurring_options.interval.monthly',
          'slub_events'
      );
      $formField .= '<input type="radio" id="interval-monthly" name="' . $PA['itemFormElName'] . '[interval]"';
      $formField .= ' value="monthly" '.$checked;
      $formField .= ' onchange="' . htmlspecialchars(implode('', $PA['fieldChangeFunc'])) . '"';
      $formField .= $PA['onFocus'];
      $formField .= ' />';
      $formField .= '</label>';

      // --- yearly
      if ($recurring_options['interval'] == 'yearly') {
        $active = 'active';
        $checked = 'checked="checked"';
      } else {
        $active = '';
        $checked = '';
      }
      $formField .= '<label for="interval-yearly" class="btn btn-primary '.$active.'">'.LocalizationUtility::translate(
          'tx_slubevents_domain_model_event.recurring_options.interval.weekly',
          'slub_events'
      );
      $formField .= '<input type="radio" id="interval-yearly" name="' . $PA['itemFormElName'] . '[interval]"';
      $formField .= ' value="yearly" '.$checked;
      $formField .= ' onchange="' . htmlspecialchars(implode('', $PA['fieldChangeFunc'])) . '"';
      $formField .= $PA['onFocus'];
      $formField .= ' />';
      $formField .= '</label>';
      $formField .= '</div>';

      return $formField;
    }
}
