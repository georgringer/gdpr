[<- back](Readme.md)

# Configuration

## Add restriction to custom tables

Example call how an own record can be added.

```php
$tca = \GeorgRinger\Gdpr\Service\Tca::getInstance('be_users');
$tca
    ->addRestriction('gdpr_restricted')
    -add('after:disable');
```

## Add randomization

```php
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

## Screenshots

The GDPR module shows the current configuration.

![Configuration](../Resources/Public/Documentation/Screenshots/Configuration.png)

# Technical background

The implementation is based on the `RestrictionContainers` of the TYPO3 core.

## Drawbacks

The limitation of the implementation is that only records having a TCA configuration are covered.
Furthermore direct access to the database without using the `QueryBuilder` of TYPO3 will still deliver every record.

