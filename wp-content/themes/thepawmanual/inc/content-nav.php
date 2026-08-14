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
 * Parse rendered post content, stamp an `id` on every H2, and collect a
 * table-of-contents list.
 *
 * Done in PHP against the server-rendered markup — not a JS-built TOC —
 * because a JS-only TOC produces anchors that only exist once a script has
 * run, which is worse for both crawlers and no-JS readers (task brief, item
 * 3). DOMDocument is guarded throughout: a missing extension or malformed
 * input falls back to the original content with an empty TOC, never a fatal.
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

	$dom = new DOMDocument();

	// The XML-prolog trick tells libxml the source is UTF-8 without an actual
	// <?xml> node ending up in the parsed tree. LIBXML_HTML_NOIMPLIED stops
	// DOMDocument silently wrapping the fragment in <html><body>, so the
	// wrapping <div> below ends up as the document's sole root element;
	// NODEFDTD drops the doctype libxml would otherwise inject.
	libxml_use_internal_errors( true );
	$loaded = $dom->loadHTML(
		'<?xml encoding="utf-8" ?><div>' . $content . '</div>',
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

	return array(
		'content' => $html,
		'items'   => $items,
	);
}

/**
 * Permalink for a published Page at a given path, or null.
 *
 * Used anywhere a template needs to hardcode a link to a Page that isn't
 * guaranteed to exist yet — e.g. footer.php's "Pet Emergency?" links to the
 * two vet-signs pages, which ship in wave 3 (dogs) and wave 6 (cats) of the
 * content rollout (content/SEO-STRATEGY.md §4), long after wave 1 launches.
 * `get_page_by_path()` matches a Page regardless of status, so the explicit
 * `publish` check is what keeps this from linking to a draft that 404s for
 * anonymous visitors — the same "never link what doesn't exist yet" rule
 * `tpm_get_emergency_callout()` already follows below.
 *
 * @param string $path Page path relative to site root, no leading/trailing slash (e.g. 'dogs/health/signs-your-dog-needs-a-vet').
 * @return string|null
 */
function tpm_get_published_page_url( $path ) {
	$page = get_page_by_path( $path, OBJECT, 'page' );
	if ( ! $page || 'publish' !== $page->post_status ) {
		return null;
	}
	return get_permalink( $page );
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
