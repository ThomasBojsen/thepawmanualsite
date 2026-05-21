<?php
/**
 * The generic fallback template — used whenever no more specific template in
 * the WordPress hierarchy matches (the blog index, archives, search results).
 *
 * @package thepawmanual
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<main id="main" tabindex="-1" class="mx-auto max-w-3xl px-4 py-12 sm:px-6 lg:px-8">
	<?php if ( have_posts() ) : ?>

		<?php if ( is_home() && ! is_front_page() ) : ?>
			<h1 class="mb-10 font-playfair text-3xl font-medium tracking-tight sm:text-4xl">
				<?php single_post_title(); ?>
			</h1>
		<?php endif; ?>

		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<article <?php post_class( 'mb-12' ); ?>>
				<h2 class="font-playfair text-2xl font-medium tracking-tight sm:text-3xl">
					<a href="<?php the_permalink(); ?>" class="transition-colors hover:text-neutral-600">
						<?php the_title(); ?>
					</a>
				</h2>
				<div class="tpm-prose mt-3">
					<?php the_excerpt(); ?>
				</div>
			</article>
			<?php
		endwhile;

		the_posts_pagination(
			array(
				'mid_size'           => 1,
				'screen_reader_text' => __( 'Posts navigation', 'thepawmanual' ),
			)
		);
		?>

	<?php else : ?>

		<h1 class="font-playfair text-3xl font-medium tracking-tight sm:text-4xl">
			<?php esc_html_e( 'Nothing here yet', 'thepawmanual' ); ?>
		</h1>
		<p class="mt-4 font-geist text-neutral-600">
			<?php esc_html_e( 'No content has been published yet. Please check back soon.', 'thepawmanual' ); ?>
		</p>

	<?php endif; ?>
</main>
<?php
get_footer();
