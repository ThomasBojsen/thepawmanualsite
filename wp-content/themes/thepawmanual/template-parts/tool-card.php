<?php
/**
 * One tool card in the homepage tools grid.
 *
 * Expects $args['tool'] — a single entry from tpm_get_homepage_tools().
 *
 * @package thepawmanual
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$tool = isset( $args['tool'] ) && is_array( $args['tool'] ) ? $args['tool'] : null;
if ( ! $tool ) {
	return;
}

$is_available = isset( $tool['status'] ) && 'available' === $tool['status'] && ! empty( $tool['url'] );
$icon         = ! empty( $tool['icon'] ) ? $tool['icon'] : 'calculator';
?>
<article class="group flex flex-col rounded-3xl border border-neutral-200 bg-white p-6 transition hover:shadow-sm animate-on-scroll [animation:fadeSlideIn_0.7s_ease-out_both]">
	<div class="mb-5 flex h-12 w-12 items-center justify-center rounded-2xl bg-neutral-100 text-neutral-900">
		<?php tpm_icon( $icon, 'h-6 w-6' ); ?>
	</div>

	<h3 class="font-geist text-lg font-semibold tracking-tight text-neutral-900">
		<?php echo esc_html( $tool['title'] ); ?>
	</h3>

	<p class="mt-2 flex-grow font-geist text-sm text-neutral-600">
		<?php echo esc_html( $tool['description'] ); ?>
	</p>

	<div class="mt-6">
		<?php if ( $is_available ) : ?>
			<a href="<?php echo esc_url( $tool['url'] ); ?>" class="inline-flex items-center gap-2 rounded-full bg-neutral-900 px-4 py-2 font-geist text-sm text-white transition hover:bg-neutral-800">
				<?php esc_html_e( 'Open Tool', 'thepawmanual' ); ?>
				<?php tpm_icon( 'arrow-right', 'h-4 w-4' ); ?>
			</a>
		<?php else : ?>
			<span class="inline-flex items-center rounded-full border border-neutral-200 bg-neutral-50 px-4 py-2 font-geist text-sm text-neutral-400">
				<?php esc_html_e( 'Coming soon', 'thepawmanual' ); ?>
			</span>
		<?php endif; ?>
	</div>
</article>
