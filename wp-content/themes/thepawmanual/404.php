<?php
/**
 * The 404 (page not found) template.
 *
 * @package thepawmanual
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<main id="main" tabindex="-1" class="mx-auto max-w-2xl px-4 py-20 text-center sm:px-6 lg:px-8">
	<p class="font-geist text-sm font-semibold uppercase tracking-wider text-neutral-500">
		<?php esc_html_e( 'Error 404', 'thepawmanual' ); ?>
	</p>
	<h1 class="mt-3 font-playfair text-4xl font-medium tracking-tight sm:text-5xl">
		<?php esc_html_e( 'We couldn’t find that page', 'thepawmanual' ); ?>
	</h1>
	<p class="mx-auto mt-4 max-w-md font-geist text-neutral-600">
		<?php esc_html_e( 'The page you were looking for may have moved or no longer exists. Head back to the homepage to find a tool.', 'thepawmanual' ); ?>
	</p>
	<div class="mt-8">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="inline-flex items-center gap-2 rounded-xl bg-neutral-900 px-5 py-2.5 font-geist text-sm text-white transition hover:bg-neutral-800">
			<?php esc_html_e( 'Back to homepage', 'thepawmanual' ); ?>
			<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" aria-hidden="true">
				<path d="M5 12h14"></path>
				<path d="m12 5 7 7-7 7"></path>
			</svg>
		</a>
	</div>
</main>
<?php
get_footer();
