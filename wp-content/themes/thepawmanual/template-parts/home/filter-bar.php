<?php
/**
 * Homepage section: the tools-grid filter.
 *
 * Two button groups, category (tpm_get_tool_categories()) and species, that
 * filter the cards in tools-grid.php client-side (theme.js,
 * initToolFilters()). Rendered inside the grid's own `section#tools`, so the
 * hero's quick links and "Find a Tool" land on the filter and the cards
 * together.
 *
 * It ships with the `hidden` attribute and theme.js reveals it: without
 * JavaScript the buttons could do nothing, so a visitor without it simply sees
 * every tool and no dead controls. The buttons are real <button>s carrying
 * aria-pressed, one pressed per group, and the count line is an aria-live
 * region so a screen reader hears how many tools are left.
 *
 * The Aura design's "Pet Size" pill was dropped on purpose (2026-10-22): every
 * tool works for every size, so a size filter could never filter anything.
 *
 * @package thepawmanual
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$tpm_categories = function_exists( 'tpm_get_tool_categories' ) ? tpm_get_tool_categories() : array();
$tpm_species    = array(
	'dogs' => __( 'Dogs', 'thepawmanual' ),
	'cats' => __( 'Cats', 'thepawmanual' ),
);
?>
<div class="mb-8" data-tpm-tool-filters hidden>
	<div class="flex flex-col gap-4 sm:flex-row sm:flex-wrap sm:items-center sm:gap-x-8">
		<div class="flex flex-wrap items-center gap-2">
			<span id="tpm-filter-category-label" class="mr-1 font-geist text-sm text-neutral-500"><?php esc_html_e( 'Category', 'thepawmanual' ); ?></span>
			<div class="flex flex-wrap gap-2" role="group" aria-labelledby="tpm-filter-category-label">
				<button type="button" class="tpm-filter-chip" data-tpm-filter="category" data-tpm-value="all" aria-pressed="true"><?php esc_html_e( 'All', 'thepawmanual' ); ?></button>
				<?php foreach ( $tpm_categories as $tpm_key => $tpm_label ) : ?>
					<button type="button" class="tpm-filter-chip" data-tpm-filter="category" data-tpm-value="<?php echo esc_attr( $tpm_key ); ?>" aria-pressed="false"><?php echo esc_html( $tpm_label ); ?></button>
				<?php endforeach; ?>
			</div>
		</div>

		<div class="flex flex-wrap items-center gap-2">
			<span id="tpm-filter-species-label" class="mr-1 font-geist text-sm text-neutral-500"><?php esc_html_e( 'Pet', 'thepawmanual' ); ?></span>
			<div class="flex flex-wrap gap-2" role="group" aria-labelledby="tpm-filter-species-label">
				<button type="button" class="tpm-filter-chip" data-tpm-filter="species" data-tpm-value="all" aria-pressed="true"><?php esc_html_e( 'All', 'thepawmanual' ); ?></button>
				<?php foreach ( $tpm_species as $tpm_key => $tpm_label ) : ?>
					<button type="button" class="tpm-filter-chip" data-tpm-filter="species" data-tpm-value="<?php echo esc_attr( $tpm_key ); ?>" aria-pressed="false"><?php echo esc_html( $tpm_label ); ?></button>
				<?php endforeach; ?>
			</div>
		</div>
	</div>

	<div class="mt-3 flex flex-wrap items-center gap-3">
		<p class="font-geist text-sm text-neutral-500" data-tpm-filter-status aria-live="polite"></p>
		<button type="button" class="font-geist text-sm text-brand-700 underline underline-offset-2 hover:text-brand-900" data-tpm-filter-clear hidden><?php esc_html_e( 'Clear filters', 'thepawmanual' ); ?></button>
	</div>
</div>
