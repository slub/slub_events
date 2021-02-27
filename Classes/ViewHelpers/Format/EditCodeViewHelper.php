<?php
namespace Slub\SlubEvents\ViewHelpers\Format;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Alexander Bigga <alexander.bigga@slub-dresden.de>, SLUB Dresden
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

use \Slub\SlubEvents\Domain\Model\Event;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

/**
 * Adds the Editcode to the form and to the user session
 *
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 * @api
 */
class EditCodeViewHelper extends \TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper
{
    use CompileWithRenderStatic;

    /**
     * Initialize arguments.
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('event', Event::class, 'Event', true);
    }

    /**
     * Get session data
     *
     * @param string $key
     * @return string
     */
    protected static function getSessionData($key)
    {
        return $GLOBALS['TSFE']->fe_user->getKey('ses', $key);
    }

    /**
     * Set session data
     *
     * @param string $key
     * @param string $data
     */
    protected static function setSessionData($key, $data)
    {
        $userGlobals = $GLOBALS['TSFE']->fe_user;
        $userGlobals->setAndSaveSessionData($key, $data);
        return;
    }

    /**
     * Render the supplied DateTime object as a formatted date.
     *
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        $event = $arguments['event'];

        $editCodeDummy = self::getSessionData('editcode');

        // create new editcode-dummy code
        if (empty($editCodeDummy)) {
            $editCodeDummy = hash('sha256', rand() . $event->getTitle() . time() . 'dummy');
        }

        // set editcode-dummy for Spam/Form-double-sent protection
        self::setSessionData('editcode', $editCodeDummy);

        return $editCodeDummy;
    }
}
