<?php
/**
 * The search-results template.
 *
 * Did not exist before this file: every search fell through to index.php,
 * which has no query-specific heading, no result count, and no empty state
 * — a visitor searching this site landed on what looked like a generic
 * listing page with no confirmation their query even ran. inc/search.php
 * (pre_get_posts) is what narrows the underlying query to the 'page' post
 * type and caps it at 10 results; this file only renders whatever that
 * query returns.
 *
 * Card language matches template-parts/content/child-grid.php's "In This
 * Section" cards deliberately, down to the excerpt strategy: get_the_excerpt()
 * is never called here. With no manual excerpt set, it falls through to the
 * full `the_content` filter chain — wptexturize, wpautop, do_shortcode, the
 * lot — run over each result's ENTIRE body once per card, on every search.
 * do_shortcode() executing every registered shortcode callback is the
 * expensive and risky part; wp_strip_all_tags( strip_shortcodes( ... ) ) +
 * wp_trim_words() gets the same "first N words + an ellipsis" shape without
 * it, and — because wp_strip_all_tags() drops a <script> element's text
 * content along with the tag, unlike plain strip_tags() — a result's FAQ
 * JSON-LD block can never leak into the card as visible text either.
 *
 * @package thepawmanual
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$tpm_search_query = get_search_query( false );
?>
<main id="main" tabindex="-1" class="mx-auto max-w-5xl px-4 py-12 sm:px-6 lg:px-8">

	<header>
		<h1 class="font-playfair text-3xl font-medium tracking-tight sm:text-5xl">
			<?php
			printf(
				/* translators: %s: the search query the visitor entered. */
				esc_html__( 'Search results for "%s"', 'thepawmanual' ),
				esc_html( $tpm_search_query )
			);
			?>
		</h1>

		<?php if ( have_posts() ) : ?>
			<p class="mt-2 font-geist text-sm text-neutral-500">
				<?php
				global $wp_query;
				$tpm_result_count = (int) $wp_query->found_posts;
				printf(
					/* translators: %s: number of results found, already number-formatted. */
					esc_html(
						_n( '%s result found.', '%s results found.', $tpm_result_count, 'thepawmanual' )
					),
					esc_html( number_format_i18n( $tpm_result_count ) )
				);
				?>
			</p>
		<?php endif; ?>

		<?php // The form so the query can be refined without going back to the header panel. Suffixed: the header panel's own canonical #tpm-search-field is already on this page via get_header(). ?>
		<div class="mt-6 max-w-md">
			<?php get_search_form( array( 'id_suffix' => 'page' ) ); ?>
		</div>
	</header>

	<?php if ( have_posts() ) : ?>

		<div class="mt-10 grid grid-cols-1 gap-5 sm:grid-cols-2">
			<?php
			while ( have_posts() ) :
				the_post();

				$tpm_result_post = get_post();
				$tpm_parent_id   = $tpm_result_post ? (int) $tpm_result_post->post_parent : 0;

				// The short H1, not the full "hook + subtitle" title stored in
				// the_title() — same reasoning as page.php's <h1>, just applied
				// to a card instead of an article header. Guarded: article-meta.php
				// is another worker's file on this same pass and may not have
				// landed yet, so a card degrades to the full title rather than
				// fataling.
				$tpm_result_title = get_the_title();
				if ( function_exists( 'tpm_split_title' ) ) {
					$tpm_split = tpm_split_title( $tpm_result_title );
					if ( ! empty( $tpm_split['h1'] ) ) {
						$tpm_result_title = $tpm_split['h1'];
					}
				}

				// See this file's top docblock for why this is strip_shortcodes()
				// + wp_strip_all_tags() + wp_trim_words() and never get_the_excerpt().
				// A literal ellipsis character (not the '&hellip;' entity) is used
				// as the trim marker so esc_html() below can escape the whole
				// string in one pass without turning it into visible "&amp;hellip;".
				$tpm_excerpt = $tpm_result_post ? wp_trim_words(
					wp_strip_all_tags( strip_shortcodes( $tpm_result_post->post_content ) ),
					30,
					' …'
				) : '';
				?>
				<a href="<?php the_permalink(); ?>" class="tpm-card group flex flex-col rounded-2xl border border-neutral-200 bg-white p-6 transition hover:border-neutral-300 hover:shadow-sm">
					<?php if ( $tpm_parent_id ) : ?>
						<span class="inline-flex w-fit items-center rounded-full bg-brand-50 px-2.5 py-1 font-geist text-xs font-medium text-brand-700">
							<?php echo esc_html( get_the_title( $tpm_parent_id ) ); ?>
						</span>
					<?php endif; ?>

					<h2 class="mt-3 font-geist text-base font-semibold tracking-tight text-neutral-900 group-hover:underline">
						<?php echo esc_html( $tpm_result_title ); ?>
					</h2>

					<?php if ( $tpm_excerpt ) : ?>
						<p class="mt-2 flex-grow font-geist text-sm text-neutral-600">
							<?php echo esc_html( $tpm_excerpt ); ?>
						</p>
					<?php endif; ?>

					<span class="mt-4 inline-flex items-center gap-1.5 font-geist text-sm font-medium text-neutral-900">
						<?php esc_html_e( 'Read more', 'thepawmanual' ); ?>
						<?php tpm_icon( 'arrow-right', 'h-4 w-4 transition-transform group-hover:translate-x-0.5' ); ?>
					</span>
				</a>
				<?php
			endwhile;
			?>
		</div>

		<div class="mt-10">
			<?php
			the_posts_pagination(
				array(
					'mid_size'  => 1,
					'prev_text' => esc_html__( 'Previous', 'thepawmanual' ),
					'next_text' => esc_html__( 'Next', 'thepawmanual' ),
				)
			);
			?>
		</div>

	<?php else : ?>

		<?php
		// A real empty state, not a blank page: confirms the query ran, and
		// hands a visitor whose search came up empty a working way back into
		// the site instead of a dead end. Every hub link is resolved through
		// tpm_get_published_page_url() (inc/internal-links.php) rather than
		// hardcoded — CLAUDE.md §13's rule applies here exactly as it does in
		// footer.php: a hub that hasn't published yet must render as nothing,
		// never as a link to a 404.
		$tpm_empty_state_hubs = array(
			'dogs'       => __( 'Dogs', 'thepawmanual' ),
			'cats'       => __( 'Cats', 'thepawmanual' ),
			'pet-guides' => __( 'Pet Guides', 'thepawmanual' ),
			'tools'      => __( 'Tools', 'thepawmanual' ),
		);
		?>
		<div class="mt-10 rounded-2xl border border-neutral-200 bg-white p-8 text-center sm:p-10">
			<p class="font-geist text-base text-neutral-700">
				<?php
				printf(
					/* translators: %s: the search query the visitor entered. */
					esc_html__( 'No results for "%s".', 'thepawmanual' ),
					esc_html( $tpm_search_query )
				);
				?>
			</p>
			<p class="mt-2 font-geist text-sm text-neutral-500">
				<?php esc_html_e( 'Try a different search, or start from one of the main sections:', 'thepawmanual' ); ?>
			</p>

			<?php
			$tpm_empty_state_links = array();
			foreach ( $tpm_empty_state_hubs as $tpm_hub_path => $tpm_hub_label ) {
				$tpm_hub_url = tpm_get_published_page_url( $tpm_hub_path );
				if ( $tpm_hub_url ) {
					$tpm_empty_state_links[ $tpm_hub_url ] = $tpm_hub_label;
				}
			}
			?>
			<?php if ( $tpm_empty_state_links ) : ?>
				<ul class="mt-5 flex flex-wrap items-center justify-center gap-2">
					<?php foreach ( $tpm_empty_state_links as $tpm_hub_url => $tpm_hub_label ) : ?>
						<li>
							<a href="<?php echo esc_url( $tpm_hub_url ); ?>" class="inline-flex items-center rounded-full border border-neutral-200 bg-white px-4 py-2 font-geist text-sm text-neutral-700 transition hover:border-neutral-300 hover:text-neutral-900">
								<?php echo esc_html( $tpm_hub_label ); ?>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>

			<?php // Suffixed for the same reason as the "refine your search" field above: the header panel's canonical #tpm-search-field is already in the DOM on this page. ?>
			<div class="mx-auto mt-8 max-w-md">
				<?php get_search_form( array( 'id_suffix' => 'empty' ) ); ?>
			</div>
		</div>

	<?php endif; ?>

</main>
<?php
get_footer();
