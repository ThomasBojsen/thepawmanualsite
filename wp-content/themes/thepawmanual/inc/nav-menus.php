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
 * (Appearance → Menus) — it is a fallback, not the source of truth for the
 * live nav, so an admin-assigned menu always wins. Matches SEO-STRATEGY.md
 * §1 item 0.8's required main nav: Dogs · Cats · Pet Guides · Tools, linking
 * to the real hub URLs from content/SITEMAP.md rather than the previous
 * placeholder "Dog Tools / Cat Tools / Bird Tools / Small Pet Tools" labels
 * and `#` hrefs.
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
		array(
			'label' => __( 'Dogs', 'thepawmanual' ),
			'path'  => '/dogs/',
		),
		array(
			'label' => __( 'Cats', 'thepawmanual' ),
			'path'  => '/cats/',
		),
		array(
			'label' => __( 'Pet Guides', 'thepawmanual' ),
			'path'  => '/pet-guides/',
		),
		array(
			'label' => __( 'Tools', 'thepawmanual' ),
			'path'  => '/tools/',
		),
	);

	echo '<ul class="' . esc_attr( $menu_class ) . '">';
	foreach ( $items as $item ) {
		printf(
			'<li><a href="%s">%s</a></li>',
			esc_url( home_url( $item['path'] ) ),
			esc_html( $item['label'] )
		);
	}
	echo '</ul>';
}
