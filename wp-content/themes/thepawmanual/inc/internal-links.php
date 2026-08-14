<?php
/**
 * Internal-link degrading.
 *
 * Extends CLAUDE.md §13's rule — "a page links only once its target is
 * actually published, so partial wave publishing never produces a dead
 * link" — from hardcoded template links (tpm_get_published_page_url(),
 * below, used e.g. by footer.php and template-parts/home/seo-content.php)
 * to imported post BODY content. Body content ships wholesale from
 * markdown (tools/wxr/) and links freely across the full ~127-page set
 * regardless of which wave has actually published — after wave 1's 17
 * pages, 201 of the 262 internal links in that content point at pages that
 * don't exist yet, and 5 point at calculator pages no wave ever creates.
 * `tpm_filter_internal_links()`, hooked on `the_content` at the bottom of
 * this file, rewrites those into plain text so a wave-1 visitor never
 * clicks through to a 404 — and because it re-evaluates on every request
 * against live Page state, a later wave's links go live automatically with
 * no re-import and no template edit.
 *
 * Split out from inc/content-nav.php (2026-08-14): what started as one
 * helper function for hardcoded template links grew, once post-body
 * content needed the same protection, into a self-contained subsystem —
 * path map, scope detection, the regex rewrite, and the `the_content`
 * filter itself — that no longer has much to do with content-nav.php's
 * actual subject (the hub/article page-template layout: child grids,
 * sibling nav, the TOC). Two different concerns that happened to share a
 * file only because the second one grew out of a function in the first.
 *
 * Depends on inc/script-protection.php for keeping `<script>` (FAQ
 * JSON-LD) blocks out of the link-rewrite pass — see
 * tpm_filter_internal_links() below for why that matters here too, not
 * just for the DOMDocument pass in content-nav.php. functions.php lists
 * script-protection.php ahead of this file for readability, but the
 * dependency isn't load-order-sensitive: the calls into it happen inside
 * function bodies at render time, long after every require_once has run.
 *
 * @package thepawmanual
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Every published Page's hierarchical path (no leading/trailing slash, e.g.
 * `dogs/health/allergies`) mapped to its permalink — built once and memoized
 * for the rest of the request.
 *
 * This is the one mechanism both `tpm_get_published_page_url()` (below, for
 * hardcoded template links) and `tpm_filter_internal_links()` (for post
 * body content) resolve "does this Page exist and is it published?"
 * against — a single get_pages() call rather than a get_page_by_path()
 * lookup per link. /dogs/ alone links to 52 other pages
 * (content/SEO-STRATEGY.md, wave-1 launch page), and a per-link DB lookup
 * would mean ~50 extra queries on that one page view.
 *
 * That single get_pages() call is also the *only* query this adds: WP_Query
 * (which get_pages() uses internally, wp-includes/post.php) caches every
 * post it returns via `update_post_caches()` when `cache_results` is true —
 * the get_pages() default — so every `get_post( $id )` the ancestor walk
 * inside get_permalink() → get_page_uri() → get_post_ancestors() makes below
 * for one of these same IDs is a runtime cache hit, not a fresh SELECT
 * (verified against wp-includes/class-wp-query.php and wp-includes/post.php
 * in the LocalWP install, not assumed).
 *
 * Every non-trashed status is fetched — not just 'publish' — because a
 * published Page's path must still be built correctly even when one of its
 * *ancestors* happens to be a draft; `get_page_by_path()` (what
 * tpm_get_published_page_url() used to call directly, and what
 * tpm_get_emergency_callout() in content-nav.php still uses for a single,
 * one-off lookup) has never cared about ancestor status, only the target's
 * own. Filtering the get_pages() query down to 'publish' would both
 * miscompute those paths and — because of how get_pages() prunes
 * hierarchical results to branches it can trace back to a status-matched
 * root — could silently drop the published descendant from the result set
 * entirely. `hierarchical => false` sidesteps that pruning: it's a flat,
 * complete list, and the hierarchy is walked here, in memory, instead.
 *
 * KNOWN LIMITATION — path-keyed, not ID-keyed: a body link is a literal
 * href string baked in once, at import time, against whatever URL its
 * target had *then*. If that Page is later moved to a different parent —
 * plausible across a 9-wave rollout with room for editorial reorganisation
 * (content/SEO-STRATEGY.md) — the map below is keyed on the Page's CURRENT
 * path, so the old href silently stops matching. The link degrades to
 * plain text and STAYS degraded permanently, even though the target is
 * live at its new URL — there's no memory of the page's former location to
 * fall back to, and no re-crawl will fix it on its own. Fixing this
 * properly would mean resolving and storing target Page IDs at import time
 * (tools/wxr/) rather than plain hrefs in the markdown, which is out of
 * this function's scope and out of scope for this fix; flagging it here so
 * a future reorganisation doesn't get diagnosed as a re-emergence of
 * Defect A instead of this known, separate limitation.
 *
 * KNOWN CEILING — full post rows, memoized per request only: this
 * get_pages() call fetches every non-trashed Page row in FULL — including
 * post_content — into PHP memory on any request that renders post content,
 * and the `static` memoization only holds for that one request's lifetime;
 * there is no persistent cache, so the next request pays the same query
 * again. At the ~127 pages CLAUDE.md §13 describes this is a rounding
 * error. CLAUDE.md §1 plans programmatic SEO (auto-generated pages from a
 * data source) toward a much larger page count — at that scale this
 * becomes worth caching properly: either a transient holding just the
 * id/name/parent/status tuples this function actually needs (not full
 * WP_Post objects — post_content specifically is dead weight here), or the
 * persistent object cache, invalidated on `save_post` for the 'page' post
 * type so a publish/unpublish/move is reflected immediately instead of
 * waiting out a TTL. Not implemented here — 127 pages doesn't need it yet,
 * and this is a note for whoever builds the programmatic-SEO phase, not a
 * bug in the POC.
 *
 * @return array<string,string> path => permalink, published Pages only.
 */
