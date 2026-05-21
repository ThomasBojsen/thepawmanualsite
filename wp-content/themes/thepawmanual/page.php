<?php
/**
 * The template for standard Pages.
 *
 * Calculator pages use this template: their editor content is the
 * [pawmanual_calculator] shortcode plus the SEO intro copy.
 *
 * @package thepawmanual
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<main id="main" tabindex="-1" class="mx-auto max-w-3xl px-4 py-12 sm:px-6 lg:px-8">
	<?php
	while ( have_posts() ) :
		the_post();
		?>
		<article <?php post_class(); ?>>
			<header class="[animation:fadeSlideIn_0.8s_ease-out_0s_both]">
				<h1 class="font-playfair text-3xl font-medium tracking-tight sm:text-5xl">
					<?php the_title(); ?>
				</h1>
			</header>

			<div class="tpm-prose mt-6">
				<?php the_content(); ?>
			</div>

			<?php
			wp_link_pages(
				array(
					'before' => '<nav class="mt-8 flex flex-wrap gap-2 font-geist text-sm" aria-label="' . esc_attr__( 'Page', 'thepawmanual' ) . '">',
					'after'  => '</nav>',
				)
			);
			?>
		</article>
		<?php
	endwhile;
	?>
</main>
<?php
get_footer();
