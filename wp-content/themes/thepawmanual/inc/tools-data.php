<?php
/**
 * Tool-card data and per-page tool ranking.
 *
 * tpm_get_homepage_tools() is the original, single source of truth for the
 * tools the site knows about (title/description/icon/status/url), plus
 * three keys — slug/species/topics — added 2026-09-14 so the same list can
 * also answer "which of these tools actually belongs on THIS page" for the
 * tools strip and the in-article tool CTA (user requirements 2/3). Nothing
 * about the homepage-facing shape changed: template-parts/home/tools-grid.php
 * and template-parts/tool-card.php read exactly the keys they always did.
 *
 * @package thepawmanual
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return the tools shown on the homepage.
 *
 * `status` is either 'available' (a built calculator) or 'coming-soon' (shown
 * as a card but not yet built). All seven are built as of 2026-10-22, but
 * "built" is not "published": the homepage grid (tpm_get_homepage_tool_cards()
 * below) and tpm_rank_tools_for_post() both re-check that the tool's Page is
 * actually live before linking it, and show it as "Coming soon" until then.
 * That lets the theme ship before or after the tool pages without ever
 * linking to a 404 (CLAUDE.md §13).
 *
 * `slug` is the tool's Page slug under `/tools/` (content/SITEMAP.md), not
 * the calculator engine id the plugin registers: tpm_rank_tools_for_post()
 * below needs the Page slug to resolve a tool against
 * tpm_get_published_page_url(), and the two differ (engine id `dog-age`,
 * Page slug `dog-age-calculator`). `species` and `topics` are what that same
 * ranking matches a page's URL hierarchy against: see
 * tpm_rank_tools_for_post() for how. `category` is one key of
 * tpm_get_tool_categories(): the homepage filter and the hero's quick links
 * group the grid by it.
 *
 * @return array[] List of tool definitions.
 */
function tpm_get_homepage_tools() {
	return array(
		array(
			'title'       => __( 'Dog Age Calculator', 'thepawmanual' ),
			'description' => __( "Calculate your dog's true age in human years, based on their breed size.", 'thepawmanual' ),
			'icon'        => 'calculator',
			'status'      => 'available',
			'url'         => home_url( '/tools/dog-age-calculator/' ),
			'slug'        => 'dog-age-calculator',
			'species'     => array( 'dogs' ),
			'topics'      => array( 'age', 'senior', 'puppy', 'life-stage' ),
			'category'    => 'age',
		),
		array(
			'title'       => __( 'Cat Age Calculator', 'thepawmanual' ),
			'description' => __( 'Find out how old your cat is in human years with the standard feline model.', 'thepawmanual' ),
			'icon'        => 'paw',
			'status'      => 'available',
			'url'         => home_url( '/tools/cat-age-calculator/' ),
			'slug'        => 'cat-age-calculator',
			'species'     => array( 'cats' ),
			'topics'      => array( 'age', 'senior', 'kitten', 'life-stage' ),
			'category'    => 'age',
		),
		array(
			'title'       => __( 'Dog Food Calculator', 'thepawmanual' ),
			'description' => __( "Work out the right daily food portion for your dog's weight and activity.", 'thepawmanual' ),
			'icon'        => 'bowl',
			'status'      => 'available',
			'url'         => home_url( '/tools/dog-food-calculator/' ),
			'slug'        => 'dog-food-calculator',
			'species'     => array( 'dogs' ),
			'topics'      => array( 'food', 'feeding', 'feed', 'portion', 'weight', 'nutrition' ),
			'category'    => 'nutrition',
		),
		array(
			'title'       => __( 'Cat Food Calculator', 'thepawmanual' ),
			'description' => __( "Work out the right daily food portion for your cat's weight and activity.", 'thepawmanual' ),
			'icon'        => 'bowl',
			'status'      => 'available',
			'url'         => home_url( '/tools/cat-food-calculator/' ),
			'slug'        => 'cat-food-calculator',
			'species'     => array( 'cats' ),
			'topics'      => array( 'food', 'feeding', 'feed', 'portion', 'weight', 'nutrition' ),
			'category'    => 'nutrition',
		),
		array(
			'title'       => __( 'Pet Cost Calculator', 'thepawmanual' ),
			'description' => __( 'See what a dog or cat will realistically cost you per year, in dollars or euros.', 'thepawmanual' ),
			'icon'        => 'gauge',
			'status'      => 'available',
			'url'         => home_url( '/tools/pet-cost-calculator/' ),
			'slug'        => 'pet-cost-calculator',
			'species'     => array( 'dogs', 'cats' ),
			'topics'      => array( 'cost', 'budget', 'insurance', 'money' ),
			'category'    => 'costs',
		),
		array(
			'title'       => __( 'Dog Breed Selector', 'thepawmanual' ),
			'description' => __( 'Find which breeds fit your home, schedule, and activity level.', 'thepawmanual' ),
			'icon'        => 'sliders-horizontal',
			'status'      => 'available',
			'url'         => home_url( '/tools/dog-breed-selector/' ),
			'slug'        => 'dog-breed-selector',
			'species'     => array( 'dogs' ),
			'topics'      => array( 'breed', 'breeds', 'choosing' ),
			'category'    => 'breeds',
		),
		array(
			'title'       => __( 'Cat Breed Selector', 'thepawmanual' ),
			'description' => __( 'Find which cat breeds suit your home, routine, and family.', 'thepawmanual' ),
			'icon'        => 'sliders-horizontal',
			'status'      => 'available',
			'url'         => home_url( '/tools/cat-breed-selector/' ),
			'slug'        => 'cat-breed-selector',
			'species'     => array( 'cats' ),
			'topics'      => array( 'breed', 'breeds', 'choosing' ),
			'category'    => 'breeds',
		),
	);
}

