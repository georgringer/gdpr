.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt

.. _extensionManager:

Global extension configuration
==============================

The following settings can be configured in the extension manager (TYPO3 8) or in the Install Tool (TYPO3 9).

.. only:: html

.. contents::
        :local:
        :depth: 1


randomizerLocale
----------------

Define the locale which is used for randomization. Different locales deliver different random values. Examples for using `fr_FR`:

- Phone number: +33 (0)1 67 97 01 31
- Name: Luce du Coulon

The available locales can be found at https://github.com/fzaninotto/Faker/tree/master/src/Faker/Provider.

overloadMediaRenderer
---------------------
If set, the media renderers for `YouTube` & `Vimeo` are improved privacy wise.