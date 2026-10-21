<?php
/**
 * "In This Section" — the hub's child-page card grid.
 *
 * The cats.com hub pattern: every hub page (breeds, health, food, …) lists
 * its children as cards so a visitor — and a crawler — can reach every leaf
 * page in two clicks from the section root. This is what makes hubs the
 * PageRank distributors the content cluster structure was written to use.
 *
 * Restyled 2026-09-14 into the same tpm-card language as
 * related-reading.php: a category chip (this hub's own SHORT title — every
 * card here shares one parent, this page, so the chip is the same for all
 * of them), the child's own SHORT title, then the excerpt and "Read more"
 * line unchanged. Since 2026-10-21 each card also opens with the child's
 * Featured Image, or a paw placeholder (template-parts/content/card-image.php),
 * the same picture the Related Reading cards show. Short titles go through tpm_split_title()
 * (inc/article-meta.php, a sibling worker's file), function_exists()-guarded
 * so a missing file degrades to the untrimmed title rather than a fatal.
 *
 * Expects $args['children'] — WP_Post[], e.g. from tpm_get_child_pages().
 * Optional $args['heading'] overrides the section heading text.
 *
 * @package thepawmanual
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$children = isset( $args['children'] ) && is_array( $args['children'] ) ? $args['children'] : array();
if ( empty( $children ) ) {
	return;
}

$heading = ! empty( $args['heading'] ) ? $args['heading'] : __( 'In This Section', 'thepawmanual' );

// Every card's chip is this hub's own short title — get_the_title() with no
// argument reads the current Page, which is still set from page.php's own
// the_post() call; this template never receives its own post_id via $args.
$chip_label = '';
if ( function_exists( 'tpm_split_title' ) ) {
	$hub_split  = tpm_split_title( get_the_title() );
	$chip_label = ! empty( $hub_split['h1'] ) ? $hub_split['h1'] : '';
}
?>
<section class="tpm-child-grid mt-12" aria-labelledby="tpm-child-grid-heading">
	<h2 id="tpm-child-grid-heading" class="font-playfair text-2xl font-medium text-neutral-900 sm:text-3xl">
		<?php echo esc_html( $heading ); ?>
	</h2>

	<?php
	if ( function_exists( 'tpm_prime_card_images' ) ) {
		tpm_prime_card_images( $children );
	}
	?>
	<div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
		<?php foreach ( $children as $child ) : ?>
			<?php
			$child_title = get_the_title( $child );
			if ( function_exists( 'tpm_split_title' ) ) {
				$child_split = tpm_split_title( $child_title );
				$child_title = ! empty( $child_split['h1'] ) ? $child_split['h1'] : $child_title;
			}
			?>
			<a
				href="<?php echo esc_url( get_permalink( $child ) ); ?>"
				class="tpm-card group flex flex-row items-start gap-4 rounded-2xl border border-neutral-200 bg-white p-6 transition hover:border-brand-200 hover:shadow-sm sm:flex-col sm:items-stretch sm:gap-0"
			>
				<?php get_template_part( 'template-parts/content/card-image', null, array( 'post_id' => $child->ID ) ); ?>
				<div class="flex min-w-0 flex-1 flex-col self-stretch sm:mt-4">
					<?php if ( $chip_label ) : ?>
						<span class="inline-flex w-fit items-center rounded-full bg-accent-50 px-2.5 py-1 font-geist text-[11px] font-semibold uppercase tracking-wide text-accent-700">
							<?php echo esc_html( $chip_label ); ?>
						</span>
					<?php endif; ?>
					<h3 class="mt-3 font-geist text-base font-semibold tracking-tight text-neutral-900 group-hover:underline">
						<?php echo esc_html( $child_title ); ?>
					</h3>

					<?php
					/*
					 * A manually-set excerpt is free (it's already in the DB — no
					 * filters to run) and get_the_excerpt() would return it
					 * as-is too, so read post_excerpt directly and only derive
					 * one when the editor hasn't written one.
					 *
					 * The derive-a-summary path deliberately does NOT call
					 * get_the_excerpt()/wp_trim_excerpt(): with no manual
					 * excerpt, that falls through to the full `the_content`
					 * filter chain (wptexturize, wpautop, shortcode_unautop,
					 * do_shortcode, …) run over the child's ENTIRE body — once
					 * per card, uncached, on every hub-page view. do_shortcode()
					 * (executing every registered shortcode callback) is the
					 * expensive/risky part; wpautop()/shortcode_unautop() are
					 * pointless here since the tags get stripped straight back
					 * out. strip_shortcodes() + wp_trim_words() reproduce the
					 * same "first 55 words + […]" shape without any of that.
					 *
					 * wp_strip_all_tags() is called explicitly (redundant with
					 * wp_trim_words()'s own internal call to it) so the
					 * script/style-block safety is visible right here at the
					 * call site, not buried inside wp_trim_words(): some child
					 * pages carry a `<script type="application/ld+json">` FAQ
					 * block in their body, and wp_strip_all_tags() — unlike
					 * plain strip_tags() — removes the whole element INCLUDING
					 * its text content, so the raw JSON can never end up as
					 * visible card text.
					 */
					$excerpt = $child->post_excerpt;
					if ( '' === $excerpt ) {
						$excerpt = wp_trim_words(
							wp_strip_all_tags( strip_shortcodes( $child->post_content ) ),
							55,
							' [&hellip;]'
						);
					}
					?>
					<?php if ( $excerpt ) : ?>
						<p class="mt-2 flex-grow font-geist text-sm text-neutral-600">
							<?php echo esc_html( $excerpt ); ?>
						</p>
					<?php endif; ?>

					<span class="mt-4 inline-flex items-center gap-1.5 font-geist text-sm font-medium text-brand-700">
						<?php esc_html_e( 'Read more', 'thepawmanual' ); ?>
						<?php tpm_icon( 'arrow-right', 'h-4 w-4 transition-transform group-hover:translate-x-0.5' ); ?>
					</span>
				</div>
			</a>
		<?php endforeach; ?>
	</div>
</section>