/**
 * The tool categories the homepage filter and hero quick links offer, in
 * display order. Every `category` in tpm_get_homepage_tools() must be a key
 * here; a category no tool uses would be a filter that empties the grid, so
 * don't add one before its first tool exists.
 *
 * @return array<string, string> Category key => translated label.
 */
function tpm_get_tool_categories() {
	return array(
		'age'       => __( 'Age', 'thepawmanual' ),
		'nutrition' => __( 'Nutrition', 'thepawmanual' ),
		'costs'     => __( 'Costs', 'thepawmanual' ),
		'breeds'    => __( 'Breeds', 'thepawmanual' ),
	);
}

/**
 * The homepage grid's tools, each linked only when its Page is published.
 *
 * Same rule tpm_rank_tools_for_post() applies to the tools strip: a tool
 * marked 'available' in code whose Page hasn't shipped yet is shown as
 * 'coming-soon', never as a link to a 404. Catalogue order is kept.
 *
 * @return array[] tpm_get_homepage_tools() rows with resolved status/url.
 */
function tpm_get_homepage_tool_cards() {
	return array_map(
		static function ( $tool ) {
			if ( 'available' === $tool['status'] ) {
				$resolved = ( ! empty( $tool['slug'] ) && function_exists( 'tpm_get_published_page_url' ) )
					? tpm_get_published_page_url( 'tools/' . $tool['slug'] )
					: null;
				if ( $resolved ) {
					$tool['url'] = $resolved;
				} else {
					$tool['status'] = 'coming-soon';
					$tool['url']    = '';
				}
			}
			return $tool;
		},
		tpm_get_homepage_tools()
	);
}

/**
 * Trim a single trailing "s" from a lowercase word — the crudest possible
 * stemmer, and deliberately so (2026-09-14 review round 2, Finding 4a).
 *
 * The vocabulary this ever runs against is closed and hand-written: every
 * `topics` entry in tpm_get_homepage_tools() and every live URL segment is
 * plain English with a regular plural, so "costs" -> "cost" and "cost" ->
 * "cost" is all the normalization the topic-match comparison below needs —
 * a real stemmer (Porter etc.) would be solving a problem this data doesn't
 * have. Guarded to leave a 1-character word alone so a hypothetical future
 * single-letter segment can never be reduced to an empty string.
 *
 * @param string $word Lowercase word — a topic stem or one hyphen-split URL segment.
 * @return string
 */
