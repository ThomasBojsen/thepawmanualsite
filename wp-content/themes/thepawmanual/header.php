<?php
/**
 * The site header: opens the document, prints <head>, and renders the sticky
 * navigation bar shared by every page.
 *
 * @package thepawmanual
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<!DOCTYPE html>
<html class="no-js h-full scroll-smooth" <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php
	/*
	 * Swap the `no-js` class for `js` before first paint. The scroll-reveal CSS
	 * is gated on `.js`, so with JavaScript disabled content is never hidden.
	 */
	?>
	<script>document.documentElement.className = document.documentElement.className.replace( 'no-js', 'js' );</script>
	<?php wp_head(); ?>
</head>

<body <?php body_class( 'h-full bg-neutral-50 font-sans text-neutral-900 antialiased' ); ?>>
<?php wp_body_open(); ?>

<a class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:rounded-lg focus:bg-neutral-900 focus:px-4 focus:py-2 focus:font-geist focus:text-sm focus:text-white" href="#main">
	<?php esc_html_e( 'Skip to content', 'thepawmanual' ); ?>
</a>

<header class="tpm-header sticky top-0 z-40 border-b border-neutral-200 bg-white/80 backdrop-blur [animation:fadeSlideIn_0.8s_ease-out_0s_both]">
	<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
		<div class="grid h-16 grid-cols-[1fr_auto_1fr] items-center gap-4">

			<?php // Left: desktop navigation, plus the mobile-menu toggle. ?>
			<div class="flex items-center">
				<nav class="hidden lg:block" aria-label="<?php esc_attr_e( 'Primary', 'thepawmanual' ); ?>">
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'primary',
							'container'      => false,
							'menu_class'     => 'tpm-primary-menu',
							'fallback_cb'    => 'tpm_primary_menu_fallback',
							'depth'          => 1,
						)
					);
					?>
				</nav>
				<button
					type="button"
					class="inline-flex items-center justify-center rounded-full border border-neutral-200 bg-white p-2 text-neutral-600 transition hover:border-neutral-300 hover:text-neutral-900 lg:hidden"
					data-tpm-nav-toggle
					aria-controls="tpm-mobile-nav"
					aria-expanded="false"
				>
					<span class="sr-only"><?php esc_html_e( 'Toggle menu', 'thepawmanual' ); ?></span>
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5" aria-hidden="true">
						<path d="M4 6h16"></path>
						<path d="M4 12h16"></path>
						<path d="M4 18h16"></path>
					</svg>
				</button>
			</div>

			<?php // Centre: the wordmark (or a custom logo, if one has been set). ?>
			<div class="flex justify-center">
				<?php if ( has_custom_logo() ) : ?>
					<?php the_custom_logo(); ?>
				<?php else : ?>
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="whitespace-nowrap font-playfair text-2xl font-medium tracking-tight sm:text-3xl">
						<?php bloginfo( 'name' ); ?>
					</a>
				<?php endif; ?>
			</div>

			<?php // Right: actions. Search is a visual-only control for the POC (CLAUDE.md §10). ?>
			<div class="flex items-center justify-end gap-3">
				<button type="button" aria-label="<?php esc_attr_e( 'Search', 'thepawmanual' ); ?>" class="hidden items-center gap-2 rounded-full border border-neutral-200 bg-white px-3 py-2 text-neutral-600 transition hover:border-neutral-300 hover:text-neutral-900 sm:flex">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" aria-hidden="true">
						<path d="m21 21-4.34-4.34"></path>
						<circle cx="11" cy="11" r="8"></circle>
					</svg>
					<span class="font-geist text-sm"><?php esc_html_e( 'Search', 'thepawmanual' ); ?></span>
				</button>
			</div>

		</div>
	</div>

	<?php // Mobile navigation panel — toggled by theme.js; never shown on desktop. ?>
	<div id="tpm-mobile-nav" class="hidden border-t border-neutral-200 lg:hidden">
		<nav class="mx-auto max-w-7xl px-4 py-3 sm:px-6" aria-label="<?php esc_attr_e( 'Primary (mobile)', 'thepawmanual' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'tpm-mobile-menu',
					'fallback_cb'    => 'tpm_primary_menu_fallback',
					'depth'          => 1,
				)
			);
			?>
		</nav>
	</div>
</header>
