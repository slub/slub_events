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

use Slub\SlubEvents\Domain\Repository\CategoryRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * @package slub_events
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class CategoryService
{
    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * CategoryService constructor.
     */
    public function __construct()
    {
        /** @var ObjectManager $objectManager */
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);

        /** @var CategoryRepository $categoryRepository */
        $this->categoryRepository = $objectManager->get(CategoryRepository::class);
    }

    /**
     * @param string $category
     * @param false $recursive
     * @return array
     */
    public function getCategoryIds($category = '', $recursive = false): array
    {
        $categoryIds = GeneralUtility::intExplode(',', $category, true);

        if ($recursive === true) {
            foreach ($categoryIds as $categoryId) {
                $categoryIds = $this->addChildCategories($categoryId, $categoryIds);
            }
        }

        return $categoryIds;
    }

    /**
     * @param int $categoryId
     * @param array $categoryIds
     * @return array
     */
    protected function addChildCategories($categoryId = 0, $categoryIds = []): array
    {
        $childCategories = $this->categoryRepository->findAllChildCategories($categoryId);

        if (count($childCategories) > 0) {
            return array_merge($childCategories, $categoryIds);
        }

        return $categoryIds;
    }
}
