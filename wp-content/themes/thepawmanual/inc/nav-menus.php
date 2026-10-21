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

/**
 * Add a show/hide button after each top-level mobile-menu item that has a
 * sub-menu.
 *
 * Without it the mobile panel opened with every sub-menu already expanded,
 * so one tap on the hamburger filled the screen with links. The parent link
 * itself still goes to its hub page; only the chevron button next to it
 * folds the children in and out (assets/js/theme.js, initMobileSubmenus()).
 * The button is rendered here rather than built in JS so its label is
 * translatable, and src/tailwind.css hides it for `.no-js` visitors, who keep
 * the always-open list instead of a button that does nothing.
 *
 * Only the mobile menu is touched: the desktop menu opens its dropdowns on
 * hover/focus and needs no button.
 *
 * @param string   $item_output The menu item's HTML.
 * @param WP_Post  $item        The menu item.
 * @param int      $depth       Depth of the item (0 = top level).
 * @param stdClass $args        wp_nav_menu() arguments.
 * @return string
 */
function tpm_add_mobile_submenu_toggle( $item_output, $item, $depth, $args ) {
	if ( 0 !== $depth || empty( $args->menu_class ) || 'tpm-mobile-menu' !== $args->menu_class ) {
		return $item_output;
	}

	$classes = is_array( $item->classes ) ? $item->classes : array();
	if ( ! in_array( 'menu-item-has-children', $classes, true ) ) {
		return $item_output;
	}

	ob_start();
	tpm_icon( 'chevron-down', 'h-4 w-4' );
	$icon = ob_get_clean();

	$label = sprintf(
		/* translators: %s: the parent menu item's label, e.g. "Pet Guides". */
		__( 'Show %s submenu', 'thepawmanual' ),
		wp_strip_all_tags( $item->title )
	);

	return $item_output . sprintf(
		'<button type="button" class="tpm-submenu-toggle" data-tpm-submenu-toggle aria-expanded="false" aria-label="%s">%s</button>',
		esc_attr( $label ),
		$icon
	);
}
add_filter( 'walker_nav_menu_start_el', 'tpm_add_mobile_submenu_toggle', 10, 4 );
