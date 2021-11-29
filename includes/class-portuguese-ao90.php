<?php
/**
 * Class file for the conversion for Portuguese AO90.
 *
 * @package GP_Convert_PT_AO90
 *
 * @since 1.0.0
 */

namespace GP_Convert_PT_AO90;

use GP;
use GP_Locales;
use GP_Translation;
use Convert_PT_AO90;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( __NAMESPACE__ . '\Portuguese_AO90' ) ) {

	/**
	 * Class Portuguese_AO90.
	 */
	class Portuguese_AO90 {


		/**
		 * Registers actions.
		 *
		 * @return void
		 */
		public static function init() {

			/**
			 * Check if GlotPress is activated.
			 */
			if ( ! self::check_gp() ) {
				return;
			}

			/**
			 * Check if AO90 exists in GlotPress version.
			 */
			if ( ! self::check_locale() ) {
				return;
			}

			/**
			 * Test.
			 */
			add_action( 'admin_notices', array( self::class, 'test' ) );

			/**
			 * Load replace pairs.
			 */
			add_action( 'gp_translation_saved', array( self::class, 'queue_translation_for_conversion' ), 5 );

		}


		/**
		 * Render GlotPress not found admin notice.
		 *
		 * @return void
		 */
		public static function test() {

			$text        = 'Activá-lo ou activá-la. Contactar a contacto. Bom aspecto!';
			$string_ao90 = Convert_PT_AO90\convert_pt_ao90( $text );

			?>
			<div class="notice notice-error is-dismissible">
				<p style="color: red;">
					<?php echo $text; ?>
				</p>
				<p style="color: green;">
					<?php echo $string_ao90; ?>
				</p>
			</div>
			<?php

		}


		/**
		 * Check if GlotPress is activated.
		 *
		 * @return bool
		 */
		public static function check_gp() {

			if ( ! class_exists( 'GP' ) ) {
				add_action( 'admin_notices', array( self::class, 'notice_gp_not_found' ) );
				return false;
			}

			return true;

		}

		/**
		 * Render GlotPress not found admin notice.
		 *
		 * @return void
		 */
		public static function notice_gp_not_found() {

			?>
			<div class="notice notice-error is-dismissible">
				<p>
					<?php
					printf(
						/* translators: 1: Plugin name. 2: Error message. */
						esc_html__( '%1$s: %2$s', 'gp-convert-pt-ao90' ),
						'<b>' . esc_html_x( 'GP Convert PT AO90', 'Plugin name', 'gp-convert-pt-ao90' ) . '</b>',
						esc_html__( 'GlotPress not found. Please install and activate it.', 'gp-convert-pt-ao90' )
					);
					?>
				</p>
			</div>
			<?php

		}

		/**
		 * Check if Locale exist in GlotPress.
		 *
		 * @return bool
		 */
		public static function check_locale() {

			/**
			 * Locale definition in GlotPress 3.0.
			 *
			 * $pt_ao90 = new GP_Locale();
			 * $pt_ao90->english_name = 'Portuguese (Portugal, AO90)';
			 * $pt_ao90->lang_code_iso_639_1 = 'pt';
			 * $pt_ao90->native_name = 'Português (AO90)';
			 * $pt_ao90->country_code = 'pt';
			 * $pt_ao90->wp_locale = 'pt_PT_ao90';
			 * $pt_ao90->slug = 'pt-ao90';
			 * $pt_ao90->google_code = 'pt-PT';
			 * $pt_ao90->variant_root = $pt->slug;
			 * $pt->variants[ $pt_ao90->slug ] = $pt_ao90->english_name;
			 */

			// Locale slug for Portuguese (Portugal, AO90).
			$locale_slug = 'pt-ao90';

			$locale = GP_Locales::by_slug( $locale_slug ); // @phpstan-ignore-lines

			if ( null === $locale ) {
				add_action( 'admin_notices', array( self::class, 'notice_locale_not_found' ) );
			}

			return $locale;

		}

		/**
		 * Render Locale not found admin notice.
		 *
		 * @return void
		 */
		public static function notice_locale_not_found() {

			?>
			<div class="notice notice-warning is-dismissible">
				<p>
					<?php
					printf(
						/* translators: 1: Plugin name. 2: Error message. */
						esc_html__( '%1$s: %2$s', 'gp-convert-pt-ao90' ),
						'<b>' . esc_html_x( 'GP Convert PT AO90', 'Plugin name', 'gp-convert-pt-ao90' ) . '</b>',
						esc_html__( 'Locale not found. Please check if your GlotPress version has the Locale "pt-ao90".', 'gp-convert-pt-ao90' )
					);
					?>
				</p>
			</div>
			<?php

		}

		/**
		 * Inserts a Portuguese translation converted to PT AO90 into the pt_PT_ao90 set.
		 *
		 * @param object $translation   \GP_Translation Created/updated translation.
		 *
		 * @return void
		 */
		public static function queue_translation_for_conversion( $translation ) {

			// Only process current translations without warnings.
			if ( 'current' !== $translation->status || ! empty( $translation->warnings ) ) { // @phpstan-ignore-line
				return;
			}

			/**
			 * Only process Portuguese (pt_PT pt/default) translation set.
			 * Locale: 'pt'
			 * Slug: 'default'
			 */
			$translation_set = GP::$translation_set->get( $translation->translation_set_id ); // @phpstan-ignore-line
			if ( ! $translation_set || 'pt' !== $translation_set->locale || 'default' !== $translation_set->slug ) {
				return;
			}

			$original = GP::$original->get( $translation->original_id ); // @phpstan-ignore-line
			if ( ! $original ) {
				return;
			}

			/**
			 * Only process if Portuguese (pt_PT_AO90 pt-ao90/default) translation set exist.
			 * Locale: 'pt-ao90'
			 * Slug: 'default'
			 */
			$translation_set_ao90 = GP::$translation_set->by_project_id_slug_and_locale( $original->project_id, 'default', 'pt-ao90' ); // @phpstan-ignore-line
			if ( ! $translation_set_ao90 ) {
				return;
			}

			$translation_ao90                     = new GP_Translation( $translation->fields() ); // @phpstan-ignore-line
			$translation_ao90->translation_set_id = $translation_set_ao90->id; // @phpstan-ignore-line
			$translation_ao90->status             = 'current'; // @phpstan-ignore-line

			$locale = GP_Locales::by_slug( $translation_set_ao90->locale ); // @phpstan-ignore-line

			$translation_changed = false;

			for ( $i = 0; $i < $locale->nplurals; $i++ ) {

				// Skip if plural don't exist.
				if ( null === $translation->{"translation_{$i}"} ) {
					continue;
				}

				// Skip if the conversion doesn't change the string.
				if ( Convert_PT_AO90\convert_pt_ao90( $translation->{"translation_{$i}"} ) === $translation->{"translation_{$i}"} ) {
					continue;
				}

				$translation_changed = true;

				// Only set AO90 converted string if the conversion produces a different string.
				$translation_ao90->{"translation_{$i}"} = Convert_PT_AO90\convert_pt_ao90( $translation->{"translation_{$i}"} );

			}

			if ( ! $translation_changed ) {
				return;
			}

			$translation_ao90 = GP::$translation->create( $translation_ao90 ); // @phpstan-ignore-line
			if ( ! $translation_ao90 ) {
				return;
			}

			$translation_ao90->set_as_current();
			gp_clean_translation_set_cache( $translation_set_ao90->id ); // @phpstan-ignore-line

		}

	}

}
