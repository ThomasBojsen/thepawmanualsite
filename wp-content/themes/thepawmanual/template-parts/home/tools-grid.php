<?php
/**
 * Homepage section: tools grid.
 *
 * Renders one card per entry in tpm_get_homepage_tools(). The `#tools` anchor
 * is the target of the "Find a Tool" / "Browse all tools" buttons.
 *
 * @package thepawmanual
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$tools = function_exists( 'tpm_get_homepage_tools' ) ? tpm_get_homepage_tools() : array();
if ( empty( $tools ) ) {
	return;
}
?>
<section id="tools" class="mt-8 scroll-mt-24">
	<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
		<h2 class="sr-only"><?php esc_html_e( 'Pet tools and calculators', 'thepawmanual' ); ?></h2>
		<div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 lg:gap-8">
			<?php
			foreach ( $tools as $tool ) {
				get_template_part( 'template-parts/tool-card', null, array( 'tool' => $tool ) );
			}
			?>
		</div>
	</div>
</section>