function tpm_get_published_page_paths() {
	static $paths = null;

	if ( null !== $paths ) {
		return $paths;
	}

	$paths = array();

	$pages = get_pages(
		array(
			'post_type'    => 'page',
			'post_status'  => array( 'publish', 'draft', 'pending', 'private', 'future' ),
			'hierarchical' => false,
			'number'       => 0,
			'sort_column'  => 'ID',
		)
	);

	if ( empty( $pages ) ) {
		return $paths;
	}

	// Indexed by ID so the ancestor walk below is an in-memory lookup, not a
	// second query per page.
	$pages_by_id = array();
	foreach ( $pages as $page ) {
		$pages_by_id[ $page->ID ] = $page;
	}

	foreach ( $pages as $page ) {
		if ( 'publish' !== $page->post_status ) {
			continue; // Only unpublished as a *link target*; still walked as an ancestor above.
		}

		$segments = array( $page->post_name );
		$ancestor = $page;
		while ( $ancestor->post_parent && isset( $pages_by_id[ $ancestor->post_parent ] ) ) {
			$ancestor = $pages_by_id[ $ancestor->post_parent ];
			array_unshift( $segments, $ancestor->post_name );
		}

		$paths[ implode( '/', $segments ) ] = get_permalink( $page );
	}

	return $paths;
}

/**
 * Permalink for a published Page at a given path, or null.
 *
 * Used anywhere a template needs to hardcode a link to a Page that isn't
 * guaranteed to exist yet — e.g. footer.php's "Pet Emergency?" links to the
 * two vet-signs pages, which ship in wave 3 (dogs) and wave 6 (cats) of the
 * content rollout (content/SEO-STRATEGY.md §4), long after wave 1 launches.
 * Resolved against tpm_get_published_page_paths() — the same "never link
 * what doesn't exist yet" rule tpm_get_emergency_callout() (content-nav.php)
 * already follows, now backed by the same one-query-per-request map post
 * body links use too, rather than a second, separate lookup mechanism.
 *
 * @param string $path Page path relative to site root, no leading/trailing slash (e.g. 'dogs/health/signs-your-dog-needs-a-vet').
 * @return string|null
 */
