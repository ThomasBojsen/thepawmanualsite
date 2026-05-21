<?php
/**
 * Navigation menu locations and the primary-menu fallback.
 *
 * @package thepawmanual
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the theme's navigation menu locations.
 */
function tpm_register_menus() {
	register_nav_menus(
		array(
			'primary' => __( 'Primary Navigation (header)', 'thepawmanual' ),
		)
	);
}
add_action( 'after_setup_theme', 'tpm_register_menus' );

/**
 * Fallback for the primary menu.
 *
 * Shown until an administrator assigns a real menu to the "primary" location
 * (Appearance → Menus). It mirrors the four pet categories from the design so
 * the header is never empty. The links are placeholders (#) and will point to
 * real category pages once those exist.
 *
 * wp_nav_menu() passes its full argument array to this callback, so the same
 * fallback renders correctly whether it is the horizontal header menu or the
 * stacked mobile menu — it just reuses whichever `menu_class` was requested.
 *
 * @param array $args The wp_nav_menu() arguments.
 */
function tpm_primary_menu_fallback( $args = array() ) {
	$menu_class = ! empty( $args['menu_class'] ) ? $args['menu_class'] : 'tpm-primary-menu';

	$items = array(
		__( 'Dog Tools', 'thepawmanual' ),
		__( 'Cat Tools', 'thepawmanual' ),
		__( 'Bird Tools', 'thepawmanual' ),
		__( 'Small Pet Tools', 'thepawmanual' ),
	);

	echo '<ul class="' . esc_attr( $menu_class ) . '">';
	foreach ( $items as $label ) {
		printf(
			'<li><a href="%s">%s</a></li>',
			esc_url( '#' ),
			esc_html( $label )
		);
	}
	echo '</ul>';
}
