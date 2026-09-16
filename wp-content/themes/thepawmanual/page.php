<?php
/**
 * The template for standard Pages.
 *
 * With ~110 pages arriving as a deep hierarchy (/dogs/health/why-is-my-dog-
 * itching/), this template branches on whether the Page has children:
 *
 * - Hub (has child Pages): breadcrumb → article-header (eyebrow, title,
 *   optional key takeaway) → intro content → a child-page card grid ("In
 *   This Section"). The cats.com hub pattern — hubs are the PageRank
 *   distributors the content cluster was written for.
 * - Article (no children): breadcrumb → article-header (eyebrow, date, read
 *   time, split title + deck, key takeaway) → optional left-rail table of
 *   contents → content (lead paragraph already lifted into the takeaway,
 *   FAQ already an accordion, contextual tool CTA already inserted by
 *   content-blocks.php) → related-reading (siblings + a link up to the hub).
 *
 * User requirements 2 and 3 (CLAUDE.md §13) apply to BOTH branches: every
 * page — hub or article — gets the tools strip and a bottom related-links
 * block, not just articles. Only the child-grid ("In This Section") stays
 * hub-only, since an article has no children to list.
 *
 * Calculator pages also use this template. Their editor content is the
 * [pawmanual_calculator] shortcode PLUS several H2s of ordinary SEO intro
 * copy — content/tools/dog-age-calculator.md has 4 PUBLISHED top-level H2s
 * and content/tools/cat-age-calculator.md has 3 (5 and 4 in the markdown
 * respectively; one H2 in each is "## Notes (not page copy)", which
 * tools/wxr/generate.js never publishes — corrected 2026-09-14, round 4:
 * the previous count here was literally true of the markdown but not of
 * what actually ships) — so the TOC DOES trigger for them, the same as any
 * other article (corrected 2026-09-14, review round 2, Finding 5: this
 * docblock previously claimed calculator pages "have no H2s," which was
 * false and reproducible against the live pages). Worth flagging because
 * the cat page's 3 published H2s sit EXACTLY on the `>= 3` threshold below
 * — a future edit that removes one of its H2s (down to 2) makes its TOC
 * disappear silently, with nothing here to warn that editor it's on the
 * line. The one thing that does NOT belong in that TOC is the calculator
 * widget's own internal `<h2 class="pm-calc__title">` — its output is
 * do_shortcode()'d into $rendered_content below like any other shortcode,
 * so it sits in the same tree tpm_inject_heading_ids() walks, and that pass
 * is responsible for excluding it so the TOC lists only the page's own
 * authored sections, not the widget's own title.
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
		 * User requirement 2's bottom related-links block, for BOTH branches
		 * below. tpm_get_sibling_pages() only ever returns something for a
		 * Page that HAS a parent (its own guard) — ten of the eighteen live
		 * URLs don't: the four content hubs themselves and the six top-level
		 * trust pages. Those instead get the OTHER top-level content hubs via
		 * tpm_get_top_level_hub_siblings(), so /dogs/ links across to /cats/,
		 * /pet-guides/ and /tools/, and a page like /terms/ ends with a real
		 * way back into the site instead of nothing.
		 */
		$related_siblings = $parent_id
			? tpm_get_sibling_pages( $post_id )
			: tpm_get_top_level_hub_siblings( $post_id );

		/*
		 * Run the TOC/heading-id pass against the fully filtered content —
		 * shortcodes executed, wpautop applied — so the H2s found here match
		 * what actually renders (see tpm_inject_heading_ids() docblock).
		 * This replaces the plain the_content() call below; nothing else in
		 * the pipeline changes.
		 */
		$rendered_content = apply_filters( 'the_content', get_the_content() );

		/*
		 * ORDER IS LOAD-BEARING: content-blocks.php's takeaway/FAQ/CTA pass
		 * MUST run before the heading-id/TOC pass, not just conventionally
		 * before it. tpm_prepare_article_content() lifts the lead paragraph
		 * out (it must be gone before the TOC walks the tree) and rebuilds
		 * the FAQ's H3/answer pairs into a <details> accordion, and its
		 * inline-tool-CTA placement — immediately before the SECOND top-level
		 * H2 — is only well-defined against that ORIGINAL flat section
		 * structure. tpm_inject_heading_ids() only stamps ids on and collects
		 * whatever H2s are left in the tree it's handed, so it has to see the
		 * tree AFTER this pass, not before. See tpm_prepare_article_content()'s
		 * own docblock (inc/content-blocks.php) for the full reasoning.
		 */
		$prepared = tpm_prepare_article_content( $rendered_content, $post_id, array( 'inline_tool' => ! $is_hub ) );
		$toc      = tpm_inject_heading_ids( $prepared['content'] );
		$show_toc = ! $is_hub && count( $toc['items'] ) >= 3;

		/*
		 * The post-aware H1/deck split (2026-09-14, review round 2,
		 * Finding 6; wired through to article-header.php in round 3,
		 * Finding 1) — resolved here, once, and handed down as a
		 * template-part arg, so the per-page override map
		 * (tpm_get_title_part_overrides(), inc/article-meta.php) has one
		 * call site to reach through rather than one per template that
		 * ever renders a Page's own H1. Guarded with function_exists()
		 * for the same reason every other cross-file call in this pipeline
		 * is (see the file docblock's "why the pieces degrade" note,
		 * CLAUDE.md §13): a missing inc/article-meta.php degrades to the
		 * raw, unsplit title rather than a fatal error.
		 *
		 * template-parts/content/article-header.php — the template that
		 * actually renders the `<h1>` — reads this value via
		 * $args['title_parts'] below (falling back to resolving it itself if
		 * it's ever called without it), so the /tools/ override takes visible
		 * effect end to end.
		 */
		$title_parts = function_exists( 'tpm_get_title_parts' )
			? tpm_get_title_parts( $post_id )
			: array(
				'h1'   => get_the_title( $post_id ),
				'deck' => '',
			);
		?>
		<article <?php post_class(); ?>>
			<header class="[animation:fadeSlideIn_0.8s_ease-out_0s_both]">
				<?php tpm_breadcrumbs(); ?>

				<?php
				get_template_part(
					'template-parts/content/article-header',
					null,
					array(
						'post_id'     => $post_id,
						'is_hub'      => $is_hub,
						'takeaway'    => $prepared['takeaway'],
						'title_parts' => $title_parts,
					)
				);
				?>
			</header>

			<?php get_template_part( 'template-parts/content/emergency-callout', null, array( 'callout' => $callout ) ); ?>

			<?php if ( $show_toc ) : ?>
				<div class="mt-8 lg:grid lg:grid-cols-[260px_minmax(0,1fr)] lg:items-start lg:gap-10">
					<?php // V3 layout: TOC is a LEFT rail. It's first in source order, and the grid's default flow already places the first item in column 1 — so this is just source order, with no lg:order-* swap needed for either breakpoint. Below lg:, the same source order stacks the TOC above the content, which is the mobile behaviour the brief asks for. ?>
					<aside class="mb-8 lg:mb-0">
						<?php get_template_part( 'template-parts/content/toc', null, array( 'items' => $toc['items'] ) ); ?>
					</aside>
					<div class="tpm-prose">
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

			<?php
			// User requirement 3: every landing/hub page AND every article
			// links to the tools — this strip ships on both branches, so a
			// visitor is never more than one section away from the two
			// working calculators regardless of where they landed.
			get_template_part( 'template-parts/content/tools-strip', null, array( 'post_id' => $post_id ) );
			?>

			<?php if ( $is_hub ) : ?>
				<?php get_template_part( 'template-parts/content/child-grid', null, array( 'children' => $children ) ); ?>
				<?php
				// User requirement 2: landing/hub pages get the bottom
				// related-links block too, not just articles. $related_siblings
				// (computed above) is already the correct set for a hub either
				// way: a NESTED hub (has a $parent_post) gets its own sibling
				// hubs from tpm_get_sibling_pages(); a TOP-LEVEL hub — /dogs/,
				// /cats/, /pet-guides/, /tools/ — has no parent at all, so it
				// falls through to tpm_get_top_level_hub_siblings() instead.
				get_template_part(
					'template-parts/content/related-reading',
					null,
					array(
						'siblings' => $related_siblings,
						'parent'   => $parent_post,
						'heading'  => __( 'Explore more', 'thepawmanual' ),
					)
				);
				?>
			<?php else : ?>
				<?php
				get_template_part(
					'template-parts/content/related-reading',
					null,
					array(
						'siblings' => $related_siblings,
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
