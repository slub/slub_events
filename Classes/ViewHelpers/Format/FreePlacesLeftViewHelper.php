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

/**
 * Calculate Free Places
 *
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 * @api
 */
class FreePlacesLeftViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
{

    /**
     * subscriberRepository
     *
     * @var \Slub\SlubEvents\Domain\Repository\SubscriberRepository
     * @inject
     */
    protected $subscriberRepository;

    /**
     * Calculate the free places for a given event.
     *
     * @param \Slub\SlubEvents\Domain\Model\Event $event
     *
     * @return int
     * @author Alexander Bigga <alexander.bigga@slub-dresden.de>
     * @api
     */
    public function render(\Slub\SlubEvents\Domain\Model\Event $event = null)
    {
        if ($event != null) {
            $free = $event->getMaxSubscriber() - $this->subscriberRepository->countAllByEvent($event);
        } else {
            $free = 0;
        }

        return ($free > 0) ? $free : 0;
    }
}
