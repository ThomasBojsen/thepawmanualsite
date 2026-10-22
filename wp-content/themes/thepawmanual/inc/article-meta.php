<?php
/**
 * Article header metadata: title splitting, read time, the eyebrow chip.
 *
 * Several pages ship an H1 that is really an H1 plus a subtitle clause typed
 * as one string — "How Much Does a Pet Cost? The Honest, Itemized Answer." —
 * because that is how the content workstream writes a hook (content/README.md).
 * Rendered whole, it reads as H1+H2 to a human and dilutes the keyword focus
 * for a crawler, which sees one enormous heading instead of a short, on-topic
 * one. tpm_split_title() below is the fix: a pure function that separates the
 * two halves so the theme can put the short half in the <h1> and the rest in
 * an italic deck line that carries the editorial voice without competing for
 * the heading slot. It is deliberately pure — no WordPress calls, no output —
 * so it can be unit-tested against the ~127-page title corpus without a
 * database.
 *
 * @package thepawmanual
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * mbstring-aware character length, falling back to byte length.
 *
 * Titles carry curly apostrophes and en/em dashes — multi-byte in UTF-8 — so
 * plain strlen()/substr() would slice mid-character on any title long enough
 * to hit the split point after one of those. mbstring ships enabled on
 * effectively every modern host including Simply.com, but this theme has no
 * hard dependency on it elsewhere, so tpm_split_title() must not fatal on a
 * host where it's missing — it degrades to byte-based slicing instead, which
 * only misbehaves on titles that contain a multi-byte character AT the split
 * point itself.
 *
 * @param string $string
 * @return int
 */
function tpm_title_mb_strlen( $string ) {
	return function_exists( 'mb_strlen' ) ? mb_strlen( $string, 'UTF-8' ) : strlen( $string );
}

/**
 * mbstring-aware substring, falling back to byte-based substr().
 *
 * @param string   $string
 * @param int      $start
 * @param int|null $length Null means "to the end", matching substr()'s own null-length behaviour.
 * @return string
 */
function tpm_title_mb_substr( $string, $start, $length = null ) {
	if ( function_exists( 'mb_substr' ) ) {
		return mb_substr( $string, $start, $length, 'UTF-8' );
	}
	return null === $length ? substr( $string, $start ) : substr( $string, $start, $length );
}

/**
 * Split a page title into its short H1 and an optional deck (subtitle).
 *
 * Scans the title left to right, character by character, for the first
 * occurrence of any of three separator shapes — "? " (a question, the "?"
 * stays attached to the H1 side), ": " (a colon, dropped entirely), or a
 * space-flanked em or en dash (also dropped entirely). Whichever shape is
 * found FIRST, at the leftmost position, is the one used — the other shapes
 * are never considered once one has matched.
 *
 * A match only becomes an actual split once three conditions all hold;
 * failing any of them means the whole title is the H1 and the deck is empty:
 * - the H1 side is at least 5 characters after trimming (so a short hook
 *   like "Why?" plus a long tail is never reduced to a two-word H1) — verified
 *   against the full content corpus (tools/wxr/publish-registry.json): 5 is
 *   exactly the shortest legitimate H1 side in use today ("Tools: Free Pet
 *   Calculators"), and raising it any further starts rejecting real "Hook:
 *   Subtitle" titles whose hook IS the page's actual ranking keyword — the
 *   original 12-character floor did exactly that to 14 titles, including the
 *   live /tools/ and /pet-guides/ pages, defeating this function's own
 *   purpose on precisely the shortest, most keyword-focused hooks,
 * - the deck side is at least 3 characters after trimming (so a trailing
 *   fragment isn't promoted into a deck line),
 * - the matched separator does not sit inside a parenthetical aside opened
 *   earlier in the title — "(and Why Yours Isn't Working)" reads as one
 *   editorial unit, so a colon or dash inside it is not a real split point
 *   even when it is textually the first candidate found. (Verified against
 *   every H1 in the current content set: none actually hits this case today,
 *   but titles are hand-written prose and a future one plausibly could.)
 *
 * Only the matched separator itself, plus the whitespace immediately
 * touching it, is ever removed — every other character of the input is
 * preserved verbatim on one side or the other.
 *
 * @param string $title Raw page title.
 * @return array{h1:string,deck:string}
 */
