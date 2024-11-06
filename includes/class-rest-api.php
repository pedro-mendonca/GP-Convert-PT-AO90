<?php
/**
 * Class file for registering Rest API encpoints.
 * https://developer.wordpress.org/rest-api/extending-the-rest-api/adding-custom-endpoints/
 *
 * @package GP_Convert_PT_AO90
 *
 * @since 1.5.0
 */

namespace GP_Convert_PT_AO90;

use GP;
use Translations;
use WP_REST_Request;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( __NAMESPACE__ . '\Rest_API' ) ) {

	/**
	 * Class Rest_API.
	 */
	class Rest_API {


		/**
		 * Constructor.
		 */
		public function __construct() {

			add_action( 'rest_api_init', array( $this, 'register_routes' ) );
		}


		/**
		 * Register routes.
		 *
		 * @since 1.5.0
		 *
		 * @return void
		 */
		public function register_routes() {

			$base         = 'translation-set';       // Base for translation set routes.
			$project_path = '(?P<project_path>.+)';  // Project path.
			$locale       = '(?P<locale>.+)';        // Locale.
			$slug         = '(?P<slug>.+)';          // Locale slug.

			// Set the main route for the Translation Set.
			$translation_set = $base . '/' . $project_path . '/' . $locale . '/' . $slug;

			// Route to bulk delete translations from a translation set, with a specific status.
			register_rest_route(
				GP_CONVERT_PT_AO90_REST_NAMESPACE,
				"/$translation_set/-convert",
				array(
					'methods'             => 'POST',
					'callback'            => array( $this, 'translation_set_convert' ),
					'permission_callback' => function () {
						return Portuguese_AO90::current_user_is_glotpress_admin();
					},
				)
			);
		}


		/**
		 * Bulk delete translations from a Translation Set, with a specified status.
		 *
		 * @since 1.5.0
		 *
		 * @param WP_REST_Request $request   Request data.
		 *
		 * @return mixed   Array of stats and number of deleted translations. Can also be a string with error message.
		 */
		public function translation_set_convert( WP_REST_Request $request ) {

			$project_path = $request->get_param( 'project_path' );
			$locale       = $request->get_param( 'locale' );
			$slug         = $request->get_param( 'slug' );

			// Get root and variant pair of Locales for conversion.
			$locales = Portuguese_AO90::locale_root_variant();

			/**
			 * Set root Locale.
			 */
			$root_locale = $locales['root'];

			/**
			 * Set variant Locale.
			 */
			$variant_locale = $locales['variant'];

			if ( $locale !== $variant_locale['locale'] || $slug !== $variant_locale['slug'] ) {
				// Return error.
				return rest_ensure_response( 'Locales not supported.' );
			}

			// Get the GP_Project.
			$project = GP::$project->by_path( $project_path );

			if ( $project === false ) {
				// Return error.
				return rest_ensure_response( 'Project not found.' );
			}

			// Get the Variant Translation_Set.
			$variant_translation_set = GP::$translation_set->by_project_id_slug_and_locale( $project->id, $variant_locale['slug'], $variant_locale['locale'] );

			if ( $variant_translation_set === false ) {
				// Return error.
				return rest_ensure_response( 'Variant Translation Set not found.' );
			}

			// Get the Root Translation_Set.
			$root_translation_set = GP::$translation_set->by_project_id_slug_and_locale( $project->id, $root_locale['slug'], $root_locale['locale'] );

			if ( $root_translation_set === false ) {
				// Return error.
				return rest_ensure_response( 'Root Translation Set not found.' );
			}

			// Bulk delete all translations existing in the variant set.
			GP::$translation->delete_many(
				array(
					'translation_set_id' => $variant_translation_set->id,
				)
			);

			// Get root set translations for further conversion.
			$root_translations = GP::$translation->for_translation(
				$project,
				$root_translation_set,
				'no-limit',
				array(
					'status' => 'current', // Only current translations.
				)
			);

			$converted_translations = Portuguese_AO90::convert_translations( $root_translations );

			$translations_for_import = new Translations();

			foreach ( $converted_translations as $converted_translation ) {
				$translations_for_import->add_entry( $converted_translation );
			}

			// Import translations to the variant.
			$variant_translation_set->import( $translations_for_import );

			// Check for any imported translations with warnings.
			$translations_with_warnings = GP::$translation->for_translation(
				$project,
				$variant_translation_set,
				'no-limit',
				array(
					'warnings' => 'yes',
				)
			);

			// Remove warnings from converted translations with import().
			if ( $translations_with_warnings !== array() ) {

				foreach ( $translations_with_warnings as $entry ) {

					$translation_with_warnings = GP::$translation->get( $entry );

					if ( $translation_with_warnings === false ) {
						continue;
					}

					if ( is_a( $translation_with_warnings, 'GP_Translation' ) ) {
						// Remove existing warnings.
						$translation_with_warnings->warnings = array();

						// Set as current.
						$translation_with_warnings->set_as_current();

						// Save translation.
						$translation_with_warnings->save();
					}
				}
			}

			gp_clean_translation_set_cache( $variant_translation_set->id );

			return rest_ensure_response(
				array(
					'percent'      => $variant_translation_set->percent_translated(),
					'current'      => $variant_translation_set->current_count(),
					'fuzzy'        => $variant_translation_set->fuzzy_count(),
					'untranslated' => $variant_translation_set->untranslated_count(),
					'waiting'      => $variant_translation_set->waiting_count(),
					'old'          => $variant_translation_set->old_count,
					'rejected'     => $variant_translation_set->rejected_count,
					'warnings'     => $variant_translation_set->warnings_count(),
				)
			);
		}
	}
}
