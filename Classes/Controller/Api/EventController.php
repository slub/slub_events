<?php

namespace Slub\SlubEvents\Controller\Api;

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

use Slub\SlubEvents\Authentication\ApiAuthentication;
use Slub\SlubEvents\Controller\AbstractController;
use Slub\SlubEvents\Mvc\View\JsonView;
use Slub\SlubEvents\Service\ApiService;
use Slub\SlubEvents\Service\EventService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * @package slub_events
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class EventController extends AbstractController
{
    /**
     * @var ApiService
     */
    protected $apiService;

    /**
     * @var ApiAuthentication
     */
    protected $apiAuthentication;

    /**
     * @var EventService
     */
    protected $eventService;

    /**
     * @var JsonView
     */
    protected $view;

    /**
     * @var string
     */
    protected $defaultViewObjectName = JsonView::class;

    /**
     * @var bool
     */
    protected $allowApiAccess = false;

    /**
     * EventController constructor.
     */
    public function __construct()
    {
        parent::__construct();

        /** @var ObjectManager $objectManager */
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);

        $this->apiService = $objectManager->get(ApiService::class);
        $this->apiAuthentication = $objectManager->get(ApiAuthentication::class);
        $this->eventService = $objectManager->get(EventService::class);

        $this->allowApiAccess = $this->apiAuthentication->authenticateUser();
    }

    /**
     * @param ViewInterface $view
     */
    public function initializeView(ViewInterface $view): void
    {
        parent::initializeView($view);

        if (!$this->allowApiAccess) {
            $this->view = $this->apiAuthentication->getError($this->view, 401);
        }
    }

    /**
     * @return void
     */
    public function listAction(): void
    {
        if ($this->allowApiAccess) {
            $arguments = $this->apiService->prepareArgumentsDefault($this->request->getArguments());
            $events = $this->eventService->findAllBySettings($arguments);

            $this->view->setVariablesToRender(['events']);
            $this->view->assign('events', $events);
        }
    }

    /**
     * @return void
     */
    public function listUserAction(): void
    {
        if ($this->allowApiAccess) {
            $arguments = $this->apiService->prepareArgumentsUser($this->request->getArguments());
            $events = $arguments['user'] === 0 ? [] : $this->eventService->findAllBySettings($arguments);
            $eventsUser = $this->eventService->prepareForUser($arguments['user'], $events, $this->settings);

            $this->view->setVariablesToRender(['eventsUser']);
            $this->view->assign('eventsUser', $eventsUser);
        }
    }
}
