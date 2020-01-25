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
class NewMonthTitleViewHelper extends \TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper
{
    /**
     * eventRepository
     *
     * @var \Slub\SlubEvents\Domain\Repository\EventRepository
     */
    protected $eventRepository;

    /**
     * injectEventRepository
     *
     * @param \Slub\SlubEvents\Domain\Repository\EventRepository $eventRepository
     *
     * @return void
     */
    public function injectEventRepository(\Slub\SlubEvents\Domain\Repository\EventRepository $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    /**
     * Render the supplied DateTime object as a formatted date.
     *
     * @param  \TYPO3\CMS\Extbase\Persistence\Generic\QueryResult $events
     * @param int $index
     *
     * @return int
     * @author Alexander Bigga <alexander.bigga@slub-dresden.de>
     * @api
     */
    public function render($events, $index)
    {
        // the first is shown anyway...
        if ($index == 0) {

            /** @var \Slub\SlubEvents\Domain\Model\Event $event */
            $event = $events[$index];
            $date = $event->getStartDateTime();

            if ($date instanceof \DateTime) {
                return $date;
            }
        } else {
            /** @var \Slub\SlubEvents\Domain\Model\Event $event */
            $event = $events[$index];
            $date = $event->getStartDateTime();
            /** @var \Slub\SlubEvents\Domain\Model\Event $preevent */
            $preevent = $events[$index - 1];
            $predate = $preevent->getStartDateTime();

            if ($date->format('m') != $predate->format('m')) {
                return $date;
            }
        }

        return;
    }
}
