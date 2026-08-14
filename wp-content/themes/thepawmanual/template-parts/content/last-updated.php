<?php
/**
 * Visible "Updated <date>" / "Published <date>" line.
 *
 * content/SEO-STRATEGY.md §1 item 0.9: health content especially needs a
 * visible last-updated date. The label logic (what counts as "meaningfully"
 * updated) lives in tpm_get_last_updated_label() — this file is just the
 * markup wrapper so page.php can get_template_part() it like the other
 * content components.
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
?>
<p class="tpm-last-updated mt-2 font-geist text-sm text-neutral-500">
	<?php echo esc_html( tpm_get_last_updated_label( $post_id ) ); ?>
</p>
