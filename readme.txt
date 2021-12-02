=== Convert PT AO90 for GlotPress ===
Contributors: pedromendonca
Donate link: https://github.com/sponsors/pedro-mendonca
Tags: localization, translation, glotpress, ao90, portuguese
Requires at least: 4.9
Tested up to: 5.8
Requires PHP: 7.2
Stable tag: 1.0.0
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

GlotPress language tool to convert text according to the Portuguese Language Orthographic Agreement of 1990 (PT AO90).

== Description ==

This plugins customizes the default behavior of GlotPress Portuguese (Portugal) Locales, allowing you to automatically convert the approved/current strings in Portuguese (Portugal) to its variant of Portuguese (Portugal, AO90).

Keep the Portuguese (Portugal) root translations and the Portuguese (Portugal, AO90) variant translations automatically converted and synced in your GlotPress install.

Only translations whose conversion are different from the original root translation are added to the variant translation set.

The strings that don't need any conversion remain untranslated on the variant, falling back to the root Locale.

This plugin was heavily inspired by the [Serbian Latin](https://meta.trac.wordpress.org/ticket/5471) solution for transliteration of Serbian Cyrillic locale from [translate.wordpress.org](https://meta.trac.wordpress.org/browser/sites/trunk/wordpress.org/public_html/wp-content/plugins/wporg-gp-customizations/inc/locales/class-serbian-latin.php?rev=10360).

The language conversion uses the open source tool [Convert PT AO90](https://github.com/pedro-mendonca/Convert-PT-AO90) to replace entire words from a prebuilt list.

== Features ==

*   Check for active GlotPress.
*   Check for existent `pt` root and `pt-ao90` variant translation sets.
*   Convert `current` Portuguese (Portugal) root translations and add to the Portuguese (Portugal, AO90) variant translations.
*   Delete variant unused translations instead of keeping as `rejected`, `fuzzy`, `old`.
*   Delete `current` variant translation if a new root translation (same `original_id`) is added and doesn't need conversion.

== Requirements ==

*   GlotPress

*   Translation set (root): `Portuguese (Portugal)`
       * Locale = `pt`;
       * Slug = `default`;

*   Translation set (variant): `Portuguese (Portugal, AO90)`
       * Locale = `pt-ao90`;
       * Slug = `default`;

== Frequently Asked Questions ==

= Can I contribute to this plugin? =
Sure! You are welcome to report any issues or add feature suggestions on the [GitHub repository](https://github.com/pedro-mendonca/GP-Convert-PT-AO90).

== Changelog ==

= Unreleased =
*   Delete not used variant translations instead of keeping as old. As the variant is intended to be read-only, all the translation work and history is kept on the root variant.

= 1.0.0 =
*   Initial release.
*   Check for active GlotPress.
*   Check for existent `pt` root and `pt-ao90` variant translation sets.
*   Convert `current` Portuguese (Portugal) root translations and add to the Portuguese (Portugal, AO90) variant translations.
*   Sync `current`, `rejected`, `fuzzy`, `old` translations between root and variant locales.
*   Obsoletes `current` variant translation if a new root translation (same `original_id`) is added and doesn't need conversion.
