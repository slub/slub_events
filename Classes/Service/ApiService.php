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
    public function getSettings($arguments = []): array
    {
        $settings = [];

        if ($arguments['category']) {
            $settings['categoryList'] = $this->categoryService->getCategoryIds(
                $arguments['category'],
                (bool)$arguments['categoryRecursive']
            );
        }

        if ($arguments['discipline']) {
            $settings['disciplineList'] = $this->disciplineService->getDisciplineIds(
                $arguments['discipline'],
                (bool)$arguments['disciplineRecursive']
            );
        }

        if ($arguments['contact']) {
            $settings['contactsSelection'] = $arguments['contact'];
        }

        if ($arguments['showPastEvents']) {
            $settings['showPastEvents'] = (bool)$arguments['showPastEvents'];
        }

        if ($arguments['showEventsFromNow']) {
            $settings['showEventsFromNow'] = (bool)$arguments['showEventsFromNow'];
        }

        if ($arguments['limitByNextWeeks']) {
            $settings['limitByNextWeeks'] = (int)$arguments['limitByNextWeeks'];
        }

        if ($arguments['startTimestamp']) {
            $settings['startTimestamp'] = (int)$arguments['startTimestamp'];
        }

        if ($arguments['stopTimestamp']) {
            $settings['stopTimestamp'] = (int)$arguments['stopTimestamp'];
        }

        if ($arguments['sorting'] === 'desc') {
            $settings['eventOrdering'] = 'DESC';
        }

        if ($arguments['limit']) {
            $settings['limit'] = (int)$arguments['limit'];
        }

        return $settings;
    }
}
