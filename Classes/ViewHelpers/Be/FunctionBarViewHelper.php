<?php
namespace Slub\SlubEvents\ViewHelpers\Be;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013-2014 Alexander Bigga <alexander.bigga@slub-dresden.de>, SLUB Dresden
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

use TYPO3\CMS\Backend\Utility\BackendUtility;

use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class FunctionBarViewHelper extends \TYPO3\CMS\Fluid\ViewHelpers\Be\AbstractBackendViewHelper
{

    /**
     * @var ConfigurationManagerInterface
     */
    protected $configurationManager;


    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * FunctionBarViewHelper constructor.
     *
     * Use dependency injection depending on TYPO3 version
     *
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(ObjectManagerInterface $objectManager)
    {

        $this->objectManager = $objectManager;

    }

    /**
     * @param ConfigurationManagerInterface $configurationManager
     *
     * @return void
     */
    public function injectConfigurationManager(ConfigurationManagerInterface $configurationManager)
    {
        $this->configurationManager = $configurationManager;
    }

    /**
     * Returns the Genius Bar Icon
     *
     * @param event \Slub\SlubEvents\Domain\Model\Event
     * @return string html output
     */
    protected function getGeniusBarIcon(\Slub\SlubEvents\Domain\Model\Event $event)
    {
        if ($event !== null) {
            if ($event->getGeniusBar()) {
                $title = LocalizationUtility::translate('tx_slubevents_domain_model_event.genius_bar', 'slub_events', $arguments = null);
                return '<span title="' . $title . '" class="geniusbar">[W]&nbsp;</span>';
            }
        }
    }

    /**
     * Returns the Recurring Icon
     *
     * @param event \Slub\SlubEvents\Domain\Model\Event
     * @return string html output
     */
    protected function getRecurringIcon(\Slub\SlubEvents\Domain\Model\Event $event)
    {
        if ($event !== null) {
            if ($event->isRecurring()) {
                $title = LocalizationUtility::translate('tx_slubevents_domain_model_event.recurring', 'slub_events', $arguments = null);
                return '<span title="' . $title . '" class="recuring">[R]&nbsp;</span>';
            } else {
                if ($event->getParent()) {
                  $title = LocalizationUtility::translate('tx_slubevents_domain_model_event.recurring', 'slub_events', $arguments = null);
                  return '<span title="' . $title . '" class="recuring">[RW]&nbsp;</span>';
                }
            }
        }
    }


    /**
     * Renders a record list as known from the TYPO3 list module
     * Note: This feature is experimental!
     *
     * @param icon string
     * @param event \Slub\SlubEvents\Domain\Model\Event
     * @return string the rendered record list
     */
    public function render($icon = 'edit', \Slub\SlubEvents\Domain\Model\Event $event = null)
    {

        if ($event !== null) {
            $row['uid'] = $event->getUid();
            $row['title'] = $event->getTitle();
            $row['hidden'] = $event->getHidden();
        }

        $frameworkConfiguration = $this->configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
        $storagePid = $frameworkConfiguration['persistence']['storagePid'];

        $iconHelper = $this->objectManager->get(\Slub\SlubEvents\Helper\IconsHelper::class);

        switch ($icon) {
            case 'new':
                $content = $iconHelper->getNewIcon('tx_slubevents_domain_model_event', $storagePid);
                break;
            case 'edit':
                $content = $iconHelper->getEditIcon('tx_slubevents_domain_model_event', $row);
                break;
            case 'hide':
                $content = $iconHelper->getHideIcon('tx_slubevents_domain_model_event', $row['uid'], $row['hidden']);
                break;
            case 'geniusbar':
                $content = $this->getGeniusBarIcon($event);
                break;
            case 'recurring':
                $content = $this->getRecurringIcon($event);
                break;
            case 'datepicker':
                $content = $iconHelper->getDatePickerIcon();
                break;
        }

        return $content;

    }

}
