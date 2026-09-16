<?php
/**
 * Post-content transform pass: key takeaway, FAQ accordion, inline tool CTA.
 *
 * tpm_prepare_article_content() is the one function every other concern in
 * this file exists to support. It does ONE DOMDocument parse of an
 * article's rendered content and runs three independent mutators over that
 * single tree — pulling the lead paragraph out as a standalone "key
 * takeaway" (user requirement 1), restructuring the FAQ section's H3/P
 * pairs into a `<details>` accordion (requirement 1), and inserting a
 * contextual tool CTA card after the article's first section (requirement
 * 3) — rather than three separate parses, for the same reason
 * tpm_inject_heading_ids() (inc/content-nav.php) does one parse instead of
 * several: libxml parsing and re-serializing ~127 pages' worth of content
 * on every request is not free, and every extra pass is another chance to
 * hit the `<script>`-block bug documented in inc/script-protection.php.
 *
 * MUST RUN BEFORE tpm_inject_heading_ids() IN page.php. Two of this file's
 * three mutators change which elements are even present in the tree before
 * headings get IDs stamped on them and the table of contents is collected:
 * the lead paragraph has to already be gone (it never carries an H2, but
 * leaving it in place until after the TOC pass would cost nothing except
 * consistency — the real requirement is the FAQ one) and, more importantly,
 * the FAQ's H3 "questions" have to already be moved inside `<summary>`
 * elements before tpm_inject_heading_ids() runs, because that pass only
 * walks `getElementsByTagName( 'h2' )` — it would leave FAQ H3s alone
 * either way — but the inline tool CTA's placement (immediately before the
 * SECOND top-level H2) is only well-defined against the ORIGINAL flat
 * section structure. Running this pass second, against a tree
 * tpm_inject_heading_ids() has already stamped `id` attributes onto, would
 * still technically work, but every other page-content pass in this theme
 * runs takeaway/FAQ/CTA restructuring before heading-ID stamping, and
 * keeping to one fixed order is what makes it safe to reason about either
 * pass in isolation.
 *
 * ORDER DEPENDENCY WITHIN THIS FILE'S OWN THREE MUTATORS (round 4 finding):
 * tpm_dom_collect_linked_paths() MUST run against $root before mutator (a)
 * (tpm_dom_extract_lead_paragraph()) detaches the lead paragraph — mutator
 * (c)'s "is this tool already linked inline" dedup check depends on seeing
 * links that only the lead paragraph carried, and (a) removes that paragraph
 * from $root before (c) ever runs. See tpm_prepare_article_content()'s own
 * body and tpm_dom_collect_linked_paths()'s docblock for the full reasoning.
 *
 * Same fail-closed contract as tpm_inject_heading_ids() throughout: an
 * empty/malformed lead paragraph, a missing "Frequently asked questions"
 * heading, or a tool with nowhere sensible to sit are all silent no-ops —
 * RECON.md notes only 121 of 127 pages match the expected shape exactly,
 * and this file has to degrade gracefully for the other 6. Losing the
 * takeaway box, the FAQ accordion, or the CTA is a non-event; losing the
 * article body is not, so every DOM operation in this file is wrapped in a
 * single try/catch that returns the untouched original on ANY unexpected
 * state, not just the specific failure modes anticipated below.
 *
 * @package thepawmanual
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Normalize HTML down to a length-comparable string for the fail-closed
 * guard below: strip tags, decode entities, then drop all whitespace.
 *
 * wp_strip_all_tags() strips tags but does NOT decode entities. Imported
 * post_content is full of `&#39;` / `&quot;` (the markdown → WXR
 * conversion escapes every apostrophe and quote), and a DOMDocument
 * round-trip decodes those to literal `'` / `"` while re-serializing — so
 * comparing raw wp_strip_all_tags() lengths shrinks the "after" side by 4
 * chars per `&#39;` and 5 per `&quot;` even though ZERO content was lost.
 * Reproduced by replaying all 17 wave-1 page bodies through the FULL
 * production `the_content` chain — wptexturize() -> wpautop() ->
 * shortcode_unautop() -> do_shortcode() (with the real
 * [pawmanual_calculator] shortcode) -> tpm_filter_internal_links() at
 * priority 30, exactly as page.php's own pipeline runs it, not a replay
 * that stops earlier in that chain — and feeding the pre-decode comparison
 * the exact protected/serialized string pair this guard actually receives:
 * it false-fired on 7 of the 17 pages, including the live `/tools/` page,
 * entirely explained by their entity counts, not by any dropped text. (Per-
 * page deltas are pipeline-dependent — an earlier replay of `/tools/` that
 * stopped at wpautop() measured a different delta/tolerance pair than the
 * full chain above does, which is why no single page's number is quoted
 * here as a standalone fact; the 7-of-17 page count, reproduced twice
 * independently under the full chain, is what this guard's correctness
 * rests on.) html_entity_decode() undoes that decode-asymmetry on
 * BOTH sides — applying it to only one side would just move the false
 * positive to the other direction — and stripping whitespace entirely
 * (not collapsing to a single space; nothing here needs to stay readable,
 * only comparable) absorbs wpautop's incidental newline/indentation
 * differences between the pre-parse and re-serialized markup.
 *
 * Twin of tpm_content_nav_normalize_for_length_comparison() in
 * inc/content-nav.php's tpm_content_nav_lost_too_much_text() — kept
 * duplicated rather than shared for the same load-order-independence
 * reason tpm_dom_content_already_links_url() above reimplements its own
 * path comparison instead of calling into inc/internal-links.php. Keep
 * the two in sync if either changes.
 *
 * @param string $html
 * @return string
 */
