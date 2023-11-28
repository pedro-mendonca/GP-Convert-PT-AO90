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

			// Register and enqueue plugin style sheet.
			add_action( 'wp_enqueue_scripts', array( self::class, 'register_plugin_styles' ) );

			/**
			 * Customize permissions on specific templates to make the Variant read-only.
			 */
			add_action( 'gp_pre_tmpl_load', array( self::class, 'pre_template_load' ), 10, 2 );

			/**
			 * Get GP-Convert-PT-AO90 templates.
			 */
			add_filter( 'gp_tmpl_load_locations', array( self::class, 'template_load_locations' ), 10, 4 );

			/**
			 * Converts a Portuguese (pt/default) translation to PT AO90 into the pt_PT_ao90 (pt-ao90/default) translation set.
			 */
			add_action( 'gp_translation_saved', array( self::class, 'queue_translation_for_conversion' ) );
		}


		/**
		 * Check if GlotPress is activated.
		 *
		 * @since 1.0.0
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
		 * @since 1.0.0
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
						'<b>' . esc_html_x( 'Convert PT AO90 for GlotPress', 'Plugin name', 'gp-convert-pt-ao90' ) . '</b>',
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
		 * @since 1.0.0
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

			$locale = GP_Locales::by_slug( $locale_slug );

			if ( is_null( $locale ) ) {
				add_action( 'admin_notices', array( self::class, 'notice_locale_not_found' ) );
			}

			return $locale;
		}


		/**
		 * Render Locale not found admin notice.
		 *
		 * @since 1.0.0
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
						'<b>' . esc_html_x( 'Convert PT AO90 for GlotPress', 'Plugin name', 'gp-convert-pt-ao90' ) . '</b>',
						esc_html__( 'Locale not found. Please check if your GlotPress version has the Locale "pt-ao90".', 'gp-convert-pt-ao90' )
					);
					?>
				</p>
			</div>
			<?php
		}


		/**
		 * Customize permissions on specific templates to make the Variant read-only.
		 *
		 * @since 1.3.0
		 *
		 * @param string            $template   The template name.
		 * @param array<int,string> $args       Arguments passed to the template.
		 *
		 * @return void
		 */
		public static function pre_template_load( $template, &$args ) {

			// Check if the the Variant is read-only.
			if ( isset( $args['locale_slug'] ) && $args['locale_slug'] === 'pt-ao90' && GP_CONVERT_PT_AO90_EDIT === false ) {

				// Customize $args on 'translations' template, and also on 'translation-row' to override the $can_approve_translation before loading 'translation-row'.
				if ( $template === 'translations' || $template === 'translation-row' ) {

					// Disable all the translation editing for the Variant.
					$args['can_edit']                = false; // Disable translation editor.
					$args['can_write']               = false; // Disable write priority on translation-row-editor-meta.
					$args['can_approve']             = false; // Disable bulk translations approval, set the appropriate colspan for the table.
					$args['can_approve_translation'] = false; // Disable single translation approval.
					$args['can_import_current']      = false; // Disable translations import as 'current'.
					$args['can_import_waiting']      = false; // Disable translations import as 'waiting'.

				}
			}
		}


		/**
		 * Get GP-Convert-PT-AO90 templates.
		 *
		 * @since 1.2.0
		 *
		 * @param array<int,string> $locations       File paths of template locations.
		 * @param string            $template        The template name.
		 * @param array<int,string> $args            Arguments passed to the template.
		 * @param string|null       $template_path   Priority template location, if any.
		 *
		 * @return array<int,string>   Template location.
		 */
		public static function template_load_locations( $locations, $template, $args, $template_path ) {

			// Destroy.
			unset( $args, $template_path );

			$gp_templates = array(
				'translation-row-preview',
			);

			if ( in_array( $template, $gp_templates, true ) ) {
				$locations = array(
					GP_CONVERT_PT_AO90_DIR_PATH . 'gp-templates/',
				);
			}

			return $locations;
		}


		/**
		 * Converts Portuguese (pt/default) translation to PT AO90 into the pt_PT_ao90 (pt-ao90/default) translation set.
		 *
		 * @since 1.0.0
		 *
		 * @param \GP_Translation $translation   Created/updated translation.
		 *
		 * @return void
		 */
		public static function queue_translation_for_conversion( $translation ) {

			/**
			 * Set root Locale.
			 */
			$root_locale = array(
				'locale' => 'pt',
				'slug'   => 'default',
			);

			/**
			 * Set variant Locale.
			 */
			$variant_locale = array(
				'locale' => 'pt-ao90',
				'slug'   => 'default',
			);

			/**
			 * Only process for Portuguese (pt_PT pt/default) root translation set.
			 * Locale: 'pt'
			 * Slug: 'default'
			 */
			$root_set = GP::$translation_set->get( $translation->translation_set_id );
			if ( ! $root_set || $root_locale['locale'] !== $root_set->locale || $root_locale['slug'] !== $root_set->slug ) { // @phpstan-ignore-line
				return;
			}

			// Get translation original.
			$original = GP::$original->get( $translation->original_id );
			if ( ! $original ) {
				return;
			}

			/**
			 * Only process if Portuguese (pt_PT_AO90 pt-ao90/default) translation set exist.
			 * Locale: 'pt-ao90'
			 * Slug: 'default'
			 */
			$variant_set = GP::$translation_set->by_project_id_slug_and_locale( $original->project_id, $variant_locale['slug'], $variant_locale['locale'] ); // @phpstan-ignore-line
			if ( ! $variant_set ) {
				return;
			}

			$project = GP::$project->get( $variant_set->project_id );
			if ( ! $project ) {
				return;
			}

			// Process if root translation is set to current without warnings.
			if ( $translation->status === 'current' && empty( $translation->warnings ) ) {
				// Create translation on the variant set.
				self::create( $translation, $project, $variant_set );
			} else {
				// Delete translation on the variant set.
				self::delete( $translation, $project, $variant_set, true );
			}
		}


		/**
		 * Create translation on the variant set, if the conversion changes the root translation.
		 * Also deletes any previous variant set translation if the new translation remains unchanged with the conversion.
		 *
		 * @since 1.0.0
		 *
		 * @param \GP_Translation     $translation   Created/updated translation.
		 * @param \GP_Project         $project       GlotPress project.
		 * @param \GP_Translation_Set $variant_set   GlotPress translation set of the variant.
		 *
		 * @return void
		 */
		public static function create( $translation, $project, $variant_set ) {

			$translation_changed = self::convert_translation( $translation, $variant_set );

			// Check if the conversion produces changes.
			if ( ! $translation_changed ) {

				// Deletes any existent mathing conversions.
				self::delete( $translation, $project, $variant_set, false );

				return;

			}

			// Add converted translation to the variant translation set and set as current.
			$variant_translation = GP::$translation->create( $translation_changed );
			if ( ! $variant_translation ) {
				return;
			}

			gp_clean_translation_set_cache( $variant_set->id );
		}


		/**
		 * Delete the pt_PT_ao90 (pt-ao90/default) variant translation if the matching Portuguese (pt/default) root translation has no conversion.
		 * Keeping no history for a read-only variant makes it lighter.
		 *
		 * @since 1.0.0
		 *
		 * @param \GP_Translation     $translation   Created/updated translation.
		 * @param \GP_Project         $project       GlotPress project.
		 * @param \GP_Translation_Set $variant_set   GlotPress variant translation set.
		 * @param bool                $all           Delete all translations or just the last for performance. Defaults to false.
		 *
		 * @return void
		 */
		public static function delete( $translation, $project, $variant_set, $all = false ) {

			// Get existing translations on the variant translation set for the original_id.
			$variant_translations = GP::$translation->for_translation(
				$project,
				$variant_set,
				'no-limit',
				array(
					'original_id' => $translation->original_id,
					'status'      => $all ? 'either' : 'current',
				)
			);

			// Set the status of the variant translation set as the root translation set for the same original_id.
			foreach ( $variant_translations as $variant_translation ) {
				$variant_translation = GP::$translation->get( $variant_translation );
				if ( ! $variant_translation ) {
					continue;
				}
				$variant_translation->delete();
			}

			gp_clean_translation_set_cache( $variant_set->id );
		}


		/**
		 * Convert the translation for the variant, including all plurals.
		 *
		 * @since 1.0.0
		 *
		 * @param \GP_Translation     $translation   GlotPress translation.
		 * @param \GP_Translation_Set $variant_set   GlotPress variant set.
		 *
		 * @return object|false   Returns a converted translation, or false if the result remains unchanged.
		 */
		public static function convert_translation( $translation, $variant_set ) {

			$locale = GP_Locales::by_slug( $variant_set->locale );

			$translation_ao90                     = new GP_Translation( $translation->fields() );
			$translation_ao90->translation_set_id = $variant_set->id;
			$translation_ao90->status             = 'current';

			$translation_changed = false;

			for ( $i = 0; $i < $locale->nplurals; $i++ ) {

				// Skip if plural don't exist.
				if ( is_null( $translation->{"translation_{$i}"} ) ) {
					continue;
				}

				// Actually try to convert the string.
				$converted = Convert_PT_AO90\convert_pt_ao90( $translation->{"translation_{$i}"} );

				// Check if the conversion process changes the translation.
				if ( $converted !== $translation->{"translation_{$i}"} ) {

					// Set converted string as PT AO90 translation.
					$translation_ao90->{"translation_{$i}"} = $converted;

					// The translation plural have changed.
					$translation_changed = true;

				}
			}

			// Check if any of the translation plurals have changed.
			if ( ! $translation_changed ) {
				return false;
			}

			return $translation_ao90;
		}


		/**
		 * Highlight the differences between the root and converted variant translations.
		 *
		 * Create the text diff inspired on wp_text_diff() but removing the unecessary table HTML.
		 * Ref: https://developer.wordpress.org/reference/functions/wp_text_diff/
		 *
		 * @since 1.2.0
		 *
		 * @param string $root_translation      Root translation string to compare.
		 * @param string $variant_translation   Variant translation string to compare.
		 *
		 * @return string   Root translation if translations are equivalent, or HTML with conversion differences highlighted.
		 */
		public static function highlight_diff( $root_translation, $variant_translation ) {

			/**
			 * Undocumented argument 'diff_threshold', passed to WP_Text_Diff_Renderer_Table to be able to diff changes like 'Update' -> 'Updated'.
			 * https://github.com/WordPress/wordpress-develop/blob/e5a0d1364d31d82d7746b06f28a1df28accac85b/src/wp-includes/class-wp-text-diff-renderer-table.php#L39
			 */
			$args = array(
				'diff_threshold'  => 1,
				'show_split_view' => false,
			);

			if ( ! class_exists( 'WP_Text_Diff_Renderer_Table', false ) ) {
				require ABSPATH . WPINC . '/wp-diff.php';
			}

			$root_lines    = explode( "\n", $root_translation );
			$variant_lines = explode( "\n", $variant_translation );
			$text_diff     = new \Text_Diff( 'auto', array( $root_lines, $variant_lines ) );
			$renderer      = new \WP_Text_Diff_Renderer_Table( $args );
			$diff          = $renderer->render( $text_diff );

			if ( ! $diff ) {
				// Return root translation.
				return $root_translation;
			}

			if ( count( $variant_lines ) > 1 ) {
				$diff = preg_replace(
					array(
						// Remove HTML row opening tags of lines with no conversion changes.
						'/<tr><td class=\'diff-context\'><span class=\'screen-reader-text\'>.*<\/span>/',
						// Remove entire HTML rows of root translations lines before conversion.
						'/<tr><td class=\'diff-deletedline\'>.*\n*<\/td><\/tr>/',
						// Remove HTML row opening tags of variant translations after conversion.
						'/<tr><td class=\'diff-addedline\'><span aria-hidden=\'true\' class=\'dashicons dashicons-plus\'><\/span><span class=\'screen-reader-text\'>.*<\/span>/',
						// Remove table rows closing tags.
						'/\n*<\/td><\/tr>\n*/',
						// Remove the last new line.
						'/\n$/',
					),
					array(
						'',
						'',
						'',
						"\n",
						'',
					),
					$diff
				);
			} else {
				$diff = preg_replace(
					array(
						// Remove entire HTML rows of root translations lines before conversion.
						'/<tr><td class=\'diff-deletedline\'>.*\n*<\/td><\/tr>/',
						// Remove HTML row opening tags of variant translations after conversion.
						'/<tr><td class=\'diff-addedline\'><span aria-hidden=\'true\' class=\'dashicons dashicons-plus\'><\/span><span class=\'screen-reader-text\'>.*<\/span>/',
						// Remove table rows closing tags.
						'/\n<\/td><\/tr>\n/',
					),
					array( '', '' ),
					$diff
				);
			}

			if ( is_null( $diff ) ) {
				// If an error ocurr, return root translation.
				return $root_translation;
			}

			return htmlspecialchars_decode( $diff );
		}


		/**
		 * Register and enqueue style sheet.
		 *
		 * @since 1.2.0
		 *
		 * @return void
		 */
		public static function register_plugin_styles() {

			// Check if SCRIPT_DEBUG is true.
			$suffix = SCRIPT_DEBUG ? '' : '.min';

			wp_register_style(
				'gp-convert-pt-ao90',
				GP_CONVERT_PT_AO90_DIR_URL . 'assets/css/admin' . $suffix . '.css',
				array(),
				GP_CONVERT_PT_AO90_VERSION
			);

			gp_enqueue_styles( 'gp-convert-pt-ao90' );
		}
	}

}
