<?php

namespace Slub\SlubEvents\Domain\Repository;

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
 *
 *
 * @package slub_events
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class ContactRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{
    /**
     * Finds all datasets and return in tree order
     *
     * @return array The found Contact Objects
     */
    public function findAllSorted()
    {
        $query = $this->createQuery();

        $constraints = [];

        if (count($constraints)) {
            $query->matching($query->logicalAnd($constraints));
        }

        $query->setOrderings(
            ['sorting' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING]
        );
        $contacts = $query->execute();

        return $contacts;
    }

    /**
     * Find contact by uid
     *
     * @return array The found Contact Objects
     */
    public function findById($uid) {
        $query = $this->createQuery();
        $constraints = array();
        $query->matching($query->equals('uid', $uid));
        $contact = $query->execute();
        return $contact;
    }
}
