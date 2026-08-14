<?php
/**
 * Sticky in-article table of contents.
 *
 * Only rendered by the caller when there are 3+ H2s (page.php decides that;
 * this file trusts $args['items'] and just needs it non-empty) — a TOC for
 * one or two headings is noise, not navigation.
 *
 * Built on a native <details>/<summary> so the mobile collapse needs no
 * JavaScript and is keyboard-operable for free (Enter/Space toggles a
 * focused <summary> in every modern browser). The wrapping <aside> in
 * page.php is what makes it `lg:sticky`; this file only owns the box itself.
 *
 * Expects $args['items'] — array<int, array{id:string,text:string}>.
 *
 * @package thepawmanual
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$items = isset( $args['items'] ) && is_array( $args['items'] ) ? $args['items'] : array();
if ( empty( $items ) ) {
	return;
}
?>
<nav class="tpm-toc rounded-2xl border border-neutral-200 bg-white lg:sticky lg:top-24" aria-label="<?php esc_attr_e( 'Table of contents', 'thepawmanual' ); ?>">
	<details class="group" open>
		<summary class="flex list-none cursor-pointer items-center justify-between gap-2 rounded-2xl px-4 py-3 font-geist text-sm font-semibold text-neutral-900 [&::-webkit-details-marker]:hidden">
			<?php esc_html_e( 'In This Article', 'thepawmanual' ); ?>
			<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 shrink-0 text-neutral-400 transition-transform group-open:rotate-180" aria-hidden="true" focusable="false"><path d="m6 9 6 6 6-6"/></svg>
		</summary>
		<ol class="space-y-1.5 px-4 pb-4 font-geist text-sm">
			<?php foreach ( $items as $item ) : ?>
				<li>
					<a href="#<?php echo esc_attr( $item['id'] ); ?>" class="block text-neutral-600 transition-colors hover:text-neutral-900 hover:underline">
						<?php echo esc_html( $item['text'] ); ?>
					</a>
				</li>
			<?php endforeach; ?>
		</ol>
	</details>
</nav>
