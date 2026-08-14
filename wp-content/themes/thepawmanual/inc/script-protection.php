<?php
/**
 * HTML `<script>`-block protection.
 *
 * A tiny, single-purpose utility, split out on its own rather than living
 * inside whichever of its two callers happened to need it first: both
 * inc/content-nav.php (the DOMDocument heading/TOC pass) and
 * inc/internal-links.php (the regex-based link-degrading pass) run
 * something over rendered post content that must never look inside a
 * `<script>` block — libxml because it mis-tokenizes raw-text content (see
 * tpm_extract_script_blocks() below), the link pass because rewriting
 * something that merely *looks* like an `<a>` tag inside a JSON string
 * would be just as wrong. Neither pass owns this concern; it's the same
 * problem asked twice, so it is solved once, here, and required by both.
 *
 * @package thepawmanual
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * A per-request random token, used to make the placeholders below
 * un-guessable.
 *
 * Without this, a placeholder appearing verbatim in a page's actual body
 * text (contrived, but not impossible in hand-edited content) would be
 * silently overwritten by tpm_restore_script_blocks()'s plain
 * str_replace(). A random suffix generated fresh each request makes that
 * collision astronomically unlikely without needing cryptographic
 * guarantees — this is collision avoidance, not a security boundary.
 * Stripped down to bare alphanumerics — see tpm_script_placeholder() for
 * why the placeholder built from it must contain nothing else.
 *
 * @return string
 */
function tpm_script_placeholder_token() {
	static $token = null;
	if ( null === $token ) {
		$token = preg_replace( '/[^a-zA-Z0-9]/', '', uniqid( '', true ) );
	}
	return $token;
}

/**
 * The literal placeholder text substituted for extracted `<script>` block
 * number $index.
 *
 * Deliberately NOT an HTML comment (`<!-- ... -->`), even though that
 * reads as the obviously-inert choice for something meant to vanish into
 * rendered markup: HTML comments do not nest, and a script that itself
 * sits inside a `<!-- ... -->` block — an SEO specialist temporarily
 * disabling a schema block during a hand-edit, a realistic authoring
 * action for content this file exists to protect — means the
 * placeholder's own `-->` closes the OUTER comment early. What's left
 * after that early close is a stray ` -->` sitting in the document as
 * plain text, rendered as visible garbage on the live page: a confirmed
 * failure of an earlier, comment-shaped version of this placeholder.
 * Plain alphanumeric text has no terminator to clash with — no `<`, `>`,
 * `&`, or `-` at all — so it round-trips identically through DOMDocument
 * whether it lands as an ordinary text node or inside someone else's
 * comment.
 *
 * The trailing, non-digit `z` is load-bearing, not decorative: without a
 * fixed character after the digits, index 1's placeholder text ("...n1")
 * would be a literal substring of index 12's ("...n12"), and
 * tpm_restore_script_blocks()'s str_replace() would splice block 1's
 * content into the middle of block 12's placeholder on its way past,
 * instead of only ever matching a complete, exact placeholder. The "z"
 * after the digits closes that gap, the same way the old placeholder's
 * fixed trailing `-->` used to.
 *
 * @param int $index Position in the extracted-blocks array.
 * @return string
 */
function tpm_script_placeholder( $index ) {
	return 'tpmscriptblock' . tpm_script_placeholder_token() . 'n' . ( (int) $index ) . 'z';
}

