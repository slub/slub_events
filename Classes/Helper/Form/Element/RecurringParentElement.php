<?php
namespace Slub\SlubEvents\Helper\Form\Element;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2020 Alexander Bigga <alexander.bigga@slub-dresden.de>
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

use TYPO3\CMS\Backend\Form\Element\AbstractFormElement;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\HiddenRestriction;

use TYPO3\CMS\Core\Utility\GeneralUtility;

class RecurringParentElement extends AbstractFormElement
{
    public function render()
    {
        // Custom TCA properties and other data can be found in $this->data, for example the above
        // parameters are available in $this->data['parameterArray']['fieldConf']['config']['parameters']
        $result = $this->initializeResultArray();

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_slubevents_domain_model_event');

        $queryBuilder
            ->getRestrictions()
            ->removeByType(HiddenRestriction::class);

        $resultQuery = $queryBuilder
            ->select('uid', 'title')
            ->from('tx_slubevents_domain_model_event')
            ->where(
                $queryBuilder->expr()->eq(
                    'uid',
                    $queryBuilder->createNamedParameter((int) $this->data['databaseRow']['parent'], Connection::PARAM_INT)
                )
            )
            ->setMaxResults(1)
            ->execute();

        if ($resArray = $resultQuery->fetch()) {
            $parentEventRow = $resArray;
        }

        $result['html'] = $this->getEditLink('tx_slubevents_domain_model_event', $parentEventRow);

        return $result;
    }

    /**
     *
     * Returns the Edit Icon with link
     *
     * @param string $table Table name
     * @param array $row Data row
     * @return string html output
     */
    protected function getEditLink($table, array $row)
    {
        $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);

        $params .= '&edit[' . $table . '][' . $row['uid'] . ']=edit';
        $title = LocalizationUtility::translate('be.editEvent', 'slub_events',
              $arguments = null) . ' ' .
              LocalizationUtility::translate('tx_slubevents_domain_model_event.recurring', 'slub_events',
              $arguments = null) . ' `' . $row['title'] . '`';
        $clickUrl = $uriBuilder->buildUriFromRoute('record_edit') . $params
            . '&returnUrl=' . rawurlencode(GeneralUtility::getIndpEnv('REQUEST_URI'));

        $link = '<a href="'. $clickUrl .'" class="btn btn-info" title="' . $title . '">'  .
            '[' . $row['uid'] . '] ' . $row['title'] .
            '</a>';

        return $link;
    }

}
