<?php
/**
 * "Pet emergency?" callout.
 *
 * Auto-rendered on pages under /dogs/health/ and /cats/health/ (excluding
 * the two vet-signs pages themselves) by tpm_get_emergency_callout() —
 * see inc/content-nav.php for how the species and target are derived from
 * the URL hierarchy. The two vet-signs pages are deliberately the
 * most-linked pages on the site (content/README.md, constitution rule 3);
 * this callout is a big share of how that happens automatically as new
 * health pages are added.
 *
 * Expects $args['callout'] — the array returned by tpm_get_emergency_callout(),
 * or null when it doesn't apply to the current page.
 *
 * @package thepawmanual
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$callout = isset( $args['callout'] ) && is_array( $args['callout'] ) ? $args['callout'] : null;
if ( ! $callout ) {
	return;
}
?>
<div class="tpm-emergency-callout mt-6 flex items-start gap-3 rounded-2xl border border-amber-200 bg-amber-50 p-4 sm:p-5" role="note">
	<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="mt-0.5 h-5 w-5 shrink-0 text-amber-600" aria-hidden="true" focusable="false"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><path d="M12 9v4"/><path d="M12 17h.01"/></svg>
	<p class="font-geist text-sm text-amber-900">
		<strong class="font-semibold"><?php esc_html_e( 'Pet emergency?', 'thepawmanual' ); ?></strong>
		<?php
		printf(
			/* translators: 1: link opening tag, 2: the vet-signs page title, 3: link closing tag. */
			esc_html__( ' Learn %1$s%2$s%3$s so you know what to watch for.', 'thepawmanual' ),
			'<a href="' . esc_url( $callout['url'] ) . '" class="font-medium text-amber-900 underline underline-offset-2 hover:no-underline">',
			esc_html( $callout['title'] ),
			'</a>'
		);
		?>
	</p>
</div>
