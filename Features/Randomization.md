[<- back](../Readme.md)

# Randomization

Randomization is a good way to remove the private data from a record and still be able to use the rest o the record.

**Example**: A user wants his orders to be removed. After randomization the private data is removed but it is still possible to generate statistics from the order, e.g. orders from a country.

## Randomize data

Records can be randomized by using the `fzaninotto/faker` library.
By providing a mapping per table, is possible to exchange the data with dummy information which looks still ok and can be used in exports. An example would be

```
Arrayhttps://bitbucket.org/georgringer/gdpr/blob/master/Readme.mdname] => martens.conny
    [email] => gerhild.hartwig@yahoo.de
    [password] => 94n3ifyp($+%u#
    [zip] => 33781
    [address] => Hans-Jürgen-Sauer-Weg 21
86788 Oberursel (Taunus)
    [city] => Pfungstadt
    [first_name] => Miriam
    [last_name] => Sander
    [telephone] => +8747861395322
    [fax] => +5484337015644
)
```

## Configuration

The code must be placed in the file `Configuration/TCA/Overrides/<tableName/>.php`

```php
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
```

Randomization uses [Faker](https://github.com/fzaninotto/Faker#formatters) and the mapping accepts any property of faker.


## Using a CLI command

By using a CLI command, all data with a specific age can be randomized: `./web/typo3/sysext/core/bin/typo3 gdpr:randomize`

Result can look like this

```
Randomize data
==============

Starting with table "be_users"
------------------------------

 // Randomization skipped as not enabled!

Starting with table "fe_users"
------------------------------

 // find all fields where value of field "tstamp" is older than 360 days

 [OK] 3 records randomized

```