function tpm_content_blocks_normalize_for_length_comparison( $html ) {
	$text = html_entity_decode( wp_strip_all_tags( (string) $html ), ENT_QUOTES, 'UTF-8' );

	// preg_replace() returns null on a PCRE engine failure (e.g. the
	// backtrack limit) rather than throwing. Falling back to the
	// entity-decoded-but-not-whitespace-stripped text keeps the comparison
	// running instead of silently treating the input as empty, which would
	// make the guard fire on every page rather than none.
	$stripped = preg_replace( '/\s+/u', '', $text );
	return null === $stripped ? $text : $stripped;
}

/**
 * Fail-closed guard: whether re-serializing the parsed tree lost a
 * suspicious amount of visible text compared to what was fed into it.
 *
 * The file docblock above promises a fallback on "ANY unexpected state," and
 * the try/catch delivers that for anything that THROWS — but libxml's HTML5
 * recovery does not throw on unbalanced markup, it silently recovers from
 * it. A stray, unmatched closing tag in hand-edited content (an extra
 * `</div>` typed while rearranging a table, say) makes `loadHTML()` close
 * the wrapping scaffold `<div>` early: `$loaded` is still true and
 * `$dom->documentElement->tagName` is still 'div', so neither guard already
 * in tpm_prepare_article_content() catches it, and everything after the
 * break silently never makes it into $root's children — reproduced directly
 * against this exact input shape, not a theoretical concern (CLAUDE.md §11,
 * 2026-08-14 pre-deploy review). None of this file's three mutators is
 * supposed to DELETE visible text — they only move or wrap nodes — so a
 * serialized result that is drastically shorter than the input it came from
 * is exactly what that failure mode looks like from the outside, with no
 * thrown error to catch it any other way.
 *
 * Both sides MUST go through tpm_content_blocks_normalize_for_length_comparison()
 * before counting, not just wp_strip_all_tags() — see that function's
 * docblock. Without entity decoding this guard false-positives on ordinary,
 * lossless entity round-tripping (7 of the 17 wave-1 pages, replayed through
 * the real pipeline — see that function's docblock for the methodology),
 * which is a false alarm strictly worse than the bug it exists to catch: it
 * silently disables the takeaway/FAQ/CTA/TOC on pages that parsed perfectly
 * fine.
 *
 * @param string $protected_content The script-protected HTML fed into DOMDocument::loadHTML().
 * @param string $serialized        Everything this pass is about to return in its place (takeaway + content, concatenated).
 * @return bool True when $serialized is missing a suspicious amount of text.
 */
function tpm_content_blocks_lost_too_much_text( $protected_content, $serialized ) {
	$before = mb_strlen( tpm_content_blocks_normalize_for_length_comparison( $protected_content ) );
	if ( 0 === $before ) {
		return false; // Nothing to lose.
	}

	$after = mb_strlen( tpm_content_blocks_normalize_for_length_comparison( $serialized ) );

	// The larger of 20 characters or 2% absorbs any residual noise this
	// normalization doesn't already account for, without masking a genuine
	// truncation — the reproduced failure drops an entire trailing section,
	// hundreds of characters, not a rounding error.
	$tolerance = max( 20, (int) ceil( $before * 0.02 ) );

	return ( $after + $tolerance ) < $before;
}

/**
 * Prepare one article's rendered content for display: extract its key
 * takeaway, restructure its FAQ into an accordion, and (optionally) inject
 * a contextual tool CTA — all in a single DOMDocument parse.
 *
 * `<script>` blocks (the FAQ JSON-LD every page carries) are pulled out via
 * tpm_extract_script_blocks() before the parse and spliced back into BOTH
 * return values afterwards — not just 'content'. The takeaway is the first
 * paragraph's inner HTML, and a `<script>` has no legitimate reason to sit
 * inside a `<p>`, but if a hand-edit ever put one there, restoring it in
 * 'takeaway' too costs nothing (tpm_restore_script_blocks() is a no-op on
 * text containing no placeholder) and avoids leaking a raw placeholder
 * token onto the page in that edge case.
 *
 * @param string $rendered_content Fully filtered post content (already run
 *                                  through the `the_content` pipeline —
 *                                  shortcodes executed, wpautop applied —
 *                                  same input tpm_inject_heading_ids() takes,
 *                                  and this MUST run before that pass; see
 *                                  the file docblock for why).
 * @param int    $post_id          The post/Page ID this content belongs to,
 *                                  used only to look up the inline tool CTA.
 * @param array  $opts             Optional. 'inline_tool' (bool, default
 *                                  true) — pass false for hub pages, which
 *                                  get their own tools strip elsewhere and
 *                                  don't need a second, in-body CTA.
 * @return array{takeaway:string,content:string} 'takeaway' is the lead
 *                                  paragraph's inner HTML, or '' when none
 *                                  qualified. 'content' is the transformed
 *                                  article body.
 */
