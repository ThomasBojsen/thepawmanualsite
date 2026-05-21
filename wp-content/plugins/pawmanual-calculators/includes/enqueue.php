<?php
/**
 * Conditional asset loading.
 *
 * The engine, the styles, and the per-calculator config scripts load only on
 * pages that actually contain the [pawmanual_calculator] shortcode — never on
 * the rest of the site (CLAUDE.md §9).
 *
 * @package pawmanual-calculators
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Extract the calculator ids used by [pawmanual_calculator] shortcodes in content.
 *
 * @param string $content Post content.
 * @return string[] Unique, sanitised calculator ids.
 */
function pmc_find_calculator_ids( $content ) {
	$ids = array();

	if ( ! is_string( $content ) || false === strpos( $content, 'pawmanual_calculator' ) ) {
		return $ids;
	}

	$pattern = get_shortcode_regex( array( 'pawmanual_calculator' ) );
	if ( preg_match_all( '/' . $pattern . '/', $content, $matches ) && ! empty( $matches[3] ) ) {
		foreach ( $matches[3] as $atts_string ) {
			$atts = shortcode_parse_atts( $atts_string );
			if ( ! empty( $atts['id'] ) ) {
				$ids[] = sanitize_key( $atts['id'] );
			}
		}
	}

	return array_values( array_unique( $ids ) );
}

/**
 * Enqueue calculator assets when the current page uses the shortcode.
 */
function pmc_enqueue_assets() {
	if ( ! is_singular() ) {
		return;
	}

	$post = get_post();
	if ( ! $post || ! has_shortcode( $post->post_content, 'pawmanual_calculator' ) ) {
		return;
	}

	// Shared styles + the engine.
	wp_enqueue_style(
		'pmc-calculators',
		PMC_PLUGIN_URL . 'assets/css/calculators.css',
		array(),
		PMC_VERSION
	);
	wp_enqueue_script(
		'pmc-core',
		PMC_PLUGIN_URL . 'assets/js/calculator-core.js',
		array(),
		PMC_VERSION,
		array( 'in_footer' => true )
	);

	// One config script per calculator actually present on this page.
	foreach ( pmc_find_calculator_ids( $post->post_content ) as $id ) {
		$calc = PMC_Registry::get( $id );
		if ( ! $calc || ! file_exists( PMC_PLUGIN_DIR . $calc['script'] ) ) {
			continue;
		}
		wp_enqueue_script(
			'pmc-calc-' . $id,
			PMC_PLUGIN_URL . $calc['script'],
			array( 'pmc-core' ),
			PMC_VERSION,
			array( 'in_footer' => true )
		);
	}
}
add_action( 'wp_enqueue_scripts', 'pmc_enqueue_assets' );
