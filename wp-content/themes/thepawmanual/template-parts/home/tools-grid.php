<?php
/**
 * Homepage section: tools grid, with its filter on top.
 *
 * Renders one card per entry in tpm_get_homepage_tool_cards() (which links a
 * tool only once its Page is published). The `#tools` anchor is the target of
 * the "Find a Tool" / hero quick links / "Browse all tools" buttons; the
 * filter (filter-bar.php) sits inside it so those links land on both.
 *
 * @package thepawmanual
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$tools = function_exists( 'tpm_get_homepage_tool_cards' ) ? tpm_get_homepage_tool_cards() : array();
if ( empty( $tools ) ) {
	return;
}
?>
<section id="tools" class="mt-10 scroll-mt-24 sm:mt-20" aria-labelledby="tpm-tools-heading">
	<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
		<h2 id="tpm-tools-heading" class="sr-only" tabindex="-1"><?php esc_html_e( 'Pet tools and calculators', 'thepawmanual' ); ?></h2>

		<?php get_template_part( 'template-parts/home/filter-bar' ); ?>

		<div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 lg:gap-8">
			<?php
			foreach ( $tools as $tool ) {
				get_template_part( 'template-parts/tool-card', null, array( 'tool' => $tool ) );
			}
			?>
		</div>

		<p class="mt-6 font-geist text-sm text-neutral-600" data-tpm-filter-empty hidden>
			<?php esc_html_e( 'No tools match those filters yet.', 'thepawmanual' ); ?>
		</p>
	</div>
</section>
