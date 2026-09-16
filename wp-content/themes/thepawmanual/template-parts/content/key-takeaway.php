<?php
/**
 * "Key takeaway" — the answer, directly under the H1.
 *
 * Task brief, requirement 1: the top of every article should put the answer
 * right under the H1, not make a visitor scroll for it. content/README.md's
 * house style already writes the first paragraph after the H1 as a direct
 * answer to the page's question — inc/content-blocks.php's
 * tpm_prepare_article_content() (owner: W2) is what lifts that paragraph out
 * of the body and hands it to this template part as already-filtered HTML;
 * this file only renders it inside the boxed callout the design calls for.
 *
 * Expects $args['takeaway'] — string, pre-filtered HTML, may be empty.
 *
 * @package thepawmanual
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$takeaway = isset( $args['takeaway'] ) ? (string) $args['takeaway'] : '';
if ( '' === trim( $takeaway ) ) {
	return;
}
?>
<aside class="tpm-key-takeaway mt-6 flex items-start gap-3 rounded-2xl border border-brand-100 bg-brand-50 p-5 sm:p-6" aria-labelledby="tpm-key-takeaway-label">
	<span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-brand-600 text-white" aria-hidden="true">
		<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-3.5 w-3.5" focusable="false"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
	</span>
	<div>
		<p id="tpm-key-takeaway-label" class="font-geist text-xs font-semibold uppercase tracking-widest text-brand-700">
			<?php esc_html_e( 'Key takeaway', 'thepawmanual' ); ?>
		</p>
		<div class="tpm-key-takeaway__body mt-1 font-geist text-sm text-neutral-800 [&_p]:mt-2 [&_p:first-child]:mt-0">
			<?php
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- already-filtered post content (tpm_prepare_article_content(), inc/content-blocks.php), same trust level the_content() itself has — the same pattern page.php uses for $toc['content'].
			echo $takeaway;
			?>
		</div>
	</div>
</aside>
