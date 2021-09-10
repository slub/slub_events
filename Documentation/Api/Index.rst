.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt

.. _api:

API
===

.. contents::
	:local:
	:depth: 1

Event list
----------

The API delivers a json formatted list with events. You can manipulate the list with additional parameter.

You have to call this API with a special page type. Just attach "?type=1452982642" to your project url and
typoscript calls the extension "slubevents" and the plugin "apieventlist".

Additional parameter
^^^^^^^^^^^^^^^^^^^^

============================================= ==================== ================================================
Parameter                                     Type                 Comment
============================================= ==================== ================================================
tx_slubevents_apieventlist[category]          String|Integer       Comma separated list of category ids
tx_slubevents_apieventlist[discipline]        String|Integer       Comma separated list of discipline ids
tx_slubevents_apieventlist[contact]           String|Integer       Comma separated list of contact ids
tx_slubevents_apieventlist[showPastEvents]    Integer (0|1)        Default is to show events beginning with today
tx_slubevents_apieventlist[showEventsFromNow] Integer (0|1)        Additional setting for "showPastEvents"
tx_slubevents_apieventlist[limitByNextWeeks]  Integer              Set a limit for the next weeks
tx_slubevents_apieventlist[startTimestamp]    Integer (Timestamp)  Influence the start date, works together with stopTimestamp
tx_slubevents_apieventlist[stopTimestamp]     Integer (Timestamp)  Influence the stop date, works together with startTimestamp
tx_slubevents_apieventlist[sorting]           String (asc|desc)    Default value is ascending
tx_slubevents_apieventlist[limit]             Integer              Limit quantity of result data
============================================= ==================== ================================================

If you use these parameter add "tx_slubevents_apieventlist" in [FE][cacheHash][cachedParametersWhiteList] and
[FE][cacheHash][excludedParameters].
