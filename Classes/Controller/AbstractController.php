<?php
namespace Slub\SlubEvents\Controller;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController as ExtbaseActionController;
use TYPO3\CMS\Extbase\Utility\ArrayUtility;

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
class AbstractController extends ExtbaseActionController
{
    /**
     * eventRepository
     *
     * @var \Slub\SlubEvents\Domain\Repository\EventRepository
     * @inject
     */
    protected $eventRepository;

    /**
     * categoryRepository
     *
     * @var \Slub\SlubEvents\Domain\Repository\CategoryRepository
     * @inject
     */
    protected $categoryRepository;

    /**
     * subscriberRepository
     *
     * @var \Slub\SlubEvents\Domain\Repository\SubscriberRepository
     * @inject
     */
    protected $subscriberRepository;

    /**
     * contactRepository
     *
     * @var \Slub\SlubEvents\Domain\Repository\ContactRepository
     * @inject
     */
    protected $contactRepository;

    /**
     * disciplineRepository
     *
     * @var \Slub\SlubEvents\Domain\Repository\DisciplineRepository
     * @inject
     */
    protected $disciplineRepository;

    /**
     * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
     */
    protected $configurationManager;

    /**
     * injectConfigurationManager
     *
     * @param \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManager
     * @return void
     */
    public function injectConfigurationManager(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManager) {

        $this->configurationManager = $configurationManager;

        $this->contentObj = $this->configurationManager->getContentObject();
        $this->settings = $this->configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS);

        // merge the storagePid into settings for the cache tags
        $frameworkConfiguration = $this->configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
        $this->settings['storagePid'] = $frameworkConfiguration['persistence']['storagePid'];
    }

    /**
     * return the corresponding user GLOBALS for FE/BE
     *
     * @return mixed
     */
    protected function getUserGlobals()
    {
        if (TYPO3_MODE === 'BE') {

            $userGlobals = $GLOBALS['BE_USER'];

        } else if (TYPO3_MODE === 'FE') {

            $userGlobals = $GLOBALS['TSFE']->fe_user;

        }

        return $userGlobals;
    }
    /**
     * Set session data
     *
     * @param string $key
     * @param string $data
     * @param boolean $persist
     */
    public function setSessionData($key, $data, $persist = null)
    {
        $userGlobals = $this->getUserGlobals();

        // write data to user configuration to persist over sessions
        if ($persist === true && TYPO3_MODE === 'BE') {

            $ucData = $userGlobals->uc['moduleData']['slubevents'];

            $ucData[$key] = $data;

            $userGlobals->uc['moduleData']['slubevents'] = $ucData;

            $userGlobals->writeUC($userGlobals->uc);
        }

        $userGlobals->setAndSaveSessionData($key, $data);

        return;
    }

    /**
     * Get session data
     *
     * @param string $key
     *
     * @return
     */
    public function getSessionData($key)
    {
        $userGlobals = $this->getUserGlobals();

        $sessionData = $userGlobals->getSessionData($key);

        $configurationData = array();

        if (TYPO3_MODE === 'BE') {

            $ucData = $userGlobals->uc['moduleData']['slubevents'];

            $configurationData = $ucData[$key];


            if (!empty($configurationData) && !(empty($sessionData))) {
                // merge session and configuration data
                $sessionData = ArrayUtility::arrayMergeRecursiveOverrule($sessionData, $configurationData);

            } else if (!empty($configurationData)) {
                // there seems to be only configuration data (after fresh login)
                $sessionData = $configurationData;

            }

        }

        return $sessionData;

    }

    /**
     * initializeAction
     *
     */
    protected function initializeAction()
    {
        if (TYPO3_MODE === 'BE') {
            global $BE_USER;
            // TYPO3 doesn't set locales for backend-users --> so do it manually like this...
            // is needed especially with strftime
            switch ($BE_USER->uc['lang']) {
                case 'en':
                    setlocale(LC_ALL, 'en_GB.utf8');
                    break;
                case 'de':
                    setlocale(LC_ALL, 'de_DE.utf8');
                    break;
            }
        }
    }

    /**
     * Safely gets Parameters from request
     * if they exist
     *
     * @param string $parameterName
     *
     * @return null|string
     */
    protected function getParametersSafely($parameterName)
    {
        if ($this->request->hasArgument($parameterName)) {
            return $this->filterSafelyParameters($this->request->getArgument($parameterName));
        }
        return null;
    }

    /**
     * remove XSS stuff recursively
     *
     * @param mixed $param
     *
     * @return string
     */
    protected function filterSafelyParameters($param)
    {
        if (is_array($param)) {
            foreach ($param as $key => $item) {
                $param[$key] = $this->filterSafelyParameters($item);
            }
            return $param;
        } else {
            return GeneralUtility::removeXSS($param);
        }
    }
}