function tpm_stem_topic_word( $word ) {
	$word = (string) $word;
	if ( strlen( $word ) > 1 && 's' === substr( $word, -1 ) ) {
		return substr( $word, 0, -1 );
	}
	return $word;
}

/**
 * Which species (a subset of `array( 'dogs', 'cats' )`) a page's own URL
 * context belongs to — the top-level ancestor slug when it IS a species
 * hub, plus either species' singular stem ("dog"/"cat") when it appears as
 * a whole segment of the page's OWN slug (e.g. 'dog-age-calculator' ->
 * `array( 'dogs' )`, even though that Page sits under /tools/, not /dogs/).
 *
 * Added 2026-09-14 (review round 2, Finding 3) for
 * tpm_get_inline_tool_for_post()'s species gate below. Deliberately a
 * SEPARATE, smaller computation from the $top_level_slug / $own_slug logic
 * inside tpm_rank_tools_for_post() rather than a refactor of it: that
 * scoring block is already reviewed and tuned (the +3/+3/+2 weights), and
 * re-deriving its already-reviewed behaviour from a shared helper is not
 * what this fix is — the SPEC's "minimal correct change" instruction, not
 * a stylistic preference.
 *
 * @param int $post_id Page ID.
 * @return string[] Zero, one, or both of 'dogs'/'cats'. Empty on purpose for
 *                   a species-general page (a /pet-guides/ article with no
 *                   dog/cat stem anywhere in its own slug) — that emptiness
 *                   is what lets tpm_get_inline_tool_for_post() leave a
 *                   species-general tool (e.g. a future Pet Cost Calculator,
 *                   `species => array( 'dogs', 'cats' )`) uncontested by the
 *                   gate rather than rejecting it for having "no context to
 *                   match," which would permanently block Finding 4's
 *                   documented future case instead of fixing it.
 */
function tpm_get_page_species_context( $post_id ) {
	$post = get_post( $post_id );
	if ( ! $post ) {
		return array();
	}

	$known_species = array( 'dogs', 'cats' );
	$context       = array();

	$ancestor_ids   = get_post_ancestors( $post_id );
	$top_level_slug = ! empty( $ancestor_ids )
		? get_post_field( 'post_name', end( $ancestor_ids ) )
		: $post->post_name; // No ancestors: this page IS the top level.

	if ( in_array( $top_level_slug, $known_species, true ) ) {
		$context[] = $top_level_slug;
	}

	$own_segments = explode( '-', (string) $post->post_name );
	foreach ( $known_species as $species_key ) {
		$stem = rtrim( $species_key, 's' ); // 'dogs' -> 'dog', 'cats' -> 'cat'.
		if ( in_array( $stem, $own_segments, true ) && ! in_array( $species_key, $context, true ) ) {
			$context[] = $species_key;
		}
	}

	return $context;
}

/**
 * A catalogued tool's `species` set, looked up by its Page slug.
 *
 * tpm_rank_tools_for_post()'s own return rows deliberately do NOT carry
 * `species` — tpm_get_tools_for_context()'s documented row shape
 * (title/description/icon/status/url/slug) is a public contract several
 * templates read, and threading an extra bookkeeping key through it for the
 * sake of one caller (the species gate below) risks that key leaking into a
 * template that was never meant to see it. A direct lookup against the
 * tool catalogue is a handful of array comparisons, not a query, so
 * there is no real cost to keeping it separate.
 *
 * @param string $slug A tool's Page slug (tpm_get_homepage_tools()'s `slug`), not the calculator engine id.
 * @return string[] The tool's `species` array, or empty when not found.
 */
function tpm_get_catalogued_tool_species( $slug ) {
	if ( '' === $slug ) {
		return array();
	}

	foreach ( tpm_get_homepage_tools() as $tool ) {
		if ( isset( $tool['slug'] ) && $tool['slug'] === $slug ) {
			return isset( $tool['species'] ) && is_array( $tool['species'] ) ? $tool['species'] : array();
		}
	}

	return array();
}