function tpm_get_published_page_url( $path ) {
	$paths = tpm_get_published_page_paths();
	$path  = trim( $path, '/' );

	return isset( $paths[ $path ] ) ? $paths[ $path ] : null;
}

/**
 * Whether an `href` value is a link to this site, as opposed to an external
 * site, an in-page `#anchor`, or a `mailto:`/`tel:` link.
 *
 * Only the two forms content/README.md's linking rule actually produces:
 * root-relative (`/dogs/health/allergies/`) and absolute-on-this-host
 * (`https://thepawmanual.com/dogs/`). Everything else — `#anchor` (no host,
 * doesn't start with `/`), `mailto:`/`tel:` (a non-http(s) scheme), another
 * site's absolute URL, or a protocol-relative link to another host
 * (`//evil.example/x` — parses to a host, so it does not fall through to the
 * "no host = root-relative" branch) — is deliberately left alone.
 *
 * @param string $href      Raw href attribute value from the markup.
 * @param string $site_host This site's host, e.g. 'thepawmanual.com'.
 * @return bool
 */
function tpm_href_is_site_internal( $href, $site_host ) {
	$href = trim( (string) $href );
	if ( '' === $href || '#' === $href[0] ) {
		return false;
	}

	$scheme = wp_parse_url( $href, PHP_URL_SCHEME );
	if ( $scheme && ! in_array( strtolower( $scheme ), array( 'http', 'https' ), true ) ) {
		return false; // mailto:, tel:, javascript:, etc.
	}

	$host = wp_parse_url( $href, PHP_URL_HOST );
	if ( empty( $host ) ) {
		// No host component at all: a root-relative path is internal;
		// anything else with no host (a scheme-less fragment that slipped
		// past the '#' check above, e.g. "search?q=x") is not a page link
		// this map can resolve.
		return '/' === $href[0];
	}

	return 0 === strcasecmp( $host, (string) $site_host );
}

/**
 * Reduce a site-internal href down to the bare path tpm_get_published_page_paths()
 * keys its map by: no scheme/host, no leading/trailing slash, no query
 * string or `#fragment`.
 *
 * @param string $href Href already confirmed site-internal by tpm_href_is_site_internal().
 * @return string
 */
function tpm_normalize_internal_path( $href ) {
	$path = (string) wp_parse_url( $href, PHP_URL_PATH );
	return trim( $path, '/' );
}

/**
 * Tokenize a raw (unparsed) HTML attribute run into name => value pairs.
 *
 * Quote-aware in the same way the tag-matching patterns in this file and in
 * script-protection.php are — `"[^"]*"` / `'[^']*'` consumed as one atomic
 * unit ahead of the bare-value fallback — so a quoted value can never be
 * mistaken for the start of a new attribute. This is the ONE scan any
 * attribute-name lookup in this file should go through, rather than a
 * free-floating regex searching the raw blob for something name-shaped:
 * an evaluator confirmed `\bhref` (a word-boundary, not an
 * attribute-boundary, assertion) matches inside `data-href` (the `-`→`h`
 * transition is a word boundary too) and inside a decoy VALUE like
 * `data-tracking="href=/x/"`, in both cases evaluating the wrong href
 * entirely - a live dead link in the first case, a wrongly-deleted `<a>`
 * with no real href at all in the second. Tokenizing into real
 * name/value pairs first and then looking `href` up by exact key makes
 * both impossible: `data-href` and `href` are different keys, and text
 * inside an already-consumed quoted value is never re-scanned for
 * attribute-shaped substrings. It also makes the result attribute-order
 * independent, which the ad hoc regex wasn't.
 *
 * `PREG_UNMATCHED_AS_NULL` is what makes `name=""` distinguishable from
 * "that value alternative didn't match" — without it, an intentionally
 * empty double-quoted value and "the double-quoted branch never matched"
 * would be indistinguishable, and the wrong branch could win.
 *
 * @param string $attrs Raw attribute text between the tag name and its closing `>`.
 * @return array<string,string> Lower-cased attribute name => value. A bare boolean attribute (e.g. `disabled`) maps to ''. First occurrence of a repeated name wins, matching browser behaviour.
 */
