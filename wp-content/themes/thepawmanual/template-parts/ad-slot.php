<?php
/**
 * Reusable advertising slot.
 *
 * A deliberately empty, labelled placeholder. The site is monetised with
 * display ads, but no ad-network code is ever hardcoded here (CLAUDE.md §9) —
 * ad code is added later, or managed by an ads plugin.
 *
 * Optional $args['label'] overrides the placeholder caption.
 *
 * @package thepawmanual
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$label = ! empty( $args['label'] ) ? $args['label'] : __( 'Advertisement', 'thepawmanual' );
?>
<div class="tpm-ad-slot flex h-24 w-full items-center justify-center rounded-xl border border-dashed border-neutral-300 bg-neutral-100 sm:h-32">
	<span class="font-geist text-sm uppercase tracking-wider text-neutral-400">
		<?php echo esc_html( $label ); ?>
	</span>
</div>
