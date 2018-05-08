# TYPO3 Extension `gdpr`

[![License](https://poser.pugx.org/georgringer/gdpr/license)](https://packagist.org/packages/georgringer/gdpr)

This extensions makes it easier for website owners and agencies to have the site compatible to the GDPR (German "DSGVO").

## Costs

- Personal license for one site: *€ 240 excl. taxes*
- Professional license for one site: *€ 1450 excl. taxes* (*\**)
- Agency license for up to 25 sites: *€ 5000 excl. taxes* (*\**)
- Agency license for unlimited sites: *€ 7500 excl. taxes* (*\**)
- Academic license for universities, research Institutions, and colleges  *€ 490 excl. taxes*

**(*\**) Important: Get a 30% discount before 25th May 2018!**

Contact me - *Georg Ringer* via [mail](mailto:mail@ringer.it), [TYPO3 slack](https://forger.typo3.com/slack) or [twitter](https://twitter.com/georg_ringer).

Costs are **per** installation - discounts possible, ask me directly.


[Screenshots](Screenshots.md)
[Setup](Setup.md)

## Drawbacks

The limitation of the implementation is that only records having a TCA configuration are covered.
Furthermore direct access to the database without using the `QueryBuilder` of TYPO3 will still deliver every record.

## Features

[Features](Features.md)


### Search

A search, similar to the one in the *DB Check* module allows to search within sensitive records.

### Logs

See and filter any action of GDPR related actions



### Report for report module

A report shows a short information about potential actions.

