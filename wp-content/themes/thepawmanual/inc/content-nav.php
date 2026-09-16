<?php
/**
 * Content-hierarchy helpers.
 *
 * Backs the hub-vs-article branch in page.php: hub/child-page lookups,
 * sibling ("related reading") navigation, the visible last-updated date, the
 * table-of-contents generator, and the emergency-callout lookup. Kept
 * together in one file because they all answer the same question — "where
 * does this Page sit in the ~110-page hierarchy, and what should link to it?"
 *
 * The internal-link *degrading* subsystem (rewriting post-body links to
 * unpublished Pages into plain text) used to live here too, growing out of
 * tpm_get_published_page_url() below, but outgrew this file's actual
 * subject once post-body content needed the same protection — see
 * inc/internal-links.php, split out 2026-08-14.
 *
 * @package thepawmanual
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Direct child Pages of a given Page, published, in display order.
 *
 * Deliberately `parent` (direct children only) rather than `child_of` (every
 * descendant) — the hub grid is one level deep. A grandchild gets its own
 * hub's grid when a visitor drills down into it.
 *
 * @param int $post_id Parent Page ID.
 * @return WP_Post[]
 */
function tpm_get_child_pages( $post_id ) {
	return get_pages(
		array(
			'parent'      => $post_id,
			'sort_column' => 'menu_order, post_title',
			'sort_order'  => 'ASC',
			'post_status' => 'publish',
		)
	);
}

/**
 * Whether a Page is a hub — i.e. has at least one published child Page.
 *
 * A page with children is a hub; a page without is an article (task brief,
 * "Hub vs. article layout").
 *
 * @param int $post_id Page ID.
 * @return bool
 */
function tpm_is_hub_page( $post_id ) {
	return ! empty( tpm_get_child_pages( $post_id ) );
}

/**
 * Sibling Pages in the same section, excluding the current Page — the
 * "related reading" set at the foot of every article.
 *
 * Same sort as the hub grid, so the section's link graph reads identically
 * whether a visitor arrives from the hub or from a sibling article: a
 * stable, deterministic order rather than a "related" heuristic that would
 * reshuffle on every request.
 *
 * @param int $post_id Current Page ID.
 * @param int $limit   Maximum siblings to return.
 * @return WP_Post[]
 */
function tpm_get_sibling_pages( $post_id, $limit = 6 ) {
	$post = get_post( $post_id );
	if ( ! $post || 0 === (int) $post->post_parent ) {
		return array();
	}

	$siblings = get_pages(
		array(
			'parent'      => $post->post_parent,
			'sort_column' => 'menu_order, post_title',
			'sort_order'  => 'ASC',
			'post_status' => 'publish',
			'exclude'     => array( $post_id ),
		)
	);

	return array_slice( $siblings, 0, max( 0, (int) $limit ) );
}

/**
 * The other top-level CONTENT hubs — the "related reading" fallback for any
 * Page with no parent at all, where tpm_get_sibling_pages() above always
 * returns nothing (its own `post_parent === 0` guard).
 *
 * Ten of the eighteen live URLs have no parent: the four content hubs
 * themselves (/dogs/, /cats/, /pet-guides/, /tools/) AND the six top-level
 * trust pages (/about/, /contact/, /terms/, /privacy-policy/,
 * /affiliate-disclosure/, /editorial-process/) — post_parent is 0 for both
 * groups alike, so nothing distinguishes them by that field. Without this
 * fallback every one of those ten pages ends its bottom related-links block
 * with nothing at all (user requirement 2, CLAUDE.md §13).
 *
 * Hardcoded to the four content-hub SLUGS on purpose, not "every top-level
 * Page": if this instead queried `get_pages( array( 'parent' => 0, … ) )`,
 * /dogs/ would end up carding "Privacy Policy" and "Terms" as "related
 * reading" — a category error a human editor would never make. This block
 * exists to distribute PageRank across the site's actual content sections,
 * the same job tpm_get_child_pages() does one level down.
 *
 * Each candidate is resolved through tpm_get_published_page_url()
 * (inc/internal-links.php) first — the SAME "not live yet this wave" check
 * search.php's own empty state already applies to this identical four-hub
 * list — so an unshipped hub produces no card, never a link to a 404.
 *
 * @param int $exclude_post_id The current Page's ID, left out of its own list.
 * @return WP_Post[]
 */
function tpm_get_top_level_hub_siblings( $exclude_post_id ) {
	$hub_slugs = array( 'dogs', 'cats', 'pet-guides', 'tools' );

	$hubs = array();
	foreach ( $hub_slugs as $slug ) {
		if ( ! tpm_get_published_page_url( $slug ) ) {
			continue; // Not live yet this wave.
		}

		$hub = get_page_by_path( $slug );
		if ( $hub && (int) $hub->ID !== (int) $exclude_post_id ) {
			$hubs[] = $hub;
		}
	}

	return $hubs;
}

