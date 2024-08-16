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

use Slub\SlubEvents\Domain\Model\Category;
use Slub\SlubEvents\Domain\Repository\CategoryRepository;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * @package slub_events
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class CategoryService
{
    public const CACHE_IDENTIFIER = 'slubevents_category';

    /**
     * @var FrontendInterface
     */
    protected $cache;

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

        /** @var CacheManager $cacheManager */
        $cacheManager = GeneralUtility::makeInstance(CacheManager::class);

        /** @var FrontendInterface $cache */
        $this->cache = $cacheManager->getCache(self::CACHE_IDENTIFIER);
    }

    /**
     * @param int $uid
     * @return Category|null
     */
    public function findByUid(int $uid): ?Category
    {
        return $this->categoryRepository->findByUid($uid);
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
     * @param array $categories
     * @return array|null
     */
    public function getRoots(array $categories): ?array
    {
        if (count($categories) === 0) {
            return null;
        }

        $roots = [];
        $rootIds = [];

        /** @var Category $category */
        foreach ($categories as $category) {
            $rootLine = $this->getRootLine($category);
            $rootUid = (int)GeneralUtility::trimExplode(',', $rootLine)[0];
            $root = $this->categoryRepository->findByUid($rootUid);

            if ($root instanceof Category && !in_array($rootUid, $rootIds, true)) {
                $roots[] = $root;
                $rootIds[] = $rootUid;
            }
        }

        return $roots;
    }

    /**
     * @param Category $category
     * @return string
     */
    protected function getRootLine(Category $category): string
    {
        $cacheIdentifier = sha1('parent-' . $category);
        $rootLine = $this->cache->get($cacheIdentifier);

        if (!$rootLine) {
            $rootLine = $this->getRootLineRecursive($category);
            $this->cache->set($cacheIdentifier, $rootLine);
        }

        return $rootLine;
    }

    /**
     * @param Category $category
     * @param array $result
     * @return string
     */
    protected function getRootLineRecursive(Category $category, $result = []): string
    {
        $result[] = $category->getUid();
        $parents = $category->getParent();

        if (count($parents) > 0) {
            foreach ($parents as $parent) {
                if ($parent instanceof Category) {
                    return $this->getRootLineRecursive($parent, $result);
                }
            }
        }

        krsort($result);

        return implode(',', $result);
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
