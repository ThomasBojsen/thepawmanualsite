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
 * Site-relative paths of the published pages under the given parent paths
 * (the parents themselves included when published).
 *
 * Feeds PawManual.isPublished() in the engine, so a calculator result (a
 * breed selector's ranked list, say) links only to pages that are live and
 * prints plain text for the rest. Same rule the theme applies to post-body
 * links, enforced here because links built in the browser never pass
 * through the_content. One get_pages() call per parent, only on pages that
 * carry a calculator declaring `link_parents`.
 *
 * @param string[] $parents Parent page paths, e.g. array( 'dogs/breeds' ).
 * @return string[] Paths like '/dogs/breeds/poodle/'.
 */
function pmc_get_published_paths( $parents ) {
	$paths = array();

	foreach ( (array) $parents as $parent_path ) {
		$parent = get_page_by_path( trim( (string) $parent_path, '/' ) );
		if ( ! $parent || 'publish' !== $parent->post_status ) {
			continue;
		}

		$pages = array_merge(
			array( $parent ),
			get_pages(
				array(
					'child_of'    => $parent->ID,
					'post_status' => 'publish',
				)
			)
		);

		foreach ( $pages as $page ) {
			$path = wp_parse_url( get_permalink( $page ), PHP_URL_PATH );
			if ( $path ) {
				$paths[] = '/' . trim( strtolower( $path ), '/' ) . '/';
			}
		}
	}

	return array_values( array_unique( $paths ) );
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
	$link_parents = array();
	foreach ( pmc_find_calculator_ids( $post->post_content ) as $id ) {
		$calc = PMC_Registry::get( $id );
		if ( ! $calc || ! file_exists( PMC_PLUGIN_DIR . $calc['script'] ) ) {
			continue;
		}
		if ( ! empty( $calc['link_parents'] ) ) {
			$link_parents = array_merge( $link_parents, (array) $calc['link_parents'] );
		}
		wp_enqueue_script(
			'pmc-calc-' . $id,
			PMC_PLUGIN_URL . $calc['script'],
			array( 'pmc-core' ),
			PMC_VERSION,
			array( 'in_footer' => true )
		);
	}

	if ( $link_parents ) {
		$published = array_fill_keys( pmc_get_published_paths( $link_parents ), true );
		wp_add_inline_script(
			'pmc-core',
			'window.PawManual = window.PawManual || {}; window.PawManual._published = ' . wp_json_encode( (object) $published ) . ';',
			'before'
		);
	}
}
add_action( 'wp_enqueue_scripts', 'pmc_enqueue_assets' );
