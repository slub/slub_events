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
