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

use Slub\SlubEvents\Domain\Repository\DisciplineRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * @package slub_events
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class DisciplineService
{
    /**
     * @var DisciplineRepository
     */
    protected $disciplineRepository;

    /**
     * DisciplineService constructor.
     */
    public function __construct()
    {
        /** @var ObjectManager $objectManager */
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);

        /** @var DisciplineRepository $disciplineRepository */
        $this->disciplineRepository = $objectManager->get(DisciplineRepository::class);
    }

    /**
     * @param string $discipline
     * @param false $recursive
     * @return array
     */
    public function getDisciplineIds($discipline = '', $recursive = false): array
    {
        $disciplineIds = GeneralUtility::intExplode(',', $discipline, true);

        if ($recursive === true) {
            foreach ($disciplineIds as $disciplineId) {
                $disciplineIds = $this->addChildDisciplines($disciplineId, $disciplineIds);
            }
        }

        return $disciplineIds;
    }

    /**
     * @param int $disciplineId
     * @param array $disciplineIds
     * @return array
     */
    protected function addChildDisciplines($disciplineId = 0, $disciplineIds = []): array
    {
        $childDisciplines = $this->disciplineRepository->findAllChildDisciplines($disciplineId);

        if (count($childDisciplines) > 0) {
            return array_merge($childDisciplines, $disciplineIds);
        }

        return $disciplineIds;
    }
}
