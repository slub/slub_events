Extension Manual
================

**slub_events** is an TYPO3 Extbase/Fluid based tool for event registration and experts booking. It is developped and used in
production at the Saxony State and University Library (SLUB) Dresden, Germany.

HowTo Start
-----------

* `read the manual`_ in reST-Format
* have a look on the live usage at https://www.slub-dresden.de/en/visit/trainings-events/
* post an issue if you find one
* contribute by creating a pull request

.. _read the manual: https://docs.typo3.org/p/slub/slub-events/master/en-us/

Api
---

There exists an api to get all the events as json format. You can call this api with a special type. Just attach
"?type=1452982642" to your url and typoscript will handle the rest like calling the extension "slubevents" and the
plugin "apieventlist".

You can influence the result of events with the following additional parameter:

* tx_slubevents_apieventlist[category]: comma separated string, list of category ids
* tx_slubevents_apieventlist[discipline]: comma separated string, list of discipline ids
* tx_slubevents_apieventlist[contact]: comma separated string, list of contact ids
* tx_slubevents_apieventlist[showPastEvents]: 0|1, in default (0) events are shown with beginning today
* tx_slubevents_apieventlist[showEventsFromNow]: 0|1, additional setting for "showPastEvents"
* tx_slubevents_apieventlist[limitByNextWeeks]: integer, set a limit for the next weeks
* tx_slubevents_apieventlist[startTimestamp]: timestamp, influence the start date, works together with stopTimestamp
* tx_slubevents_apieventlist[stopTimestamp]: timestamp, influence the stop date, works together with startTimestamp
* tx_slubevents_apieventlist[sorting]: asc|desc, default value is ascending
* tx_slubevents_apieventlist[limit]: integer, limitation of result data

If you use these parameter add "tx_slubevents_apieventlist" in [FE][cacheHash][cachedParametersWhiteList] and
[FE][cacheHash][excludedParameters].
