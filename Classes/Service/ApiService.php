<?php

namespace Slub\SlubEvents\Service;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 3
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;

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
            return ['user' => 0];
        }

        $preparedArguments = $this->prepareArgumentsDefault($arguments);

        if ($arguments['user']) {
            $preparedArguments['user'] = (int)$arguments['user'];
        }

        return $preparedArguments;
    }
}
