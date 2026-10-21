<?php
/**
 * "Related Reading" — sibling navigation at the foot of every article, and
 * (via the optional args below) the hub-to-hub version of the same block.
 *
 * Lists the other pages in the same section (excluding the current one) as
 * a card grid, plus a link up to the parent hub — this is the other half of
 * the internal-linking layer: the hub links down to every child
 * (child-grid.php), the article links across to its siblings and back up to
 * the hub. Rewritten 2026-09-14 (user requirement 2) from a plain link list
 * to the V3 "Related Reading" card grid: each card carries a category chip
 * (the card's own parent hub's SHORT title — see tpm_split_title()), the
 * linked page's own SHORT title, and a "Read guide" line. Since 2026-10-21
 * each card also opens with the linked page's Featured Image, or a paw
 * placeholder (template-parts/content/card-image.php). Titles go through
 * tpm_split_title() rather than the raw post title for the same reason user
 * requirement 5 exists at all: several page titles are one over-long H1
 * ("Hook: Subtitle clause"), and the full string wrecks a card's layout as
 * badly as it wrecks the page's own <h1>.
 *
 * tpm_split_title() lives in inc/article-meta.php, a sibling worker's file —
 * every call below is function_exists()-guarded so this template degrades to
 * the untrimmed post title (long, but still correct and still linked) rather
 * than fataling if that file hasn't loaded for any reason.
 *
 * Expects $args['siblings'] — WP_Post[], e.g. from tpm_get_sibling_pages().
 * Expects $args['parent'] — WP_Post|null, the immediate parent hub; also the
 * source of every card's category chip, since every sibling here shares that
 * one parent.
 * Optional $args['heading'] — defaults to "Related Reading"; a hub page
 * reusing this part for hub-to-hub links can pass e.g. "Explore more".
 * Optional $args['cta_label'] — full replacement text for the up-link
 * (default: "See all pages in {hub}"), for when "{hub}" doesn't read
 * naturally for the caller's context.
 *
 * @package thepawmanual
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$siblings = isset( $args['siblings'] ) && is_array( $args['siblings'] ) ? $args['siblings'] : array();
$parent   = isset( $args['parent'] ) && $args['parent'] instanceof WP_Post ? $args['parent'] : null;

if ( empty( $siblings ) && ! $parent ) {
	return;
}

$heading = ! empty( $args['heading'] ) ? $args['heading'] : __( 'Related Reading', 'thepawmanual' );

// Every card's chip is the SAME value — the parent hub these siblings share
// — computed once here rather than once per card.
$chip_label = '';
if ( $parent && function_exists( 'tpm_split_title' ) ) {
	$parent_split = tpm_split_title( get_the_title( $parent ) );
	$chip_label   = ! empty( $parent_split['h1'] ) ? $parent_split['h1'] : get_the_title( $parent );
}

$up_link_text = '';
if ( $parent ) {
	$up_link_text = ! empty( $args['cta_label'] ) ? $args['cta_label'] : sprintf(
		/* translators: %s: the parent hub page's title. */
		__( 'See all pages in %s', 'thepawmanual' ),
		get_the_title( $parent )
	);
}
?>
<section class="tpm-related-reading mt-16 border-t border-neutral-200 pt-10" aria-labelledby="tpm-related-heading">
	<div class="flex flex-wrap items-baseline justify-between gap-x-6 gap-y-2">
		<h2 id="tpm-related-heading" class="font-playfair text-2xl font-medium text-neutral-900 sm:text-3xl">
			<?php echo esc_html( $heading ); ?>
		</h2>

		<?php if ( $parent ) : ?>
			<a
				href="<?php echo esc_url( get_permalink( $parent ) ); ?>"
				class="inline-flex items-center gap-1.5 font-geist text-sm font-medium text-brand-700 underline-offset-2 hover:text-brand-600 hover:underline"
			>
				<?php echo esc_html( $up_link_text ); ?>
				<?php tpm_icon( 'arrow-right', 'h-3.5 w-3.5' ); ?>
			</a>
		<?php endif; ?>
	</div>

	<?php if ( ! empty( $siblings ) ) : ?>
		<?php
		if ( function_exists( 'tpm_prime_card_images' ) ) {
			tpm_prime_card_images( $siblings );
		}
		?>
		<div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
			<?php foreach ( $siblings as $sibling ) : ?>
				<?php
				$sibling_title = get_the_title( $sibling );
				if ( function_exists( 'tpm_split_title' ) ) {
					$sibling_split = tpm_split_title( $sibling_title );
					$sibling_title = ! empty( $sibling_split['h1'] ) ? $sibling_split['h1'] : $sibling_title;
				}
				?>
				<a
					href="<?php echo esc_url( get_permalink( $sibling ) ); ?>"
					class="tpm-card group flex flex-row items-start gap-4 rounded-2xl border border-neutral-200 bg-white p-5 transition hover:border-brand-200 hover:shadow-sm sm:flex-col sm:items-stretch sm:gap-0"
				>
					<?php get_template_part( 'template-parts/content/card-image', null, array( 'post_id' => $sibling->ID ) ); ?>
					<div class="flex min-w-0 flex-1 flex-col sm:mt-4">
						<?php if ( $chip_label ) : ?>
							<span class="inline-flex w-fit items-center rounded-full bg-accent-50 px-2.5 py-1 font-geist text-[11px] font-semibold uppercase tracking-wide text-accent-700">
								<?php echo esc_html( $chip_label ); ?>
							</span>
						<?php endif; ?>
						<h3 class="mt-3 font-geist text-base font-semibold tracking-tight text-neutral-900 group-hover:underline">
							<?php echo esc_html( $sibling_title ); ?>
						</h3>
						<span class="mt-4 inline-flex items-center gap-1.5 font-geist text-sm font-medium text-brand-700">
							<?php esc_html_e( 'Read guide', 'thepawmanual' ); ?>
							<?php tpm_icon( 'arrow-right', 'h-4 w-4 transition-transform group-hover:translate-x-0.5' ); ?>
						</span>
					</div>
				</a>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</section>