function tpm_prepare_article_content( $rendered_content, $post_id, $opts = array() ) {
	$fallback = array(
		'takeaway' => '',
		'content'  => (string) $rendered_content,
	);

	// Same two guards, same order, as tpm_inject_heading_ids(): an empty
	// body has nothing to parse, and a DOMDocument-less environment (a
	// stripped-down PHP build without ext-dom) can't run any of this safely.
	if ( '' === trim( (string) $rendered_content ) || ! class_exists( 'DOMDocument' ) ) {
		return $fallback;
	}

	// Everything past this point — script extraction, the DOM parse, all
	// three mutators, re-serialization — runs inside one try/catch. Three
	// independent mutators share this one tree below; any of them hitting
	// DOM state genuinely unexpected for this content shape (a malformed
	// fragment built by tpm_dom_fragment_from_html(), for instance) must
	// fall back to the untouched original rather than take the article
	// down with it — the same posture tpm_inject_heading_ids() takes, just
	// enforced with a catch block instead of return-value checks alone,
	// because this file's mutators do far more tree surgery than that one
	// attribute-setting loop ever does.
	try {
		list( $protected_content, $script_blocks, $scripts_protected ) = tpm_extract_script_blocks( $rendered_content );

		if ( ! $scripts_protected ) {
			return $fallback;
		}

		$dom = new DOMDocument();

		// Identical wrapper-div trick to tpm_inject_heading_ids(): the
		// XML-prolog line declares UTF-8 without an <?xml> node surviving
		// into the tree, LIBXML_HTML_NOIMPLIED stops libxml from silently
		// adding <html><body> around the fragment, and NODEFDTD drops the
		// doctype it would otherwise inject — so the wrapping <div> below
		// ends up as the document's one and only root element.
		libxml_use_internal_errors( true );
		$loaded = $dom->loadHTML(
			'<?xml encoding="utf-8" ?><div>' . $protected_content . '</div>',
			LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
		);
		libxml_clear_errors();

		if ( ! $loaded || ! $dom->documentElement || 'div' !== $dom->documentElement->tagName ) {
			return $fallback;
		}

		$root = $dom->documentElement;

		// ORDER-DEPENDENT SNAPSHOT — must be taken here, before ANY mutator
		// below runs, not inlined into (c)'s own dedup check. (a) detaches
		// the lead paragraph from $root; a link that happens to sit in that
		// paragraph (the exact live shape of /pet-guides/pet-costs/, whose
		// lead links /tools/pet-cost-calculator/ — CLAUDE.md §11) would
		// otherwise be invisible to (c)'s "is this tool already linked"
		// check, since by the time (c) runs, $root no longer contains it.
		// Capturing the full set now, against the still-intact tree, and
		// handing it down rather than letting (c) re-derive it from $root
		// later is what makes that check correct regardless of what (a) or
		// (b) go on to remove or move. If a future edit reorders these
		// mutators, this call must stay first, immediately after $root is
		// established — see tpm_dom_collect_linked_paths()'s own docblock.
		$linked_paths = tpm_dom_collect_linked_paths( $root );

		// (a) Lead paragraph out first — it's always the very first node,
		// so nothing downstream needs to account for it still being there.
		$takeaway_html = tpm_dom_extract_lead_paragraph( $root );

		// (b) FAQ restructuring second — the inline-tool placement below
		// (c) needs to know where the FAQ's own H2 sits among the OTHER
		// H2s, and running (b) before (c) means that answer is always
		// "wherever it already was," never something (c) has to guess at.
		tpm_dom_build_faq_accordion( $dom, $root );

		// (c) Inline tool CTA last, and only when the caller wants one.
		$inline_tool = ! isset( $opts['inline_tool'] ) || (bool) $opts['inline_tool'];
		if ( $inline_tool ) {
			tpm_dom_inject_inline_tool_cta( $dom, $root, $linked_paths, (int) $post_id );
		}

		// Serialize child-by-child, exactly like tpm_inject_heading_ids()
		// — $dom->saveHTML() with no argument would re-emit the wrapping
		// <div> itself, which was only ever scaffolding for the parse.
		$html = '';
		foreach ( $root->childNodes as $child ) {
			$html .= $dom->saveHTML( $child );
		}

		// Fail closed on a truncating parse — see the guard's own
		// docblock. Checked BEFORE restoring script placeholders so both
		// sides of the comparison are on equal footing (the placeholder
		// text, not the real script body, on both sides).
		if ( tpm_content_blocks_lost_too_much_text( $protected_content, $takeaway_html . $html ) ) {
			return $fallback;
		}

		return array(
			'takeaway' => tpm_restore_script_blocks( $takeaway_html, $script_blocks ),
			'content'  => tpm_restore_script_blocks( $html, $script_blocks ),
		);
	} catch ( \Throwable $e ) {
		return $fallback;
	}
}

