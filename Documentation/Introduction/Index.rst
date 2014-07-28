.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


What does it do?
================

This extension is yet another event listing and registration solution
with TYPO3 using extbase and fluid. This extension is developped and
used in production at the Saxony State and University Library in
Dresden, Germany (SLUB): `www.slub-dresden.de <http://www.slub-
dresden.de/>`_

There are two use cases supported:

#. Event listing and detail view with optional subscription

#. Category listing with subscription for single consultation
   ("Genius Bar", "Wissensbar")

On subscription, the customer receives an email including an
ics-calendar invitation. MS Outlook and Thunderbird/Lightning recognize
this component as calendar event.

The templates are not yet all localized but are easily changeable by
overwriting templateRootPath etc. as usual with extbase/fluid
extensions.

Please use for your feedback the `Forge project website
<http://forge.typo3.org/projects/extension-slub_events>`_ . There you
find a issue tracker and the recent code in a repository.


Credits
-------

Some backend icons by `Yusuke Kamiyamane <http://p.yusukekamiyamane.com/>`_.
Licensed under a `Creative Commons Attribution 3.0 License <http://creativecommons.org/licenses/by/3.0/>`_.