/**
 * Rank every catalogued tool against one page's context (internal — not part
 * of this file's public contract; tpm_get_tools_for_context() and
 * tpm_get_inline_tool_for_post() below are the two public entry points, and
 * both need the same ranked+status-resolved list, so the work happens once
 * here instead of twice).
 *
 * Scoring: +3 when the page's top-level ancestor slug ('dogs'/'cats' — the
 * species hub) is in the tool's `species`; +3 again when the tool's OWN
 * singular species stem ('dogs' -> 'dog') is a whole slug segment of the
 * page's own slug (this is what lets /tools/cat-age-calculator/ prefer the
 * Cat Age Calculator over the Dog Age Calculator: neither calculator page
 * sits under /dogs/ or /cats/, so the ancestor check never fires for
 * either, and both would otherwise tie on the shared 'age' topic stem
 * below); +2 for every `topics` stem found as a WHOLE slug segment anywhere
 * in the page's own slug or any ancestor's slug — segment-matched
 * (`explode( '-', $slug )`), not a raw substring test, so a topic stem can
 * never match merely because it appears mid-word inside an unrelated slug
 * once wave 2 adds more pages (e.g. 'age' inside a hypothetical 'massage'
 * slug). A page with no ancestors (post_parent = 0) IS the top level — e.g.
 * the /dogs/ hub itself — so it's judged against its own slug, same as
 * tpm_get_emergency_callout() (content-nav.php) does for the species lookup
 * there.
 *
 * A tool never promotes itself: the page currently being rendered can BE one
 * of these tools (a calculator's own Page also carries explanatory article
 * copy, so it goes through this same ranking for its own tools-strip/inline
 * CTA), and neither of those is a mechanism for a page to link to itself.
 * Resolved by PATH — the same tolerant comparison
 * tpm_dom_content_already_links_url() (inc/content-blocks.php) already uses
 * for the identical "is this already linked" question — rather than by ID,
 * so it needs no extra query. A tool that matches the current page is
 * dropped entirely, not merely deprioritised, so it can never surface lower
 * down the strip either.
 *
 * Every 'available' tool is re-checked against tpm_get_published_page_url()
 * (inc/internal-links.php) rather than trusted at face value: the tool list
 * is fixed in code, but a tool's Page goes live only when its wave is
 * imported and published, and a wave that hasn't shipped yet must not produce a
 * live link to a 404 (CLAUDE.md §13, same rule tpm_filter_internal_links()
 * enforces for post-body links). A tool that fails this check is downgraded
 * to 'coming-soon' here, in the one place both public functions read from —
 * neither of them, nor any template, ever sees the raw hardcoded url.
 *
 * @param int $post_id Page ID to rank tools against.
 * @return array[] Every catalogued tool EXCEPT the current page's own,
 *                 each carrying the resolved 'status'/'url' plus internal
 *                 'score'/'order'/'topic_matched' bookkeeping, sorted
 *                 available-first then by descending score (ties keep the
 *                 catalogue's own order).
 */
