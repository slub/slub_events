<?php

namespace Slub\SlubEvents\Controller\Api;

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

use Slub\SlubEvents\Controller\AbstractController;
use Slub\SlubEvents\Mvc\View\JsonView;
use Slub\SlubEvents\Service\ApiService;
use Slub\SlubEvents\Service\EventService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
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
     * EventController constructor.
     */
    public function __construct()
    {
        parent::__construct();

        /** @var ObjectManager $objectManager */
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);

        $this->apiService = $objectManager->get(ApiService::class);
        $this->eventService = $objectManager->get(EventService::class);
    }

    /**
     * @return void
     */
    public function listAction(): void
    {
        $arguments = $this->apiService->prepareArgumentsDefault($this->request->getArguments());
        $events = $this->eventRepository->findAllBySettings($arguments);

        $this->view->setVariablesToRender(['events']);
        $this->view->assign('events', $events);
    }

    /**
     * @return void
     */
    public function listUserAction(): void
    {
        $arguments = $this->apiService->prepareArgumentsUser($this->request->getArguments());
        $events = $arguments['user'] === 0 ? [] : $this->eventRepository->findAllBySettings($arguments)->toArray();
        $eventsUser = $this->eventService->prepareForUser($arguments['user'], $events, $this->settings);

        $this->view->setVariablesToRender(['eventsUser']);
        $this->view->assign('eventsUser', $eventsUser);
    }
}