/**
 * A visible "Updated <date>" / "Published <date>" label for article pages
 * (content/SEO-STRATEGY.md §1 item 0.9 — health content especially needs a
 * visible last-updated date).
 *
 * WordPress bumps post_modified on every save — including a typo fix or a
 * category change — so a raw modified-date would flip on edits that have
 * nothing to do with the content's currency. Only call it "updated" once the
 * modified date is a full day past the publish date; same-day edits during
 * initial drafting/QA don't count as a content refresh, so those pages show
 * their publish date instead.
 *
 * @param int $post_id Page ID.
 * @return string Plain text, ready for esc_html() at the call site.
 */
function tpm_get_last_updated_label( $post_id ) {
	$published = (int) get_the_date( 'U', $post_id );
	$modified  = (int) get_the_modified_date( 'U', $post_id );

	$meaningfully_updated = ( $modified - $published ) > DAY_IN_SECONDS;

	if ( $meaningfully_updated ) {
		return sprintf(
			/* translators: %s: date the page was last updated. */
			__( 'Updated %s', 'thepawmanual' ),
			get_the_modified_date( get_option( 'date_format' ), $post_id )
		);
	}

	return sprintf(
		/* translators: %s: date the page was published. */
		__( 'Published %s', 'thepawmanual' ),
		get_the_date( get_option( 'date_format' ), $post_id )
	);
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
 * Reproduced by replaying all 17 wave-1 page bodies through the real
 * `the_content` pipeline and feeding the pre-decode comparison the exact
 * protected/serialized string pair THIS guard receives: it never actually
 * false-fires here (0 of 17), because by the time this pass runs, the
 * content has already been through content-blocks.php's own DOMDocument
 * round-trip once (page.php runs that pass first — see its own docblock for
 * why the order is load-bearing), which already decoded every entity this
 * guard would otherwise be tripped by. content-blocks.php's OWN twin guard
 * is the one that measurably false-fires (7 of 17) — see that function's
 * docblock for the methodology and numbers. The fix below is still applied
 * here too, on principle rather than a reproduced count of its own: this
 * function's only production call site (page.php) happens to always follow
 * content-blocks.php's pass, but nothing in this function's own signature
 * requires that — a future caller handing it raw, undecoded content
 * directly would hit the exact false alarm this file's twin already proved
 * is real. html_entity_decode() undoes that decode-asymmetry on
 * BOTH sides — applying it to only one side would just move the false
 * positive to the other direction — and stripping whitespace entirely
 * (not collapsing to a single space; nothing here needs to stay readable,
 * only comparable) absorbs wpautop's incidental newline/indentation
 * differences between the pre-parse and re-serialized markup.
 *
 * Twin of tpm_content_blocks_normalize_for_length_comparison() in
 * inc/content-blocks.php's tpm_content_blocks_lost_too_much_text() —
 * reimplemented locally, not called from that file, for the same
 * load-order-independence reason given a few lines below in
 * tpm_inject_heading_ids()'s own docblock (that function reimplements the
 * loss-guard rather than depending on content-blocks.php loading first).
 * Keep the two in sync if either changes.
 *
 * @param string $html
 * @return string
 */
