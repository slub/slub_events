<?php
namespace Slub\SlubEvents\Controller\Backend;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use Slub\SlubEvents\Domain\Model\Event;
use Slub\SlubEvents\Helper\EmailHelper;
use Slub\SlubEvents\Helper\EventHelper;
use Slub\SlubEvents\Utility\TextUtility;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Annotation as Extbase;

/**
 * @package slub_events
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class SubscriberController extends BaseController
{
    /**
     * action beList
     *
     * @return void
     */
    public function beListAction()
    {
        // get data from BE session
        $searchParameter = $this->getSessionData('tx_slubevents');

        // set the startDateStamp
        if (empty($searchParameter['selectedStartDateStamp'])) {
            $searchParameter['selectedStartDateStamp'] = date('d-m-Y');
        }

        // if search was triggered
        $submittedSearchParams = $this->getParametersSafely('searchParameter');
        if (is_array($submittedSearchParams)) {

            $searchParameter = $submittedSearchParams;

            // save session data
            $this->setSessionData('tx_slubevents', $searchParameter);
        }

        // Categories
        // ------------------------------------------------------------------------------------

        // get the categories
        $categories = $this->categoryRepository->findAllTree();

        // check which categories have been selected
        if (!is_array($submittedSearchParams['category'])) {
            $allCategories = $this->categoryRepository->findAll()->toArray();
            foreach ($allCategories as $category) {
                $searchParameter['category'][$category->getUid()] = $category->getUid();
            }
        }
        $this->view->assign('categoriesSelected', $searchParameter['category']);

        // Events
        // ------------------------------------------------------------------------------------
        // get the events to show
        $events = $this->eventRepository->findAllByCategoriesAndDate(
            $searchParameter['category'],
            strtotime($searchParameter['selectedStartDateStamp'])
        );


        // Subscribers
        // ------------------------------------------------------------------------------------
        if (sizeof($events->toArray()) > 0) {
            $subscribers = $this->subscriberRepository->findAllByEvents($events);
            $this->view->assign('subscribers', $subscribers);
        } else {
            $this->addFlashMessage('No events found.', 'Error', FlashMessage::ERROR);
        }

        $this->view->assign('categories', $categories);
        $this->view->assign('events', $events);
        $this->view->assign('selectedStartDateStamp', $searchParameter['selectedStartDateStamp']);
    }

    /**
     * action beOnlineSurveyAction
     *
     * --> see ics template in Resources/Private/Templates/Email/
     *
     * @param Event   $event
     * @param integer $step
     * @Extbase\IgnoreValidation("event")
     *
     * @return void
     */
    public function beOnlineSurveyAction(Event $event, $step = 0)
    {
        // get the onlineSurveyLink and potential timestamp of last sent
        $onlineSurveyLink = GeneralUtility::trimExplode('|', $event->getOnlinesurvey(), true);

        // set the link to the current object to get access inside the email
        $event->setOnlinesurvey($onlineSurveyLink[0]);

        if ($step == 0) {
            /** @var \TYPO3Fluid\Fluid\View\StandaloneView $emailViewHTML */
            $emailViewHTML = $this->objectManager->get('TYPO3\\CMS\\Fluid\\View\\StandaloneView');

            $emailViewHTML->getRequest()->setControllerExtensionName($this->extensionName);
            $emailViewHTML->setFormat('html');
            $emailViewHTML->assign('onlineSurveyLink', $onlineSurveyLink[0]);
            $emailViewHTML->assign('event', $event);
            $emailViewHTML->assign('subscriber', ['name' => '###Name wird automatisch ausgefüllt###']);

            $emailViewHTML->setTemplateRootPaths(EmailHelper::resolveTemplateRootPaths($this->configurationManager));
            $emailViewHTML->setPartialRootPaths(EmailHelper::resolvePartialRootPaths($this->configurationManager));

            $emailViewHTML->setTemplate('Email/' . 'OnlineSurvey.html');

            $emailTextHTML = $emailViewHTML->render();
        }

        if ($step == 1) {
            $helper['now'] = time();
            $helper['description'] = TextUtility::foldline(EmailHelper::html2rest($event->getDescription()));
            $helper['location'] = EventHelper::getLocationNameWithParent($event);
            $helper['locationics'] = TextUtility::foldline($helper['location']);

            $allSubscribers = $event->getSubscribers();
            foreach ($allSubscribers as $uid => $subscriber) {
                EmailHelper::sendTemplateEmail(
                    [$subscriber->getEmail() => $subscriber->getName()],
                    [$event->getContact()->getEmail() => $event->getContact()->getName()],
                    'Online-Umfrage zu ' . $event->getTitle(),
                    'OnlineSurvey',
                    [
                        'event'      => $event,
                        'subscriber' => $subscriber,
                        'helper'     => $helper,
                        'settings'   => $this->settings,
                        'attachCsv'  => false,
                        'attachIcs'  => false,
                    ],
                    $this->configurationManager
                );
            }

            // change the onlineSurvey link to see, that we sent it already
            $event->setOnlinesurvey($onlineSurveyLink[0] . '|' . time());
            // we changed the event inside the repository and have to
            // update the repo manually as of TYPO3 6.1
            $this->eventRepository->update($event);
        }

        $this->view->assign('event', $event);

        if (isset($onlineSurveyLink[1])) {
            $this->view->assign('onlineSurveyLastSent', $onlineSurveyLink[1]);
        }

        $this->view->assign('subscribers', $event->getSubscribers());
        $this->view->assign('step', $step);
        $this->view->assign('emailText', $emailTextHTML);
    }
}
