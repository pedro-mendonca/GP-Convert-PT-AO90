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
use GP_Locale;
use GP_Locales;
use GP_Project;
use GP_Translation;
use GP_Translation_Set;
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

			// Register and enqueue plugin scripts.
			add_action( 'wp_enqueue_scripts', array( self::class, 'register_plugin_scripts' ) );

			/**
			 * Customize permissions on specific templates to make the Variant read-only.
			 */
			add_action( 'gp_pre_tmpl_load', array( self::class, 'pre_template_load' ), 10, 2 );

			/**
			 * Add inline CSS to show read-only mode in the Project view.
			 */
			add_action( 'gp_post_tmpl_load', array( self::class, 'post_template_load' ), 10, 2 );

			/**
			 * Get GP-Convert-PT-AO90 templates.
			 */
			add_filter( 'gp_tmpl_load_locations', array( self::class, 'template_load_locations' ), 10, 4 );

			/**
			 * Converts a Portuguese (pt/default) translation to PT AO90 into the pt_PT_ao90 (pt-ao90/default) translation set.
			 * The conversion is queued after saving the root translation.
			 */
			add_action( 'gp_translation_saved', array( self::class, 'queue_translation_for_conversion' ) );

			/**
			 * Move the Variant set below the Root translation set.
			 */
			add_filter( 'gp_translation_sets_sort', array( self::class, 'sort_translation_sets' ) );

			/**
			 * Force convert the whole project again.
			 */
			add_action( 'wp_ajax_convert_project', array( self::class, 'convert_project' ) );
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
		 * @param string               $template   The template name.
		 * @param array<string,string> $args       Arguments passed to the template.
		 *
		 * @return void
		 */
		public static function pre_template_load( $template, &$args ) {

			$is_ptao90 = false;

			if ( isset( $args['locale_slug'] ) && $args['locale_slug'] === 'pt-ao90' ) {

				$is_ptao90 = true;

				// Check if the the Variant is read-only.
				if ( GP_CONVERT_PT_AO90_EDIT === false ) {

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

				// Customize $args on 'translations' template.
				if ( $template === 'translations' ) {

					$project = self::gp_project( $args['project'] );

					if ( is_null( $project ) ) {
						return;
					}

					// Check if Variants are supported.
					$supports_variants = self::supports_variants();

					$root_translation_set = GP::$translation_set->by_project_id_slug_and_locale( $project->id, 'default', 'pt' );

					$has_root = false;
					// Only set the root translation flag if we have a valid root translation set, otherwise there's no point in querying it later.
					if ( ! is_null( $root_translation_set ) && $root_translation_set !== false ) {
						$has_root = true;
					}

					$root_translations = null;
					if ( ! $supports_variants && GP_CONVERT_PT_AO90_SHOWDIFF === true && $has_root === true ) {
						$root_translations = GP::$translation->for_translation( $project, $root_translation_set, 'no-limit', gp_get( 'filters', array( 'status' => 'current' ) ) );
					}

					$args['supports_variants']    = $supports_variants;
					$args['has_root']             = $has_root;
					$args['root_translation_set'] = $root_translation_set;
					$args['root_translations']    = $root_translations;
				}
			}

			$args['is_ptao90'] = $is_ptao90;
		}


		/**
		 * Add inline CSS in 'translations' to show read-only mode icon on the translation set title.
		 *
		 * @since 1.3.2
		 *
		 * @param string               $template   The template name.
		 * @param array<string,string> $args       Arguments passed to the template.
		 *
		 * @return void
		 */
		public static function post_template_load( $template, &$args ) {

			// Add inline CSS to show read-only mode in the translations view.
			if ( $template === 'translations' ) {

				if ( isset( $args['locale_slug'] ) && $args['locale_slug'] === 'pt-ao90' ) {

					// Check if the the Variant is read-only.
					if ( GP_CONVERT_PT_AO90_EDIT === false ) {
						// CSS for variant PT AO90.
						?>
						<style media="screen">

							.gp-content .gp-heading h2::after {
								font-family: dashicons;
								font-weight: normal;
								font-size: 0.75em;
								content: "\f160";
							}

						</style>
						<?php
					}
				}
			}
		}


		/**
		 * Get GP-Convert-PT-AO90 templates.
		 *
		 * @since 1.2.0
		 *
		 * @param array<int,string>    $locations       File paths of template locations.
		 * @param string               $template        The template name.
		 * @param array<string,string> $args            Arguments passed to the template.
		 * @param string|null          $template_path   Priority template location, if any.
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
		 * Converts the Portuguese (pt/default) translation to PT AO90 into the pt_PT_ao90 (pt-ao90/default) translation set.
		 *
		 * @since 1.0.0
		 *
		 * @param \GP_Translation $translation   Created/updated translation.
		 *
		 * @return void
		 */
		public static function queue_translation_for_conversion( $translation ) {

			// Get root and variant pair of Locales for conversion.
			$locales = self::locale_root_variant();

			/**
			 * Set root Locale.
			 */
			$root_locale = $locales['root'];

			/**
			 * Set variant Locale.
			 */
			$variant_locale = $locales['variant'];

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
		 * Create translation on the variant set, if the conversion changes the root translation, or, optionally, always create.
		 * Also deletes any previous variant set translation if the new translation remains unchanged with the conversion.
		 *
		 * @since 1.0.0
		 * @since 1.3.3   The new filter 'gp_convert_pt_ao90_always_create_variant_translation' allows to force create translations on the variant, even if the conversion doesn't produce any changes. This makes the variant entirely translated, without fallback to the root locale.
		 *
		 * @param \GP_Translation     $translation   Created/updated translation.
		 * @param \GP_Project         $project       GlotPress project.
		 * @param \GP_Translation_Set $variant_set   GlotPress translation set of the variant.
		 *
		 * @return void
		 */
		public static function create( $translation, $project, $variant_set ) {

			// Deletes any existent matching conversions. Only the current.
			self::delete( $translation, $project, $variant_set, false );

			// Check if GlotPress version supports the Variants functionality.
			$supports_variants = self::supports_variants();

			// If there is no support for real Variants, always create translation in the variant.
			$always_create_variant_translation = true;

			// If there is support for real Variants, don't always create translation in the variant, only if is different from root.
			if ( $supports_variants ) {
				$always_create_variant_translation = false;
			}

			/**
			 * For Real Variants:
			 *   - GlotPress 3.0.0-alpha.4:               https://github.com/GlotPress/GlotPress/releases/tag/3.0.0-alpha.4
			 *   - GlotPress 4.0.0-alpha.11 + Variants:   https://github.com/pedro-mendonca/GlotPress/tree/develop-with-variants
			 * To not populate the Variant Locale with unnecessary translations, exact copies of root Locale, set to False to allow fallback translation to the Root Locale.
			 * If you still need to have the Variant fully translated, because reasons, set to True, and all the translations will be added, equal or converted from Root Locale.
			 *
			 * For Pseudo Variants:
			 *   - GlotPress current development:   https://github.com/GlotPress/GlotPress/releases/tag/4.0.0-alpha.11
			 * In this case, it should always create the translation in the variant.
			 *
			 * @since 1.3.3
			 *
			 * @param bool $always_create_variant_translation   True to force create translations in the variant. False to only create if the conversion produces changes, falling back to real Variant.
			 */
			$always_create_variant_translation = apply_filters( 'gp_convert_pt_ao90_always_create_variant_translation', $always_create_variant_translation );

			// Get the converted translation, or false if nothing changes.
			$translation_changed = self::convert_translation( $translation, $variant_set );

			// Check if the conversion produces changes.
			if ( ! $translation_changed ) {

				// Check wether to always create variant translations or only if differ from root.
				if ( ! $always_create_variant_translation ) {
					return;
				}

				// Use the unconverted translation for the variant set.
				$translation_changed = $translation;

				// Set the ID of the variant set.
				$translation->translation_set_id = $variant_set->id;

			}

			// Add converted translation to the variant translation set and set as current.
			$variant_translation = GP::$translation->create( $translation_changed );
			if ( ! $variant_translation ) {
				return;
			}

			do_action( 'gp_translation_saved', $variant_translation );

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
		 * Force convert the whole project.
		 *
		 * @since 1.4.2
		 *
		 * @return void
		 */
		public static function convert_project() {

			check_ajax_referer( 'gp-convert-pt-ao90-nonce', 'nonce' );

			// Initialize variables.
			$project_path = '';
			$locale       = '';

			if ( isset( $_POST['projectPath'] ) ) {
				$project_path = sanitize_key( $_POST['projectPath'] );
			}

			if ( isset( $_POST['locale'] ) ) {
				$locale = sanitize_key( $_POST['locale'] );
			}

			if ( isset( $_POST['slug'] ) ) {
				$slug = sanitize_key( $_POST['slug'] );
			}

			// Get root and variant pair of Locales for conversion.
			$locales = self::locale_root_variant();

			/**
			 * Set root Locale.
			 */
			$root_locale = $locales['root'];

			/**
			 * Set variant Locale.
			 */
			$variant_locale = $locales['variant'];

			$locale_root_variant = self::locale_root_variant();

			if ( $locale === $variant_locale['locale'] && $slug === $variant_locale['slug'] ) {

				// Get the GP_Project.
				$project = GP::$project->by_path( $project_path );

				// Get the Root Translation_Set.
				$root_translation_set = GP::$translation_set->by_project_id_slug_and_locale( $project->id, $root_locale['slug'], $root_locale['locale'] );

				// Get the Variant Translation_Set.
				$variant_translation_set = GP::$translation_set->by_project_id_slug_and_locale( $project->id, $variant_locale['slug'], $variant_locale['locale'] );

			}

			if ( $variant_translation_set !== false ) {
				$variant_translations = GP::$translation->for_translation( $project, $variant_translation_set, 'no-limit', gp_get( 'filters', array( 'status' => 'current' ) ) );
			}

			if ( $root_translation_set !== false ) {
				$root_translations = GP::$translation->for_translation( $project, $root_translation_set, 'no-limit', gp_get( 'filters', array( 'status' => 'current' ) ) );
			}

			// Bulk convert all translations from root to variant.
			/*
			foreach ( $root_translations as $root_translation ) {
				// Create translation on the variant set.
				self::create( $root_translation, $project, $variant_translation_set );
			}
			*/

			// Bulk convert all translations from root to variant.
			foreach ( $variant_translations as $variant_translation ) {

				// var_dump( $variant_translation );

				// $variant_translation->reject();
				//self::delete( $variant_translation, $project, $variant_translation_set, true );

				// Create translation on the variant set.
				self::create( $root_translation, $project, $variant_translation_set );
			}






			wp_die();
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
				GP_CONVERT_PT_AO90_DIR_URL . 'assets/css/style' . $suffix . '.css',
				array(
					'buttons',
				),
				GP_CONVERT_PT_AO90_VERSION
			);

			gp_enqueue_styles( array( 'gp-convert-pt-ao90', 'dashicons' ) );
		}


		/**
		 * Register and enqueue scripts.
		 *
		 * @since 1.4.0
		 *
		 * @return void
		 */
		public static function register_plugin_scripts() {

			// Check if SCRIPT_DEBUG is true.
			$suffix = SCRIPT_DEBUG ? '' : '.min';

			wp_register_script(
				'gp-convert-pt-ao90',
				GP_CONVERT_PT_AO90_DIR_URL . 'assets/js/scripts' . $suffix . '.js',
				array(),
				GP_CONVERT_PT_AO90_VERSION,
				false
			);

			gp_enqueue_scripts( 'gp-convert-pt-ao90' );

			$edit = 'true';
			if ( GP_CONVERT_PT_AO90_EDIT === false ) {
				$edit = 'false';
			}

			wp_localize_script(
				'gp-convert-pt-ao90',
				'gpConvertPTAO90',
				array(
					'edit'           => $edit,
					'admin'          => GP::$permission->current_user_can( 'admin' ),
					'gp_url'         => gp_url(), // /glotpress/.
					'gp_url_project' => gp_url_project(), // /glotpress/projects/.
					'ajaxurl'        => admin_url( 'admin-ajax.php' ),
					'nonce'          => wp_create_nonce( 'gp-convert-pt-ao90-nonce' ),
				)
			);
		}


		/**
		 * Check if GlotPress supports real variants.
		 *
		 * @since 1.3.3
		 *
		 * @return bool
		 */
		public static function supports_variants() {

			$locale = new GP_Locale();

			if ( ! property_exists( $locale, 'variant_root' ) ) {
				return false;
			}

			if ( ! property_exists( $locale, 'variants' ) ) {
				return false;
			}

			return true;
		}


		/**
		 * Pair of Locale Root and Variant for conversion.
		 *
		 * @since 1.3.4
		 *
		 * @return array<string,array<string,string>>
		 */
		public static function locale_root_variant() {

			return array(
				/**
				 * Set root Locale.
				 */
				'root'    => array(
					'locale' => 'pt',
					'slug'   => 'default',
				),
				/**
				 * Set variant Locale.
				 */
				'variant' => array(
					'locale' => 'pt-ao90',
					'slug'   => 'default',
				),
			);
		}


		/**
		 * Check if project is GP_Project.
		 *
		 * @since 1.3.4
		 *
		 * @param mixed $project   Project obtained from GP::$project->by_path().
		 *
		 * @return GP_Project|null   GP_Project instance, or null if not a GP_Project.
		 */
		public static function gp_project( $project ) {

			if ( is_object( $project ) && is_a( $project, 'GP_Project' ) ) {
				return $project;
			}

			return null;
		}


		/**
		 * Move the Variant set below the Root translation set.
		 *
		 * @since 1.4.0
		 *
		 * @param array<int,GP_Translation_Set> $translation_sets   An array of translation sets.
		 *
		 * @return array<int,GP_Translation_Set>   The sorted array of translation sets with Variant below Root.
		 */
		public static function sort_translation_sets( $translation_sets ) {

			$variant_translation_sets = array();

			$root_locale    = 'pt';
			$variant_locale = 'pt-ao90';

			// Move variants sets below its roots.
			foreach ( $translation_sets as $key => $translation_set ) {

				$root_translation_set = null;

				if ( $translation_set->locale === $variant_locale ) {

					$root_translation_set = GP::$translation_set->by_project_id_slug_and_locale( $translation_set->project_id, $translation_set->slug, $root_locale );

					// Only set the root translation flag if we have a valid root translation set, otherwise there's no point in querying it later.
					if ( $root_translation_set ) {
						$variant_translation_sets[] = $translation_set;
						unset( $translation_sets[ $key ] );
					}
				}
			}

			// Check if exist any variant.
			if ( empty( $variant_translation_sets ) ) {
				return $translation_sets;
			}

			$translation_sets = array_values( $translation_sets );

			// Sort variant translation sets by slug, descending. Useful for when there will be more than one.
			usort(
				$variant_translation_sets,
				/**
				 * Sort Translation Sets by Locale.
				 *
				 * @param GP_Translation_Set $a   Translation Set.
				 * @param GP_Translation_Set $b   Translation Set.
				 *
				 * @return int
				 */
				function ( GP_Translation_Set $a, GP_Translation_Set $b ): int {
					return intval( $a->locale < $b->locale );
				}
			);

			// Move variants sets below its roots.
			foreach ( $variant_translation_sets as $variant_translation_set ) {

				foreach ( $translation_sets as $root_key => $translation_set ) {

					$insert = null;
					if ( $translation_set->locale === $root_locale ) {
						$insert[0] = $variant_translation_set;
						array_splice(
							$translation_sets, // Array of Translation Sets.
							$root_key + 1,     // After the Root set key.
							0,                 // Lenght to override, 0 to insert without deleting any.
							$insert            // The actual Variants array.
						);
					}
				}
			}

			return $translation_sets;
		}
	}
}
