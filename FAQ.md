[<- back](Readme.md)

# FAQ

## Why not add the GDPR extension to the core?

It is a lot easier to maintain an extension.
Releases can happen a lot more often and it is possible to support 8.7 with new features which would not be possible if it would be in the core.

## What about support for 7.6 or 6.2?

Certain features like hiding records everywhere is not possible in 7.6 because there is no *Doctrine DBAL*.

It would be possible to make a separate version for 7.6 or even for 6.2 covering the following features:

- Randomization
- Form overview

If you are interested in this version and can help sponsoring it, please contact me

## What about the GDPR features in the core?

All GDPR features which have been added to the latest versions of TYPO3 core have there beginning in this extension.
Those have been developed and tested as feature in the extension first and then have been moved to the core.