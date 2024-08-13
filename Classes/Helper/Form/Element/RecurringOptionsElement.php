<?php
namespace Slub\SlubEvents\Helper\Form\Element;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2020 Alexander Bigga <typo3@slub-dresden.de>
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

class RecurringOptionsElement extends AbstractFormElement
{
    public function render()
    {
        // Custom TCA properties and other data can be found in $this->data, for example the above
        // parameters are available in $this->data['parameterArray']['fieldConf']['config']['parameters']
        $result = $this->initializeResultArray();

        $recurring_options = unserialize($this->data['parameterArray']['itemFormElValue']);

        $startDateTime = $this->data['databaseRow']['start_date_time'];

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
             $formField .= '<input type="hidden" name="' . $this->data['parameterArray']['itemFormElName'] . '[weekday][]" value="' . $i . '" />';
          }
          $formField .= '<input type="checkbox" name="' . $this->data['parameterArray']['itemFormElName'] . '[weekday][]"';
          $formField .= ' value="' . $i . '" ' . $checked;
          if ($disabled) {
              $formField .= ' readonly disabled';
          }
          $formField .= ' onchange="' . htmlspecialchars(implode('', $this->data['parameterArray']['fieldChangeFunc'])) . '"';
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
        $formField .= '<input type="radio" id="interval-weekly" name="' . $this->data['parameterArray']['itemFormElName'] . '[interval]"';
        $formField .= ' value="weekly" '.$checked;
        $formField .= ' onchange="' . htmlspecialchars(implode('', $this->data['parameterArray']['fieldChangeFunc'])) . '"';
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
        $formField .= '<input type="radio" id="interval-2weekly" name="' . $this->data['parameterArray']['itemFormElName'] . '[interval]"';
        $formField .= ' value="2weekly" '.$checked;
        $formField .= ' onchange="' . htmlspecialchars(implode('', $this->data['parameterArray']['fieldChangeFunc'])) . '"';
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
        $formField .= '<input type="radio" id="interval-4weekly" name="' . $this->data['parameterArray']['itemFormElName'] . '[interval]"';
        $formField .= ' value="4weekly" '.$checked;
        $formField .= ' onchange="' . htmlspecialchars(implode('', $this->data['parameterArray']['fieldChangeFunc'])) . '"';
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
        $formField .= '<input type="radio" id="interval-monthly" name="' . $this->data['parameterArray']['itemFormElName'] . '[interval]"';
        $formField .= ' value="monthly" '.$checked;
        $formField .= ' onchange="' . htmlspecialchars(implode('', $this->data['parameterArray']['fieldChangeFunc'])) . '"';
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
        $formField .= '<input type="radio" id="interval-yearly" name="' . $this->data['parameterArray']['itemFormElName'] . '[interval]"';
        $formField .= ' value="yearly" '.$checked;
        $formField .= ' onchange="' . htmlspecialchars(implode('', $this->data['parameterArray']['fieldChangeFunc'])) . '"';
        $formField .= ' />';
        $formField .= LocalizationUtility::translate(
            'tx_slubevents_domain_model_event.recurring_options.interval.yearly',
            'slub_events') . '</label>';
        $formField .= '</div>';

        $result['html'] = $formField;

        return $result;
    }
}
