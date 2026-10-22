<?php
/**
 * Homepage section: closing call-to-action.
 *
 * Repurposed from the Aura template's e-commerce "Shop Now" banner into a
 * content-oriented call to action (CLAUDE.md §10).
 *
 * @package thepawmanual
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<section class="mt-16 sm:mt-24">
	<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
		<div class="relative overflow-hidden rounded-3xl animate-on-scroll [animation:fadeSlideIn_1s_ease-out_0.1s_both]">
			<img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/promo-dog.jpg' ) ); ?>" alt="" class="h-[460px] w-full object-cover" loading="lazy" decoding="async">
			<div class="absolute inset-0 bg-gradient-to-tr from-black/70 via-black/30 to-transparent"></div>
			<div class="absolute bottom-8 left-6 max-w-lg sm:bottom-12 sm:left-10">
				<h2 class="font-playfair text-3xl font-medium tracking-tight text-white sm:text-5xl">
					<?php esc_html_e( 'Every pet tool, in one place', 'thepawmanual' ); ?>
				</h2>
				<p class="mt-2 font-geist text-sm text-neutral-200">
					<?php esc_html_e( 'A growing library of free, easy-to-use calculators for dogs and cats, with birds and small pets on the way.', 'thepawmanual' ); ?>
				</p>
				<div class="mt-5 flex flex-wrap items-center gap-3">
					<a href="#tools" data-tpm-filter-link="all" class="inline-flex items-center gap-2 rounded-xl bg-brand-600 px-4 py-2.5 font-geist text-sm text-white transition hover:bg-brand-700">
						<?php esc_html_e( 'Browse all tools', 'thepawmanual' ); ?>
						<?php tpm_icon( 'arrow-right', 'h-4 w-4' ); ?>
					</a>
					<a href="<?php echo esc_url( home_url( '/tools/dog-age-calculator/' ) ); ?>" class="inline-flex items-center gap-2 rounded-xl border border-white/30 bg-white/10 px-4 py-2.5 font-geist text-sm text-white transition hover:bg-white/20">
						<?php esc_html_e( 'Try the Dog Age Calculator', 'thepawmanual' ); ?>
					</a>
				</div>
			</div>
		</div>
	</div>
</section>
