.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _configuration:


Plugin Reference
================

.. contents::
	:local:
	:depth: 1

Common Settings
---------------

Page of List View
^^^^^^^^^^^^^^^^^

Page which containts the list view (see `SLUB: Events: Listing`_).

Page of Single View
^^^^^^^^^^^^^^^^^^^

Page which contains the single view (see `SLUB: Events: Listing`_).

Page of Subscribe Form
^^^^^^^^^^^^^^^^^^^^^^

Page which contains the subscription form plugi (see `SLUB: Events: Registration`_).


.. _configuration:plugin-eventlist

SLUB: Events: Listing
---------------------

This plugin offers the main list and single event view.

Properties
^^^^^^^^^^

Select Function
"""""""""""""""
Select function of this plugin:

   * list view: show list of events depending on settings
   * single view: show one single event

Show Only Selected Categories
"""""""""""""""""""""""""""""

Show only events with selected categories.

Include Child Categories
""""""""""""""""""""""""

Show also events of all sub-categories.

Show Only Selected Disciplines
""""""""""""""""""""""""""""""

Show only events with selected disciplines.

Include Child Disciplines
"""""""""""""""""""""""""

Show also events of all sub-disciplines.

Show Past Events
""""""""""""""""

Show events in the past. This may be interesting for some archive page.

The default behaviour of the listview is to show only events which start today.

Event Ordering
""""""""""""""

Ordering of the listed events by event start date and time.

Show Only Selected Contacts
"""""""""""""""""""""""""""

Show only event of the selected contact(s).



.. _configuration:plugin-eventsubscribe

SLUB: Events: Registration
--------------------------

This plugin offers the form for subscribe and unsubscribe to events.

Properties
^^^^^^^^^^

Select Function
"""""""""""""""

  * Subscribe View: show the event subscribe form
  * Unsubscribe View: show the event unsubscribe form

Page of MyEvents View
"""""""""""""""""""""

Page which contains the user panel plugin (see `SLUB: Events: User Panel`_).

Page of Subscribe Form
""""""""""""""""""""""""

Page which contains this plugin with the subscribe view.

Page of Unsubscribe Form
""""""""""""""""""""""""

Page which contains this plugin with the unsubscribe view.


.. _configuration:plugin-eventsubscribe

SLUB: Events: User Panel
------------------------

This plugin shows all future events, the logged in user subscribed to.

Past events are not shown.

Properties
^^^^^^^^^^

Page of Unsubscribe Form
""""""""""""""""""""""""

Page which contains this plugin with the unsubscribe view (see `SLUB: Events: Registration`_).
