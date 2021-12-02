# Convert PT AO90 for GlotPress

GlotPress language tool to convert text according to the Portuguese Language Orthographic Agreement of 1990 (PT AO90).

[![WordPress Plugin Version](https://img.shields.io/wordpress/plugin/v/gp-convert-pt-ao90?label=Plugin%20Version&logo=wordpress)](https://wordpress.org/plugins/gp-convert-pt-ao90/)
[![WordPress Plugin Rating](https://img.shields.io/wordpress/plugin/stars/gp-convert-pt-ao90?label=Plugin%20Rating&logo=wordpress)](https://wordpress.org/support/plugin/gp-convert-pt-ao90/reviews/)
[![WordPress Plugin Downloads](https://img.shields.io/wordpress/plugin/dt/gp-convert-pt-ao90.svg?label=Downloads&logo=wordpress)](https://wordpress.org/plugins/gp-convert-pt-ao90/advanced/)
[![Sponsor](https://img.shields.io/badge/GitHub-🤍%20Sponsor-ea4aaa?logo=github)](https://github.com/sponsors/pedro-mendonca)

[![WordPress Plugin Required PHP Version](https://img.shields.io/wordpress/plugin/required-php/gp-convert-pt-ao90?label=PHP%20Required&logo=php&logoColor=white)](https://wordpress.org/plugins/gp-convert-pt-ao90/)
[![WordPress Plugin: Required WP Version](https://img.shields.io/wordpress/plugin/wp-version/gp-convert-pt-ao90?label=WordPress%20Required&logo=wordpress)](https://wordpress.org/plugins/gp-convert-pt-ao90/)
[![WordPress Plugin: Tested WP Version](https://img.shields.io/wordpress/plugin/tested/gp-convert-pt-ao90.svg?label=WordPress%20Tested&logo=wordpress)](https://wordpress.org/plugins/gp-convert-pt-ao90/)

[![Coding Standards](https://github.com/pedro-mendonca/GP-Convert-PT-AO90/actions/workflows/coding-standards.yml/badge.svg)](https://github.com/pedro-mendonca/GP-Convert-PT-AO90/actions/workflows/coding-standards.yml)
[![Static Analysis](https://github.com/pedro-mendonca/GP-Convert-PT-AO90/actions/workflows/static-analysis.yml/badge.svg)](https://github.com/pedro-mendonca/GP-Convert-PT-AO90/actions/workflows/static-analysis.yml)
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/534909194f4446c3a865f66536ac4e03)](https://app.codacy.com/manual/pedro-mendonca/GP-Convert-PT-AO90?utm_source=github.com&utm_medium=referral&utm_content=pedro-mendonca/GP-Convert-PT-AO90&utm_campaign=Badge_Grade_Settings)

## Description

This plugins customizes the default behavior of GlotPress Portuguese (Portugal) Locales, allowing you to automatically convert the approved/current strings in Portuguese (Portugal) to its variant of Portuguese (Portugal, AO90).

Keep the Portuguese (Portugal) root translations and the Portuguese (Portugal, AO90) variant translations automatically converted and synced in your GlotPress install.

Only translations whose conversion are different from the original root translation are added to the variant translation set.

The strings that don't need any conversion remain untranslated on the variant, falling back to the root Locale.

This plugin was heavily inspired by the [Serbian Latin](https://meta.trac.wordpress.org/ticket/5471) solution for transliteration of Serbian Cyrillic locale from [translate.wordpress.org](https://meta.trac.wordpress.org/browser/sites/trunk/wordpress.org/public_html/wp-content/plugins/wporg-gp-customizations/inc/locales/class-serbian-latin.php?rev=10360).

The language conversion uses the open source tool [Convert PT AO90](https://github.com/pedro-mendonca/Convert-PT-AO90) to replace entire words from a prebuilt list.

## Features

* Check for active GlotPress.
* Check for existent `pt` root and `pt-ao90` variant translation sets.
* Convert `current` Portuguese (Portugal) root translations and add to the Portuguese (Portugal, AO90) variant translations.
* Delete variant unused translations instead of keeping as `rejected`, `fuzzy`, `old`.
* Delete `current` variant translation if a new root translation (same `original_id`) is added and doesn't need conversion.

## Requirements

* GlotPress 3.x with variants support

* Translation set (root): `Portuguese (Portugal)`
  * Locale = `pt`;
  * Slug = `default`;

* Translation set (variant): `Portuguese (Portugal, AO90)`
  * Locale = `pt-ao90`;
  * Slug = `default`;

## Frequently Asked Questions

### Can I contribute to this plugin?

Sure! You are welcome to report any issues or add feature suggestions on the [GitHub repository](https://github.com/pedro-mendonca/GP-Convert-PT-AO90).

## Changelog

### 1.0.0

* Initial release.
* Check for active GlotPress.
* Check for existent `pt` root and `pt-ao90` variant translation sets.
* Convert `current` Portuguese (Portugal) root translations and add to the Portuguese (Portugal, AO90) variant translations.
* Delete unused variant translations instead of keeping as old. As the variant is intended to be read-only, all the translation work and history is kept on the root variant.
