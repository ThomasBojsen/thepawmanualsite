<?php
/**
 * Homepage section: statement band.
 *
 * The Aura template's e-commerce promo line ("Use code FUREVER…") has been
 * dropped and the copy rewritten to be tool/content-oriented (CLAUDE.md §10).
 *
 * @package thepawmanual
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$tiles = array(
	array( 'file' => 'statement-1.jpg', 'alt' => __( 'A content dog', 'thepawmanual' ) ),
	array( 'file' => 'statement-2.jpg', 'alt' => __( 'A relaxed cat', 'thepawmanual' ) ),
	array( 'file' => 'statement-3.jpg', 'alt' => __( 'A pet bird', 'thepawmanual' ) ),
	array( 'file' => 'statement-4.jpg', 'alt' => __( 'A small pet', 'thepawmanual' ) ),
);
?>
<section class="mt-20 border-t-4 border-brand-600 bg-neutral-900 text-white sm:mt-28">
	<div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 sm:py-24 lg:px-8">
		<h2 class="font-playfair text-4xl font-medium tracking-tight animate-on-scroll [animation:fadeSlideIn_1s_ease-out_0.1s_both] sm:text-6xl">
			<?php esc_html_e( 'Happy Pets, Happy Life', 'thepawmanual' ); ?>
		</h2>
		<p class="mt-4 max-w-xl font-geist text-sm leading-relaxed text-neutral-300 animate-on-scroll [animation:fadeSlideIn_1s_ease-out_0.2s_both]">
			<?php esc_html_e( 'Practical, science-backed tools that make it easier to care for your pet at every stage of life, free to use, for every kind of companion.', 'thepawmanual' ); ?>
		</p>
		<div class="mt-10 grid grid-cols-2 gap-6 sm:grid-cols-4">
			<?php foreach ( $tiles as $tile ) : ?>
				<div class="overflow-hidden rounded-2xl animate-on-scroll [animation:fadeSlideIn_0.8s_ease-out_both]">
					<img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/' . $tile['file'] ) ); ?>" alt="<?php echo esc_attr( $tile['alt'] ); ?>" class="h-40 w-full object-cover" loading="lazy" decoding="async">
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
