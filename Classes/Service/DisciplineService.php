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
