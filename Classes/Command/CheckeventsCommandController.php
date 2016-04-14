<?php
namespace Slub\SlubEvents\Command;

    /***************************************************************
     *  Copyright notice
     *
     *  (c) 2013 Alexander Bigga <alexander.bigga@slub-dresden.de>
     *  All rights reserved
     *
     *  This script is part of the Typo3 project. The Typo3 project is
     *  free software; you can redistribute it and/or modify
     *  it under the terms of the GNU General Public License as published by
     *  the Free Software Foundation; either version 2 of the License, or
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
 * Command Controller for CLI
 *
 *
 *
 * @author    Alexander Bigga <alexander.bigga@slub-dresden.de>
 */
use Slub\SlubEvents\Helper\EmailHelper;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

class CheckeventsCommandController extends \TYPO3\CMS\Extbase\Mvc\Controller\CommandController
{

    /**
     * eventRepository
     *
     * @var \Slub\SlubEvents\Domain\Repository\EventRepository
     * @inject
     */
    protected $eventRepository;

    /**
     * subscriberRepository
     *
     * @var \Slub\SlubEvents\Domain\Repository\SubscriberRepository
     * @inject
     */
    protected $subscriberRepository;

    /**
     * @var ConfigurationManagerInterface
     */
    protected $configurationManager;

    /**
     * initializeAction
     *
     * @return
     */
    protected function initializeAction()
    {

        // TYPO3 doesn't set locales for backend-users --> so do it manually like this...
        // is needed with strftime
        setlocale(LC_ALL, 'de_DE.utf8');

        // simulate BE_USER setting to force fluid using the proper translation
        $GLOBALS['BE_USER']->uc['lang'] = 'de';
        $GLOBALS['LANG']->init('de');
    }

