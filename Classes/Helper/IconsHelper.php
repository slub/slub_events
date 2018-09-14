<?php
namespace Slub\SlubEvents\Helper;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2018 Alexander Bigga <alexander.bigga@slub-dresden.de>
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

use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Backend\Utility\IconUtility;

use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;

class IconsHelper
{
    /**
     * @var IconFactory
     */
    protected $iconFactory;

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

        $this->iconFactory = $this->objectManager->get('TYPO3\\CMS\\Core\\Imaging\\IconFactory');

    }

    /**
     * Returns the Edit Icon with link
     *
     * @param string $table Table name
     * @param array $row Data row
     * @return string html output
     */
    public function getEditIcon($table, array $row)
    {

        $params .= '&edit[' . $table . '][' . $row['uid'] . ']=edit';
        $title = LocalizationUtility::translate('be.editEvent', 'slub_events',
                $arguments = null) . ' ' . $row['uid'] . ': ' . $row['title'];
        $icon = '<a href="#" onclick="' . htmlspecialchars(BackendUtility::editOnClick($params, $this->backPath,
                -1)) . '" title="' . $title . '">' .
            $this->getSpriteIcon('actions-document-open') .
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
    public function getNewIcon($table, $storagePid)
    {

        $params .= '&edit[' . $table . '][' . $storagePid . ']=new';
        $title = LocalizationUtility::translate('be.newEvent', 'slub_events', $arguments = null);
        $icon = '<a href="#" onclick="' . htmlspecialchars(BackendUtility::editOnClick($params, $this->backPath,
                -1)) . '" title="' . $title . '">' .
            $this->getSpriteIcon('actions-document-new') .
            '</a>';

        return $icon;
    }

    /**
     * Returns the Hide Icon with link
     *
     * @param string $table Table name
     * @param array $row Data row
     * @return string html output
     */
    public function getHideIcon($table, $uid, $hidden)
    {

        if ($hidden) {
            $title = LocalizationUtility::translate('be.unhideEvent', 'slub_events', $arguments = null);
            $params = '&data[' . $table . '][' . $uid . '][hidden]=0';

            $hideLink = '';
            $quoteLink = "'";
            $hideLink = \TYPO3\CMS\Backend\Utility\BackendUtility::getLinkToDataHandlerAction($params);

            $icon = '<a href="#" onclick="' . htmlspecialchars('return jumpToUrl(' . $quoteLink . $hideLink . $quoteLink . ');') . '" title="' . $title . '">' .
                $this->getSpriteIcon('actions-edit-unhide') .
                '</a>';
            // Hide
        } else {
            $title = LocalizationUtility::translate('be.hideEvent', 'slub_events', $arguments = null);
            $params = '&data[' . $table . '][' . $uid . '][hidden]=1';

            $hideLink = '';
            $quoteLink = "'";
            $hideLink = \TYPO3\CMS\Backend\Utility\BackendUtility::getLinkToDataHandlerAction($params);

            $icon = '<a href="#" onclick="' . htmlspecialchars('return jumpToUrl(' . $quoteLink . $hideLink . $quoteLink . ');') . '" title="' . $title . '">' .
                $this->getSpriteIcon('actions-edit-hide') .
                '</a>';
        }
        return $icon;
    }

    /**
     * Get the requested Sprite Icon
     *
     * (was) compatibility helper for TYPO3 6.2 and 7.6
     *
     * @param $iconName
     * @param $options
     *
     * @return full HTML tag
     */

    private function getSpriteIcon($iconName, $options = [])
    {

        return $this->iconFactory->getIcon($iconName, Icon::SIZE_SMALL);

    }

}
