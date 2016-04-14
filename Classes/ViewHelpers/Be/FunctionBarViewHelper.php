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
use TYPO3\CMS\Backend\Utility\IconUtility;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class FunctionBarViewHelper extends \TYPO3\CMS\Fluid\ViewHelpers\Be\AbstractBackendViewHelper
{

    /**
     * @var ConfigurationManagerInterface
     */
    protected $configurationManager;

    /**
     * @var IconFactory
     */
    protected $iconFactory;

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
     * @param IconFactory $iconFactory
     */
    public function injectIconFactory(IconFactory $iconFactory)
    {
        $this->iconFactory = $iconFactory;
    }

    /**
     * Returns the Edit Icon with link
     *
     * @param string $table Table name
     * @param array  $row   Data row
     *
     * @return string html output
     */
    protected function getEditIcon($table, array $row)
    {

        // back GET parameter have to be like this with '%26' instead of '&':
        $params = '&edit[' . $table . '][' . $row['uid'] . ']=edit';

        $title =
            LocalizationUtility::translate(
                'be.editEvent',
                'slub_events',
                $arguments = null
            )
            . ' ' . $row['uid'] . ': ' . $row['title'];

        $icon = '<a href="#" onclick="' .
            htmlspecialchars(
                BackendUtility::editOnClick(
                    $params,
                    $this->backPath,
                    -1
                )
            )
            . '" title="' . $title . '">' .
            $this->iconFactory->getIcon('actions-document-open', Icon::SIZE_SMALL) .
            '</a>';

        return $icon;
    }

    /**
     * Returns the New Icon with link
     *
     * @param string $table Table name
     * @param array  $row   Data row
     *
     * @return string html output
     */
    protected function getNewIcon($table, array $row)
    {
        $params .= '&edit[' . $table . '][' . $row['storagePid'] . ']=new';
        $title = LocalizationUtility::translate('be.newEvent', 'slub_events', $arguments = null);
        $icon = '<a href="#" onclick="' .
            htmlspecialchars(
                BackendUtility::editOnClick(
                    $params,
                    $this->backPath,
                    -1
                )
            )
            . '" title="' . $title . '">' .
            $this->iconFactory->getIcon('actions-document-new', Icon::SIZE_SMALL) .
            '</a>';

        return $icon;
    }

    /**
     * Returns the New Icon with link
     *
     * @param string $table Table name
     * @param array  $row   Data row
     *
     * @return string html output
     */
    protected function getHideIcon($table, array $row)
    {
        $doc = $this->getDocInstance();
        if ($row['hidden']) {
            $title = LocalizationUtility::translate('be.unhideEvent', 'slub_events', $arguments = null);
            $params = '&data[' . $table . '][' . $row['uid'] . '][hidden]=0';
            $icon = '<a href="#" onclick="' .
                htmlspecialchars('return jumpToUrl(' . $doc->issueCommand($params, -1) . ');')
                . '" title="' . $title . '">' .
                $this->iconFactory->getIcon('actions-edit-unhide', Icon::SIZE_SMALL)
                . '</a>';
            // Hide
        } else {
            $title = LocalizationUtility::translate('be.hideEvent', 'slub_events', $arguments = null);
            $params = '&data[' . $table . '][' . $row['uid'] . '][hidden]=1';
            $icon = '<a href="#" onclick="' .
                htmlspecialchars('return jumpToUrl(' . $doc->issueCommand($params, -1) . ');')
                . '" title="' . $title . '">' .
                $this->iconFactory->getIcon('actions-edit-hide', Icon::SIZE_SMALL) .
                '</a>';
        }
        return $icon;
    }

    /**
     * Returns the Genius Bar Icon
     *
     * @param \Slub\SlubEvents\Domain\Model\Event $event
     *
     * @return string html output
     */
    protected function getGeniusBarIcon(\Slub\SlubEvents\Domain\Model\Event $event)
    {
        if ($event !== null) {
            if ($event->getGeniusBar()) {
                return '<span title="Wissensbar-Termin" class="geniusbar">W&nbsp;</span>';
            }
        }
    }

    /**
     * Returns the Datepicker img
     *
     * @return string html output
     */
    protected function getDatePickerIcon()
    {
        return $this->iconFactory->getIcon('actions-edit-pick-date', Icon::SIZE_SMALL);

        return IconUtility::getSpriteIcon(
            'actions-edit-pick-date',
            [
                'style' => 'cursor:pointer;',
                'id'    => 'picker-tceforms-datefield-1',
                'class' => 't3-icon t3-icon-actions t3-icon-actions-edit t3-icon-edit-pick-date',
            ]
        );
    }


    /**
     * Renders a record list as known from the TYPO3 list module
     * Note: This feature is experimental!
     *
     * @param string                              $icon
     * @param \Slub\SlubEvents\Domain\Model\Event $event
     *
     * @return string the rendered record list
     */
    public function render($icon = 'edit', \Slub\SlubEvents\Domain\Model\Event $event = null)
    {
        if ($event !== null) {
            $row['uid'] = $event->getUid();
            $row['title'] = $event->getTitle();
            $row['hidden'] = $event->getHidden();
        }

        $frameworkConfiguration = $this->configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK
        );
        $row['storagePid'] = $frameworkConfiguration['persistence']['storagePid'];

        $content = '';
        switch ($icon) {
            case 'new':
                $content = $this->getNewIcon('tx_slubevents_domain_model_event', $row);
                break;
            case 'edit':
                $content = $this->getEditIcon('tx_slubevents_domain_model_event', $row);
                break;
            case 'hide':
                $content = $this->getHideIcon('tx_slubevents_domain_model_event', $row);
                break;
            case 'geniusbar':
                $content = $this->getGeniusBarIcon($event);
                break;
            case 'datepicker':
                $content = $this->getDatePickerIcon();
                break;
        }

        return $content;
    }
}