/**
 * Parse a trusted HTML string into a DOMDocumentFragment importable into
 * $target — the DOM-construction equivalent of a template partial, used
 * anywhere hand-building nodes with createElement()/setAttribute() calls
 * would turn a few lines of markup into a page of procedural code (the FAQ
 * chevron icon, the tool CTA card).
 *
 * DOMDocumentFragment::appendXML() looks like the obvious built-in for
 * this but is XML-strict: it rejects perfectly ordinary HTML5 — an
 * unescaped `&` in a label some SEO specialist wrote, or void-element
 * shorthand — that this file has no business assuming will never occur.
 * Routing through DOMDocument::loadHTML() instead accepts real HTML syntax,
 * via the identical wrapper-div trick tpm_prepare_article_content() itself
 * uses to parse the post body, so there's exactly one HTML-parsing
 * technique in this file, not two.
 *
 * Fails the same way everything else here does: any parse trouble returns
 * null instead of a fatal, so a caller building decorative markup can skip
 * that one piece — no chevron, no CTA — rather than losing the rest of the
 * pass over it.
 *
 * @param DOMDocument $target Document the returned fragment's nodes are imported into.
 * @param string      $html   Trusted HTML fragment. Never pass raw post content through this — it has no `<script>` protection of its own.
 * @return DOMDocumentFragment|null
 */
function tpm_dom_fragment_from_html( DOMDocument $target, $html ) {
	if ( '' === trim( (string) $html ) ) {
		return null;
	}

	$helper = new DOMDocument();

	libxml_use_internal_errors( true );
	$loaded = $helper->loadHTML(
		'<?xml encoding="utf-8" ?><div>' . $html . '</div>',
		LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
	);
	libxml_clear_errors();

	if ( ! $loaded || ! $helper->documentElement || 'div' !== $helper->documentElement->tagName ) {
		return null;
	}

	$fragment = $target->createDocumentFragment();
	foreach ( $helper->documentElement->childNodes as $child ) {
		$imported = $target->importNode( $child, true );
		if ( $imported ) {
			$fragment->appendChild( $imported );
		}
	}

	return $fragment;
}

/* -------------------------------------------------------------------------
 * (a) Key takeaway — pull the lead paragraph out as standalone intro copy.
 * ---------------------------------------------------------------------- */

/**
 * If the article's first element is a substantial paragraph, remove it from
 * the tree and return its inner HTML; otherwise change nothing.
 *
 * RECON.md: "the first paragraph after the H1 is already 'the answer' —
 * the key takeaway." The 80-character floor exists because not every page
 * opens that way — a short one-line stub ("Here's what you need to know:")
 * is a lead-in to the REST of the article, not itself an answer worth
 * pulling out and presenting as one, so treating it as the takeaway would
 * misrepresent it. Below that length, this is a silent no-op: the
 * paragraph stays exactly where it was, as ordinary body copy.
 *
 * @param DOMElement $root The parsed article body's root element.
 * @return string Inner HTML of the removed paragraph, or '' when none qualified.
 */
function tpm_dom_extract_lead_paragraph( DOMElement $root ) {
	$first_element = null;
	foreach ( $root->childNodes as $child ) {
		if ( XML_ELEMENT_NODE === $child->nodeType ) {
			$first_element = $child;
			break;
		}
	}

	if ( ! $first_element instanceof DOMElement || 'p' !== $first_element->tagName ) {
		return '';
	}

	// mb_strlen(), not strlen(): this is a "is this a real paragraph, not a
	// stub" character-count heuristic, and WP always polyfills mb_strlen()
	// (wp-includes/compat.php) even where ext-mbstring itself is absent, so
	// there's no availability guard needed the way there is for DOMDocument.
	if ( mb_strlen( trim( $first_element->textContent ) ) < 80 ) {
		return '';
	}

	$inner_html = tpm_dom_inner_html( $first_element );
	$first_element->parentNode->removeChild( $first_element );

	return $inner_html;
}

/**
 * Serialize a DOMElement's child nodes only — its inner HTML, not the
 * element's own opening/closing tags.
 *
 * @param DOMElement $element Element whose ownerDocument backs the serialization.
 * @return string
 */
function tpm_dom_inner_html( DOMElement $element ) {
	$html = '';
	$dom  = $element->ownerDocument;
	foreach ( $element->childNodes as $child ) {
		$html .= $dom->saveHTML( $child );
	}
	return $html;
}

/* -------------------------------------------------------------------------
 * (b) FAQ accordion — restructure the "Frequently asked questions" H3/P
 * pairs into <details>/<summary> items.
 * ---------------------------------------------------------------------- */

/**
 * Lower-case a heading's text and strip punctuation/collapse whitespace, so
 * "Frequently Asked Questions:" and "frequently asked questions" compare
 * equal without caring about the exact punctuation an SEO specialist typed.
 *
 * @param string $text Raw heading textContent.
 * @return string
 */
