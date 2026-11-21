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
 * Add a show/hide button after each mobile-menu item that has a sub-menu, at
 * the top level and at the second level (a nested accordion).
 *
 * Without it the mobile panel opened with every sub-menu already expanded,
 * so one tap on the hamburger filled the screen with links. The parent link
 * itself still goes to its hub page; only the chevron button next to it
 * folds the children in and out (assets/js/theme.js, initMobileSubmenus()).
 * The button is rendered here rather than built in JS so its label is
 * translatable, and src/tailwind.css hides it for `.no-js` visitors, who keep
 * the always-open list instead of a button that does nothing.
 *
 * Only the mobile menu is touched: the desktop menu opens its dropdowns and
 * flyouts on hover/focus and needs no button. theme.js needs no change for
 * the second level: it already wires every [data-tpm-submenu-toggle] to the
 * `.sub-menu` directly inside the button's own <li>.
 *
 * @param string   $item_output The menu item's HTML.
 * @param WP_Post  $item        The menu item.
 * @param int      $depth       Depth of the item (0 = top level).
 * @param stdClass $args        wp_nav_menu() arguments.
 * @return string
 */
function tpm_add_mobile_submenu_toggle( $item_output, $item, $depth, $args ) {
	// Depth 0 (Dogs) and depth 1 (Dog Health) can both have a list under
	// them since header.php asks for three levels; depth 2 is the last level
	// and never has one.
	if ( $depth > 1 || empty( $args->menu_class ) || 'tpm-mobile-menu' !== $args->menu_class ) {
		return $item_output;
	}

	$classes = is_array( $item->classes ) ? $item->classes : array();
	if ( ! in_array( 'menu-item-has-children', $classes, true ) ) {
		return $item_output;
	}

	ob_start();
	tpm_icon( 'chevron-down', 'h-4 w-4' );
	$icon = ob_get_clean();

	// The same short label the link itself shows ("Dog Health", not the
	// sub-hub's full H1), via the nav_menu_item_title filter in
	// inc/article-meta.php.
	$label = sprintf(
		/* translators: %s: the parent menu item's label, e.g. "Pet Guides". */
		__( 'Show %s submenu', 'thepawmanual' ),
		wp_strip_all_tags( apply_filters( 'nav_menu_item_title', $item->title, $item, $args, $depth ) )
	);

	return $item_output . sprintf(
		'<button type="button" class="tpm-submenu-toggle" data-tpm-submenu-toggle aria-expanded="false" aria-label="%s">%s</button>',
		esc_attr( $label ),
		$icon
	);
}
add_filter( 'walker_nav_menu_start_el', 'tpm_add_mobile_submenu_toggle', 10, 4 );


/*
 * The header menu's third level, filled in automatically.
 *
 * The menu in Appearance -> Menus holds two levels that an editor maintains
 * by hand: the hubs (Dogs) and the sub-hubs (Dog Health). The third level,
 * the articles inside each sub-hub, is never typed into the menu builder.
 * Instead, every second-level item that links to a Page gets that Page's
 * published child Pages appended under it at render time. A new article
 * therefore appears in the menu the moment its Page is published, and an
 * unpublished one never does: the same "links light up once published" rule
 * inc/content-nav.php and tpm_get_published_page_url() follow everywhere
 * else.
 *
 * Children an editor did add by hand under a second-level item are kept, in
 * their own order, and any published child Page not already among them is
 * appended after them, so a Page is never listed twice under one parent.
 *
 * The work is split so that the logic can be tested without WordPress
 * (tools/tests/run.php): tpm_nav_depth_one_page_ids() and
 * tpm_nav_append_child_page_items() are pure functions over the menu item
 * list; only tpm_nav_get_published_child_pages() and the filter callback
 * itself talk to WordPress.
 */

/**
 * The second-level menu items that link to a Page.
 *
 * "Second level" is depth 1 in WordPress's counting: the item's parent is a
 * top-level item. Items whose parent is missing from the list (WordPress
 * keeps an orphaned item's parent ID when the parent is deleted) are not
 * counted, since the walker would not render them at that depth either.
 *
 * @param array $menu_items Menu item objects, as passed to wp_nav_menu_objects.
 * @return array<int,int> Map of the menu item's db_id => the linked Page ID.
 */
