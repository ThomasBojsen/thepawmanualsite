<?php
/**
 * Homepage section: Pet Life Gallery.
 *
 * Decorative/editorial. The species pills are visual only (CLAUDE.md §10).
 *
 * @package thepawmanual
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<section class="mt-16 sm:mt-24">
	<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
		<h2 class="font-playfair text-3xl font-medium tracking-tight animate-on-scroll [animation:fadeSlideIn_1s_ease-out_0.1s_both] sm:text-4xl">
			<?php esc_html_e( 'Pet Life Gallery', 'thepawmanual' ); ?>
		</h2>

		<div class="mt-6 grid gap-6 lg:grid-cols-12">
			<div class="overflow-hidden rounded-3xl bg-neutral-900 animate-on-scroll [animation:fadeSlideIn_1s_ease-out_0.2s_both] lg:col-span-7">
				<img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/gallery-dog.jpg' ) ); ?>" alt="<?php esc_attr_e( 'A dog playing outdoors', 'thepawmanual' ); ?>" class="h-[420px] w-full object-cover" loading="lazy" decoding="async">
			</div>
			<div class="grid gap-6 sm:grid-cols-2 lg:col-span-5 lg:grid-cols-1">
				<div class="overflow-hidden rounded-3xl animate-on-scroll [animation:fadeSlideIn_1s_ease-out_0.3s_both]">
					<img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/gallery-cat.jpg' ) ); ?>" alt="<?php esc_attr_e( 'A cat portrait', 'thepawmanual' ); ?>" class="h-48 w-full object-cover" loading="lazy" decoding="async">
				</div>
				<div class="overflow-hidden rounded-3xl animate-on-scroll [animation:fadeSlideIn_1s_ease-out_0.4s_both]">
					<img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/gallery-bird.jpg' ) ); ?>" alt="<?php esc_attr_e( 'A bird in nature', 'thepawmanual' ); ?>" class="h-48 w-full object-cover" loading="lazy" decoding="async">
				</div>
			</div>
		</div>

		<div class="mt-6 flex flex-wrap gap-2 animate-on-scroll [animation:fadeSlideIn_1s_ease-out_0.1s_both]">
			<span class="rounded-full bg-neutral-900 px-3 py-1.5 font-geist text-xs text-white"><?php esc_html_e( 'Dogs', 'thepawmanual' ); ?></span>
			<span class="rounded-full border border-neutral-200 bg-white px-3 py-1.5 font-geist text-xs text-neutral-600"><?php esc_html_e( 'Cats', 'thepawmanual' ); ?></span>
			<span class="rounded-full border border-neutral-200 bg-white px-3 py-1.5 font-geist text-xs text-neutral-600"><?php esc_html_e( 'Birds', 'thepawmanual' ); ?></span>
			<span class="rounded-full border border-neutral-200 bg-white px-3 py-1.5 font-geist text-xs text-neutral-600"><?php esc_html_e( 'Rabbits', 'thepawmanual' ); ?></span>
			<span class="rounded-full border border-neutral-200 bg-white px-3 py-1.5 font-geist text-xs text-neutral-600"><?php esc_html_e( 'Reptiles', 'thepawmanual' ); ?></span>
		</div>
	</div>
</section>