function tpm_dom_normalize_heading_text( $text ) {
	$text = strtolower( trim( (string) $text ) );
	$text = preg_replace( '/[^a-z0-9\s]/', '', $text );
	$text = preg_replace( '/\s+/', ' ', (string) $text );
	return trim( (string) $text );
}

/**
 * Whether a node is inert whitespace (a blank text node) or a comment — the
 * kind of node that can sit between an H2/H3 and the "real" content that
 * follows it without meaning anything, and which this pass can safely skip
 * over while scanning, then discard once the surrounding real content has
 * been moved elsewhere.
 *
 * @param DOMNode $node
 * @return bool
 */
function tpm_dom_is_blank_text_node( DOMNode $node ) {
	if ( XML_TEXT_NODE !== $node->nodeType && XML_COMMENT_NODE !== $node->nodeType ) {
		return false;
	}
	return '' === trim( (string) $node->textContent );
}

/**
 * Whether a node IS, or WRAPS ONLY, the inert text placeholder
 * tpm_extract_script_blocks() substitutes for the FAQ JSON-LD `<script>`
 * block.
 *
 * inc/script-protection.php exports tpm_script_placeholder( $index ) — which
 * needs an index this pass never has, since it's walking sibling nodes, not
 * re-deriving which extracted block a given node stands in for — but no
 * predicate that answers "is this text a placeholder" directly. Rather than
 * hardcode the full placeholder shape (including its per-request random
 * token) here, this reconstructs the pattern from tpm_script_placeholder_token()
 * — the one piece that function documents as public and memoized, so it
 * returns the SAME token used when the placeholders were substituted in
 * earlier in this same request — plus the literal 'tpmscriptblock' / 'n' /
 * 'z' format tpm_script_placeholder()'s own docblock documents. If that
 * format ever changes, this needs updating alongside it; there is currently
 * no third option that avoids that coupling entirely.
 *
 * Why this checks BOTH shapes (round 3, Finding 2 — the original version of
 * this function checked only the DOMText shape, and was consequently a
 * no-op against real content): the placeholder text never reaches this pass
 * as a bare top-level DOMText node the way it would if it were substituted
 * directly into `$root`. wpautop() — which already ran, as part of the
 * `the_content` pipeline, before tpm_prepare_article_content() ever sees the
 * content — wraps ANY bare line sitting between two blank lines in a `<p>`,
 * placeholder included, exactly the way it would wrap ordinary prose. A real
 * wave-1 body mid-pipeline therefore contains `<p>tpmscriptblock<token>n0z</p>`,
 * a `<p>` ELEMENT whose one child is the placeholder text — never the bare
 * text node this function originally assumed. Reproduced against all 11
 * wave-1 pages carrying a FAQPage block (CLAUDE.md §13): the "stop the
 * answer run here" check in tpm_dom_build_faq_accordion() below — which
 * tests `instanceof DOMElement` for an H2/H3 boundary, so a `<p>` never trips
 * it — absorbed that wrapper paragraph into the last FAQ answer on every one
 * of them, restoring the JSON-LD correctly (the script text itself is
 * untouched either way) but leaving it nested inside `<details><div
 * class="tpm-faq__a">`, not at the top level search engines expect. This
 * function now unwraps a single-text-child element first, so both the
 * pre-wpautop shape (a bare DOMText, kept as a fallback in case a future
 * caller ever hands this pass unwrapped content) and the real, wpautop'd
 * shape are recognised.
 *
 * @param DOMNode $node
 * @return bool
 */
function tpm_dom_is_script_placeholder_node( DOMNode $node ) {
	if ( $node instanceof DOMElement && 1 === $node->childNodes->length && XML_TEXT_NODE === $node->firstChild->nodeType ) {
		$node = $node->firstChild; // Unwrap wpautop's <p> before the text-node check below.
	}

	if ( XML_TEXT_NODE !== $node->nodeType ) {
		return false;
	}

	$token   = tpm_script_placeholder_token();
	$pattern = '/^tpmscriptblock' . preg_quote( $token, '/' ) . 'n\d+z$/';

	return 1 === preg_match( $pattern, trim( (string) $node->textContent ) );
}

/**
 * The inline chevron icon markup for each FAQ accordion item's summary.
 *
 * A plain HTML string, not built via createElement() calls, and fed
 * through tpm_dom_fragment_from_html() at the call site — the same
 * lucide-style inline SVG template-parts/content/toc.php already uses for
 * its own <details> disclosure chevron, kept here as a literal string
 * rather than a new entry in inc/icons.php's tpm_icon() map because that
 * helper only echoes (it's built for direct template output, not for
 * feeding into a second DOMDocument), and this file needs the markup as a
 * string it can parse.
 *
 * @return string
 */
function tpm_dom_faq_chevron_svg() {
	return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="tpm-faq__chevron h-4 w-4 shrink-0 text-neutral-400 transition-transform group-open:rotate-180" aria-hidden="true" focusable="false"><path d="m6 9 6 6 6-6"/></svg>';
}