    /**
     * injectConfigurationManager
     *
     * @param \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManager
     * @return void
     */
    public function injectConfigurationManager(ConfigurationManagerInterface $configurationManager)
    {

        $this->configurationManager = $configurationManager;

        $this->contentObj = $this->configurationManager->getContentObject();

        $this->settings = $this->configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS);

    }

    /**
     * checkForSubscriptionEndCommand
     *
     * @param int    stroagePid
     * @param string senderEmailAddress
     *
     * @return void
     */
    public function checkForSubscriptionEndCommand($storagePid, $senderEmailAddress = '')
    {

        // do some init work...
        $this->initializeAction();

        // abort if no storagePid is found
        if (!MathUtility::canBeInterpretedAsInteger($storagePid)) {
            echo 'NO storagePid given. Please enter the storagePid in the scheduler task.';
            exit(1);
        }
        // abort if no senderEmailAddress is found
        if (empty($senderEmailAddress)) {
            echo 'NO senderEmailAddress given. Please enter the senderEmailAddress in the scheduler task.';
            exit(1);
        }

        // set storagePid to point extbase to the right repositories
        $configurationArray = [
            'persistence' => [
                'storagePid' => $storagePid,
            ],
        ];
        $this->configurationManager->setConfiguration($configurationArray);

        // start the work...

        // 1. get events in future which reached the subscription deadline
        $allevents = $this->eventRepository->findAllSubscriptionEnded();

        foreach ($allevents as $event) {

            // startDateTime may never be empty
            $helper['start'] = $event->getStartDateTime()->getTimestamp();
            // endDateTime may be empty
            if (($event->getEndDateTime() instanceof \DateTime) && ($event->getStartDateTime() != $event->getEndDateTime())) {
                $helper['end'] = $event->getEndDateTime()->getTimestamp();
            } else {
                $helper['end'] = $helper['start'];
            }

            if ($event->isAllDay()) {
                $helper['allDay'] = 1;
            }
            $helper['now'] = time();
            $helper['nameto'] = strtolower(str_replace([',', ' '], ['', '-'], $event->getContact()->getName()));
            $helper['description'] = $this->foldline($event->getDescription());
            // location may be empty...
            if (is_object($event->getLocation())) {
                $helper['location'] = $event->getLocation()->getName();
                $helper['locationics'] = $this->foldline($event->getLocation()->getName());
            }

            // check if we have to cancel the event
            if ($this->subscriberRepository->countAllByEvent($event) < $event->getMinSubscriber()) {
                // --> ok, we have to cancel the event because not enough subscriber were found

                // email to all subscribers
                foreach ($event->getSubscribers() as $subscriber) {
                    $cronLog .= 'Absage an Teilnehmer: ' . $event->getTitle() . ': ' . strftime('%x %H:%M',
                            $event->getStartDateTime()->getTimestamp()) . ' --> ' . $subscriber->getEmail() . "\n";
                    $out = EmailHelper::sendTemplateEmail(
                        [$subscriber->getEmail() => $subscriber->getName()],
                        [$event->getContact()->getEmail() => $event->getContact()->getName()],
                        'Absage der Veranstaltung: ' . $event->getTitle(),
                        'CancellEvent',
                        [
                            'event' => $event,
                            'subscribers' => '',
                            'helper' => $helper,
                            'attachIcs' => true,
                        ]
                    );
                }

                $cronLog .= 'Absage an Veranstalter: ' . $event->getTitle() . ': ' . strftime('%x %H:%M',
                        $event->getStartDateTime()->getTimestamp()) . ' --> ' . $event->getContact()->getEmail() . "\n";
                // email to event owner
                $out = EmailHelper::sendTemplateEmail(
                    [$event->getContact()->getEmail() => $event->getContact()->getName()],
                    [$senderEmailAddress => 'SLUB Veranstaltungen - noreply'],
                    'Absage der Veranstaltung: ' . $event->getTitle(),
                    'CancellEvent',
                    [
                        'event' => $event,
                        'subscribers' => $event->getSubscribers(),
                        'helper' => $helper,
                        'attachCsv' => true,
                        'attachIcs' => true,
                    ]
                );
                if ($out >= 1) {
                    $event->setSubEndDateInfoSent(true);
                    $event->setCancelled(true);
                    $this->eventRepository->update($event);
                }
            } else {
                // event takes place but subscription is not possible anymore...
                // email to event owner
                $cronLog .= 'Anmeldefrist abgelaufen an Veranstalter: ' . $event->getTitle() . ': ' . strftime('%x %H:%M',
                        $event->getStartDateTime()->getTimestamp()) . ' --> ' . $event->getContact()->getEmail() . "\n";
                $out = EmailHelper::sendTemplateEmail(
                    [$event->getContact()->getEmail() => $event->getContact()->getName()],
                    [$senderEmailAddress => 'SLUB Veranstaltungen - noreply'],
                    'Veranstaltung Anmeldefrist abgelaufen: ' . $event->getTitle(),
                    'DeadlineReached',
                    [
                        'event' => $event,
                        'subscribers' => $event->getSubscribers(),
                        'helper' => $helper,
                        'attachCsv' => true,
                        'attachIcs' => true,
                    ]
                );
                if ($out == 1) {
                    $event->setSubEndDateInfoSent(true);
                    $this->eventRepository->update($event);
                }
            }
        }

        echo $cronLog;

        return;
    }


    /**
     * Function foldline folds the line after 73 signs
     * rfc2445.txt: lines SHOULD NOT be longer than 75 octets
     *
     * @param string $content : Anystring
     *
     * @return string        $content: Manipulated string
     */
    private function foldline($content)
    {
        $text = trim(strip_tags(html_entity_decode($content), '<br>,<p>,<li>'));
        $text = preg_replace('/<p[\ \w\=\"]{0,}>/', '', $text);
        $text = preg_replace('/<li[\ \w\=\"]{0,}>/', '- ', $text);
        // make newline formated (yes, really write \n into the text!
        $text = str_replace('</p>', '\n', $text);
        $text = str_replace('</li>', '\n', $text);
        // remove tabs
        $text = str_replace("\t", ' ', $text);
        // remove multiple spaces
        $text = preg_replace('/[\ ]{2,}/', '', $text);
        $text = str_replace('<br />', '\n', $text);
        // remove more than one empty line
        $text = preg_replace('/[\n]{3,}/', '\n\n', $text);
        // remove windows linkebreak
        $text = preg_replace('/[\r]/', '', $text);
        // newlines are not allowed
        $text = str_replace("\n", '\n', $text);
        // semicolumns are not allowed
        $text = str_replace(';', '\;', $text);

        $firstline = substr($text, 0, (75 - 12));
        $restofline = implode("\n ", str_split(trim(substr($text, (75 - 12), strlen($text))), 73));

        return $firstline . "\n " . $restofline;
    }
}
