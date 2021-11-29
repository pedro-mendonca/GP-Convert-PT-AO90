=== GP Convert PT AO90 ===
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

This plugins customizes the default behavior of GlotPress Portuguese (Portugal) Locales, allows you to use automatically convert the approved/current strings in Portuguese (Portugal AO90) to its variant of Portuguese (Portugal).

Only translations whose conversion are different from the original translation are set in the Variant translation table.

The strings where there is no conversion needed remain untranslated on the Variant, falling back to the root Locale.

== Requirements ==

*   GlotPress
*   Locale 'pt-ao90':
       Locale = `pt-ao90`;
       Slug   = `default`;

= Can I contribute to this plugin? =
Sure! You are welcome to report any issues or add feature suggestions on the [GitHub repository](https://github.com/pedro-mendonca/GP-Convert-PT-AO90).

== Changelog ==

= 1.0.0 =
*   Initial release.