/**
 * Find the first top-level "Frequently asked questions" H2, restructure
 * every immediately-following H3 "question" + its answer content into a
 * `<details class="tpm-faq__item">` accordion item, and replace that whole
 * run with a single `<div class="tpm-faq">` right after the H2. A no-op
 * when the heading is missing, or when it's followed by zero H3s.
 *
 * This is a SINGLE pass over $root's direct children, not
 * getElementsByTagName() over the whole subtree — RECON.md's "ground
 * truth about the markup" confirms the article body is a flat sequence
 * (p/h2/h3/figure/ul/script, no nesting), so the FAQ heading and its H3s
 * are always direct children of $root. If a page ever doesn't match that
 * shape, the scan below simply finds nothing to restructure and leaves the
 * tree untouched — the same graceful-miss RECON.md notes 6 of 127 pages
 * need.
 *
 * The h3 elements are MOVED into their new `<summary>` (appendChild()
 * detaches a node from wherever it currently lives before re-attaching it)
 * rather than recreated from `->textContent`, and their own child nodes
 * travel with them completely untouched. This is load-bearing, not just
 * convenient: CLAUDE.md §13 requires the visible FAQ question text to stay
 * byte-identical to what ships in the page's FAQPage JSON-LD, and any
 * string round-trip through ->textContent / a new DOMText node risks
 * re-encoding entities or dropping inline markup (a question wrapped in
 * `<em>`, say) that a straight node move never touches. Keeping the real
 * `<h3>` element inside `<summary>` (rather than a `<span>` copy) also
 * keeps it in the page's heading outline for crawlers and screen readers —
 * a `<summary>` legitimately accepts heading content.
 *
 * @param DOMDocument $dom  The owning document (for createElement() calls).
 * @param DOMElement  $root The parsed article body's root element.
 */
function tpm_dom_build_faq_accordion( DOMDocument $dom, DOMElement $root ) {
	$faq_heading = null;
	foreach ( $root->childNodes as $child ) {
		if ( $child instanceof DOMElement && 'h2' === $child->tagName
			&& 'frequently asked questions' === tpm_dom_normalize_heading_text( $child->textContent ) ) {
			$faq_heading = $child;
			break;
		}
	}

	if ( ! $faq_heading ) {
		return;
	}

	// Read-only scan first: walk the siblings after the H2 and record what
	// each FAQ item's question/answer nodes are, plus every blank text node
	// found ALONG THE WAY (leading, and between items) for later removal.
	// Nothing is moved yet — if this scan turns up zero H3s, the tree must
	// end up completely untouched, and the simplest way to guarantee that
	// is to not touch it until we know there's at least one item to build.
	$blank_nodes = array();
	$items       = array();

	$node = $faq_heading->nextSibling;
	while ( $node && tpm_dom_is_blank_text_node( $node ) ) {
		$blank_nodes[] = $node;
		$node          = $node->nextSibling;
	}

	while ( $node instanceof DOMElement && 'h3' === $node->tagName ) {
		$question     = $node;
		$answer_nodes = array();

		$sibling = $node->nextSibling;
		while ( $sibling ) {
			if ( $sibling instanceof DOMElement && ( 'h2' === $sibling->tagName || 'h3' === $sibling->tagName ) ) {
				break; // Next question, or an unexpected next section: either way, this answer is done.
			}
			if ( tpm_dom_is_script_placeholder_node( $sibling ) ) {
				break; // Never absorb the FAQ JSON-LD placeholder into an answer — see that function's docblock.
			}
			$answer_nodes[] = $sibling;
			$sibling         = $sibling->nextSibling;
		}

		$items[] = array(
			'question' => $question,
			'answer'   => $answer_nodes,
		);

		$node = $sibling;
		while ( $node && tpm_dom_is_blank_text_node( $node ) ) {
			$blank_nodes[] = $node;
			$node          = $node->nextSibling;
		}
	}

	if ( empty( $items ) ) {
		return; // The H2 is followed by zero H3s — nothing to restructure.
	}

	$faq_container = $dom->createElement( 'div' );
	$faq_container->setAttribute( 'class', 'tpm-faq mt-8 space-y-3' );

	foreach ( $items as $item ) {
		$details = $dom->createElement( 'details' );
		// `group` alongside the contractual `tpm-faq__item` class: it's what
		// makes the chevron's `group-open:rotate-180` (tpm_dom_faq_chevron_svg())
		// work, the same Tailwind pattern template-parts/content/toc.php's own
		// <details class="group"> already uses for its chevron.
		$details->setAttribute( 'class', 'tpm-faq__item group rounded-2xl border border-neutral-200 bg-white' );

		$summary = $dom->createElement( 'summary' );
		$summary->setAttribute( 'class', 'tpm-faq__q flex list-none cursor-pointer items-center justify-between gap-3 px-4 py-3 font-geist text-sm font-semibold text-neutral-900 [&::-webkit-details-marker]:hidden' );

		$question_el    = $item['question'];
		$existing_class = trim( $question_el->getAttribute( 'class' ) );
		$question_el->setAttribute( 'class', '' === $existing_class ? 'tpm-faq__q-text' : $existing_class . ' tpm-faq__q-text' );
		$summary->appendChild( $question_el ); // Moves the live node — see docblock above for why that's required.

		$chevron = tpm_dom_fragment_from_html( $dom, tpm_dom_faq_chevron_svg() );
		if ( $chevron ) {
			$summary->appendChild( $chevron );
		}

		$details->appendChild( $summary );

		$answer = $dom->createElement( 'div' );
		$answer->setAttribute( 'class', 'tpm-faq__a border-t border-neutral-200 px-4 pb-4 pt-3' );
		foreach ( $item['answer'] as $answer_node ) {
			$answer->appendChild( $answer_node ); // Moves each node.
		}
		$details->appendChild( $answer );

		$faq_container->appendChild( $details );
	}

	// Only the blank whitespace nodes are still attached to $root at this
	// point — every question and every answer node was already detached by
	// the appendChild() calls above. Removing them first, THEN inserting the
	// container, means $faq_heading->nextSibling below is guaranteed to be
	// exactly "whatever comes right after the FAQ run," with no leftover
	// blank node in between.
	foreach ( $blank_nodes as $blank_node ) {
		if ( $blank_node->parentNode === $root ) {
			$root->removeChild( $blank_node );
		}
	}

	$root->insertBefore( $faq_container, $faq_heading->nextSibling );
}

