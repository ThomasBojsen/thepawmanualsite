<?php
/**
 * Plugin Name:       PawManual Calculators
 * Plugin URI:        https://thepawmanual.com/
 * Description:       Interactive pet calculators for ThePawManual, delivered through the [pawmanual_calculator] shortcode. Self-contained and theme-independent.
 * Version:           0.1.0
 * Requires at least: 6.3
 * Requires PHP:      7.4
 * Author:            ThePawManual
 * Author URI:        https://thepawmanual.com/
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       pawmanual-calculators
 *
 * @package pawmanual-calculators
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Prevent direct access.
}

/** Plugin version — used to cache-bust enqueued assets. */
define( 'PMC_VERSION', '0.1.0' );

/** Absolute filesystem path to the plugin directory, with a trailing slash. */
define( 'PMC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

/** Public URL to the plugin directory, with a trailing slash. */
define( 'PMC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once PMC_PLUGIN_DIR . 'includes/class-registry.php';
require_once PMC_PLUGIN_DIR . 'includes/enqueue.php';
require_once PMC_PLUGIN_DIR . 'includes/shortcode.php';