function tpm_tokenize_attributes( $attrs ) {
	$pairs = array();

	preg_match_all(
		'#([^\s"\'=/>]+)(?:\s*=\s*(?:"([^"]*)"|\'([^\']*)\'|([^\s>]*)))?#',
		(string) $attrs,
		$matches,
		PREG_SET_ORDER | PREG_UNMATCHED_AS_NULL
	);

	foreach ( $matches as $match ) {
		$name = strtolower( $match[1] );
		if ( isset( $pairs[ $name ] ) ) {
			continue; // Repeated attribute: first occurrence wins.
		}

		if ( null !== $match[2] ) {
			$pairs[ $name ] = $match[2]; // name="value"
		} elseif ( null !== $match[3] ) {
			$pairs[ $name ] = $match[3]; // name='value'
		} elseif ( isset( $match[4] ) && null !== $match[4] ) {
			$pairs[ $name ] = $match[4]; // name=bare-value
		} else {
			$pairs[ $name ] = ''; // Boolean attribute, e.g. `disabled`.
		}
	}

	return $pairs;
}

/**
 * The `href` attribute's value from a raw (unparsed) attribute string, by
 * exact name — see tpm_tokenize_attributes() for why this is a lookup
 * against real attribute boundaries rather than a regex over the blob.
 *
 * @param string $attrs Raw attribute text between `<a` and the tag's closing `>`.
 * @return string|null The href value, or null if there is no href attribute at all.
 */
function tpm_extract_href_attribute( $attrs ) {
	$pairs = tpm_tokenize_attributes( $attrs );
	return isset( $pairs['href'] ) ? $pairs['href'] : null;
}

/**
 * Rewrite `<a>` tags in already script-protected HTML: an internal link
 * whose target isn't a published Page loses its `<a>` and keeps only its
 * visible content; every other link — external, `#anchor`, `mailto:`,
 * or an internal link that *is* published — passes through completely
 * unmodified, byte for byte.
 *
 * Regex, not DOMDocument, deliberately: this runs on every the_content()
 * call site-wide (see the add_filter() call below), not once per article
 * like the TOC pass in content-nav.php, so it avoids a second full
 * parse-tree build and re-serialize on top of that one.
 *
 * The opening-tag pattern captures the attribute text as one blob using
 * the same quote-aware alternation as tpm_extract_script_blocks() —
 * `"[^"]*"` / `'[^']*'` ahead of the bare-character fallback — rather than
 * a plain `[^>]*`. That distinction is load-bearing, not cosmetic: a plain
 * `[^>]*` treats the FIRST `>` it sees as the tag's end, even one sitting
 * inside a quoted attribute value. This content is full of size
 * comparisons ("dogs > 25 kg") that a human can very plausibly retype into
 * a hand-written `title` attribute, and it produces two different failures
 * depending on which side of `href` the `>` lands on: a `>` in an
 * attribute AFTER href truncates the match early, so the "inner text"
 * capture swallows a fragment of markup and prints it onto the page as
 * text; a `>` in an attribute BEFORE href stops the tag from matching at
 * all, so a link to an unpublished page is left completely untouched — the
 * exact case this whole filter exists to catch. Treating quoted values as
 * atomic units closes both. `href` itself is then pulled out of that blob
 * separately by tpm_extract_href_attribute(), which additionally handles
 * unquoted values (`href=/dogs/x/`) and is case-insensitive (`HREF`),
 * matching the outer pattern's `i` flag on `<a`/`</a>`.
 *
 * @param string $html Script-protected HTML (see tpm_extract_script_blocks()).
 * @return string
 */
