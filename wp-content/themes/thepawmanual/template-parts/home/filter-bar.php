<?php
/**
 * Homepage section: filter bar.
 *
 * Visual only for the POC — the controls are intentionally inert. Real
 * filtering of the tools grid is a post-POC task (CLAUDE.md §10).
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
						<span class="h-3 w-3 rounded-full border border-amber-600 bg-amber-500"></span>
					</span>
				</div>
			</div>
			<div class="hidden w-64 items-center gap-2 rounded-full border border-neutral-200 bg-white px-3 py-2 text-neutral-500 md:flex">
				<?php tpm_icon( 'search', 'h-4 w-4' ); ?>
				<label for="tpm-tools-search" class="sr-only"><?php esc_html_e( 'Search tools', 'thepawmanual' ); ?></label>
				<input type="search" id="tpm-tools-search" placeholder="<?php esc_attr_e( 'Search', 'thepawmanual' ); ?>" class="w-full bg-transparent font-geist text-sm text-neutral-900 outline-none placeholder:text-neutral-400">
			</div>
		</div>
	</div>
</section>
