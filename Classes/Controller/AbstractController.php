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

        $this->contentObj = $this->configurationManager->getContentObjectRenderer();
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

    /**
     * Function foldline folds the line after 73 signs
     * rfc2445.txt: lines SHOULD NOT be longer than 75 octets
     *
     * @param string $content : Anystring
     *
     * @return string        $content: Manipulated string
     */
    protected function foldline($content)
    {
        $text = trim(strip_tags(html_entity_decode($content), '<br>,<p>,<li>'));
        $text = preg_replace('/<p[\ \w\=\"]{0,}>/', '', $text);
        $text = preg_replace('/<li[\ \w\=\"]{0,}>/', '- ', $text);
        // make newline formated (yes, really write \n into the text!
        $text = str_replace('</p>', '\n', $text);
        $text = str_replace('</li>', '\n', $text);
        // remove tabs
        $text = str_replace("\t", ' ', $text);
        // remove multiple spaces
        $text = preg_replace('/[\ ]{2,}/', '', $text);
        $text = str_replace('<br />', '\n', $text);
        // remove more than one empty line
        $text = preg_replace('/[\n]{3,}/', '\n\n', $text);
        // remove windows linkebreak
        $text = preg_replace('/[\r]/', '', $text);
        // newlines are not allowed
        $text = str_replace("\n", '\n', $text);
        // semicolumns are not allowed
        $text = str_replace(';', '\;', $text);

        $firstline = substr($text, 0, (75 - 12));
        $restofline = implode("\n ", str_split(trim(substr($text, (75 - 12), strlen($text))), 73));

        if (strlen($restofline) > 0) {
            $foldedline = $firstline . "\n " . $restofline;
        } else {
            $foldedline = $firstline;
        }

        return $foldedline;
    }

    /**
     * html2rest
     *
     * this converts the HTML email to something Rest-Style like text form
     *
     * @param $text
     *
     * @return mixed|string
     * @internal param $htmlString
     *
     */
    public function html2rest($text)
    {
        $text = strip_tags(
            html_entity_decode(
                $text,
                ENT_COMPAT,
                'UTF-8'
            ),
            '<br>,<p>,<b>,<h1>,<h2>,<h3>,<h4>,<h5>,<a>,<li>'
        );

        // header is getting **
        $text = preg_replace('/<h[1-5]>|<\/h[1-5]>/', '**', $text);
        // bold is getting * ([[\w\ \d:\/~\.\?\=&%\"]+])
        $text = preg_replace('/<b>|<\/b>/', '*', $text);
        // get away links but preserve href with class slub-event-link
        $text = preg_replace(
            '/(<a[\ \w\=\"]{0,})(class=\"slub-event-link\" href\=\")([\w\d:\-\/~\.\?\=&%]+)([\"])([\"]{0,1}>)([\ \w\d\p{P}]+)(<\/a>)/',
            "$6\n$3",
            $text
        );
        // Remove separator characters (like non-breaking spaces...)
        $text = preg_replace('/\p{Z}/u', ' ', $text);
        $text = str_replace('<br />', "\n", $text);
        // get away paragraphs including class, title etc.
        $text = preg_replace('/<p[\s\w\=\"]*>(?s)(.*?)<\/p>/u', "$1\n", $text);
        $text = str_replace('<li>', '- ', $text);
        $text = str_replace('</li>', "\n", $text);
        // remove multiple spaces
        $text = preg_replace('/[\ ]{2,}/', '', $text);
        // remove multiple tabs
        $text = preg_replace('/[\t]{1,}/', '', $text);
        // remove more than one empty line
        $text = preg_replace('/[\n]{3,}/', "\n\n", $text);
        // remove all remaining html tags
        $text = strip_tags($text);

        return $text;
    }

}