function tpm_rewrite_internal_links_html( $html ) {
	$site_host       = wp_parse_url( home_url(), PHP_URL_HOST );
	$published_paths = tpm_get_published_page_paths();

	$rewritten = preg_replace_callback(
		'#<a\b((?:[^>"\']|"[^"]*"|\'[^\']*\')*)>(.*?)</a>#is',
		static function ( $matches ) use ( $site_host, $published_paths ) {
			list( $whole_match, $attrs, $inner_html ) = $matches;

			$href = tpm_extract_href_attribute( $attrs );
			if ( null === $href ) {
				return $whole_match; // No href at all - not a navigable link, nothing to evaluate.
			}

			if ( ! tpm_href_is_site_internal( $href, $site_host ) ) {
				return $whole_match;
			}

			$path = tpm_normalize_internal_path( $href );

			// '' => the front page. It's a theme template (front-page.php),
			// never a WP Page (CLAUDE.md §13), so it can never appear in
			// the published-page map — without this, every homepage link
			// on the site would wrongly degrade to plain text.
			if ( '' === $path || isset( $published_paths[ $path ] ) ) {
				return $whole_match;
			}

			return $inner_html; // Not published (yet, or ever): keep the text, drop the link.
		},
		$html
	);

	// preg_replace_callback() returns null on a regex-engine failure — fail
	// open to the untouched input rather than emitting nothing. This is a
	// different failure mode than tpm_extract_script_blocks()'s fail-CLOSED
	// contract: a regex-engine error here just means "this specific pass
	// didn't run," and the input passed in has already been proven
	// script-safe by the caller before it ever reaches this function.
	return null === $rewritten ? $html : $rewritten;
}

/**
 * `the_content` filter: extend "a page links only once its target is
 * actually published" (CLAUDE.md §13) to post BODY content — see this
 * file's top-of-file docblock for the full rationale and the 201-of-262
 * dead-link figure that motivated it.
 *
 * Hooked at priority 30, after wpautop (core, priority 10) and do_shortcode
 * (core, priority 11): the anchors this rewrites are the ones an actual
 * visitor's browser sees, not raw pre-wpautop markup. page.php's TOC branch
 * calls `apply_filters( 'the_content', get_the_content() )` directly (rather
 * than the template tag `the_content()`), so this filter runs — and this
 * result is what tpm_inject_heading_ids() (content-nav.php) then parses —
 * on that path too.
 *
 * Script blocks are extracted via tpm_extract_script_blocks()
 * (inc/script-protection.php) before the rewrite runs, for the same
 * reason the DOMDocument TOC pass needs it: the FAQ JSON-LD every page
 * carries is exactly the kind of text a size-comparison ("cats <8 lbs") or
 * an embedded HTML example could accidentally make look like markup to a
 * pattern that isn't expecting it. If extraction can't prove every
 * `<script>` block was safely pulled out — most likely an unclosed tag
 * from a hand-edit — this bails out and returns the content completely
 * unmodified rather than running the rewrite over content that might still
 * contain a live, unprotected script body: a stale link that doesn't get
 * caught on this one render is a non-event, the same fail-closed posture
 * tpm_inject_heading_ids() takes for the same reason.
 *
 * @param string $content Rendered post content.
 * @return string
 */
function tpm_filter_internal_links( $content ) {
	if ( '' === trim( (string) $content ) ) {
		return $content;
	}

	list( $protected_content, $script_blocks, $scripts_protected ) = tpm_extract_script_blocks( $content );

	if ( ! $scripts_protected ) {
		return $content;
	}

	$rewritten = tpm_rewrite_internal_links_html( $protected_content );

	return tpm_restore_script_blocks( $rewritten, $script_blocks );
}
add_filter( 'the_content', 'tpm_filter_internal_links', 30 );
