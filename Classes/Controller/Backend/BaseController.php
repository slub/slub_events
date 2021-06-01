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

use Slub\SlubEvents\Controller\AbstractController;
use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Backend\View\BackendTemplateView;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Base class for backend modules
 * Parts of the code are inspired by EXT:news
 *
 * @package slub_events
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class BaseController extends AbstractController
{
    /**
     * Backend Template Container
     *
     * @var BackendTemplateView
     */
    protected $defaultViewObjectName = BackendTemplateView::class;

    /**
     * @var array
     */
    protected $pageInformation;

    /**
     * Function will be called before every other action
     *
     */
    public function initializeAction()
    {
        $this->pageUid = (int)GeneralUtility::_GET('id');
        $this->pageInformation = BackendUtility::readPageAccess($this->pageUid, '');
        parent::initializeAction();
    }

    /**
     * Set up the doc header properly here
     *
     * @param ViewInterface $view
     */
    protected function initializeView(ViewInterface $view)
    {
        /** @var BackendTemplateView $view */
        parent::initializeView($view);
        if ($view->getModuleTemplate()) {
            $view->getModuleTemplate()->getDocHeaderComponent()->setMetaInformation([]);

            $this->createMenu();
            $this->createButtons();
        }
    }

    /**
     * Create menu
     */
    protected function createMenu()
    {
        $uriBuilder = $this->objectManager->get(UriBuilder::class);
        $uriBuilder->setRequest($this->request);

        $menu = $this->view->getModuleTemplate()->getDocHeaderComponent()->getMenuRegistry()->makeMenu();
        $menu->setIdentifier('slub_events');

        $actions = [
            ['controller' => 'Backend\Event', 'action' => 'beList', 'label' => 'tx_slubevents.be.eventmanagement'],
            ['controller' => 'Backend\Subscriber', 'action' => 'beList', 'label' => 'tx_slubevents.be.subscriberlist']
        ];

        foreach ($actions as $action) {
            $item = $menu->makeMenuItem()
                ->setTitle(
                    // TODO: make this more flexible and changeable by TypoScript or an alternative language file
                    LocalizationUtility::translate($action['label'], 'slub_events')
                )
                ->setHref($uriBuilder->reset()->uriFor($action['action'], [], $action['controller']))
                ->setActive(
                    $this->request->getControllerName() === $action['controller'] &&
                    $this->request->getControllerActionName() === $action['action']
                );
            $menu->addMenuItem($item);
        }

        $this->view->getModuleTemplate()->getDocHeaderComponent()->getMenuRegistry()->addMenu($menu);

        if (is_array($this->pageInformation)) {
            $this->view->getModuleTemplate()->getDocHeaderComponent()->setMetaInformation($this->pageInformation);
        }
    }

    /**
     * Create the panel of buttons
     */
    protected function createButtons()
    {
        $buttonBar = $this->view->getModuleTemplate()->getDocHeaderComponent()->getButtonBar();

        // Shortcut
        if ($this->getBackendUser()->mayMakeShortcut()) {
            $shortcutButton = $buttonBar->makeShortcutButton()
                ->setModuleName('web_SlubEventsSlubevents')
                ->setGetVariables(['route', 'module', 'id'])
                ->setDisplayName('Shortcut');
            $buttonBar->addButton($shortcutButton, ButtonBar::BUTTON_POSITION_RIGHT);
        }
    }

    /**
     * Get backend user
     *
     * @return BackendUserAuthentication
     */
    protected function getBackendUser()
    {
        return $GLOBALS['BE_USER'];
    }
}