function tpm_rank_tools_for_post( $post_id ) {
	$post = get_post( $post_id );

	$context_slugs  = array();
	$top_level_slug = '';
	$own_slug       = $post ? (string) $post->post_name : '';
	$own_path       = '';

	if ( $post ) {
		$context_slugs[] = $post->post_name;

		// get_post_ancestors() is nearest-parent-first (content-nav.php's
		// tpm_get_emergency_callout() relies on the same fact), so the LAST
		// entry is the top-level ancestor — the species hub.
		$ancestor_ids = get_post_ancestors( $post_id );
		foreach ( $ancestor_ids as $ancestor_id ) {
			$context_slugs[] = get_post_field( 'post_name', $ancestor_id );
		}

		$top_level_slug = ! empty( $ancestor_ids )
			? get_post_field( 'post_name', end( $ancestor_ids ) )
			: $post->post_name; // No ancestors: this page IS the top level.

		$permalink = get_permalink( $post );
		$own_path  = $permalink ? trim( (string) wp_parse_url( $permalink, PHP_URL_PATH ), '/' ) : '';
	}

	$ranked = array();

	foreach ( tpm_get_homepage_tools() as $index => $tool ) {
		$status = isset( $tool['status'] ) ? $tool['status'] : 'coming-soon';
		$url    = '';

		if ( 'available' === $status ) {
			$resolved = ! empty( $tool['slug'] ) ? tpm_get_published_page_url( 'tools/' . $tool['slug'] ) : null;
			if ( $resolved ) {
				$url = $resolved;
			} else {
				$status = 'coming-soon'; // Catalogued as built, but this wave hasn't published its Page yet.
			}
		}

		if ( 'available' === $status && '' !== $own_path ) {
			$tool_path = trim( (string) wp_parse_url( $url, PHP_URL_PATH ), '/' );
			if ( $tool_path === $own_path ) {
				continue; // This IS the tool's own page — never a candidate for its own CTA or strip card.
			}
		}

		$score         = 0;
		$topic_matched = false;
		$species       = isset( $tool['species'] ) && is_array( $tool['species'] ) ? $tool['species'] : array();

		if ( '' !== $top_level_slug && in_array( $top_level_slug, $species, true ) ) {
			$score += 3;
		}

		if ( '' !== $own_slug ) {
			$own_segments = explode( '-', $own_slug );
			foreach ( $species as $species_key ) {
				$species_stem = rtrim( (string) $species_key, 's' ); // 'dogs' -> 'dog', 'cats' -> 'cat'.
				if ( '' !== $species_stem && in_array( $species_stem, $own_segments, true ) ) {
					$score += 3;
					break;
				}
			}
		}

		$topics = isset( $tool['topics'] ) && is_array( $tool['topics'] ) ? $tool['topics'] : array();
		foreach ( $topics as $topic ) {
			// tpm_stem_topic_word() on BOTH sides (2026-09-14 review round 2,
			// Finding 4a): a topic stem is always written singular in
			// tpm_get_homepage_tools() ('cost'), but a URL segment carrying
			// the same idea is often plural ('pet-costs' -> 'costs') — a raw
			// in_array() only ever matched when a page's own slug happened to
			// repeat the exact singular word, which silently missed every
			// plural segment, /pet-guides/pet-costs/ included.
			$topic_stem = tpm_stem_topic_word( $topic );
			foreach ( $context_slugs as $context_slug ) {
				if ( '' === $context_slug ) {
					continue;
				}
				$segment_stems = array_map( 'tpm_stem_topic_word', explode( '-', $context_slug ) );
				if ( in_array( $topic_stem, $segment_stems, true ) ) {
					$score        += 2;
					$topic_matched = true;
					break; // One stem counts once even if several ancestor slugs contain it.
				}
			}
		}

		$ranked[] = array(
			'title'         => $tool['title'],
			'description'   => $tool['description'],
			'icon'          => isset( $tool['icon'] ) ? $tool['icon'] : 'calculator',
			'status'        => $status,
			'url'           => $url,
			'slug'          => isset( $tool['slug'] ) ? $tool['slug'] : '',
			'score'         => $score,
			'order'         => $index, // Tiebreak so equal scores keep the catalogue's own order, not sort()'s.
			'topic_matched' => $topic_matched,
		);
	}

	usort(
		$ranked,
		static function ( $a, $b ) {
			$a_available = 'available' === $a['status'];
			$b_available = 'available' === $b['status'];
			if ( $a_available !== $b_available ) {
				return $a_available ? -1 : 1;
			}
			if ( $a['score'] !== $b['score'] ) {
				return $b['score'] - $a['score'];
			}
			return $a['order'] - $b['order'];
		}
	);

	return $ranked;
}

/**
 * The tools worth showing on one specific page, ranked for it.
 *
 * Backs the tools strip (template-parts/content/tools-strip.php) that ships
 * on every landing/hub page (user requirement 3). Always returns at least
 * two rows — even if a caller passes a smaller $limit — so the strip can
 * never render looking broken; the catalogue has seven tools total, minus the
 * current page's own when that page IS one of them (tpm_rank_tools_for_post()'s
 * self-exclusion), so this is never short of stock.
 *
 * @param int $post_id Page ID.
 * @param int $limit   Maximum rows to return (default 4).
 * @return array[] Ranked tool rows: title/description/icon/status/url/slug.
 */
