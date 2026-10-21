<?php
/**
 * The picture at the top of a page card: the linked page's Featured Image,
 * or a paw placeholder when it has none yet.
 *
 * Used by related-reading.php and child-grid.php so both card grids look the
 * same. The placeholder keeps every card in a row the same height while only
 * some pages have images; it is replaced automatically the moment an editor
 * sets that page's Featured Image, with no template edit.
 *
 * Square on purpose: the page images are square, so they show whole. Below
 * `sm` the card lays out as a row and this is an 80px thumbnail beside the
 * text; from `sm` up it fills the card's width above the text.
 *
 * Expects $args['post_id'].
 *
 * @package thepawmanual
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post_id = isset( $args['post_id'] ) ? (int) $args['post_id'] : 0;
if ( ! $post_id ) {
	return;
}

$card_image = function_exists( 'tpm_get_card_image' ) ? tpm_get_card_image( $post_id ) : '';
?>
<div class="tpm-card-image aspect-square w-20 shrink-0 overflow-hidden rounded-xl bg-brand-50 sm:w-full">
	<?php if ( '' !== $card_image ) : ?>
		<?php
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- markup built and escaped by WordPress core (get_the_post_thumbnail()).
		echo $card_image;
		?>
	<?php else : ?>
		<div class="flex h-full w-full items-center justify-center text-brand-200" aria-hidden="true">
			<?php tpm_icon( 'paw', 'h-8 w-8 sm:h-12 sm:w-12' ); ?>
		</div>
	<?php endif; ?>
</div>
