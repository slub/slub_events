<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Alexander Bigga <alexander.bigga@slub-dresden.de>, SLUB Dresden
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
class Tx_SlubEvents_Controller_AbstractController extends Tx_Extbase_MVC_Controller_ActionController {

	/**
	 * eventRepository
	 *
	 * @var Tx_SlubEvents_Domain_Repository_EventRepository
	 * @inject
	 */
	protected $eventRepository;

	/**
	 * categoryRepository
	 *
	 * @var Tx_SlubEvents_Domain_Repository_CategoryRepository
	 * @inject
	 */
	protected $categoryRepository;

	/**
	 * subscriberRepository
	 *
	 * @var Tx_SlubEvents_Domain_Repository_SubscriberRepository
	 * @inject
	 */
	protected $subscriberRepository;

	/**
	 * @var Tx_Extbase_Configuration_ConfigurationManagerInterface
	*/
	protected $configurationManager;

	/**
	 * injectConfigurationManager
	 *
	 * @param Tx_Extbase_Configuration_ConfigurationManagerInterface $configurationManager
	 * @return void
	*/
	public function injectConfigurationManager(Tx_Extbase_Configuration_ConfigurationManagerInterface $configurationManager) {
		$this->configurationManager = $configurationManager;

		$this->contentObj = $this->configurationManager->getContentObject();
		$this->settings = $this->configurationManager->getConfiguration(Tx_Extbase_Configuration_ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS);

		// merge the storagePid into settings for the cache tags
		$frameworkConfiguration = $this->configurationManager->getConfiguration(Tx_Extbase_Configuration_ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
		$this->settings['storagePid'] = $frameworkConfiguration['persistence']['storagePid'];
	}

	/**
	 * Get session data
	 *
	 * @return
	 */
	public function getSessionData($key) {

		return $GLOBALS["TSFE"]->fe_user->getKey("ses", $key);

	}

	 /**
	 * initializeAction
	 *
	 * @return
	 */
	//~ protected function initializeAction() {
//~
		//~ if (TYPO3_MODE === 'BE') {
			//~ global $BE_USER;
//~
			//~ // TYPO3 doesn't set locales for backend-users --> so do it manually like this...
			//~ // is needed especially with strftime
			//~ switch ($BE_USER->uc['lang']) {
				//~ case 'en': setlocale(LC_ALL, 'en_GB.utf8');
					//~ break;
				//~ case 'de': setlocale(LC_ALL, 'de_DE.utf8');
					//~ break;
			//~ }
		//~ }
	//~ }

	/**
	 * Safely gets Parameters from request
	 * if they exist
	 *
	 * @param string $parameterName
	 * @return *
	 */
	protected function getParametersSafely($parameterName) {
		if($this->request->hasArgument( $parameterName )){
			return $this->request->getArgument( $parameterName );
		}
		return NULL;
	}

}
?>
