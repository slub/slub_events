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
 *
 *
 * @package slub_events
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */


class Tx_SlubEvents_Controller_FormController extends Tx_SlubEvents_Controller_AbstractController {


	/**
	 * @var Tx_SlubEvents_Domain_Repository_EventRepository
	 */
	protected $eventRepository;

	/**
	 * Initializes the current action
	 *
	 * @return void
	 */
	public function initializeAction() {
		$this->eventRepository = t3lib_div::makeInstance('Tx_SlubEvents_Domain_Repository_EventRepository');
	}
	
	/**
	 * get the event id from GET parameter
	 * 
	 * @param void
	 * @return string
	 */
	public function fillEventId() {

		$event = t3lib_div::_GP('tx_slubevents_eventsubscribe');
		
		return $event['event'];
	
	}

	/**
	 * @param void
	 * @return string The rendered view
	 */
	public function fillEventName() {
	
		$this->initializeAction();
		$name = $this->eventRepository->findByUid($this->fillEventId());
		t3lib_utility_Debug::debug($name, 'fillEventName: name');
		return $name->getTitle();
	}
	
	/**
	 * @param void
	 * @return array The rendered view
	 */
	public function fillEvent() {
	
		$this->initializeAction();
		$name = $this->eventRepository->findByUid($this->fillEventId());

		$event['name'] = $name->getTitle();
		$event['date'] = $name->startDate();
		//~ t3lib_utility_Debug::debug($name, 'fillEventName: name');
		return $event;
	}
}

?>
