<?php
/**
 * Homepage section: filter bar.
 *
 * Visual only for the POC — the controls are intentionally inert. Real
 * filtering of the tools grid is a post-POC task (CLAUDE.md §10).
 *
 * The search FIELD this bar used to carry was removed 2026-09-14 (requirement
 * 4 review): it had no wrapping `<form>` and no action, so pressing Enter in
 * it did nothing — a second dead search control on the site's highest-traffic
 * page, right next to the header search this same pass made real. An inert
 * text input that LOOKS like it submits is worse than the honestly-decorative
 * pills beside it; the header's working `#tpm-search-panel` is the one search
 * entry point on the homepage until the tools-grid filtering this bar is
 * built for actually exists.
 *
 * @package thepawmanual
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<section class="mt-10 sm:mt-20">
	<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
		<div class="flex flex-wrap items-center justify-between gap-4 animate-on-scroll [animation:fadeSlideIn_1s_ease-out_0.1s_both]">
			<div class="flex flex-wrap items-center gap-3">
				<span class="font-geist text-sm text-neutral-500"><?php esc_html_e( 'Filter by', 'thepawmanual' ); ?></span>
				<div class="flex flex-wrap items-center gap-2">
					<span class="inline-flex items-center gap-2 rounded-full border border-neutral-200 bg-white px-3 py-1.5 font-geist text-sm text-neutral-700">
						<?php tpm_icon( 'sliders-horizontal', 'h-4 w-4' ); ?>
						<?php esc_html_e( 'Category', 'thepawmanual' ); ?>
					</span>
					<span class="inline-flex items-center gap-2 rounded-full border border-neutral-200 bg-white px-3 py-1.5 font-geist text-sm text-neutral-700">
						<?php esc_html_e( 'Pet Size', 'thepawmanual' ); ?>
						<?php tpm_icon( 'chevron-down', 'h-4 w-4' ); ?>
					</span>
					<span class="inline-flex items-center gap-2 rounded-full border border-neutral-200 bg-white px-3 py-1.5 font-geist text-sm text-neutral-700">
						<?php esc_html_e( 'Type: Dogs', 'thepawmanual' ); ?>
						<span class="h-3 w-3 rounded-full border border-brand-600 bg-brand-500"></span>
					</span>
				</div>
			</div>
		</div>
	</div>
</section>
