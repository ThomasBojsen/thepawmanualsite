<?php
/**
 * Front-end asset registration and enqueuing.
 *
 * @package thepawmanual
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue the theme's fonts, compiled stylesheet and script on the front end.
 */
function tpm_enqueue_assets() {
	$theme_uri = get_template_directory_uri();

	// Google Fonts: Inter (base), Playfair Display (display headings) and Geist
	// (UI text). Self-hosting these fonts is a post-POC task — see CLAUDE.md §10.
	wp_enqueue_style(
		'tpm-fonts',
		'https://fonts.googleapis.com/css2?family=Geist:wght@300..700&family=Inter:wght@300..800&family=Playfair+Display:wght@400..900&display=swap',
		array(),
		null
	);

	// Compiled Tailwind stylesheet. It is versioned by file-modification time
	// so that every rebuild cache-busts the browser during development.
	$css_rel  = 'assets/css/theme.css';
	$css_path = TPM_THEME_DIR . $css_rel;
	wp_enqueue_style(
		'tpm-theme',
		$theme_uri . '/' . $css_rel,
		array( 'tpm-fonts' ),
		file_exists( $css_path ) ? (string) filemtime( $css_path ) : TPM_THEME_VERSION
	);

	// Theme script: the mobile-nav toggle and the scroll-reveal effect. Loaded
	// in the footer and deferred — it only enhances the page, so it is never
	// needed for the first paint.
	$js_rel  = 'assets/js/theme.js';
	$js_path = TPM_THEME_DIR . $js_rel;
	wp_enqueue_script(
		'tpm-theme',
		$theme_uri . '/' . $js_rel,
		array(),
		file_exists( $js_path ) ? (string) filemtime( $js_path ) : TPM_THEME_VERSION,
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);
}
add_action( 'wp_enqueue_scripts', 'tpm_enqueue_assets' );
