=== Convert PT AO90 for GlotPress ===
Contributors: pedromendonca
Donate link: https://github.com/sponsors/pedro-mendonca
Tags: localization, translation, glotpress, ao90, portuguese
Requires at least: 5.3
Tested up to: 6.4
Requires PHP: 7.4
Stable tag: 1.2.5
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Language tool for GlotPress to convert text according to the Portuguese Language Orthographic Agreement of 1990 (PT AO90).

== Description ==

This plugin for GlotPress customizes the default behavior of the Portuguese (Portugal) Locales, allowing you to automatically convert the approved/current strings in Portuguese (Portugal) to its variant of Portuguese (Portugal, AO90).

It keeps the Portuguese (Portugal) root translations automatically converted and synced with its Portuguese (Portugal, AO90) variant translations within your GlotPress install.

Optionally it's possible to disable the Portuguese (Portugal, AO90) variant translations, making it read-only.

The only translations added to the Portuguese (Portugal, AO90) variant translation set are those which are different from the Portuguese (Portugal) root translation.

The strings that don't need any conversion remain untranslated on the variant, falling back to the root Locale.

This plugin was heavily inspired by the [Serbian Latin](https://meta.trac.wordpress.org/ticket/5471) solution for transliteration of Serbian Cyrillic locale from [translate.wordpress.org](https://meta.trac.wordpress.org/browser/sites/trunk/wordpress.org/public_html/wp-content/plugins/wporg-gp-customizations/inc/locales/class-serbian-latin.php?rev=10360).

The language conversion uses the open source tool [Convert PT AO90](https://github.com/pedro-mendonca/Convert-PT-AO90) to replace entire words from a prebuilt list.

== Features ==

*   Check for active GlotPress.
*   Check for existent `pt` root and `pt-ao90` variant translation sets.
*   Convert `current` Portuguese (Portugal) root translations and add to the Portuguese (Portugal, AO90) variant translations.
*   Delete variant unused translations instead of keeping as `rejected`, `fuzzy`, `old`.
*   Delete `current` variant translation if a new root translation (same `original_id`) is added and doesn't need conversion.
*   Highlight the differences in the automatically converted texts.
*   Use the filter `gp_convert_pt_ao90_edit` to disallow editing translations in the `pt-ao90` variant, making it read-only.

== Requirements ==

*   [GlotPress 3.0.0-alpha](https://github.com/GlotPress/GlotPress/releases/tag/3.0.0-alpha.4) with Variants support.

*   Translation set (root): `Portuguese (Portugal)`
       * Locale = `pt`;
       * Slug = `default`;

*   Translation set (variant): `Portuguese (Portugal, AO90)`
       * Locale = `pt-ao90`;
       * Slug = `default`;

== Frequently Asked Questions ==

= Is it possible to make the variant Portuguese (Portugal, AO90) read-only?
As the translations are automatically converted from the root Locale Portuguese (Portugal), you can disable the users to submit translations to the variant.
As translations are automatically converted from the root Locale Portuguese (Portugal), you can disable the possibility for users to submit translations for the variant, making it a read-only Locale.
To disable editing translations for PT AO90, you can use the filter as follows:
```
/**
 * Disable editing translations for PT AO90.
 */
add_filter( 'gp_convert_pt_ao90_edit', '__return_false' );
```

= I want my WordPress in Portuguese (Portugal, AO90), does this plugin help?
No! To use your WordPress in Portuguese (Portugal AO90) you must go to your Settings and select it in the Language field.
You can also use the plugin [PT AO90](https://wordpress.org/plugins/pt-ao90/) to make sure your site falls back to Portuguese (Portugal) instead of English if there is no translation to Portuguese (Portugal, AO90) for your theme or plugins.

= So what does this plugin really do, after all?
It extends the translation platform GlotPress used to translate WordPress projects.
Since GlotPress 3.x there is a new Variants feature, enabling some Locales to be a variant of a root Locale. With this, comes fallback.
If a translation doesn't exist on the variant, it assumes its root translation.
This plugin links both Portuguese Locales in a way that you only need to focus in translating and manage consistency on the root Portuguese (Portugal), knowing that the variant Portuguese (Portugal, AO90) is being automatically converted and synced with no human action needed.
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

= What versions of GlotPress are compatible with this plugin?
The Variants feature was introduced in [GlotPress 3.0.0-alpha](https://github.com/GlotPress/GlotPress/releases/tag/3.0.0-alpha.4).
Later, on [GlotPress 3.0.0-beta](https://github.com/GlotPress/GlotPress/releases/tag/3.0.0-beta.1) the Variants feature [was removed temporarily](https://github.com/GlotPress/GlotPress/pull/1327), so for now the GlotPress alpha version is the only Variants compatible version, and you must install it for this plugin to do its magic.

= Can I contribute to this plugin? =
Sure! You are welcome to report any issues or add feature suggestions on the [GitHub repository](https://github.com/pedro-mendonca/GP-Convert-PT-AO90).

== Screenshots ==

1. Translation table with gray root translations and green automatically converted strings, with highlighted conversion diffs
2. Variant translation table only with green automatically converted strings, with highlighted conversion diffs

== Changelog ==

= Unreleased =

*   Add filter 'gp_convert_pt_ao90_edit' to allow disable editing and make the Variant read-only.
*   Tested up to WP 6.4.

= 1.2.5 =
*   Use own CSS that is still missing on GlotPress 3.0.0-alpha.4
*   Update the actual [Convert-PT-AO90](https://github.com/pedro-mendonca/Convert-PT-AO90) tool to v1.3.2.
*   Add some more replace pairs.
*   Update dependencies.
*   Tested up to WP 6.1.
*   Tested only on supported PHP versions (7.4+).

= 1.2.4 =
*   Fix HTML escaping.

= 1.2.3 =
*   Add plurals to original text in the translation row preview.
*   Add plural forms labels.

= 1.2.2 =
*   Add prepare to print out to root translation preview row.

= 1.2.1 =
*   Fix missing version number.

= 1.2.0 =
*   Highlight the differences in the automatically converted texts.

= 1.1.1 =
*   Update the actual [Convert-PT-AO90](https://github.com/pedro-mendonca/Convert-PT-AO90) tool to v1.3.1.
*   Fix matching for words with exact case on the replace pairs.

= 1.1.0 =
*   Update the actual [Convert-PT-AO90](https://github.com/pedro-mendonca/Convert-PT-AO90) tool to v1.3.
*   Rebuild replace pairs with half the size.
*   Improve performance by using only lowercase replace pairs.
*   Fix matching words starting with an accented vowel.
*   Remove wrong replace pairs about cardinal points (lowercased since 1945).
*   Add some more replace pairs.
*   Update dependencies.
*   Tested up to WP 6.0.

= 1.0.0 =
*   Initial release.
*   Check for active GlotPress.
*   Check for existent `pt` root and `pt-ao90` variant translation sets.
*   Convert `current` Portuguese (Portugal) root translations and add to the Portuguese (Portugal, AO90) variant translations.
*   Delete unused variant translations instead of keeping as old. As the variant is intended to be read-only, all the translation work and history is kept on the root set.
