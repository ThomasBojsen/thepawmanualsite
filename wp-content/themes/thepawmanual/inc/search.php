<?php
/**
 * Search-query tuning.
 *
 * @package thepawmanual
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Narrow the front-end search query to the 'page' post type and cap the
 * result count.
 *
 * 'page' is the ONLY post type this site has ever published to — every hub,
 * article and calculator page ships through tools/wxr/ as a WordPress Page
 * (CLAUDE.md §13), and 'post' sits completely unused. WordPress's own
 * search runs across every public post type by default, so leaving this
 * query alone would not surface a single wrong result today; restricting it
 * anyway turns that into a documented decision instead of an accident
 * nobody happened to trip over yet, and it stops a future built-in comments
 * feature, a stray draft some other plugin creates in its own post type, or
 * a future blog relaunch from silently appearing in results before anyone
 * decides it should. If a real post type for articles is ever introduced
 * and should be searchable too, add its slug to the array below — do not
 * remove the restriction outright, or every registered post type (including
 * the internal ones some plugins register) becomes searchable again.
 *
 * `posts_per_page` is set on the same query rather than a second hook: a
 * default WP_Query already has one to override, WordPress's built-in search
 * has no relevance ranking, and an unbounded result list serves no design
 * need on a ~127-page site. 10 is enough that the_posts_pagination() on
 * search.php actually has something to page through instead of never firing.
 *
 * Guarded exactly the way inc/internal-links.php's the_content filter is:
 * `! is_admin()` keeps wp-admin's own list-table searches (Pages, plugins,
 * users, …) untouched, and `is_main_query()` keeps this from clipping a
 * secondary WP_Query some template part builds for its own purposes (e.g.
 * tpm_get_child_pages()) — this only ever touches the one query built from
 * the URL's own `?s=`.
 *
 * @param WP_Query $query The query WordPress is about to run.
 */
function tpm_tune_search_query( $query ) {
	if ( is_admin() || ! $query->is_main_query() || ! $query->is_search() ) {
		return;
	}

	$query->set( 'post_type', 'page' );
	$query->set( 'posts_per_page', 10 );
}
add_action( 'pre_get_posts', 'tpm_tune_search_query' );