function tpm_get_tools_for_context( $post_id, $limit = 4 ) {
	$ranked = tpm_rank_tools_for_post( (int) $post_id );
	$count  = max( 2, (int) $limit );

	$rows = array_slice( $ranked, 0, min( $count, count( $ranked ) ) );

	// 'score'/'order'/'topic_matched' are this file's own ranking bookkeeping,
	// not part of what a template should read — the same five keys
	// tpm_get_homepage_tools() rows always carried, plus 'slug'.
	return array_map(
		static function ( $row ) {
			unset( $row['score'], $row['order'], $row['topic_matched'] );
			return $row;
		},
		$rows
	);
}

/**
 * One tool to feature as an in-body CTA on an article page, or null.
 *
 * STRICT on purpose: this only returns a tool that is (a) actually built AND
 * (b) its Page is actually published AND (c) it scored a genuine TOPIC match
 * for THIS page AND (d) is species-compatible with this page — never just
 * "whatever ranks first." A bare species match (+3 for merely living
 * somewhere under /dogs/ or /cats/) is deliberately NOT enough on its own:
 * every page in the dog cluster would otherwise clear a `score > 0` gate
 * regardless of what it's actually about, putting an age calculator on a
 * training or grooming article that has nothing to do with age. An inline
 * CTA sits inside the article's own prose, presented as if the author chose
 * it for that specific page; a tool an editor never intended for this topic
 * reads as a mistake, not a convenience, so no topic match at all means no
 * CTA — an irrelevant one is worse than none.
 *
 * (d) is a NECESSARY addition on top of (c), not a restatement of it
 * (2026-09-14 review round 2, Finding 3): tpm_rank_tools_for_post() already
 * drops a page's OWN tool from the candidate list, but on a calculator page
 * that leaves the OTHER age calculator as the only 'available' candidate at
 * all, and it topic-matches on the shared 'age' stem the two calculators'
 * `topics` arrays both carry — so, pre-fix, /tools/dog-age-calculator/
 * cleared (a)-(c) and inserted a mid-article CTA promoting the CAT Age
 * Calculator, and /tools/cat-age-calculator/ promoted the DOG one, on both
 * of the site's two live, indexed calculator pages. The fix is a hard gate,
 * not a scoring tweak: tpm_get_page_species_context() resolves what species
 * THIS page is actually about, and a candidate whose own `species` set
 * shares none of that is rejected outright regardless of how high its score
 * or how real its topic match — the gate only applies when this page HAS a
 * species context at all (empty context, e.g. a general /pet-guides/
 * article, never blocks anything; see that function's own docblock for
 * why). Never loosen this to a bare species match in place of the topic
 * check above — that would resurrect the exact "any dog-cluster page
 * qualifies" failure mode (c) exists to prevent, just gated by species
 * instead of by nothing. On a calculator page this now correctly yields no
 * inline CTA at all; template-parts/content/tools-strip.php still
 * cross-links the OTHER calculator there, in a "here are the other tools"
 * context where that reads correctly instead of as a mismatched pick.
 *
 * @param int $post_id Page ID.
 * @return array|null Same shape as one tpm_get_tools_for_context() row, or null.
 */
function tpm_get_inline_tool_for_post( $post_id ) {
	$post_id = (int) $post_id;
	$ranked  = tpm_rank_tools_for_post( $post_id );
	$top     = isset( $ranked[0] ) ? $ranked[0] : null;

	if ( ! $top || 'available' !== $top['status'] || empty( $top['topic_matched'] ) ) {
		return null;
	}

	$page_species = tpm_get_page_species_context( $post_id );
	if ( ! empty( $page_species ) ) {
		$tool_species = tpm_get_catalogued_tool_species( $top['slug'] );
		if ( ! empty( $tool_species ) && ! array_intersect( $page_species, $tool_species ) ) {
			return null; // Disjoint species — see the gate's rationale above.
		}
	}

	unset( $top['score'], $top['order'], $top['topic_matched'] );
	return $top;
}
