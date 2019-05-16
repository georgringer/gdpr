.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt

.. _gdprForExtensions:

GDPR support for custom extensions
==================================

Follow this chapter to learn how the privacy features can be used in a 3rd party extension.

.. only:: html

.. contents::
        :local:
        :depth: 1

.. Info::

    **Technical background**: The implementation is based on the `RestrictionContainers` of the TYPO3 core.

    **Drawbacks**: The limitation of the implementation is that only records having a TCA configuration are covered.
    Furthermore direct access to the database without using the `QueryBuilder` of TYPO3 will still deliver every record.


Basic implementation
--------------------

The basic implementation enables you to have the checkbox "Record must be hidden everywhere"
but without the randomization which will be explained below.

In this example, the persisted mails of *EXT:powermail* are extended with the privacy feature.
For your custom extensions, just exchange the tablenames!

Code changes
^^^^^^^^^^^^

The following 2 files need to be changed/added to your theme extension:

`ext_tables.sql`:

.. code-block:: sql

    CREATE TABLE tx_powermail_domain_model_mail (
        gdpr_restricted tinyint(4) DEFAULT '0' NOT NULL
    );


`Configuration/TCA/Overrides/tx_powermail_domain_model_mail.php`:

.. code-block:: php

    <?php
    $tca = \GeorgRinger\Gdpr\Service\Tca::getInstance('tx_powermail_domain_model_mail');
    $tca
        ->addRestriction('gdpr_restricted') // name of the field used for the checkbox to flag records
        ->add('after:disable'); // positioning of the new field

Required actions
^^^^^^^^^^^^^^^^
Switch to the Install Tool and do a **Database compare**. After clearing all caches, everything should work out fine.


Randomization
-------------
If you want to use randomization, you also need the features of the basic implementation.

In this example, the records of *fe_users* are extended with the privacy and randomization feature.
For your custom extensions, just exchange the tablenames!

Code changes
^^^^^^^^^^^^

The following 2 files need to be changed/added to your theme extension:

`ext_tables.sql`:

.. code-block:: sql

    CREATE TABLE fe_users (
        gdpr_restricted tinyint(4) DEFAULT '0' NOT NULL,
        gdpr_randomized tinyint(4) DEFAULT '0' NOT NULL
    );

`Configuration/TCA/Overrides/fe_users.php`:

.. code-block:: php

    <?php
    $tca = \GeorgRinger\Gdpr\Service\Tca::getInstance('fe_users');
    $tca
        ->addRestriction('gdpr_restricted')
        ->addRandomization('gdpr_randomized', [
            'dateField' => 'tstamp',
            'expirePeriod' => 360,
            'mapping' => [
                'username' => 'userName',
                'email' => 'email',
                'password' => 'password',
                'zip' => 'postcode',
                'address' => 'address',
                'city' => 'city',
                'first_name' => 'firstName',
                'last_name' => 'lastName',
                'telephone' => 'e164PhoneNumber',
                'fax' => 'e164PhoneNumber',
            ]
        ])
        ->add('after:disable');

Randomization uses a 3rd party library called `Faker` and the mapping accepts any property of faker
which are described in detail at https://github.com/fzaninotto/Faker#formatters.


Working with the restrictions
-----------------------------
If you implent the privacy feature into one of your extensions, you should read about the following query restrictions which are available within the GDPR extension.

GdprRestriction
^^^^^^^^^^^^^^^
This restriction is **always** in use and takes care about hiding records.
This means that even if a code like :php:`$queryBuilder->getRestrictions()->removeAll()` this restriction is still active!

If you want to work with *all* records, you need to explicitly remove this restriction with a code like this:

.. code-block:: php

    $queryBuilder = $this->getQueryBuilder($tableName);
    $queryBuilder->getRestrictions()
        ->removeAll()
        ->removeByType(\GeorgRinger\Gdpr\Database\Query\Restriction\GdprRestriction::class);


GdprOnlyRestriction
^^^^^^^^^^^^^^^^^^^
Use this restriction to get only those records which are marked as hidden.

.. code-block:: php

    $queryBuilder->getRestrictions()
        ->removeAll()
        ->removeByType(\GeorgRinger\Gdpr\Database\Query\Restriction\GdprRestriction::class)
        ->add(GeneralUtility::makeInstance(\GeorgRinger\Gdpr\Database\Query\Restriction\GdprOnlyRestriction::class));

GdprRandomizedRestriction
^^^^^^^^^^^^^^^^^^^^^^^^^
This query restriction should be used if you don't want to get those records as result which are marked as randomized.
As an example: If you send newsletters to some email addresses, you should remove the randomized records as this doesn't make sense. Furhtermore those mails could even reach real persons if those have registered the randomized email address!

.. code-block:: php

    $queryBuilder->getRestrictions()
        ->add(GeneralUtility::makeInstance(\GeorgRinger\Gdpr\Database\Query\Restriction\GdprRandomizedRestriction::class));
