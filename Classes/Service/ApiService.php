<?php

namespace Slub\SlubEvents\Service;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012-2014 Alexander Bigga <alexander.bigga@slub-dresden.de>, SLUB Dresden
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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

/**
 * @package slub_events
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class ApiService
{
    /**
     * @var CategoryService
     */
    protected $categoryService;

    /**
     * @var DisciplineService
     */
    protected $disciplineService;

    /**
     * ApiService constructor.
     */
    public function __construct()
    {
        /** @var CategoryService $categoryService */
        $this->categoryService = GeneralUtility::makeInstance(CategoryService::class);

        /** @var DisciplineService $disciplineService */
        $this->disciplineService = GeneralUtility::makeInstance(DisciplineService::class);
    }

    /**
     * @param array $arguments
     * @return array
     */
    public function prepareArgumentsDefault($arguments = []): array
    {
        $preparedArguments = [];

        if (count($arguments) === 0) {
            return $preparedArguments;
        }

        if ($arguments['category']) {
            $preparedArguments['categoryList'] = $this->categoryService->getCategoryIds(
                $arguments['category'],
                (bool)$arguments['categoryRecursive']
            );
        }

        if ($arguments['discipline']) {
            $preparedArguments['disciplineList'] = $this->disciplineService->getDisciplineIds(
                $arguments['discipline'],
                (bool)$arguments['disciplineRecursive']
            );
        }

        if ($arguments['contact']) {
            $preparedArguments['contactsSelection'] = $arguments['contact'];
        }

        if ($arguments['showPastEvents']) {
            $preparedArguments['showPastEvents'] = (bool)$arguments['showPastEvents'];
        }

        if ($arguments['showEventsFromNow']) {
            $preparedArguments['showEventsFromNow'] = (bool)$arguments['showEventsFromNow'];
        }

        if ($arguments['limitByNextWeeks']) {
            $preparedArguments['limitByNextWeeks'] = (int)$arguments['limitByNextWeeks'];
        }

        if ($arguments['startTimestamp']) {
            $preparedArguments['startTimestamp'] = (int)$arguments['startTimestamp'];
        }

        if ($arguments['stopTimestamp']) {
            $preparedArguments['stopTimestamp'] = (int)$arguments['stopTimestamp'];
        }

        if ($arguments['sorting'] === 'desc') {
            $preparedArguments['eventOrdering'] = 'DESC';
        }

        if ($arguments['limit']) {
            $preparedArguments['limit'] = (int)$arguments['limit'];
        }

        return $preparedArguments;
    }

    /**
     * @param array $arguments
     * @return array
     */
    public function prepareArgumentsUser($arguments = []): array
    {
        if (count($arguments) === 0) {
            return [];
        }

        $preparedArguments = $this->prepareArgumentsDefault($arguments);

        if ($arguments['user']) {
            $preparedArguments['user'] = (int)$arguments['user'];
        }

        return $preparedArguments;
    }
}
