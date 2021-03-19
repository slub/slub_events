<?php
namespace Slub\SlubEvents\ViewHelpers\Be;
use TYPO3\CMS\Fluid\ViewHelpers\Be\AbstractBackendViewHelper;
use Slub\SlubEvents\Helper\IconsHelper;
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

use \Slub\SlubEvents\Domain\Model\Event;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

class FunctionBarViewHelper extends AbstractBackendViewHelper
{
    use CompileWithRenderStatic;

    /**
     * As this ViewHelper renders HTML, the output must not be escaped.
     *
     * @var bool
     */
    protected $escapeOutput = false;

    /**
     * Initialize arguments.
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('icon', 'string', 'Icon', true);
        $this->registerArgument('event', Event::class, 'Events', false);
    }

    /**
     * @var ConfigurationManagerInterface
     */
    protected $configurationManager;


    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * Returns the Genius Bar Icon
     *
     * @param Event event
     * @return string html output
     */
    protected static function getGeniusBarIcon(Event $event)
    {
        if ($event !== null) {
            if ($event->getGeniusBar()) {
                $title = LocalizationUtility::translate('tx_slubevents_domain_model_event.genius_bar', 'slub_events', $arguments = null);
                return '<span title="' . $title . '" class="geniusbar">[W]&nbsp;</span>';
            }
        }
    }

    /**
     * Renders a record list as known from the TYPO3 list module
     * Note: This feature is experimental!
     *
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        $icon = $arguments['icon'];
        $event = $arguments['event'];
        if ($event !== null) {
            $row['uid'] = $event->getUid();
            $row['title'] = $event->getTitle();
            $row['hidden'] = $event->getHidden();
        }

        $configurationManager = GeneralUtility::makeInstance(ConfigurationManager::class);
        $frameworkConfiguration = $configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
        $storagePid = $frameworkConfiguration['persistence']['storagePid'];

        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $iconHelper = $objectManager->get(IconsHelper::class);

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
                $content = self::getGeniusBarIcon($event);
                break;
            case 'datepicker':
                $content = $iconHelper->getDatePickerIcon();
                break;
        }

        return $content;

    }

}