function tpm_nav_depth_one_page_ids( array $menu_items ) {
	$parent_of = array();
	foreach ( $menu_items as $item ) {
		$parent_of[ (int) $item->db_id ] = (int) $item->menu_item_parent;
	}

	$page_ids = array();
	foreach ( $menu_items as $item ) {
		$parent = (int) $item->menu_item_parent;

		$is_depth_one = $parent > 0
			&& isset( $parent_of[ $parent ] )
			&& 0 === $parent_of[ $parent ];

		if ( $is_depth_one
			&& isset( $item->type, $item->object, $item->object_id )
			&& 'post_type' === $item->type
			&& 'page' === $item->object
			&& (int) $item->object_id > 0
		) {
			$page_ids[ (int) $item->db_id ] = (int) $item->object_id;
		}
	}

	return $page_ids;
}

/**
 * Append synthetic menu items for published child Pages under their
 * second-level parents.
 *
 * Each synthetic item is shaped like the items wp_setup_nav_menu_item()
 * produces for a Page, so Walker_Nav_Menu and every filter it runs
 * (including tpm_filter_nav_menu_item_title(), which shortens the long H1 to
 * its part before the first ": " or "? ") treat it like a hand-made one:
 * - Its ID and db_id are the child Page's own post ID. Pages and menu items
 *   share the wp_posts table, so a Page ID can never equal a real menu
 *   item's ID.
 * - Its title is the Page's title run through the `the_title` filter,
 *   exactly what WordPress stores on an unlabelled menu item, so the title
 *   filter recognises it as "not relabelled by an editor" and shortens it.
 * - `menu-item-has-children` is added to the parent here because
 *   wp_nav_menu() computes that class before wp_nav_menu_objects runs.
 *
 * @param array $menu_items         Menu item objects (wp_nav_menu_objects).
 * @param array $children_by_parent Map of parent Page ID => list of children,
 *                                  each an array with keys `id` (Page ID),
 *                                  `title`, `url` and `current` (bool).
 * @return array The menu items with the children appended.
 */
function tpm_nav_append_child_page_items( array $menu_items, array $children_by_parent ) {
	$depth_one = tpm_nav_depth_one_page_ids( $menu_items );
	if ( ! $depth_one ) {
		return $menu_items;
	}

	// Pages already present as hand-made children, per parent menu item.
	$existing_children = array();
	$max_order         = 0;
	foreach ( $menu_items as $item ) {
		$max_order = max( $max_order, (int) $item->menu_order );

		$parent = (int) $item->menu_item_parent;
		if ( isset( $depth_one[ $parent ] ) && isset( $item->object, $item->object_id ) && 'page' === $item->object ) {
			$existing_children[ $parent ][ (int) $item->object_id ] = true;
		}
	}

	$new_items     = array();
	$gains_a_child = array();
	foreach ( $depth_one as $parent_db_id => $parent_page_id ) {
		if ( empty( $children_by_parent[ $parent_page_id ] ) ) {
			continue;
		}

		foreach ( $children_by_parent[ $parent_page_id ] as $child ) {
			$child_id = (int) $child['id'];
			if ( isset( $existing_children[ $parent_db_id ][ $child_id ] ) ) {
				continue; // Added by hand already; keep that one.
			}
			$existing_children[ $parent_db_id ][ $child_id ] = true;

			$classes = array( 'menu-item', 'menu-item-type-post_type', 'menu-item-object-page', 'tpm-auto-menu-item' );
			if ( ! empty( $child['current'] ) ) {
				$classes[] = 'current-menu-item';
				$classes[] = 'page_item';
				$classes[] = 'page-item-' . $child_id;
				$classes[] = 'current_page_item';
			}

			$new_items[] = tpm_nav_make_menu_item(
				array(
					'ID'               => $child_id,
					'db_id'            => $child_id,
					'menu_item_parent' => (string) $parent_db_id,
					'menu_order'       => ++$max_order,
					'post_type'        => 'nav_menu_item',
					'post_status'      => 'publish',
					'post_parent'      => 0,
					'object_id'        => (string) $child_id,
					'object'           => 'page',
					'type'             => 'post_type',
					'type_label'       => 'Page',
					'title'            => (string) $child['title'],
					'post_title'       => '',
					'url'              => (string) $child['url'],
					'target'           => '',
					'attr_title'       => '',
					'description'      => '',
					'classes'          => $classes,
					'xfn'              => '',
					'current'          => ! empty( $child['current'] ),
					'current_item_ancestor' => false,
					'current_item_parent'   => false,
				)
			);
			$gains_a_child[ $parent_db_id ] = true;
		}
	}

	if ( ! $new_items ) {
		return $menu_items;
	}

	foreach ( $menu_items as $item ) {
		if ( isset( $gains_a_child[ (int) $item->db_id ] ) ) {
			$classes = is_array( $item->classes ) ? $item->classes : array();
			if ( ! in_array( 'menu-item-has-children', $classes, true ) ) {
				$classes[]     = 'menu-item-has-children';
				$item->classes = $classes;
			}
		}
	}

	// Keys are kept unique (wp_nav_menu() keys its list by menu_order) in
	// case another filter relies on them; the walker itself only reads
	// menu_item_parent, so the children's position in the array does not
	// matter beyond their order among themselves.
	foreach ( $new_items as $new_item ) {
		$menu_items[ $new_item->menu_order ] = $new_item;
	}

	return $menu_items;
}

