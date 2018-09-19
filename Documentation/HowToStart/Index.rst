.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt

.. _howToStart:

How to start
============
This walkthrough will help you to implement the extension gdpr for your
TYPO3 site.

.. only:: html

.. contents::
        :local:
        :depth: 1

.. _howToStart:

Install extension
-----------------

The extension needs to be installed as any other extension of TYPO3 CMS:

#. Switch to the module “Extension Manager”.

#. Get the extension

   #. **Get it from the Extension Manager:** Press the “Retrieve/Update”
      button and search for the extension key *gdpr* and import the
      extension from the repository.

   #. **Get it from typo3.org:** You can always get current version from
      `http://typo3.org/extensions/repository/view/gdpr/current/
      <http://typo3.org/extensions/repository/view/gdpr/current/>`_ by
      downloading either the t3x or zip version. Upload
      the file afterwards in the Extension Manager.

   #. **Use composer**:

      .. code-block:: bash

          composer require georgringer/gdpr

   #. **Latest version from git**:

      You can get the latest version from git by using the git command:

      .. code-block:: bash

          git clone git@github.com:georgringer/gdpr.git



Enable the administration module
--------------------------------
By default, the administration module must be activated for every backend user separately.

This needs to be done in the record of the BE-User in the tab *Options* with the setting **Enable GDPR module**.

|img-screenshot-enable-module|