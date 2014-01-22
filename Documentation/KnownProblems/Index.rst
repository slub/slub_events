.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt

.. _known_problems:

Known Problems
================



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

- Please use the `issue tracker on the Forge project website <http://forge.typo3.org/projects/extension-slub_events>`_, if you encounter problems.
