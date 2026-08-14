<?php
/**
 * "In This Section" — the hub's child-page card grid.
 *
 * The cats.com hub pattern: every hub page (breeds, health, food, …) lists
 * its children as cards so a visitor — and a crawler — can reach every leaf
 * page in two clicks from the section root. This is what makes hubs the
 * PageRank distributors the content cluster structure was written to use.
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
?>
<section class="tpm-child-grid mt-12" aria-labelledby="tpm-child-grid-heading">
	<h2 id="tpm-child-grid-heading" class="font-playfair text-2xl font-medium text-neutral-900 sm:text-3xl">
		<?php echo esc_html( $heading ); ?>
	</h2>

	<div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
		<?php foreach ( $children as $child ) : ?>
			<a
				href="<?php echo esc_url( get_permalink( $child ) ); ?>"
				class="group flex flex-col rounded-2xl border border-neutral-200 bg-white p-6 transition hover:border-neutral-300 hover:shadow-sm"
			>
				<h3 class="font-geist text-base font-semibold tracking-tight text-neutral-900 group-hover:underline">
					<?php echo esc_html( get_the_title( $child ) ); ?>
				</h3>

				<?php $excerpt = get_the_excerpt( $child ); ?>
				<?php if ( $excerpt ) : ?>
					<p class="mt-2 flex-grow font-geist text-sm text-neutral-600">
						<?php echo esc_html( $excerpt ); ?>
					</p>
				<?php endif; ?>

				<span class="mt-4 inline-flex items-center gap-1.5 font-geist text-sm font-medium text-neutral-900">
					<?php esc_html_e( 'Read more', 'thepawmanual' ); ?>
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 transition-transform group-hover:translate-x-0.5" aria-hidden="true" focusable="false"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
				</span>
			</a>
		<?php endforeach; ?>
	</div>
</section>
