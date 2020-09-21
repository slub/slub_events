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

use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Backend\Utility\IconUtility;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;

use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
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
        $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);

        $params .= '&edit[' . $table . '][' . $row['uid'] . ']=edit';
        $title = LocalizationUtility::translate('be.editEvent', 'slub_events',
                $arguments = null) . ' ' . $row['uid'] . ': ' . $row['title'];
        $clickUrl = $uriBuilder->buildUriFromRoute('record_edit') . $params
        . '&returnUrl=' . rawurlencode(GeneralUtility::getIndpEnv('REQUEST_URI'));

        $icon = '<a href="'. $clickUrl .'" title="' . $title . '">'  .
            $this->iconFactory->getIcon('actions-document-open', Icon::SIZE_SMALL)->render() .
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
        $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);

        $params = '&edit[' . $table . '][' . $storagePid . ']=new';
        $title = LocalizationUtility::translate('be.newEvent', 'slub_events', $arguments = null);
        $clickUrl = $uriBuilder->buildUriFromRoute('record_edit') . $params
            . '&returnUrl=' . rawurlencode(GeneralUtility::getIndpEnv('REQUEST_URI'));

        $icon = '<a href="'. $clickUrl .'" title="' . $title . '">' .
            $this->iconFactory->getIcon('actions-document-new', Icon::SIZE_SMALL)->render() .
            '</a>';

        return $icon;
    }

    /**
     * Returns the Hide Icon with link
     *
     * @param string $table Table name
     * @param integer $uid uid of data row
     * @param boolean $hidden hidden flag
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
                $this->iconFactory->getIcon('actions-edit-unhide', Icon::SIZE_SMALL)->render() .
                '</a>';
            // Hide
        } else {
            $title = LocalizationUtility::translate('be.hideEvent', 'slub_events', $arguments = null);
            $params = '&data[' . $table . '][' . $uid . '][hidden]=1';

            $hideLink = '';
            $quoteLink = "'";
            $hideLink = \TYPO3\CMS\Backend\Utility\BackendUtility::getLinkToDataHandlerAction($params);

            $icon = '<a href="#" onclick="' . htmlspecialchars('return jumpToUrl(' . $quoteLink . $hideLink . $quoteLink . ');') . '" title="' . $title . '">' .
                $this->iconFactory->getIcon('actions-edit-hide', Icon::SIZE_SMALL)->render() .
                '</a>';
        }
        return $icon;
    }

    /**
     * Returns the Hide Icon
     *
     * @param string $table Table name
     * @param integer $uid uid of data row
     * @param boolean $hidden hidden flag
     * @return string html output
     */
    public function getHiddenCheckbox($table, $uid, $hidden)
    {
        if ($hidden) {
            $hidden = 1;
            $inline = true;
            $invert = false;
            $visible = 'hidden';
            $hiddenIcon = $this->iconFactory->getIcon('actions-edit-unhide', Icon::SIZE_SMALL)->render();
            $title = LocalizationUtility::translate('be.unhideEvent', 'slub_events', $arguments = null);
            $toggleTitle = LocalizationUtility::translate('be.hideEvent', 'slub_events', $arguments = null);
        } else {
            $hidden = 0;
            $inline = true;
            $invert = true;
            $visible = 'visible';
            $hiddenIcon = $this->iconFactory->getIcon('actions-edit-hide', Icon::SIZE_SMALL)->render();
            $title = LocalizationUtility::translate('be.hideEvent', 'slub_events', $arguments = null);
            $toggleTitle = LocalizationUtility::translate('be.unhideEvent', 'slub_events', $arguments = null);
        }

        return '
            <td class="col-icon nowrap">
                <div class="btn-group" role="group">
                    <a class="btn btn-default t3js-record-hide" data-state="'.$visible.'" href="#"
                    data-params="data['.$table.']['.$uid.'][hidden]='.(($hidden == 1) ? 0 : 1).'"
                    title="'.$title.'"
                    data-toggle-title="'.$toggleTitle.'"
                    >
                        ' . $hiddenIcon . '
                    </a>
                </div>
            </td>
        ';
    }

    /**
     * Returns the Record Icon with hidden state
     *
     * @param string $table Table name
     * @param integer $uid uid of data row
     * @param boolean $hidden hidden flag
     * @return string html output
     */
    public function getHiddenRecordIcon($table, $uid, $hidden)
    {
        $hiddenRecordIcon = $this->iconFactory->getIconForRecord($table, ['uid' => $uid, 'hidden' => $hidden], Icon::SIZE_SMALL)->render();

        return '
        <td class="col-icon nowrap">
            <a href="#" class="t3js-contextmenutrigger" data-table="'.$table.'" data-uid="'.$uid.'">
            <span data-toggle="tooltip" data-title=" id='.$uid.'" data-html="true" data-placement="right" data-original-title="" title="">
            <span class="t3js-icon icon icon-size-small icon-state-default icon-tcarecords-'.$table.'-default"
            data-identifier="tcarecords-'.$table.'-default">
            ' . $hiddenRecordIcon . '
            </a>
        </td>
        ';
    }
}
