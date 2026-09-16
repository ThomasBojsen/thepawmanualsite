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
 * Deliberately NOT built from `brand`/`amber`/`red` (deploy-gate finding,
 * 2026-09-14, post terracotta rebrand): against `brand-600` (#B4522F) every
 * stock warm hue sits within ~30° of it, so hue alone cannot separate this
 * from a "Key takeaway" box (template-parts/content/key-takeaway.php) —
 * swapping amber for red buys nothing when the whole palette is warm. This
 * is why the fix is WEIGHT, not hue: a solid `caution` left bar + filled
 * icon circle + dark heading on a plain white card, vs key-takeaway's
 * uniformly pale `brand-50` tint. Measured (dE76, CIE Lab):
 *   caution-700 (this file's left bar / icon fill)  vs brand-600 ≈ 31.2
 *   caution-900 (this file's heading text)          vs brand-700 ≈ 30.0
 * The pre-rebrand regression this replaces measured 30.5 and 8.4 for the
 * same two pairs (amber-600/900 vs the new brand colours) — the icon number
 * was never the problem (hue alone was always going to land ~30 apart from
 * a warm brand colour, exactly as predicted above); the heading number was:
 * amber-900 body text at 8.4 read as "the same brown" as brand-700. Putting
 * the accent colour on a bold, large heading instead of small pale body
 * text is what actually fixed it, not the swatch alone.
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
<div class="tpm-emergency-callout mt-6 flex items-start gap-3 rounded-2xl border border-neutral-200 border-l-8 border-l-caution-700 bg-white p-4 sm:p-5" role="note">
	<span class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-caution-700 text-white" aria-hidden="true">
		<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5" focusable="false"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><path d="M12 9v4"/><path d="M12 17h.01"/></svg>
	</span>
	<div>
		<p class="font-playfair text-lg font-bold text-caution-900">
			<?php esc_html_e( 'Pet emergency?', 'thepawmanual' ); ?>
		</p>
		<p class="mt-1 font-geist text-sm text-neutral-800">
			<?php
			printf(
				/* translators: 1: link opening tag, 2: the vet-signs page title, 3: link closing tag. */
				esc_html__( 'Learn %1$s%2$s%3$s so you know what to watch for.', 'thepawmanual' ),
				'<a href="' . esc_url( $callout['url'] ) . '" class="font-semibold text-caution-700 underline underline-offset-2 hover:no-underline">',
				esc_html( $callout['title'] ),
				'</a>'
			);
			?>
		</p>
	</div>
</div>
