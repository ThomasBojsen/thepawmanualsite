<?php
/**
 * "Related Reading" — sibling navigation at the foot of every article.
 *
 * Lists the other pages in the same section (excluding the current one) plus
 * a link up to the parent hub, with descriptive anchors (page titles), never
 * "click here" — this is the other half of the internal-linking layer: the
 * hub links down to every child (child-grid.php), the article links across
 * to its siblings and back up to the hub.
 *
 * Expects $args['siblings'] — WP_Post[], e.g. from tpm_get_sibling_pages().
 * Expects $args['parent'] — WP_Post|null, the immediate parent hub.
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
?>
<section class="tpm-related-reading mt-16 border-t border-neutral-200 pt-10" aria-labelledby="tpm-related-heading">
	<h2 id="tpm-related-heading" class="font-playfair text-2xl font-medium text-neutral-900 sm:text-3xl">
		<?php esc_html_e( 'Related Reading', 'thepawmanual' ); ?>
	</h2>

	<?php if ( ! empty( $siblings ) ) : ?>
		<ul class="mt-6 grid grid-cols-1 gap-3 sm:grid-cols-2">
			<?php foreach ( $siblings as $sibling ) : ?>
				<li>
					<a
						href="<?php echo esc_url( get_permalink( $sibling ) ); ?>"
						class="flex items-center gap-2 rounded-xl border border-neutral-200 bg-white px-4 py-3 font-geist text-sm text-neutral-800 transition hover:border-neutral-300 hover:text-neutral-900"
					>
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 shrink-0 text-neutral-400" aria-hidden="true" focusable="false"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
						<span><?php echo esc_html( get_the_title( $sibling ) ); ?></span>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>

	<?php if ( $parent ) : ?>
		<p class="mt-6 font-geist text-sm">
			<a href="<?php echo esc_url( get_permalink( $parent ) ); ?>" class="inline-flex items-center gap-1.5 font-medium text-neutral-900 underline underline-offset-2 hover:no-underline">
				<?php
				printf(
					/* translators: %s: the parent hub page's title. */
					esc_html__( 'See all pages in %s', 'thepawmanual' ),
					esc_html( get_the_title( $parent ) )
				);
				?>
			</a>
		</p>
	<?php endif; ?>
</section>
