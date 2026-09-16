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
		// Same short-H1 rule as the final crumb below, applied to every
		// ancestor too (deploy-gate finding, 2026-09-14): before this fix
		// only the LAST crumb was split, so a deep page under /tools/ showed
		// the short "Free Pet Calculators" for itself but the full stored
		// "Tools: Free Pet Calculators" for that same hub one level up —
		// same page, two different titles in one trail. function_exists()
		// guards the same load-order edge case the final crumb already
		// guards against.
		$ancestor_title = function_exists( 'tpm_get_title_parts' )
			? tpm_get_title_parts( $ancestor_id )['h1']
			: get_the_title( $ancestor_id );

		$trail[] = array(
			'url'   => get_permalink( $ancestor_id ),
			'title' => $ancestor_title,
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
				<?php
				// The final crumb must read the same short phrase as the <h1>
				// immediately below it — page.php resolves tpm_get_title_parts()
				// once and hands it to article-header.php, so a raw
				// get_the_title() here would show the long "Hook: Deck" form in
				// the trail while the H1 shows only the short "Hook" half of the
				// same page. function_exists() guards this so the file degrades
				// to the raw title (never fatals) if load order ever changes and
				// inc/article-meta.php hasn't loaded yet.
				//
				// This only covers the theme's OWN fallback trail, built above
				// when RankMath is absent — the early return at the top of this
				// function hands off to rank_math_the_breadcrumbs() entirely
				// when RankMath is active, and that trail's final-crumb text is
				// that plugin's own per-page "Breadcrumb Title" field, not
				// anything this file controls.
				$crumb_title = function_exists( 'tpm_get_title_parts' )
					? tpm_get_title_parts( $post->ID )['h1']
					: get_the_title( $post );
				echo esc_html( $crumb_title );
				?>
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
