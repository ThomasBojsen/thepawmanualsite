<?php
/**
 * Sticky in-article table of contents.
 *
 * Only rendered by the caller when there are 3+ H2s (page.php decides that;
 * this file trusts $args['items'] and just needs it non-empty) — a TOC for
 * one or two headings is noise, not navigation.
 *
 * Markup only, deliberately — inc/enqueue.php already loads
 * assets/css/theme.css and assets/js/theme.js unconditionally on every
 * front-end page, so there's no "saves an HTTP request" excuse for inlining
 * styling or behaviour into a template part instead. The mobile-collapsed/
 * desktop-open mechanics documented below live in:
 * - src/tailwind.css — the `@screen lg` rules under "Table of contents".
 * - assets/js/theme.js — initTocDesktopOpen() and isLgBreakpoint().
 *
 * Built on a native <details>/<summary> so the toggle needs no JavaScript
 * and is keyboard-operable for free (Enter/Space toggles a focused
 * <summary> in every modern browser). Below `lg:`, page.php stacks this TOC
 * BEFORE the article body in DOM order, so it must start COLLAPSED there or
 * a long article's entire heading list buries the first word of real
 * content on a phone. At `lg:` and up it's the sticky sidebar (via the
 * wrapping <aside> in page.php) and should read as fully open, matching the
 * always-visible desktop TOC the design calls for.
 *
 * The markup ships with NO `open` attribute at all. `open` is boolean HTML
 * and PHP can't know the visitor's viewport width at render time, so
 * there's no server-side way to conditionally include it per breakpoint.
 * Native <details> defaults to closed without it — which is exactly the
 * mobile behaviour, for free, with zero CSS or JS.
 *
 * Getting it open on desktop needs BOTH the CSS and the JS:
 * - src/tailwind.css's `@screen lg` rules override the browser's own
 *   "closed details hides its content" behaviour so it PAINTS open on
 *   desktop even before any script runs. That behaviour isn't one stable,
 *   cross-browser rule, though: Firefox and Safari hide it by applying
 *   `display: none` straight to the non-summary children (the HTML spec's
 *   suggested rendering — an ordinary author `display: block` beats that,
 *   since author styles always win over user-agent ones), but Chrome
 *   (verified in 151 via computed-style inspection) now renders the hidden
 *   content inside an internal `::details-content` pseudo-element and
 *   hides THAT with `content-visibility: hidden` instead — a `display`
 *   override on the children does nothing there, because the ancestor
 *   pseudo-element never generates their boxes in the first place. Two
 *   different mechanisms, so two different rules are needed to cover both;
 *   there's no single selector that reaches every current engine.
 * - Even covering every engine's hiding mechanism only fixes the paint.
 *   The actual `open` attribute (and the `aria-expanded` a screen reader
 *   derives from it) still reports "closed" on desktop until a visitor
 *   manually activates the summary — a real assistive-tech mismatch, not
 *   just a cosmetic one, and CSS has no way to flip a boolean HTML
 *   attribute to fix it. assets/js/theme.js's initTocDesktopOpen() is what
 *   closes that gap: it's the one part of this that's identical in every
 *   browser, and the only piece of the fix that genuinely needs JavaScript.
 *
 * With JavaScript disabled: mobile needs none of the above — closed by
 * default plus the native click/Enter-to-open toggle already works with no
 * JS in any browser. Desktop degrades to "visually open via the CSS, but
 * `aria-expanded` stays stale" rather than the stuck-closed, unopenable TOC
 * the brief warns against.
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
	<details class="group">
		<summary class="flex list-none cursor-pointer items-center justify-between gap-2 rounded-2xl px-4 py-3 font-geist text-sm font-semibold text-neutral-900 [&::-webkit-details-marker]:hidden">
			<?php esc_html_e( 'In This Article', 'thepawmanual' ); ?>
			<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="tpm-toc-chevron h-4 w-4 shrink-0 text-neutral-400 transition-transform group-open:rotate-180" aria-hidden="true" focusable="false"><path d="m6 9 6 6 6-6"/></svg>
		</summary>
		<ol class="space-y-1.5 px-4 pb-4 font-geist text-sm lg:block">
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
