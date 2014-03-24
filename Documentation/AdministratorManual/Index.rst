.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt

.. _admin-manual:

Administrator Manual
====================

.. _set-storage-folder:

Set Storage Folder of Event Data
--------------------------------

You should set the storagePid for the frontend and for the backend
module to your ``Eventdata`` folder.

For example:

.. container:: table-row

   Property
         storagePid

   Data type
         integer

   Description
         The uid of your storage folder. In this folder all events,
         contacts, categories and subscribers are stored.

         This setting is important for the frontend plugin (``plugin.tx_slubevents.persistence``) to find the
         data.

         It's also important for the backend module (``module.tx_slubevents.persistence``) if your data is not stored on PID ``0``.

   Default
         empty

.. container:: table-row

   Property
         hidePagination

   Data type
         boolean

   Description
         In list view (backend module and frontend plugin) a pagination
         is shown by default. If you want to suppress this pagination for some reason set this option to TRUE

   Default
         0: show pagination

[tsref:plugin.tx_slubevents.persistence]
[tsref:module.tx_slubevents.persistence]


Configure Email Handling
------------------------


.. container:: table-row

   Property
         senderEmailAddress

   Data type
         string

   Description
         Set the sender email address of all outgoing mails.

   Default
         ``webmaster@slub-dresden.de``

.. container:: table-row

   Property
         emailToContact.sendEmailOnMaximumReached

   Data type
         boolean

   Description
         Send email to the contact person if maximum number of subscribers
         is reached and the subscription is closed. The email contains
         the current subscriber list inline and as CSV file.

   Default
         1 = Always send mails.

.. container:: table-row

   Property
         emailToContact.sendEmailOnFreeAgain

   Data type
         boolean

   Description
         In case of cancellation by a customer, an email is sent to the
         contact person if the minimum number of subscribers is not
         reached anymore. The event is not guaranteed anymore.

   Default
         1 = Send mails to contact person in case of cancellation by customer.

.. container:: table-row

   Property
         emailToContact.sendEmailOnEveryBooking

   Data type
         boolean

   Description
         Send email on every subscription / booking that is made to the
         contact person. The email contains always the up-to-date
         subscriber list inline.

   Default
         0 = Don't send mails.

.. container:: table-row

   Property
         baseURL

   Data type
         string

   Description
         In most sent emails the event description is included. The
         description field is an RTE-field and you may use images inside.
         The url of these images in the email are relative and won't work
         in the email program. That's why you may set an baseURL
         which gets included in the HTML content of the emails.

   Default
         empty
         Example: ``http://www.slub-dresden.de/``

[tsref:plugin.tx_slubevents.settings]

Scheduler Tasks
---------------

You can add different task to the backend scheduler.

Check for End of Subscription Period
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

``slub_events:checkevents:checkforsubscriptionend``

This task checks e.g. every 20 minutes if the subscription end is already
reached of future events. If this is the case, an email is sent to the
contact person with the subscription list. After this the subscription
is closed officially. The event gets marked in the database that this
email has been sent already.

If there are not enough subcribers (min_subscriber not reached) the
event is getting cancelled by setting the ``is_cancelled`` property of
the event.

.. t3-field-list-table::
 :header-rows: 1

 - :Argument:
         Argument

   :Example:
         Example

   :Description:
         Description

 - :Argument:
         storagePid

   :Example:
		1234

   :Description:
         Set the storagePid of your ``Eventdata`` folder. The scheduler
         has no access to your page template. That's why you set it here
         again.

 - :Argument:
         senderEmailAddress

   :Example:
		``webmaster@slub-dresden.de``

   :Description:
         Set the sender email address of outgoing emails to the
         contact person.
         Emails to the subscribers get the contact persons email as
         sender address.



Make Statistics Report
^^^^^^^^^^^^^^^^^^^^^^
``slub_events:checkevents:makestatisticsreports``

You may sent a statistics report to one or more given email address
every month about the last months events. The report contains a list of
all events and the number of subscribers inline and as CSV attachment.

.. t3-field-list-table::
 :header-rows: 1

 - :Argument:
         Argument

   :Example:
         Example

   :Description:
         Description

 - :Argument:
         storagePid

   :Example:
		1234

   :Description:
         Set the storagePid of your ``Eventdata`` folder. The scheduler
         has no access to your page template. That's why you set it here
         again.

 - :Argument:
         senderEmailAddress

   :Example:
		``webmaster@slub-dresden.de``

   :Description:
         Set the sender email address of outgoing emails to the
         contact person.
         Emails to the subscribers get the contact persons email as
         sender address.

 - :Argument:
         receiverEmailAddress

   :Example:
		``abc@slub-dresden.de, dfg@slub-dresden.de``

   :Description:
         Set the receiver email address of the statistics report. You
         may enter a comma separated list.