function tpm_split_title( $title ) {
	$title = (string) $title;

	$fallback = array(
		'h1'   => trim( $title ),
		'deck' => '',
	);

	// Order here is not a priority order — the loop below tests every one of
	// these at every character position, so whichever matches at the
	// LEFTMOST position wins regardless of where it sits in this list.
	// 'keep_on_h1' is what (if anything) of the separator itself stays
	// attached to the H1 side; the rest of the token is always dropped.
	$separators = array(
		array(
			'token'      => '? ',
			'keep_on_h1' => '?',
		),
		array(
			'token'      => ': ',
			'keep_on_h1' => '',
		),
		array(
			'token'      => " \xe2\x80\x94 ", // U+2014 EM DASH, space-flanked.
			'keep_on_h1' => '',
		),
		array(
			'token'      => " \xe2\x80\x93 ", // U+2013 EN DASH, space-flanked.
			'keep_on_h1' => '',
		),
	);

	$length = tpm_title_mb_strlen( $title );

	for ( $i = 0; $i < $length; $i++ ) {
		foreach ( $separators as $separator ) {
			$token_length = tpm_title_mb_strlen( $separator['token'] );

			if ( tpm_title_mb_substr( $title, $i, $token_length ) !== $separator['token'] ) {
				continue;
			}

			// This is the leftmost match across every separator shape — the
			// decision made from here (split, or fall back to the whole
			// title) is final; the scan never continues past it looking for
			// a second candidate.
			$prefix = tpm_title_mb_substr( $title, 0, $i );

			// '(' / ')' are single-byte ASCII in UTF-8 (never a continuation
			// byte of a multi-byte character), so a plain byte-based count
			// is safe here even in the no-mbstring fallback path.
			if ( substr_count( $prefix, '(' ) > substr_count( $prefix, ')' ) ) {
				return $fallback;
			}

			$h1   = trim( $prefix . $separator['keep_on_h1'] );
			$deck = trim( tpm_title_mb_substr( $title, $i + $token_length ) );

			if ( tpm_title_mb_strlen( $h1 ) < 5 || tpm_title_mb_strlen( $deck ) < 3 ) {
				return $fallback;
			}

			return array(
				'h1'   => $h1,
				'deck' => $deck,
			);
		}
	}

	return $fallback;
}

/**
 * Estimated reading time for a Page's own body, in whole minutes.
 *
 * 225 wpm (ceil, minimum 1) against the RAW post_content — not the rendered,
 * shortcode-expanded, wpautop'd output the_content() would produce — because
 * a reading-time estimate is about how much the visitor has to READ, and
 * expanding shortcodes or adding wrapper markup changes nothing about that.
 * strip_shortcodes() runs first so a shortcode tag's own characters (e.g. a
 * future inline-tool shortcode) never count as words; wp_strip_all_tags()
 * runs second and — unlike a bare strip_tags() — removes a `<script>`
 * element's content along with the element itself, which is what keeps the
 * FAQ JSON-LD block every page carries out of the count entirely.
 *
 * @param int $post_id Page ID.
 * @return int Whole minutes, minimum 1.
 */
function tpm_get_read_time( $post_id ) {
	$post = get_post( $post_id );
	if ( ! $post ) {
		return 1;
	}

	$text = trim( wp_strip_all_tags( strip_shortcodes( $post->post_content ) ) );
	if ( '' === $text ) {
		return 1;
	}

	$words      = preg_split( '/\s+/u', $text, -1, PREG_SPLIT_NO_EMPTY );
	$word_count = is_array( $words ) ? count( $words ) : 0;

	return max( 1, (int) ceil( $word_count / 225 ) );
}

/**
 * The small category chip above an article's H1 — "PET GUIDES" in the design
 * — sourced from the page's immediate parent hub, not a taxonomy: the site's
 * information architecture already lives in the Page hierarchy (CLAUDE.md
 * §13, inc/content-nav.php), so the eyebrow is just that hierarchy made
 * visible one more time, with no separate taxonomy to keep in sync.
 *
 * The label runs the parent's title through tpm_split_title() too — a hub
 * title like "Pet Guides: Everything Before You Bring One Home" would
 * otherwise blow out this small chip — and is returned in its natural
 * casing. The design shows it uppercase and letter-spaced, but that is a CSS
 * treatment (`uppercase tracking-widest`) applied at the call site, never
 * done here: a screen reader spells out an all-caps STRING character by
 * character unless the visual transform is left to CSS's `text-transform`,
 * which is read as ordinary words instead.
 *
 * @param int $post_id Page ID.
 * @return array{label:string,url:string}|null Null when there is no parent, or the parent is not published.
 */