/**
 * Pull every `<script>...</script>` block out of rendered HTML, replacing
 * each with an inert placeholder (see tpm_script_placeholder()).
 *
 * libxml (what DOMDocument::loadHTML() wraps) does not implement HTML5's
 * "script data" raw-text state, so it does not know a `<script>` body is
 * opaque. Any literal `<` followed by a letter inside one — e.g. FAQ JSON-LD
 * an SEO specialist hand-edits, like `"cats <8 lbs"` typo'd as `"cats <b..."`,
 * or a stray `</div>` in answer text — gets tokenized as real markup. A
 * `</div>` closes whatever wrapping `<div>` a DOM pass built (see
 * tpm_inject_heading_ids() in content-nav.php) early, and since these
 * passes only re-serialize the wrapper's own childNodes, everything after
 * that point — the rest of the script, the real closing `</script>`, and
 * all following content — silently disappears from the page. A
 * `<b`-shaped fragment instead gets its "closing tag" stripped, corrupting
 * the answer text with no error either way. Removing script bodies before
 * parsing — and splicing the untouched originals back in afterwards via
 * tpm_restore_script_blocks() — sidesteps the whole bug class instead of
 * trying to out-guess libxml's tokenizer.
 *
 * The opening-tag pattern below treats quoted attribute values atomically
 * (`"[^"]*"` / `'[^']*'` as whole units, ahead of the bare `[^>"']`
 * fallback) so a `>` inside an attribute value — `<script data-note="x > y">`
 * — can't be mistaken for the tag's real closing `>`. A naive `[^>]*` here
 * would have exactly the same "stops at the wrong `>`" failure this
 * function exists to prevent, just one step earlier.
 *
 * Fails CLOSED, not open: this only reports success (the third return
 * value, `true`) when the number of `<script` openings found in the raw
 * input exactly matches the number of complete `<script>...</script>`
 * blocks it managed to extract. An unclosed `<script>` — an SEO specialist
 * dropping the closing tag during a hand-edit is a realistic way for this
 * to happen — is exactly the mismatch case: the extraction regex requires
 * a literal `</script>` to match at all, so an unclosed tag contributes to
 * the "openings found" count but not the "blocks extracted" count. Every
 * caller MUST check the third element and skip its own pass entirely when
 * it is `false`, returning the original content untouched — the content
 * still has a real, unprotected `<script>` body sitting in it, and running
 * a DOM or regex pass over that is the exact bug this function exists to
 * prevent, not a degraded-but-safe fallback.
 *
 * @param string $html Rendered HTML that may contain <script> blocks.
 * @return array{0:string,1:string[],2:bool} [ html with placeholders substituted in (only meaningful when element 2 is true), the extracted blocks in order, whether extraction is provably complete ].
 */
function tpm_extract_script_blocks( $html ) {
	$open_tag_pattern = '<script\b(?:[^>"\']|"[^"]*"|\'[^\']*\')*>';

	$expected_count = preg_match_all( '#' . $open_tag_pattern . '#is', $html );
	if ( false === $expected_count ) {
		// PCRE itself failed on this input (e.g. backtrack limit). Cannot
		// prove anything about what's in this content — fail closed.
		return array( $html, array(), false );
	}

	$blocks = array();

	$placeholdered = preg_replace_callback(
		'#' . $open_tag_pattern . '.*?</script>#is',
		static function ( $match ) use ( &$blocks ) {
			$blocks[] = $match[0];
			// See tpm_script_placeholder() for why this is plain
			// alphanumeric text rather than an HTML comment: a comment
			// placeholder breaks when the <script> it stands in for was
			// itself inside someone else's <!-- ... --> comment.
			return tpm_script_placeholder( count( $blocks ) - 1 );
		},
		$html
	);

	if ( null === $placeholdered || count( $blocks ) !== $expected_count ) {
		// A null result means the regex engine itself gave up. A count
		// mismatch means at least one `<script` opening tag never found
		// its `</script>` — most plausibly an unclosed tag from a
		// hand-edit. Either way, this function cannot certify that every
		// script body has been pulled out, so it reports failure rather
		// than handing back a partially-protected string a caller might
		// mistake for a safe one.
		return array( $html, array(), false );
	}

	return array( $placeholdered, $blocks, true );
}

/**
 * Reverse of tpm_extract_script_blocks(): put the original `<script>`
 * blocks back where their placeholders are.
 *
 * Only call this after checking tpm_extract_script_blocks()'s third return
 * value was `true` — the "extraction failed" branch hands back the
 * original, placeholder-free string, with an empty $blocks array, so
 * calling this on it is a harmless no-op (nothing to restore because
 * nothing was substituted), but the caller should not have run its own
 * DOM/regex pass on that unprotected content in the first place.
 *
 * @param string   $html   HTML containing tpm_script_placeholder() tokens.
 * @param string[] $blocks Script blocks, in the order tpm_extract_script_blocks() returned them.
 * @return string
 */
function tpm_restore_script_blocks( $html, array $blocks ) {
	if ( empty( $blocks ) ) {
		return $html;
	}

	foreach ( $blocks as $index => $block ) {
		$html = str_replace( tpm_script_placeholder( $index ), $block, $html );
	}

	return $html;
}
