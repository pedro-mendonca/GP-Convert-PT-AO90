<?php
/**
 * Convert PT AO90 for GlotPress
 *
 * Heavily inspired by the Serbian Latin solution for transliteration of Serbian Cyrillic locale from translate.wordpress.org.
 * https://meta.trac.wordpress.org/ticket/5471
 * https://meta.trac.wordpress.org/browser/sites/trunk/wordpress.org/public_html/wp-content/plugins/wporg-gp-customizations/inc/locales/class-serbian-latin.php?rev=10360
 * https://wordpress.slack.com/archives/C02RP4R9F/p1637139808076000
 *
 * @package           GP_Convert_PT_AO90
 * @link              https://github.com/pedro-mendonca/GP_Convert_PT_AO90
 * @author            Pedro Mendonça
 * @copyright         2021 Pedro Mendonça
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       Convert PT AO90 for GlotPress
 * Plugin URI:        https://wordpress.org/plugins/gp-convert-pt-ao90/
 * GitHub Plugin URI: https://github.com/pedro-mendonca/GP-Convert-PT-AO90
 * Primary Branch:    main
 * Description:       Language tool for GlotPress to convert text according to the Portuguese Language Orthographic Agreement of 1990 (PT AO90).
 * Version:           1.4.2
 * Requires at least: 5.3
 * Tested up to:      6.4
 * Requires PHP:      7.4
 * Author:            Pedro Mendonça
 * Author URI:        https://profiles.wordpress.org/pedromendonca/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       gp-convert-pt-ao90
 * Domain Path:       /languages
 */

namespace GP_Convert_PT_AO90;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


// Check if get_plugin_data() function exists.
if ( ! function_exists( 'get_plugin_data' ) ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

// Get plugin headers data.
$gp_convert_pt_ao90_data = get_plugin_data( __FILE__, false, false );


// Set Convert PT AO90 for GlotPress plugin version.
if ( ! defined( 'GP_CONVERT_PT_AO90_VERSION' ) ) {
	define( 'GP_CONVERT_PT_AO90_VERSION', $gp_convert_pt_ao90_data['Version'] );
}

// Set Convert PT AO90 for GlotPress required PHP version. Needed for PHP compatibility check for WordPress < 5.1.
if ( ! defined( 'GP_CONVERT_PT_AO90_REQUIRED_PHP' ) ) {
	define( 'GP_CONVERT_PT_AO90_REQUIRED_PHP', $gp_convert_pt_ao90_data['RequiresPHP'] );
}

// Set Convert PT AO90 for GlotPress plugin URL.
define( 'GP_CONVERT_PT_AO90_DIR_URL', plugin_dir_url( __FILE__ ) );

// Set Convert PT AO90 for GlotPress plugin filesystem path.
define( 'GP_CONVERT_PT_AO90_DIR_PATH', plugin_dir_path( __FILE__ ) );

// Set Convert PT AO90 for GlotPress file path.
define( 'GP_CONVERT_PT_AO90_FILE', plugin_basename( __FILE__ ) );

/**
 * Disable editing translations for PT AO90.
 * add_filter( 'gp_convert_pt_ao90_edit', '__return_false' );
 */

/**
 * Always create the Variant translation.
 * add_filter( 'gp_convert_pt_ao90_always_create_variant_translation', '__return_true' );
 */

// Set hooks.
add_action(
	'wp_loaded',
	function () {
		// Define the PT AO90 Locale Variant as writable. Set to false to make it read-only.
		if ( ! defined( 'GP_CONVERT_PT_AO90_EDIT' ) ) {
			define( 'GP_CONVERT_PT_AO90_EDIT', apply_filters( 'gp_convert_pt_ao90_edit', true ) );
		}
		// Define wether to highlight the conversion diffs.
		if ( ! defined( 'GP_CONVERT_PT_AO90_SHOWDIFF' ) ) {
			define( 'GP_CONVERT_PT_AO90_SHOWDIFF', apply_filters( 'gp_convert_pt_ao90_showdiff', true ) );
		}
	}
);

// Include Composer autoload.
require_once GP_CONVERT_PT_AO90_DIR_PATH . 'vendor/autoload.php';

/**
 * Initialize the plugin.
 *
 * @return void
 */
function gp_convert_pt_ao90_init() {
	Portuguese_AO90::init();
}
add_action( 'plugins_loaded', __NAMESPACE__ . '\gp_convert_pt_ao90_init' );
