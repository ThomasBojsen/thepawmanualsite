<?php
/**
 * The homepage template — the site's front page.
 *
 * Assembles the Aura homepage from the section partials in
 * template-parts/home/. WordPress uses this template for the front page
 * automatically, whether the front page is set to a static page or to the
 * latest posts.
 *
 * @package thepawmanual
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<main id="main" tabindex="-1">

	<?php get_template_part( 'template-parts/home/hero' ); ?>

	<?php get_template_part( 'template-parts/home/filter-bar' ); ?>

	<?php get_template_part( 'template-parts/home/tools-grid' ); ?>

	<?php
	/*
	 * The homepage's indexable text (SEO-STRATEGY.md §1 item 0.4): the four
	 * hub summaries, the trust/E-E-A-T block, and the FAQ, carried from
	 * content/home.md. Placed directly below the tools grid, matching that
	 * file's own implementation notes.
	 */
	get_template_part( 'template-parts/home/seo-content' );
	?>

	<section class="mt-16 sm:mt-24">
		<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
			<?php get_template_part( 'template-parts/ad-slot', null, array( 'label' => __( 'Advertisement', 'thepawmanual' ) ) ); ?>
		</div>
	</section>

	<?php get_template_part( 'template-parts/home/gallery' ); ?>

	<?php get_template_part( 'template-parts/home/statement' ); ?>

	<?php get_template_part( 'template-parts/home/promo' ); ?>

</main>
<?php
get_footer();
