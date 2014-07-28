.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt

.. _known_problems:

Known Problems
================


Configuration
-------------

- In the list view, month names are written as fulltext as separators.
  If you encounter problems with the selected language or with the encoding,
  please check your locale settings. Of course, the set locale must
  exist on your server, because these date strings are rendered directly
  by an PHP function (strftime).

  .. figure:: ../Images/KnownProblems/slub-events-php-locales.jpg
	:width: 500px
	:alt: Wrong locale setting

	Broken "Umlaute" because of missing or wrong locale settings.


  .. code-block:: none

	config.locale_all = de_DE.UTF-8

- Not everything is localized yet. Especially some fluid templates have
  pure German texts. But these templates you have to customize anyway.
  Nevertheless, we continue our work to translate the contents
  step-by-step.


Limitations
-----------

- There is no way to add extra fields to the registration form.
  The fields are connected to the data model which makes it easy to use
  the built-in validation by Extbase.

  To add new fields, you have to
  change the database scheme, the data model, the subscriber controler,
  etc. If you have a better approach: please fill in a `feature request
  on the Forge project website
  <http://forge.typo3.org/projects/extension-slub_events>`_ and explain
  your solution.

  Thank you!