/**
 * Build one menu item object.
 *
 * A WP_Post when WordPress is loaded, because third-party nav filters
 * sometimes type-hint WP_Post; a plain object in the test harness. The
 * WP_Post constructor only copies properties and touches no cache, unlike
 * running wp_setup_nav_menu_item() on the child Page itself, which would
 * mutate the cached Page object.
 *
 * @param array $props Menu item properties.
 * @return object
 */
function tpm_nav_make_menu_item( array $props ) {
	$object = (object) $props;
	return class_exists( 'WP_Post' ) ? new WP_Post( $object ) : $object;
}

/**
 * Published child Pages of the given Pages, grouped by parent and ordered by
 * menu_order, then title (the same order tpm_get_child_pages() gives the hub
 * grids).
 *
 * One query for every parent at once, remembered for the rest of the
 * request: header.php renders the same menu twice (desktop and mobile), and
 * the second render must not repeat it.
 *
 * @param int[] $parent_ids Page IDs.
 * @return array<int,WP_Post[]> Map of parent Page ID => child Pages.
 */
function tpm_nav_get_published_child_pages( array $parent_ids ) {
	static $memo = array();

	$parent_ids = array_values( array_unique( array_map( 'intval', $parent_ids ) ) );
	sort( $parent_ids );
	if ( ! $parent_ids ) {
		return array();
	}

	$key = implode( ',', $parent_ids );
	if ( isset( $memo[ $key ] ) ) {
		return $memo[ $key ];
	}

	$query = new WP_Query(
		array(
			'post_type'              => 'page',
			'post_status'            => 'publish', // Explicit, so a logged-in editor's view never lists drafts or private Pages either.
			'post_parent__in'        => $parent_ids,
			'posts_per_page'         => -1,
			'orderby'                => array(
				'menu_order' => 'ASC',
				'title'      => 'ASC',
			),
			'no_found_rows'          => true,
			'ignore_sticky_posts'    => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		)
	);

	$grouped = array();
	foreach ( $query->posts as $page ) {
		$grouped[ (int) $page->post_parent ][] = $page;
	}

	$memo[ $key ] = $grouped;
	return $grouped;
}

/**
 * wp_nav_menu_objects callback: add the third level to the header menu.
 *
 * Only the `primary` location is touched (header.php renders it twice, as
 * the desktop dropdown and the mobile panel); any other menu a widget or
 * block prints is left exactly as built.
 *
 * @param array    $menu_items The sorted menu items.
 * @param stdClass $args       wp_nav_menu() arguments.
 * @return array
 */
function tpm_nav_inject_published_child_pages( $menu_items, $args ) {
	if ( ! is_array( $menu_items ) || ! $menu_items || empty( $args->theme_location ) || 'primary' !== $args->theme_location ) {
		return $menu_items;
	}

	$depth_one = tpm_nav_depth_one_page_ids( $menu_items );
	if ( ! $depth_one ) {
		return $menu_items;
	}

	$current_page_id = is_page() ? (int) get_queried_object_id() : 0;

	$children_by_parent = array();
	foreach ( tpm_nav_get_published_child_pages( array_values( $depth_one ) ) as $parent_id => $pages ) {
		foreach ( $pages as $page ) {
			$children_by_parent[ $parent_id ][] = array(
				'id'      => (int) $page->ID,
				// The same expression wp_setup_nav_menu_item() uses for an
				// unlabelled Page item (not get_the_title(), which would add
				// "Protected: " to a password-protected Page's label).
				'title'   => apply_filters( 'the_title', $page->post_title, $page->ID ),
				'url'     => get_permalink( $page ),
				'current' => $current_page_id === (int) $page->ID,
			);
		}
	}

	return tpm_nav_append_child_page_items( $menu_items, $children_by_parent );
}
add_filter( 'wp_nav_menu_objects', 'tpm_nav_inject_published_child_pages', 10, 2 );
