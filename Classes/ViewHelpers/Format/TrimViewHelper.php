<?php

namespace Slub\SlubEvents\ViewHelpers\Format;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Alexander Bigga <alexander.bigga@slub-dresden.de>, SLUB Dresden
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

/**
 * trim output
 *
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 * @api
 */
class TrimViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
{
    /**
     * trim whitespaces before and after
     *
     * @param string $htmlString
     *
     * @return string
     * @author Alexander Bigga <alexander.bigga@slub-dresden.de>
     * @api
     */
    public function render($htmlString = null)
    {
        if ($htmlString === null) {
            $htmlString = $this->renderChildren();
            if ($htmlString === null) {
                return '';
            }
        }

        return trim($htmlString);
    }
}