/* -------------------------------------------------------------------------
 * (c) Inline tool CTA — a contextual calculator card after the article's
 * first section.
 * ---------------------------------------------------------------------- */

/**
 * The second top-level H2 in $root, or null when there are fewer than two.
 *
 * @param DOMElement $root
 * @return DOMElement|null
 */
function tpm_dom_second_top_level_h2( DOMElement $root ) {
	$count = 0;
	foreach ( $root->childNodes as $child ) {
		if ( $child instanceof DOMElement && 'h2' === $child->tagName ) {
			++$count;
			if ( 2 === $count ) {
				return $child;
			}
		}
	}
	return null;
}

/**
 * Every unique URL PATH (scheme/host ignored, leading/trailing slashes
 * trimmed) that an `<a href>` anywhere in $root links to, as a set (values
 * are all `true`; only key membership matters) for O(1) lookup by
 * tpm_dom_content_already_links_url() below.
 *
 * MUST be called against $root BEFORE any of this file's three mutators
 * run — see tpm_prepare_article_content()'s call site and file docblock.
 * tpm_dom_extract_lead_paragraph() (mutator (a)) detaches the lead
 * paragraph from $root; a link that paragraph alone carried (the live
 * /pet-guides/pet-costs/ shape: its lead links /tools/pet-cost-calculator/
 * — CLAUDE.md §11) would be invisible to a scan taken after that point.
 * Capturing the set once, up front, against the still-complete tree, means
 * mutator (c)'s dedup check is correct regardless of what (a) or (b) go on
 * to remove or move — it never needs to re-derive "what's linked" from
 * whatever's left of $root by the time it runs.
 *
 * Matched by PATH only, the same tolerant comparison
 * inc/internal-links.php's tpm_normalize_internal_path() uses, reimplemented
 * locally rather than called directly so this file has no load-order
 * dependency on inc/internal-links.php loading first (this file's own
 * position in functions.php's require chain isn't this file's to decide —
 * see the "decisions" note this task reports).
 *
 * @param DOMElement $root The parsed article body's root element, BEFORE any mutator has run.
 * @return array<string,bool>
 */
function tpm_dom_collect_linked_paths( DOMElement $root ) {
	$paths = array();

	foreach ( $root->getElementsByTagName( 'a' ) as $anchor ) {
		$href = $anchor->getAttribute( 'href' );
		if ( '' === trim( $href ) ) {
			continue;
		}

		$path = trim( (string) wp_parse_url( $href, PHP_URL_PATH ), '/' );
		if ( '' !== $path ) {
			$paths[ $path ] = true;
		}
	}

	return $paths;
}

/**
 * Whether $linked_paths (a pre-collected set — see
 * tpm_dom_collect_linked_paths(), which MUST have run before mutator (a)
 * for this to be trustworthy) already contains $target_url's normalized
 * path.
 *
 * Several pages already link a calculator inline in their own copy
 * (CLAUDE.md §13); a second CTA for the same tool a paragraph later reads
 * as spam, not help, so this check runs before ever building the card.
 *
 * @param array  $linked_paths Set built by tpm_dom_collect_linked_paths().
 * @param string $target_url   The tool's URL.
 * @return bool
 */
function tpm_dom_content_already_links_url( array $linked_paths, $target_url ) {
	$target_path = trim( (string) wp_parse_url( $target_url, PHP_URL_PATH ), '/' );
	if ( '' === $target_path ) {
		return false; // Nothing meaningful to compare against; never block the CTA on an empty path.
	}

	return isset( $linked_paths[ $target_path ] );
}

