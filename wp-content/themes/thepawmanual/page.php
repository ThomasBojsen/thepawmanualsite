<?php
/**
 * The template for standard Pages.
 *
 * With ~110 pages arriving as a deep hierarchy (/dogs/health/why-is-my-dog-
 * itching/), this template branches on whether the Page has children:
 *
 * - Hub (has child Pages): breadcrumb → title → intro content → a
 *   child-page card grid ("In This Section"). The cats.com hub pattern —
 *   hubs are the PageRank distributors the content cluster was written for.
 * - Article (no children): breadcrumb → title → last-updated date →
 *   optional sticky table of contents → content → related-reading
 *   (siblings + a link up to the hub).
 *
 * Calculator pages also use this template — their editor content is just the
 * [pawmanual_calculator] shortcode plus SEO intro copy, which has no H2s, so
 * the TOC simply doesn't trigger for them; they render as a plain article.
 *
 * @package thepawmanual
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<main id="main" tabindex="-1" class="mx-auto max-w-5xl px-4 py-12 sm:px-6 lg:px-8">
	<?php
	while ( have_posts() ) :
		the_post();

		$post_id      = get_the_ID();
		$current_post = get_post( $post_id );
		$children     = tpm_get_child_pages( $post_id );
		$is_hub       = ! empty( $children );
		$parent_id    = $current_post ? (int) $current_post->post_parent : 0;
		$parent_post  = $parent_id ? get_post( $parent_id ) : null;
		$callout      = tpm_get_emergency_callout( $post_id );

		/*
		 * Run the TOC/heading-id pass against the fully filtered content —
		 * shortcodes executed, wpautop applied — so the H2s found here match
		 * what actually renders (see tpm_inject_heading_ids() docblock).
		 * This replaces the plain the_content() call below; nothing else in
		 * the pipeline changes.
		 */
		$rendered_content = apply_filters( 'the_content', get_the_content() );
		$toc               = tpm_inject_heading_ids( $rendered_content );
		$show_toc          = ! $is_hub && count( $toc['items'] ) >= 3;
		?>
		<article <?php post_class(); ?>>
			<header class="[animation:fadeSlideIn_0.8s_ease-out_0s_both]">
				<?php tpm_breadcrumbs(); ?>

				<h1 class="mt-4 font-playfair text-3xl font-medium tracking-tight sm:text-5xl">
					<?php the_title(); ?>
				</h1>

				<?php if ( ! $is_hub ) : ?>
					<?php get_template_part( 'template-parts/content/last-updated', null, array( 'post_id' => $post_id ) ); ?>
				<?php endif; ?>
			</header>

			<?php get_template_part( 'template-parts/content/emergency-callout', null, array( 'callout' => $callout ) ); ?>

			<?php if ( $show_toc ) : ?>
				<div class="mt-8 lg:grid lg:grid-cols-[minmax(0,1fr)_280px] lg:items-start lg:gap-10">
					<?php // First in source order (task brief: TOC before content) — the grid placement is what moves it beside the text on lg:, while mobile simply stacks it above. ?>
					<aside class="mb-8 lg:order-2 lg:col-start-2 lg:mb-0">
						<?php get_template_part( 'template-parts/content/toc', null, array( 'items' => $toc['items'] ) ); ?>
					</aside>
					<div class="tpm-prose lg:order-1 lg:col-start-1">
						<?php
						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- already-filtered post content (the_content pipeline), DOM-parsed and re-serialized; same trust level the_content() itself has.
						echo $toc['content'];
						?>
					</div>
				</div>
			<?php else : ?>
				<?php // No TOC sidebar to share the row with, so the prose column keeps its own readable measure — true for both hub intros and short articles. ?>
				<div class="tpm-prose mx-auto mt-8 max-w-3xl">
					<?php
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- see note above.
					echo $toc['content'];
					?>
				</div>
			<?php endif; ?>

			<?php
			wp_link_pages(
				array(
					'before' => '<nav class="mt-8 flex flex-wrap gap-2 font-geist text-sm" aria-label="' . esc_attr__( 'Page', 'thepawmanual' ) . '">',
					'after'  => '</nav>',
				)
			);
			?>

			<?php if ( $is_hub ) : ?>
				<?php get_template_part( 'template-parts/content/child-grid', null, array( 'children' => $children ) ); ?>
			<?php else : ?>
				<?php
				get_template_part(
					'template-parts/content/related-reading',
					null,
					array(
						'siblings' => tpm_get_sibling_pages( $post_id ),
						'parent'   => $parent_post,
					)
				);
				?>
			<?php endif; ?>
		</article>
		<?php
	endwhile;
	?>
</main>
<?php
get_footer();
