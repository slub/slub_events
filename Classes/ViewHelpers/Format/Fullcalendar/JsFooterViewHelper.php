<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Alexander Bigga <alexander.bigga@slub-dresden.de>, SLUB Dresden
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
 * Add Fullcalendar specific JS code
 *
 * = Examples =
 *

 *
 * @api
 * @scope prototype
 */
class Tx_SlubEvents_ViewHelpers_Format_Fullcalendar_JsFooterViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {

	/**
	 * Looks for already checked form from last request
	 *
	 * @param array $categories
	 * @param array $settings
	 * @param string $link
	 * @return string
	 * @api
	 */
	public function render($categories = NULL, $settings = NULL, $link = NULL) {

		// get field configuration
			$js1 = '<script>';

			$js1 .= "$(document).ready(function() {";
			$js1 .= "$('#calendar').fullCalendar({";
			if (! empty($settings['fullCalendarJS']))
				$js1 .= $settings['fullCalendarJS'];

			$js1 .= "events: {
					url: '?eID=slubCal',
					data: function() {
						var eventurl = '';
						var disurl = '';
						$('.slubevents-category input:checked').each(function() {
							var cal = $(this).attr('id').split(\"-\")[2];
							eventurl = eventurl + ',' + cal;
						});
						$('.slubevents-discipline input:checked').each(function() {
							var cal = $(this).attr('id').split(\"-\")[2];
							disurl = disurl + ',' + cal;
						});
						return {
							categories: eventurl,
							disciplines: disurl,
							link: '" . urlencode($link) . "',
							detailPid: '" . $settings['pidDetails'] . "'
						};
					}
			},";
			// add event name to title attribute on mouseover
			$js1 .= "eventMouseover: function(event, jsEvent, view) {
							$(jsEvent.target).attr('title', moment(event.start).format('LT') + ' - ' + moment(event.end).format('LT') + ' ' + event.title);
					},";

			$js1 .= "eventRender: function(event, element, view) {
						if (view.name === 'agendaDay' && event.freePlaces != '0') {
							element.find('.fc-event-title')
								.after('<div class=\"fc-event-freeplaces\">' + event.freePlaces + '</div>');
						}
					},";
			// show/hide div #loading
			$js1 .= "loading: function(bool) {
						if (bool) {
							$('#loading').show();
						} else {
							$('#loading').hide();
						}
					},";

			// close fullCalendar()
			$js1 .= '});';
			// close $(document).ready()
			$js1 .= '});';

			// add the eventcatX variables at the end
			$js1 .= $js_eventcat;
			$js1 .= '</script>';

			// dirty but working. Has to be called after the <form> and the jqueryvalidation validate()
			// getPagerender() doesn't work in 4.7.x....
			// see: http://forge.typo3.org/issues/22273
			$GLOBALS['TSFE']->additionalFooterData['tx_slub_forms'] .= $js1;

	}


}

?>
