<?php
/**
 * "Free tools for pet owners" — the contextual tools strip.
 *
 * Renders tpm_get_tools_for_context()'s ranked, per-page tool selection as a
 * card grid. User requirement 3 (CLAUDE.md §13): every landing/hub page gets
 * this, so a visitor is never more than one section away from the two
 * working calculators, wherever on the site they landed.
 *
 * A "coming soon" card deliberately renders as a <div>, never an <a> — the
 * calculator behind it doesn't exist yet, and tpm_get_tools_for_context()
 * has already re-checked every 'available' row against actually-published
 * Pages, so an available card here only ever links somewhere real.
 *
 * Expects $args['post_id'] (int) — the page the strip is being shown on, so
 * tpm_get_tools_for_context() can rank the tools for it. Optional
 * $args['heading'], $args['limit'] (passed straight through, default 4).
 *
 * @package thepawmanual
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post_id = isset( $args['post_id'] ) ? (int) $args['post_id'] : 0;
if ( ! $post_id || ! function_exists( 'tpm_get_tools_for_context' ) ) {
	return;
}

$limit = isset( $args['limit'] ) ? (int) $args['limit'] : 4;
$tools = tpm_get_tools_for_context( $post_id, $limit );
if ( empty( $tools ) ) {
	return;
}

$heading = ! empty( $args['heading'] ) ? $args['heading'] : __( 'Free tools for pet owners', 'thepawmanual' );
?>
<section class="tpm-tools-strip mt-12 rounded-3xl border border-neutral-200 bg-neutral-50 p-6 sm:p-8" aria-labelledby="tpm-tools-strip-heading">
	<p class="font-geist text-xs font-semibold uppercase tracking-wider text-brand-600">
		<?php esc_html_e( 'Pet Tools', 'thepawmanual' ); ?>
	</p>
	<h2 id="tpm-tools-strip-heading" class="mt-2 font-playfair text-2xl font-medium text-neutral-900 sm:text-3xl">
		<?php echo esc_html( $heading ); ?>
	</h2>
	<p class="mt-2 font-geist text-sm text-neutral-600">
		<?php esc_html_e( 'Free calculators built for real decisions, not guesswork.', 'thepawmanual' ); ?>
	</p>

	<div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
		<?php foreach ( $tools as $tool ) : ?>
			<?php $is_available = 'available' === $tool['status'] && ! empty( $tool['url'] ); ?>
			<?php if ( $is_available ) : ?>
				<a
					href="<?php echo esc_url( $tool['url'] ); ?>"
					class="group flex flex-col rounded-2xl border border-neutral-200 bg-white p-5 transition hover:border-brand-200 hover:shadow-sm"
				>
					<div class="mb-4 flex h-10 w-10 items-center justify-center rounded-xl bg-brand-50 text-brand-600">
						<?php tpm_icon( $tool['icon'], 'h-5 w-5' ); ?>
					</div>
					<h3 class="font-geist text-sm font-semibold tracking-tight text-neutral-900">
						<?php echo esc_html( $tool['title'] ); ?>
					</h3>
					<p class="mt-1.5 flex-grow font-geist text-xs text-neutral-600">
						<?php echo esc_html( $tool['description'] ); ?>
					</p>
					<span class="mt-4 inline-flex items-center gap-1.5 font-geist text-xs font-medium text-brand-700">
						<?php esc_html_e( 'Try tool', 'thepawmanual' ); ?>
						<?php tpm_icon( 'arrow-right', 'h-3.5 w-3.5 transition-transform group-hover:translate-x-0.5' ); ?>
					</span>
				</a>
			<?php else : ?>
				<div class="flex flex-col rounded-2xl border border-dashed border-neutral-200 bg-white/60 p-5">
					<div class="mb-4 flex h-10 w-10 items-center justify-center rounded-xl bg-neutral-100 text-neutral-400">
						<?php tpm_icon( $tool['icon'], 'h-5 w-5' ); ?>
					</div>
					<h3 class="font-geist text-sm font-semibold tracking-tight text-neutral-700">
						<?php echo esc_html( $tool['title'] ); ?>
					</h3>
					<p class="mt-1.5 flex-grow font-geist text-xs text-neutral-600">
						<?php echo esc_html( $tool['description'] ); ?>
					</p>
					<span class="mt-4 inline-flex w-fit items-center rounded-full border border-neutral-200 bg-neutral-100 px-2.5 py-1 font-geist text-[11px] font-medium text-neutral-600">
						<?php esc_html_e( 'Coming soon', 'thepawmanual' ); ?>
					</span>
				</div>
			<?php endif; ?>
		<?php endforeach; ?>
	</div>
</section>
