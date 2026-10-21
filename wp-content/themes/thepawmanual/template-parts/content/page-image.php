<?php
/**
 * The page image: a Page's Featured Image, between the deck and the
 * key-takeaway box.
 *
 * Renders nothing when the Page has no Featured Image, so every page without
 * one looks exactly as before. See tpm_get_page_image() (inc/page-image.php)
 * for how the image is chosen and why its markup looks the way it does.
 *
 * No animation of its own: it sits inside page.php's animated <header> and
 * fades in with the title above it.
 *
 * Expects $args['post_id']. Takes $args['image'], tpm_get_page_image()'s
 * result, when the caller already has it (article-header.php needs it to pick
 * its layout), so the image is only looked up once.
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

// Degrade to "no image", never a fatal on every Page, if inc/page-image.php is
// ever not loaded: the same posture as the other cross-file calls in page.php.
if ( ! function_exists( 'tpm_get_page_image' ) ) {
	return;
}

$image = isset( $args['image'] ) && is_array( $args['image'] ) ? $args['image'] : tpm_get_page_image( $post_id );
if ( empty( $image['html'] ) ) {
	return;
}
?>
<?php // Below `lg` the width follows the image's own shape (a square is 448px, a 16:9 picture wider), so no fixed utility class fits: it arrives as a custom property, the same number the image's `sizes` is built from. From `lg` up the figure fills article-header.php's image column instead. mt-6 matches the key-takeaway box below. ?>
<figure class="tpm-page-image mt-6 max-w-[var(--tpm-page-image-max)] overflow-hidden rounded-2xl bg-neutral-100 lg:mt-0 lg:max-w-none" style="--tpm-page-image-max: <?php echo (int) $image['slot']; ?>px">
	<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- markup built and escaped by WordPress core (get_the_post_thumbnail()).
	echo $image['html'];
	?>
</figure>