function tpm_get_eyebrow( $post_id ) {
	$post = get_post( $post_id );
	if ( ! $post || ! $post->post_parent ) {
		return null;
	}

	$parent = get_post( $post->post_parent );
	if ( ! $parent || 'publish' !== $parent->post_status ) {
		return null;
	}

	$split = tpm_split_title( get_the_title( $parent ) );

	return array(
		'label' => $split['h1'],
		'url'   => get_permalink( $parent ),
	);
}

/**
 * Per-page override map for tpm_get_title_parts() below, keyed by a Page's
 * hierarchical path — `get_page_uri()`'s own format: no leading/trailing
 * slash, e.g. `'tools'`, `'dogs/health/allergies'`.
 *
 * Two override shapes, both understood by tpm_get_title_parts():
 * - `true` — keep the whole post_title as the `<h1>`, no deck. Not used by
 *   any entry below today; documented here because it is still a live code
 *   path, for the next page where the untouched title genuinely IS the
 *   wanted H1.
 * - `array( 'h1' => string, 'deck' => string )` — hand-author a split
 *   tpm_split_title() would not find on its own. This is what the one entry
 *   below uses, now IN USE rather than hypothetical.
 *
 * Seeded with exactly one entry (2026-09-14, client design decision, made
 * after a review of the live /tools/ page): `'tools' => array( 'h1' =>
 * 'Free Pet Calculators', 'deck' => '' )`. The WP post title stays "Tools:
 * Free Pet Calculators" — tpm_split_title() would leave that whole too,
 * since "Tools" alone sits under its own 5-character H1-side floor — but
 * the client wants the RENDERED `<h1>` to read "Free Pet Calculators" with
 * no deck line. The reasoning: "Tools" is a navigation word, not a search
 * phrase; "Free Pet Calculators" is the phrase a visitor actually types
 * into Google. Changing a live H1 is normally a ranking risk, but /tools/
 * is not yet indexed (RECON.md — 18 URLs are live in GSC and this is not
 * one of them), so there is no existing ranking on the old wording to
 * protect by keeping it.
 *
 * Extended 2026-10-22 with the five new tool pages (Dog/Cat Food, Pet Cost,
 * Dog/Cat Breed Selector): each renders its bare tool name as the H1 with
 * no deck, so all seven tool pages share the takeaway-beside-image header
 * the two age calculators already had.
 *
 * Filterable (`apply_filters( 'tpm_title_part_overrides', … )`) so the
 * SEO specialist can add, retire, or replace entries without a theme
 * deploy — the same posture CLAUDE.md §9 asks of every SEO-adjacent knob in
 * this theme.
 *
 * @param int $post_id Page ID the override is being resolved for — unused by
 *                      the default map, passed through purely so a filter
 *                      callback can make a per-page decision without a
 *                      second global lookup of its own.
 * @return array<string,mixed> Hierarchical path => override.
 */
function tpm_get_title_part_overrides( $post_id ) {
	$overrides = array(
		'tools'                     => array(
			'h1'   => __( 'Free Pet Calculators', 'thepawmanual' ),
			'deck' => '',
		),
		// The five 2026-10-22 tool pages: H1 = the tool's name, no deck, so
		// their header matches the Dog/Cat Age Calculator pages (takeaway
		// beside the image). Post titles and RankMath titles are unchanged.
		'tools/dog-food-calculator' => array(
			'h1'   => __( 'Dog Food Calculator', 'thepawmanual' ),
			'deck' => '',
		),
		'tools/cat-food-calculator' => array(
			'h1'   => __( 'Cat Food Calculator', 'thepawmanual' ),
			'deck' => '',
		),
		'tools/pet-cost-calculator' => array(
			'h1'   => __( 'Pet Cost Calculator', 'thepawmanual' ),
			'deck' => '',
		),
		'tools/dog-breed-selector'  => array(
			'h1'   => __( 'Dog Breed Selector', 'thepawmanual' ),
			'deck' => '',
		),
		'tools/cat-breed-selector'  => array(
			'h1'   => __( 'Cat Breed Selector', 'thepawmanual' ),
			'deck' => '',
		),
	);

	return apply_filters( 'tpm_title_part_overrides', $overrides, $post_id );
}

