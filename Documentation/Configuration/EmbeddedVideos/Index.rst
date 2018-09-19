.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt

.. _gdprForExtensions:

Improved privacy for embedded videos
====================================

The template used to show the overlay can be configured with an additional setting
of the ViewHelper `<f:media />`.

Since the versions `8.7.14` and `7.6.28` the ViewHelper call in `fluid_styled_content` looks like this:

.. code-block:: html

    <f:media class="video-embed-item" file="{file}"
        width="{dimensions.width}" height="{dimensions.height}"
        alt="{file.alternative}" title="{file.title}"
        additionalConfig="{settings.media.additionalConfig}" />

This allows to override the template with the following TypoScript:

.. code-block:: typoscript

    lib.contentElement {
        settings {
            media {
                additionalConfig {
                    gdpr-vimeo-template = EXT:sitepackage/Resources/Private/Templates/Gdpr/Vimeo.html
                    gdpr-youtube-template = EXT:sitepackage/Resources/Private/Templates/Gdpr/Youtube.html
                }
            }
        }
    }

Usage in custom extensions
--------------------------
If the `<f:media />` is used in custom extensions, use a configuration like `additionalConfig="{settings.mediaConfiguration}"` and a TS like

.. code-block:: typoscript

    plugin.tx_yourExt {
        settings {
            mediaConfiguration {
                gdpr-vimeo-template = EXT:sitepackage/Resources/Private/Templates/Gdpr/Vimeo.html
                gdpr-youtube-template = EXT:sitepackage/Resources/Private/Templates/Gdpr/Youtube.html
            }
        }
    }


Templates
---------
The templates (default can be found at `EXT:gdpr/Resources/Private/Templates/Rendering/` contain
the styling and vanilla JS for exchanging the overlay with the actual video iframe.
