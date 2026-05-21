<?php
/**
 * Homepage section: hero.
 *
 * The giant wordmark, a feature image with category pills, and the
 * "Featured Tool" card promoting the Dog Age Calculator.
 *
 * @package thepawmanual
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<section class="relative">
	<div class="mx-auto max-w-7xl px-4 pt-10 sm:px-6 sm:pt-14 lg:px-8">

		<h1 class="font-playfair text-[clamp(2rem,9vw,8rem)] font-medium leading-[0.9] tracking-tight text-neutral-900 [animation:fadeSlideIn_1s_ease-out_0.1s_both]">
			<?php bloginfo( 'name' ); ?>
		</h1>

		<div class="mt-8 grid gap-8 lg:grid-cols-12">

			<?php // Feature image with the "Find a Tool" button and category pills. ?>
			<div class="relative overflow-hidden rounded-3xl [animation:fadeSlideIn_1s_ease-out_0.2s_both] lg:col-span-8">
				<img
					src="<?php echo esc_url( get_theme_file_uri( 'assets/images/hero-dog.jpg' ) ); ?>"
					alt="<?php esc_attr_e( 'A happy dog running outdoors', 'thepawmanual' ); ?>"
					class="h-[300px] w-full object-cover sm:h-[440px]"
					fetchpriority="high"
					decoding="async"
				>
				<div class="absolute bottom-4 left-4">
					<a href="#tools" class="hidden items-center gap-2 rounded-full bg-neutral-900 px-4 py-2 font-geist text-sm text-white transition hover:bg-neutral-800 sm:inline-flex">
						<?php esc_html_e( 'Find a Tool', 'thepawmanual' ); ?>
						<?php tpm_icon( 'arrow-right', 'h-4 w-4' ); ?>
					</a>
				</div>
				<div class="absolute bottom-4 left-1/2 -translate-x-1/2 sm:left-auto sm:right-4 sm:translate-x-0">
					<div class="flex items-center gap-1 rounded-full bg-white/80 p-2 shadow backdrop-blur">
						<span class="rounded-full bg-neutral-900 px-3 py-1.5 font-geist text-xs text-white"><?php esc_html_e( 'Age', 'thepawmanual' ); ?></span>
						<span class="rounded-full px-3 py-1.5 font-geist text-xs text-neutral-600"><?php esc_html_e( 'Nutrition', 'thepawmanual' ); ?></span>
						<span class="rounded-full px-3 py-1.5 font-geist text-xs text-neutral-600"><?php esc_html_e( 'Health', 'thepawmanual' ); ?></span>
						<span class="rounded-full px-3 py-1.5 font-geist text-xs text-neutral-600"><?php esc_html_e( 'Puppies', 'thepawmanual' ); ?></span>
					</div>
				</div>
			</div>

			<?php // Featured-tool card. ?>
			<div class="flex flex-col rounded-3xl border border-neutral-200 bg-white p-5 [animation:fadeSlideIn_1s_ease-out_0.3s_both] sm:p-6 lg:col-span-4">
				<div>
					<h2 class="font-playfair text-2xl font-medium tracking-tight sm:text-3xl">
						<?php esc_html_e( 'Featured Tool', 'thepawmanual' ); ?>
					</h2>
					<p class="mt-2 font-geist text-sm text-neutral-600">
						<?php esc_html_e( "The Dog Age Calculator converts your dog's age into human years using a breed-size-aware veterinary model.", 'thepawmanual' ); ?>
					</p>
				</div>
				<div class="mt-5 flex flex-grow flex-col items-center justify-center gap-3 rounded-2xl border border-neutral-100 bg-neutral-50 p-6 text-center">
					<div class="flex h-12 w-12 items-center justify-center rounded-full border border-neutral-200 bg-white text-neutral-900 shadow-sm">
						<?php tpm_icon( 'calculator', 'h-6 w-6' ); ?>
					</div>
					<div>
						<p class="font-geist text-sm font-semibold text-neutral-900"><?php esc_html_e( 'Breed-aware result', 'thepawmanual' ); ?></p>
						<p class="mt-1 font-geist text-xs text-neutral-500"><?php esc_html_e( 'Based on veterinary guidelines', 'thepawmanual' ); ?></p>
					</div>
				</div>
				<div class="mt-4">
					<a href="<?php echo esc_url( home_url( '/dog-age-calculator/' ) ); ?>" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-neutral-900 px-4 py-2.5 font-geist text-sm text-white transition hover:bg-neutral-800">
						<?php esc_html_e( 'Open Calculator', 'thepawmanual' ); ?>
						<?php tpm_icon( 'arrow-right', 'h-4 w-4' ); ?>
					</a>
				</div>
			</div>

		</div>
	</div>
</section>
