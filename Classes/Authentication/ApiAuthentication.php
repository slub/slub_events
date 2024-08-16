<?php

namespace Slub\SlubEvents\Authentication;

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

use Slub\SlubEvents\Mvc\View\JsonView;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * Class ApiAuthentication
 * @package Slub\SlubEvents\Authentication
 */
class ApiAuthentication
{
    public const LL_PATH = 'LLL:EXT:slub_events/Resources/Private/Language/locallang_api.xlf';
    public const EXTENSION_NAME = 'slubevents';

    /**
     * @var string[]
     */
    public $error = [
        401 => 'Invalid authorization'
    ];

    /**
     * @var ConfigurationManagerInterface
     */
    protected $configurationManager;

    public function __construct()
    {
        /** @var ObjectManager $objectManager */
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);

        $this->configurationManager = $objectManager->get(ConfigurationManagerInterface::class);
    }

    /**
     * @return bool
     */
    public function authenticateUser(): bool
    {
        $users = $this->findAllUsers();
        $apiUser = $this->getApiUser();

        return $this->isValiduser($users, $apiUser);
    }

    /**
     * @param JsonView $view
     * @param int $status
     * @return JsonView
     */
    public function getError(JsonView $view, int $status): JsonView
    {
        $view->setVariablesToRender(['error']);
        $view->assign('error', [
            'error' => [
                'status' => $status,
                'message' => $this->error[$status]
            ]
        ]);

        return $view;
    }

    /**
     * @param array $users
     * @param array $apiUser
     * @return bool
     */
    protected function isValidUser(array $users, array $apiUser): bool
    {
        if (count($users) === 0 || count($apiUser) === 0) {
            return false;
        }

        foreach ($users as $user) {
            // Security risk if there is a user with empty username and empty password
            // Well, close the api in general
            if (empty($user['username']) || empty($user['password'])) {
                return false;
            }

            if ($user['username'] === $apiUser['username'] &&
                $user['password'] === $apiUser['password']
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array
     */
    protected function getApiUser(): array
    {
        $user = [];
        $authorization = $_SERVER['HTTP_AUTHORIZATION'] ?? null;

        if (stripos($authorization, 'Basic ') === 0) {
            $user = GeneralUtility::trimExplode(':', base64_decode(substr($authorization, 6)), 2);
        }

        if (count($user) === 2) {
            return [
                'username' => $user[0],
                'password' => $user[1]
            ];
        }

        return [];
    }

    /**
     * @return array
     */
    protected function findAllUsers(): array
    {
        return $this->getExtensionSettings()['api']['users'] ?? [];
    }

    /**
     * @return array
     */
    protected function getExtensionSettings(): array
    {
        return (array)$this->configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS,
            self::EXTENSION_NAME
        );
    }
}
