<?php

namespace Slub\SlubEvents\Mvc\View;

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

use DateTime;
use TYPO3\CMS\Extbase\Mvc\View\JsonView as ExtbaseJsonView;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * @package slub_events
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class JsonView extends ExtbaseJsonView
{
    /**
     * @var array
     */
    protected $configuration = [
        'events' => [
            '_descendAll' => [
                '_exclude' => ['pid'],
                '_descend' => [
                    'categories' => [
                        '_descendAll' => [
                            '_only' => ['uid', 'title']
                        ]
                    ],
                    'contact' => [
                        '_exclude' => ['pid']
                    ],
                    'discipline' => [
                        '_descendAll' => [
                            '_exclude' => ['pid']
                        ],
                    ],
                    'endDateTime' => [],
                    'location' => [
                        '_exclude' => ['pid']
                    ],
                    'parent' => [
                        '_only' => ['uid', 'title']
                    ],
                    'recurringOptions' => [],
                    'recurringEndDateTime' => [],
                    'startDateTime' => [],
                    'subscribers' => [
                        '_descendAll' => [
                            '_only' => ['uid', 'customerid']
                        ]
                    ]
                ]
            ]
        ],
        'eventsUser' => [
            '_descendAll' => [
                '_exclude' => ['pid'],
                '_descend' => [
                    'categories' => [
                        '_descendAll' => [
                            '_only' => ['uid', 'title']
                        ]
                    ],
                    'contact' => [
                        '_exclude' => ['pid']
                    ],
                    'discipline' => [
                        '_descendAll' => [
                            '_exclude' => ['pid']
                        ],
                    ],
                    'endDateTime' => [],
                    'location' => [
                        '_exclude' => ['pid']
                    ],
                    'parent' => [
                        '_only' => ['uid', 'title']
                    ],
                    'recurringOptions' => [],
                    'recurringEndDateTime' => [],
                    'startDateTime' => []
                ]
            ]
        ]
    ];

    /**
     * Always transforming object storages to arrays for the JSON view
     *
     * @param mixed $value
     * @param array $configuration
     * @return array|null
     */
    protected function transformValue($value, array $configuration): ?array
    {
        if ($value instanceof ObjectStorage) {
            $value = $value->toArray();
        }

        if ($value instanceof DateTime) {
            return [
                'format' => $value->format('c'),
                'timestamp' => $value->getTimestamp()
            ];
        }

        // "recurringOptions" is written as serialized string with "weekday" and "interval".
        // Use them as key words to identify and return as array. If not it fails
        if (is_array($value) && (isset($value['weekday'], $value['interval']))) {
            return [
                'weekday' => $value['weekday'],
                'interval' => $value['interval']
            ];
        }

        return parent::transformValue($value, $configuration);
    }
}