function tpm_content_nav_normalize_for_length_comparison( $html ) {
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
 * This function only stamps `id` attributes — it never removes a node — so
 * the serialized output should carry essentially the same amount of text as
 * the script-protected input it was built from. libxml's HTML5 recovery can
 * still close the scaffolding `<div>` early on unbalanced markup (a stray
 * `</div>` from a hand-edit) without throwing anything: `loadHTML()` still
 * returns true and `documentElement` is still the expected 'div', so the
 * guard right below this function's caller never catches it, and everything
 * after the break is silently absent from what gets serialized (CLAUDE.md
 * §11, 2026-08-14 pre-deploy review — reproduced directly, not theoretical).
 *
 * Both sides MUST go through tpm_content_nav_normalize_for_length_comparison()
 * before counting, not just wp_strip_all_tags() — see that function's
 * docblock for the reproduced numbers (7 of 17 wave-1 pages false-fire on
 * content-blocks.php's twin guard without entity decoding; this guard's own
 * production call order happens to never see the raw, undecoded input that
 * would trip it, but is fixed the same way on principle, not a measured
 * count). Without entity decoding this guard false-positives on ordinary,
 * lossless entity round-tripping, which is a false alarm strictly worse than
 * the bug it exists to catch: it silently strips every H2's id and empties
 * the table of contents on pages that parsed perfectly fine.
 *
 * @param string $protected_content The script-protected HTML fed into DOMDocument::loadHTML().
 * @param string $serialized        The content this pass is about to return in its place.
 * @return bool True when $serialized is missing a suspicious amount of text.
 */
function tpm_content_nav_lost_too_much_text( $protected_content, $serialized ) {
	$before = mb_strlen( tpm_content_nav_normalize_for_length_comparison( $protected_content ) );
	if ( 0 === $before ) {
		return false; // Nothing to lose.
	}

	$after = mb_strlen( tpm_content_nav_normalize_for_length_comparison( $serialized ) );

	// The larger of 20 characters or 2% absorbs any residual noise this
	// normalization doesn't already account for, without masking a genuine
	// truncation.
	$tolerance = max( 20, (int) ceil( $before * 0.02 ) );

	return ( $after + $tolerance ) < $before;
}

/**
 * Whether $node has an ancestor element carrying $class as one of its
 * class-attribute tokens, walking parentNode upward no further than
 * $boundary (inclusive).
 *
 * Token-matched against `explode( ' ', ... )`, not a substring check —
 * `strpos( $classes, 'pm-calc' )` would also match a hypothetical
 * "pm-calc-wide" or "not-pm-calc" class and skip a heading that has
 * nothing to do with the calculator widget. Bounded by $boundary (always
 * $root, the parse's own wrapping scaffold `<div>` — see
 * tpm_inject_heading_ids()) rather than walking to ownerDocument, so a
 * malformed tree can never turn this into an unbounded loop.
 *
 * @param DOMNode    $node     Element to check the ancestors of.
 * @param string     $class    A single class name (no leading dot, no combinators).
 * @param DOMElement $boundary Stop walking once this ancestor is reached.
 * @return bool
 */
function tpm_dom_has_ancestor_class( DOMNode $node, $class, DOMElement $boundary ) {
	$parent = $node->parentNode;

	while ( $parent instanceof DOMElement ) {
		if ( $parent->hasAttribute( 'class' ) ) {
			$classes = preg_split( '/\s+/', trim( $parent->getAttribute( 'class' ) ) );
			if ( in_array( $class, $classes, true ) ) {
				return true;
			}
		}

		if ( $parent === $boundary ) {
			break;
		}

		$parent = $parent->parentNode;
	}

	return false;
}

/**
 * Parse rendered post content, stamp an `id` on every H2, and collect a
 * table-of-contents list.
 *
 * Done in PHP against the server-rendered markup — not a JS-built TOC —
 * because a JS-only TOC produces anchors that only exist once a script has
 * run, which is worse for both crawlers and no-JS readers (task brief, item
 * 3). DOMDocument is guarded throughout: a missing extension or malformed
 * input falls back to the original content with an empty TOC, never a fatal.
 *
 * `<script>` blocks (the FAQ JSON-LD every page carries) are pulled out
 * before the parse and spliced back in verbatim afterwards — see
 * tpm_extract_script_blocks() (inc/script-protection.php) for why that has
 * to happen. That helper fails CLOSED, not open: if it can't prove every
 * `<script>` block was safely extracted — most plausibly an unclosed
 * `<script>` left by a hand-edit — this function takes the same fallback
 * it uses for a missing DOMDocument or empty content: return the original,
 * completely unparsed, with an empty TOC. A page rendering without a TOC
 * is a harmless, silent degrade; a page that stops rendering halfway
 * through — the original bug this whole mechanism exists to prevent — is
 * not, so this never trades one for the other.
 *
 * @param string $content Fully filtered post content (i.e. already run
 *                         through the `the_content` filter — shortcodes
 *                         executed, wpautop applied — so the H2s found here
 *                         match what the browser actually renders).
 * @return array{content:string,items:array<int,array{id:string,text:string}>}
 */
function tpm_inject_heading_ids( $content ) {
	$fallback = array(
		'content' => $content,
		'items'   => array(),
	);

	if ( '' === trim( (string) $content ) || ! class_exists( 'DOMDocument' ) ) {
		return $fallback;
	}

	list( $protected_content, $script_blocks, $scripts_protected ) = tpm_extract_script_blocks( $content );

	if ( ! $scripts_protected ) {
		return $fallback;
	}

	$dom = new DOMDocument();

	// The XML-prolog trick tells libxml the source is UTF-8 without an actual
	// <?xml> node ending up in the parsed tree. LIBXML_HTML_NOIMPLIED stops
	// DOMDocument silently wrapping the fragment in <html><body>, so the
	// wrapping <div> below ends up as the document's sole root element;
	// NODEFDTD drops the doctype libxml would otherwise inject.
	libxml_use_internal_errors( true );
	$loaded = $dom->loadHTML(
		'<?xml encoding="utf-8" ?><div>' . $protected_content . '</div>',
		LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
	);
	libxml_clear_errors();

	if ( ! $loaded || ! $dom->documentElement || 'div' !== $dom->documentElement->tagName ) {
		return $fallback;
	}

	$root     = $dom->documentElement;
	$items    = array();
	$used_ids = array();

	// getElementsByTagName() is a live list, but this loop only sets an
	// attribute — it never adds/removes nodes — so iterating it directly is
	// safe.
	foreach ( $root->getElementsByTagName( 'h2' ) as $heading ) {
		// getElementsByTagName() walks the WHOLE subtree, not just $root's
		// direct children — so it also finds the [pawmanual_calculator]
		// shortcode's own <h2 class="pm-calc__title"> a few levels down
		// inside .pm-calc (plugin: templates/calculator-shell.php). That
		// heading titles the CALCULATOR WIDGET, not a section of the
		// article, so it has no business in a table of contents that lists
		// the article's own sections — skip anything with a .pm-calc
		// ancestor, bounded at $root so this can never walk past the
		// parse's own scaffolding div.
		if ( tpm_dom_has_ancestor_class( $heading, 'pm-calc', $root ) ) {
			continue;
		}

		$text = trim( $heading->textContent );
		if ( '' === $text ) {
			continue;
		}

		$id = sanitize_title( $text );
		if ( '' === $id ) {
			$id = 'section';
		}

		// Disambiguate repeated headings (two "FAQ"s, etc.) rather than
		// emitting duplicate ids.
		$base_id = $id;
		$suffix  = 2;
		while ( isset( $used_ids[ $id ] ) ) {
			$id = $base_id . '-' . $suffix;
			++$suffix;
		}
		$used_ids[ $id ] = true;

		$heading->setAttribute( 'id', $id );
		$items[] = array(
			'id'   => $id,
			'text' => $text,
		);
	}

	$html = '';
	foreach ( $root->childNodes as $child ) {
		$html .= $dom->saveHTML( $child );
	}

	// Fail closed on a truncating parse: see tpm_content_blocks_lost_too_much_text()
	// in inc/content-blocks.php for the full reasoning (libxml's HTML5 recovery
	// closes an unbalanced wrapper EARLY, without throwing, on a stray closing
	// tag in hand-edited content, and everything after the break silently never
	// makes it into $root's children). Reimplemented locally, not called from
	// that file, for the same reason tpm_dom_content_already_links_url()
	// (inc/content-blocks.php) reimplements its own path comparison rather than
	// depending on inc/internal-links.php: this function has no load-order
	// dependency on content-blocks.php, and none is worth introducing for a
	// dozen-line guard.
	if ( tpm_content_nav_lost_too_much_text( $protected_content, $html ) ) {
		return $fallback;
	}

	return array(
		'content' => tpm_restore_script_blocks( $html, $script_blocks ),
		'items'   => $items,
	);
}

/**
 * Emergency-callout target for the current Page, if one applies.
 *
 * The two vet-signs pages are deliberately the most-linked pages on the site
 * (content/README.md, editorial constitution rule 3). Rather than hardcode
 * their Page IDs, the species and applicability are derived from the URL
 * hierarchy itself: any Page under /dogs/health/ links to the dog vet-signs
 * page, any Page under /cats/health/ links to the cat one, and the two
 * target pages never link to themselves.
 *
 * @param int $post_id Current Page ID.
 * @return array{url:string,title:string,species:string}|null
 */
function tpm_get_emergency_callout( $post_id ) {
	$post = get_post( $post_id );
	if ( ! $post ) {
		return null;
	}

	$ancestor_ids = get_post_ancestors( $post );
	if ( empty( $ancestor_ids ) ) {
		return null;
	}

	$ancestor_slugs = array();
	foreach ( $ancestor_ids as $ancestor_id ) {
		$ancestor_slugs[] = get_post_field( 'post_name', $ancestor_id );
	}

	if ( ! in_array( 'health', $ancestor_slugs, true ) ) {
		return null;
	}

	// get_post_ancestors() is nearest-parent-first, so the top-level
	// ancestor (the species hub — "dogs" or "cats") is the last slug.
	$species_slug = end( $ancestor_slugs );

	$species_map = array(
		'dogs' => 'dog',
		'cats' => 'cat',
	);
	if ( ! isset( $species_map[ $species_slug ] ) ) {
		return null;
	}
	$species = $species_map[ $species_slug ];

	$target_slug = 'signs-your-' . $species . '-needs-a-vet';

	// The vet-signs pages never callout-link to themselves.
	if ( $post->post_name === $target_slug ) {
		return null;
	}

	$target = get_page_by_path( $species_slug . '/health/' . $target_slug );
	if ( ! $target ) {
		return null;
	}

	return array(
		'url'     => get_permalink( $target ),
		'title'   => get_the_title( $target ),
		'species' => $species,
	);
}