/**
 * Post-aware H1/deck split: the one call site that should be used anywhere
 * a TEMPLATE needs "the short H1 plus its deck for THIS Page" — it checks
 * tpm_get_title_part_overrides() first and falls back to tpm_split_title()
 * for every Page not named in that map.
 *
 * Added 2026-09-14 (review round 2, Finding 6) specifically so a per-page
 * exception (the /tools/ override above) never has to leak into
 * tpm_split_title() itself, which stays PURE on purpose — no WordPress
 * calls, no post identity, unit-tested directly against the title corpus in
 * tools/tests/run.php with no database. Post identity enters the decision
 * here, in the wrapper, and nowhere else.
 *
 * page.php calls this once per request and passes the result down as
 * `$args['title_parts']` to template-parts/content/article-header.php — the
 * template that actually renders a Page's own `<h1>` — which reads it from
 * there (round 3, Finding 1), falling back to calling this function directly
 * if it's ever invoked without that arg.
 *
 * @param int $post_id Page ID.
 * @return array{h1:string,deck:string}
 */
function tpm_get_title_parts( $post_id ) {
	$post_id = (int) $post_id;
	$title   = get_the_title( $post_id );

	$path      = get_page_uri( $post_id ); // Same path shape tpm_get_published_page_url() paths are keyed by (inc/internal-links.php).
	$overrides = tpm_get_title_part_overrides( $post_id );

	if ( is_string( $path ) && '' !== $path && isset( $overrides[ $path ] ) ) {
		$override = $overrides[ $path ];

		if ( true === $override ) {
			return array(
				'h1'   => trim( (string) $title ),
				'deck' => '',
			);
		}

		if ( is_array( $override ) && isset( $override['h1'] ) ) {
			return array(
				'h1'   => (string) $override['h1'],
				'deck' => isset( $override['deck'] ) ? (string) $override['deck'] : '',
			);
		}
		// Any other override shape (a stray filter callback returning
		// something unexpected) falls through to the normal split below
		// rather than producing a blank H1 — same fail-closed posture as
		// the rest of this render pipeline.
	}

	return tpm_split_title( $title );
}

/**
 * Shorten a nav-menu link's label the same way an article H1 is shortened —
 * "How Much Does a Pet Cost?" in the header dropdown, not the full
 * hook-plus-deck string the Page's post_title actually stores (task brief,
 * "several pages look like H1+H2" — the same defect the menu inherits by
 * linking straight to that title).
 *
 * Two escape hatches, in order:
 * - `is_admin()`: the menu editor (wp-admin/nav-menus.php) renders through
 *   this same filter for its own item list, and an admin needs to see (and
 *   edit) the label exactly as stored, not a derived shortened version.
 * - A custom label: WordPress pre-fills a new menu item's title from the
 *   linked object's title, so it only ever DIFFERS from
 *   get_the_title( $item->object_id ) when an editor deliberately retyped
 *   it. That deliberate choice wins over this filter unconditionally — this
 *   function exists to fix an accident (a long H1 dragged verbatim into a
 *   menu), never to override a human decision.
 *
 * Deliberately calls tpm_split_title() directly, NOT tpm_get_title_parts()
 * (round 3, Finding 1) — a nav label's only job is to be short, and
 * tpm_split_title()'s h1 side is exactly that, independently of whatever the
 * per-page override map (tpm_get_title_part_overrides()) does to the
 * rendered `<h1>` for that same page. The /tools/ entry there, for example,
 * hand-authors the on-page `<h1>` down to "Free Pet Calculators" with an
 * empty deck; the nav item should still read "Tools" regardless, because a
 * menu item and a page's `<h1>` are answering different questions — "where
 * do I click" versus "what phrase does this page rank for" — so there is no
 * reason for one to track the other. Do not "fix" this into consistency with
 * the override map.
 *
 * @param string   $title The menu item's title, as WordPress is about to render it.
 * @param WP_Post  $item  The menu item object.
 * @param stdClass $args  wp_nav_menu() arguments (unused; present for the correct filter signature).
 * @param int      $depth Menu depth (unused; present for the correct filter signature).
 * @return string
 */
function tpm_filter_nav_menu_item_title( $title, $item = null, $args = null, $depth = 0 ) {
	if ( is_admin() ) {
		return $title;
	}

	if ( $item instanceof WP_Post
		&& isset( $item->type, $item->object_id )
		&& 'post_type' === $item->type
		&& get_the_title( $item->object_id ) !== $item->title
	) {
		return $title;
	}

	return tpm_split_title( $title )['h1'];
}
add_filter( 'nav_menu_item_title', 'tpm_filter_nav_menu_item_title', 10, 4 );
