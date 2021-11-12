<?php

namespace Slub\SlubEvents\Mvc\View;

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
        'error' => [
        ],
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
                    'rootCategories' => [
                        '_descendAll' => [
                            '_only' => ['uid', 'title']
                        ]
                    ],
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
                    'rootCategories' => [
                        '_descendAll' => [
                            '_only' => ['uid', 'title']
                        ]
                    ],
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
     * @return mixed
     */
    protected function transformValue($value, array $configuration)
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
