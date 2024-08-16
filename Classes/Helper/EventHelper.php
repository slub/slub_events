<?php
namespace Slub\SlubEvents\Helper;

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

use Slub\SlubEvents\Domain\Model\Event;

class EventHelper
{

    /**
     * Resolves the location name of an event,
     * if a parent is available their names will be concatinated
     * e.g. <parent>, <child>
     *
     * @param Event Event to get the location from
     *
     * @return string Manipulated string
     */
    public static function getLocationNameWithParent(Event $event)
    {
        $locationName = '';

        if (is_object($event->getLocation())) {
            if (is_object($event->getLocation()->getParent()->current())) {
                $locationName = $event->getLocation()->getParent()->current()->getName() . ', ';
            }
            $locationName .= $event->getLocation()->getName();
        }

        return $locationName;
    }
}