/**
 * Build the tool CTA card's markup as an HTML string, every dynamic value
 * escaped for its context before interpolation.
 *
 * The Tailwind classes below are all literal text in this format string —
 * never assembled by concatenating a variable in — so tailwind.config.js's
 * content scanner (which greps theme .php files for class-name-shaped
 * substrings; it does not execute PHP) can see every one of them, per the
 * hard rule every worker on this build follows.
 *
 * @param string $title       Tool title, e.g. "Dog Age Calculator".
 * @param string $description Tool one-line description.
 * @param string $url         Tool page URL.
 * @param string $icon_name   A key from inc/icons.php's tpm_icon() map.
 * @return string
 */
function tpm_dom_tool_cta_html( $title, $description, $url, $icon_name ) {
	ob_start();
	tpm_icon( $icon_name, 'h-5 w-5' );
	$icon_svg = ob_get_clean();

	ob_start();
	tpm_icon( 'arrow-right', 'h-4 w-4' );
	$arrow_svg = ob_get_clean();

	$cta_label = sprintf(
		/* translators: %s: the tool's title, e.g. "Dog Age Calculator". */
		__( 'Open the %s', 'thepawmanual' ),
		$title
	);

	return sprintf(
		'<aside class="tpm-tool-cta my-8 flex items-start gap-4 rounded-2xl border border-brand-100 bg-brand-50 p-5">'
			. '<span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-white text-brand-600 shadow-sm">%1$s</span>'
			. '<div class="min-w-0 flex-1">'
				. '<p class="font-geist text-xs font-semibold uppercase tracking-wide text-brand-600">%2$s</p>'
				. '<p class="mt-1 font-geist text-base"><strong class="font-semibold text-neutral-900">%3$s</strong></p>'
				. '<p class="mt-1 font-geist text-sm text-neutral-600">%4$s</p>'
				. '<a href="%5$s" class="mt-3 inline-flex items-center gap-1.5 font-geist text-sm font-semibold text-brand-700 hover:text-brand-900 hover:underline">'
					. '<span>%6$s</span>%7$s'
				. '</a>'
			. '</div>'
		. '</aside>',
		$icon_svg, // Trusted, static SVG markup from tpm_icon() — same already-escaped-by-construction trust level icons.php's own output carries.
		esc_html__( 'Free tool', 'thepawmanual' ),
		esc_html( $title ),
		esc_html( $description ),
		esc_url( $url ),
		esc_html( $cta_label ),
		$arrow_svg
	);
}

/**
 * Insert a contextual tool CTA card immediately before the article's second
 * top-level H2 — the end of the first section, where a reader has just got
 * the gist and a calculator is genuinely the next useful step.
 *
 * tpm_get_inline_tool_for_post() (inc/tools-data.php) is owned by a
 * different worker on this build; function_exists() guards the call so a
 * load-order surprise (this file's own require_once position in
 * functions.php is not this file's to set — see "decisions") degrades to
 * "no CTA" instead of a fatal error taking down every article page.
 *
 * @param DOMDocument $dom          The owning document (for createElement() calls).
 * @param DOMElement  $root         The parsed article body's root element (already past mutators (a) and (b) by the time this runs).
 * @param array       $linked_paths Set from tpm_dom_collect_linked_paths(), captured BEFORE mutator (a) ran — see that function's docblock for why $root alone is no longer enough by this point.
 * @param int         $post_id      The post/Page ID to look up a tool for.
 */
function tpm_dom_inject_inline_tool_cta( DOMDocument $dom, DOMElement $root, array $linked_paths, $post_id ) {
	if ( ! function_exists( 'tpm_get_inline_tool_for_post' ) ) {
		return;
	}

	$tool = tpm_get_inline_tool_for_post( $post_id );
	if ( empty( $tool ) || ! is_array( $tool ) ) {
		return; // No BUILT+PUBLISHED tool scored a real match for this page.
	}

	$title = isset( $tool['title'] ) ? (string) $tool['title'] : '';
	$url   = isset( $tool['url'] ) ? (string) $tool['url'] : '';
	if ( '' === trim( $title ) || '' === trim( $url ) ) {
		return; // Malformed tool row — nothing safe to link to.
	}

	$second_h2 = tpm_dom_second_top_level_h2( $root );
	if ( ! $second_h2 ) {
		return; // Fewer than two H2s — nowhere sensible for a CTA to sit.
	}

	if ( 'frequently asked questions' === tpm_dom_normalize_heading_text( $second_h2->textContent ) ) {
		return; // The second H2 IS the FAQ heading — never inside or after it.
	}

	if ( tpm_dom_content_already_links_url( $linked_paths, $url ) ) {
		return; // Already linked inline in the article's own copy — including in the lead paragraph, which is no longer in $root by this point.
	}

	$description = isset( $tool['description'] ) ? (string) $tool['description'] : '';
	$icon_name   = isset( $tool['icon'] ) && is_string( $tool['icon'] ) && '' !== $tool['icon'] ? $tool['icon'] : 'calculator';

	$card_html = tpm_dom_tool_cta_html( $title, $description, $url, $icon_name );
	$fragment  = tpm_dom_fragment_from_html( $dom, $card_html );
	if ( ! $fragment ) {
		return; // Parse trouble building the card — skip the CTA, not the article.
	}

	$root->insertBefore( $fragment, $second_h2 );
}