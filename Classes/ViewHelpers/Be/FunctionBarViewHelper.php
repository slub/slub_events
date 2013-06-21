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

class Tx_SlubEvents_ViewHelpers_Be_FunctionBarViewHelper extends Tx_Fluid_ViewHelpers_Be_AbstractBackendViewHelper {

	/**
	 * @var Tx_Extbase_Configuration_ConfigurationManagerInterface
	 */
	protected $configurationManager;

	/**
	 * @param Tx_Extbase_Configuration_ConfigurationManagerInterface $configurationManager
	 * @return void
	 */
	public function injectConfigurationManager(Tx_Extbase_Configuration_ConfigurationManagerInterface $configurationManager) {
		$this->configurationManager = $configurationManager;
	}

	/**
	 * Returns the Edit Icon with link
	 *
	 * @param string $table Table name
	 * @param array $row Data row
	 * @return string html output
	 */
	protected function getEditIcon($table, array $row){

		// back GET parameter have to be like this with '%26' instead of '&':
		//~ $params = '%26selectedStartDateStamp='.$row['selectedStartDateStamp'];
		//~ $params .= '%26selectedCategories='.$row['selectedCategories'];
		$params .= '&edit[' . $table . '][' . $row['uid'] . ']=edit';
		$title = Tx_Extbase_Utility_Localization::translate('be.editEvent', 'slub_events', $arguments=NULL) . ' ' . $row['uid'] . ': ' . $row['title'] ;
		$icon = '<a href="#" onclick="' . htmlspecialchars(t3lib_BEfunc::editOnClick($params, $this->backPath, -1)) . '" title="' . $title . '">' .
							t3lib_iconWorks::getSpriteIcon('actions-document-open') .
						'</a>';

		return $icon;
	}

	/**
	 * Returns the New Icon with link
	 *
	 * @param string $table Table name
	 * @param array $row Data row
	 * @return string html output
	 */
	protected function getNewIcon($table, array $row){

		$params .= '&edit[' . $table . '][' . $row['storagePid'] . ']=new';
		$title = Tx_Extbase_Utility_Localization::translate('be.newEvent', 'slub_events', $arguments=NULL);
		$icon = '<a href="#" onclick="' . htmlspecialchars(t3lib_BEfunc::editOnClick($params, $this->backPath, -1)) . '" title="' . $title . '">' .
							t3lib_iconWorks::getSpriteIcon('actions-document-new') .
						'</a>';

		return $icon;
	}

	/**
	 * Returns the New Icon with link
	 *
	 * @param string $table Table name
	 * @param array $row Data row
	 * @return string html output
	 */
	protected function getHideIcon($table, array $row){

		$doc = $this->getDocInstance();
		if ($row['hidden'])   {
				$title = Tx_Extbase_Utility_Localization::translate('be.unhideEvent', 'slub_events', $arguments=NULL);
				$params = '&data[' . $table . '][' . $row['uid'] . '][hidden]=0';
				$icon = '<a href="#" onclick="' . htmlspecialchars('return jumpToUrl(\'' . $doc->issueCommand($params, -1) . '\');') . '" title="' . $title . '">' .
									t3lib_iconWorks::getSpriteIcon('actions-edit-unhide') .
							'</a>';
				// Hide
		} else {
				$title = Tx_Extbase_Utility_Localization::translate('be.hideEvent', 'slub_events', $arguments=NULL);
				$params = '&data[' . $table . '][' . $row['uid'] . '][hidden]=1';
				$icon = '<a href="#" onclick="' . htmlspecialchars('return jumpToUrl(\'' . $doc->issueCommand($params, -1) . '\');') . '" title="' . $title . '">'.
									t3lib_iconWorks::getSpriteIcon('actions-edit-hide') .
							'</a>';
		}
		return $icon;
	}

	/**
	 * Returns the Genius Bar Icon
	 *
	 * @param event Tx_SlubEvents_Domain_Model_Event
	 * @return string html output
	 */
	protected function getGeniusBarIcon(Tx_SlubEvents_Domain_Model_Event $event){

		if ($event->getGeniusBar())
			return '<span title="Wissensbar-Termin" class="geniusbar">W&nbsp;</span>';
	}

	/**
	 * Returns the Datepicker img
	 *
	 * @param event Tx_SlubEvents_Domain_Model_Event
	 * @return string html output
	 */
	protected function getDatePickerIcon() {

			return t3lib_iconWorks::getSpriteIcon(
                                'actions-edit-pick-date',
                                array(
                                        'style' => 'cursor:pointer;',
                                        'id' => 'picker-tceforms-datefield-1'
                                )
                        );

			return '<img' . t3lib_iconWorks::skinImg($this->backPath, 'gfx/datepicker.gif', '', 0) . ' style="cursor:pointer; vertical-align:middle;" alt=""' . ' id="picker-tceforms-datetimefield-1" />';
	}


	/**
	 * Renders a record list as known from the TYPO3 list module
	 * Note: This feature is experimental!
	 *
	 * @param icon string
	 * @param event Tx_SlubEvents_Domain_Model_Event
	 * @return string the rendered record list
	 */
	public function render($icon = 'edit', Tx_SlubEvents_Domain_Model_Event $event = NULL) {

		if ($event !== NULL) {
			$row['uid'] = $event->getUid();
			$row['title'] = $event->getTitle();
			$row['hidden'] = $event->getHidden();
		}

		$frameworkConfiguration = $this->configurationManager->getConfiguration(Tx_Extbase_Configuration_ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
		$row['storagePid'] = $frameworkConfiguration['persistence']['storagePid'];

		switch ($icon) {
			case 'new': 	$content = $this->getNewIcon('tx_slubevents_domain_model_event', $row);
				break;
			case 'edit': 	$content = $this->getEditIcon('tx_slubevents_domain_model_event', $row);
				break;
			case 'hide': 	$content = $this->getHideIcon('tx_slubevents_domain_model_event', $row);
				break;
			case 'geniusbar': 	$content = $this->getGeniusBarIcon($event);
				break;
			case 'datepicker': 	$content = $this->getDatePickerIcon();
				break;
		}

		return $content;

	}
}
?>
