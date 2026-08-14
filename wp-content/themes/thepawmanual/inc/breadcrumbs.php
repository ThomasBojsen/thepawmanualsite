<?php
/**
 * Breadcrumb trail for Pages.
 *
 * The URL hierarchy IS the site's information architecture (content/SEO-
 * STRATEGY.md §3: "the URL hierarchy is the information architecture — show
 * it") — every Page below a hub shows its ancestors here. RankMath owns
 * breadcrumb markup and BreadcrumbList schema when it's active (CLAUDE.md §9
 * — the theme must never duplicate what the SEO plugin owns), so this only
 * renders a fallback trail for when RankMath is absent, and never emits its
 * own JSON-LD.
 *
 * @package thepawmanual
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Print the breadcrumb trail for the current Page.
 *
 * Deliberately a plain print function rather than a template part: the
 * RankMath deference check has to happen before anything is echoed, which
 * reads more clearly as an early-return function than as a template file.
 */
function tpm_breadcrumbs() {
	// Hard rule (CLAUDE.md §9): when RankMath's breadcrumb output is
	// available, defer to it entirely — no theme markup, no competing schema.
	if ( function_exists( 'rank_math_the_breadcrumbs' ) ) {
		rank_math_the_breadcrumbs();
		return;
	}

	if ( ! is_page() ) {
		return;
	}

	$post = get_queried_object();
	if ( ! ( $post instanceof WP_Post ) ) {
		return;
	}

	// get_post_ancestors() walks nearest-parent-first; reverse it so the
	// trail reads top (site root) to bottom (the current page), the order a
	// breadcrumb is read in.
	$ancestor_ids = array_reverse( get_post_ancestors( $post ) );

	$trail   = array();
	$trail[] = array(
		'url'   => home_url( '/' ),
		'title' => __( 'Home', 'thepawmanual' ),
	);

	foreach ( $ancestor_ids as $ancestor_id ) {
		$trail[] = array(
			'url'   => get_permalink( $ancestor_id ),
			'title' => get_the_title( $ancestor_id ),
		);
	}
	?>
	<nav class="tpm-breadcrumbs mt-6 overflow-x-auto" aria-label="<?php esc_attr_e( 'Breadcrumb', 'thepawmanual' ); ?>">
		<ol class="flex flex-nowrap items-center gap-1.5 whitespace-nowrap font-geist text-sm text-neutral-500">
			<?php foreach ( $trail as $crumb ) : ?>
				<li class="flex items-center gap-1.5">
					<a href="<?php echo esc_url( $crumb['url'] ); ?>" class="transition-colors hover:text-neutral-900">
						<?php echo esc_html( $crumb['title'] ); ?>
					</a>
					<?php tpm_breadcrumb_separator(); ?>
				</li>
			<?php endforeach; ?>
			<li class="text-neutral-900" aria-current="page">
				<?php echo esc_html( get_the_title( $post ) ); ?>
			</li>
		</ol>
	</nav>
	<?php
}

/**
 * A small inline chevron used between breadcrumb items.
 *
 * Kept local to this file rather than added to inc/icons.php: it's a one-off
 * separator glyph for this component, not a reusable content icon, and
 * inc/icons.php belongs to another worker's territory on this task.
 */
function tpm_breadcrumb_separator() {
	echo '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="h-3.5 w-3.5 shrink-0 text-neutral-300" aria-hidden="true" focusable="false"><path d="m9 18 6-6-6-6"/></svg>';
}
