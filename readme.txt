=== Convert PT AO90 for GlotPress ===
Contributors: pedromendonca
Donate link: https://github.com/sponsors/pedro-mendonca
Tags: localization, translation, glotpress, ao90, portuguese
Requires at least: 5.3
Tested up to: 5.8
Requires PHP: 7.2
Stable tag: 1.0.0
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Language tool for GlotPress to convert text according to the Portuguese Language Orthographic Agreement of 1990 (PT AO90).

== Description ==

This plugin for GlotPress customizes the default behavior of the Portuguese (Portugal) Locales, allowing you to automatically convert the approved/current strings in Portuguese (Portugal) to its variant of Portuguese (Portugal, AO90).

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

= I want my WordPress in Portuguese (Portugal, AO90), does this plugin help?
No! To use your WordPress in Portuguese (Portugal AO90) you must go to your Settings and select it in the Language field.
You can also use the plugin [PT AO90](https://wordpress.org/plugins/pt-ao90/) to make sure your site falls back to Portuguese (Portugal) instead of English if there is no translation to Portuguese (Portugal, AO90) for your theme or plugins.

= So what does this plugin really do, after all?
It extends the translation platform GlotPress used to translate WordPress projects.
Since GlotPress 3.x there is a new Variants feature, enabling some Locales to be a variant of a root Locale. With this, comes fallback.
If a translation doesn't exist on the variant, it assumes its root translation.
This plugin links both Portuguese Locales in a way that you only need to focus in translating and manage consistency on the root Portuguese (Portugal), knowing that the variant is being automatically converted and synced with no human action needed.
With this tool, the translators can continue to provide both Locales with the minimum effort.

= Does this means that translations are now converted automatically on translate.wp.org?
No(t yet). This is a working proof of concept, it works on any GlotPress 3.x, but isn't running on [translate.wp.org](https://translate.wp.org) (GlotPress based) at the moment.
Hopefully it will, or at least a clone of this, as this is an open source tool.

= Should this feature be a part of GlotPress itself?
No. And yes.
The relationship between root/variant depend on each team that uses GlotPress.
Depending on how the translation team decides to work. It's useful if automatic conversion is wanted.
For teams that want a root/variant to work automatically, than yes, GlotPress could integrate this optional feature of setting a specific pair of root/variant automatically converted with some custom hookable process, and turning the variant read-only.
This is not an exclusive need of the Portuguese Locales, this is surely useful for other Locales as well.
What should not be a part of GlotPress core is the actual Portuguese conversion, that is plugin territory.
This plugin is intended to be a proof of concept to use and test this workflow.

= Can I contribute to this plugin? =
Sure! You are welcome to report any issues or add feature suggestions on the [GitHub repository](https://github.com/pedro-mendonca/GP-Convert-PT-AO90).

== Changelog ==

= 1.0.0 =
*   Initial release.
*   Check for active GlotPress.
*   Check for existent `pt` root and `pt-ao90` variant translation sets.
*   Convert `current` Portuguese (Portugal) root translations and add to the Portuguese (Portugal, AO90) variant translations.
*   Delete unused variant translations instead of keeping as old. As the variant is intended to be read-only, all the translation work and history is kept on the root set.
