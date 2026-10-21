<?php
/**
 * Dependency-free assertion harness for the theme's pure content-transform
 * logic — no WordPress install, no database. Run with:
 *
 *   php tools/tests/run.php
 *
 * Scope is narrow on purpose: the files below (inc/article-meta.php,
 * inc/script-protection.php, inc/content-blocks.php, inc/content-nav.php, and
 * the page-image helper plus its template parts, whose WP calls get their own
 * stubs in the page-image section at the bottom) do
 * real work — title splitting, FAQ restructuring, the CLAUDE.md §13
 * `<script>`-block bug, heading-id/TOC collection — with pure functions over
 * strings and DOM trees, not WordPress state. That's what makes them
 * testable without a WP install: stub the sliver of the WP API they
 * actually call, then assert real behaviour against it.
 *
 * @package thepawmanual
 */

define( 'ABSPATH', __DIR__ . '/' );

// -- WordPress stubs: only what the three files under test call. ---------

function __( $text, $domain = 'default' ) {
	return $text;
}
function esc_html__( $text, $domain = 'default' ) {
	return htmlspecialchars( (string) $text, ENT_QUOTES, 'UTF-8' );
}
function esc_html( $text ) {
	return htmlspecialchars( (string) $text, ENT_QUOTES, 'UTF-8' );
}
function esc_attr( $text ) {
	return htmlspecialchars( (string) $text, ENT_QUOTES, 'UTF-8' );
}
function esc_url( $url ) {
	return (string) $url;
}
// Real wp_parse_url() is a parse_url() shim that papers over cross-PHP-version
// parsing quirks; none of those quirks are in play for the plain absolute
// paths ("/tools/pet-cost-calculator/") this harness's fixtures use, so a
// direct pass-through is behaviourally identical for what's under test here.
// Needed the moment ANY fixture below exercises tpm_dom_collect_linked_paths()
// / tpm_dom_content_already_links_url() (inc/content-blocks.php) — every
// earlier fixture in this file has zero <a> tags in it, so this was never
// called until the ROUND 4, FINDING 1 fixture below.
function wp_parse_url( $url, $component = -1 ) {
	return parse_url( (string) $url, $component );
}
function sanitize_title( $title ) {
	return strtolower( trim( preg_replace( '/[^a-zA-Z0-9]+/', '-', (string) $title ), '-' ) );
}
function absint( $n ) {
	return abs( (int) $n );
}
function trailingslashit( $string ) {
	return rtrim( (string) $string, '/' ) . '/';
}
// Real wp_strip_all_tags() drops a <script>/<style> element's CONTENT along
// with the tags (not just the tags) — tpm_get_read_time() relies on that to
// keep the FAQ JSON-LD out of the word count, so the stub must match.
function wp_strip_all_tags( $string, $remove_breaks = false ) {
	$string = preg_replace( '@<(script|style)[^>]*?>.*?</\\1>@si', '', (string) $string );
	$string = strip_tags( $string );
	return trim( $remove_breaks ? preg_replace( '/[\r\n\t ]+/', ' ', $string ) : $string );
}
function strip_shortcodes( $content ) {
	return preg_replace( '/\[[^\]]+\]/', '', (string) $content );
}
// Not in the brief's stub list, but load-bearing regardless: article-meta.php
// calls add_filter() at the TOP LEVEL, so requiring that file with no
// add_filter() defined is a fatal error, not a skippable code path. A no-op
// is correct — this harness calls the tested functions directly, it never
// dispatches WP hooks.
// The registrations are RECORDED (never dispatched) so the page-image section
// at the bottom can prove a temporary filter is removed again.
function add_filter( $hook, $callback, ...$args ) {
	$GLOBALS['tpm_test_filters'][ $hook ][] = $callback;
}
function remove_filter( $hook, $callback, ...$args ) {
	foreach ( $GLOBALS['tpm_test_filters'][ $hook ] ?? array() as $i => $registered ) {
		if ( $registered === $callback ) {
			unset( $GLOBALS['tpm_test_filters'][ $hook ][ $i ] );
			return true;
		}
	}
	return false;
}
// tpm_get_title_part_overrides() (article-meta.php) runs its map through
// apply_filters() — unused until a test actually calls tpm_get_title_parts()
// (round 3, Finding 1, below), but load-bearing the moment one does. Passes
// the value through unchanged, same posture as add_filter()'s no-op above.
function apply_filters( $tag, $value, ...$args ) {
	return $value;
}

// -- Tiny assertion helpers: print ok/FAIL per case, tally a total. -------

$GLOBALS['tpm_test_pass'] = 0;
$GLOBALS['tpm_test_fail'] = 0;

function tpm_test_report( $ok, $label, $detail = '' ) {
	if ( $ok ) {
		++$GLOBALS['tpm_test_pass'];
		echo "  ok   {$label}\n";
	} else {
		++$GLOBALS['tpm_test_fail'];
		echo "  FAIL {$label}\n" . ( '' !== $detail ? "       {$detail}\n" : '' );
	}
}
function tpm_assert_same( $expected, $actual, $label ) {
	tpm_test_report( $expected === $actual, $label, 'expected ' . var_export( $expected, true ) . ' got ' . var_export( $actual, true ) );
}
function tpm_assert( $condition, $label, $detail = '' ) {
	tpm_test_report( (bool) $condition, $label, $detail );
}
function tpm_assert_contains( $needle, $haystack, $label ) {
	tpm_assert( false !== strpos( (string) $haystack, (string) $needle ), $label, 'missing: ' . var_export( $needle, true ) );
}
function tpm_assert_not_contains( $needle, $haystack, $label ) {
	tpm_assert( false === strpos( (string) $haystack, (string) $needle ), $label, 'unexpectedly present: ' . var_export( $needle, true ) );
}

// -- Load the files under test. -------------------------------------------

$theme_inc = __DIR__ . '/../../wp-content/themes/thepawmanual/inc';
require $theme_inc . '/article-meta.php';
require $theme_inc . '/script-protection.php';
require $theme_inc . '/content-blocks.php';
// tpm_inject_heading_ids() plus its twin loss-guard and the ancestor-class
// helper (FINDING 5's fix) live here. Everything else in this file touches
// live WP state (get_pages(), get_post(), …) inside functions this harness
// never calls, so no extra stubs are needed to require it safely.
require $theme_inc . '/content-nav.php';
// tpm_icon() only — a self-contained lookup+printf over a static SVG map,
// with no dependency beyond esc_attr() (already stubbed above). Needed the
// moment a fixture makes tpm_dom_inject_inline_tool_cta() actually BUILD a
// CTA card (tpm_dom_tool_cta_html() calls tpm_icon() directly) rather than
// dedup it away before that point — see ROUND 4, FINDING 1 below, whose
// mutation-proof case is the first fixture in this suite that reaches it.
require $theme_inc . '/icons.php';
// tpm_get_page_image(): its WP calls (get_post_thumbnail_id(),
// wp_get_attachment_metadata(), get_the_post_thumbnail()) are stubbed in the
// page-image section below.
require $theme_inc . '/page-image.php';

/* =========================== tpm_split_title() ========================= */

echo "== tpm_split_title() ==\n";

// "?" is the one separator whose own character stays on the H1 side — a
// question is still a complete sentence without its answer.
$r = tpm_split_title( 'How Much Does a Pet Cost? The Honest, Itemized Answer' );
tpm_assert_same( 'How Much Does a Pet Cost?', $r['h1'], 'question mark: "?" stays on h1' );
tpm_assert_same( 'The Honest, Itemized Answer', $r['deck'], 'question mark: remainder becomes the deck' );

// ":" is dropped entirely — neither side keeps it.
$r = tpm_split_title( 'Cat Care Guides: Built on How Cats Actually Work' );
tpm_assert_same( 'Cat Care Guides', $r['h1'], 'colon: dropped, not kept on h1' );
tpm_assert_same( 'Built on How Cats Actually Work', $r['deck'], 'colon: remainder becomes the deck' );

// Em dash — no title in the current corpus uses one WITHOUT an earlier "?"
// or ":" also present first, so this is synthetic: it isolates the shape.
$r = tpm_split_title( "Choosing a Harness \xe2\x80\x94 What Actually Matters" );
tpm_assert_same( 'Choosing a Harness', $r['h1'], 'em dash: h1 side' );
tpm_assert_same( 'What Actually Matters', $r['deck'], 'em dash: deck side' );

// En dash — the sibling shape; synthetic for the same reason.
$r = tpm_split_title( "Annual Vet Visits \xe2\x80\x93 What to Expect Each Time" );
tpm_assert_same( 'Annual Vet Visits', $r['h1'], 'en dash: h1 side' );
tpm_assert_same( 'What to Expect Each Time', $r['deck'], 'en dash: deck side' );

// No separator at all: whole title is the h1, deck is empty.
$r = tpm_split_title( 'About ThePawManual' );
tpm_assert_same( 'About ThePawManual', $r['h1'], 'no separator: h1 is the whole title' );
tpm_assert_same( '', $r['deck'], 'no separator: deck is empty' );

// H1 side under 5 chars: split REJECTED despite a real colon match. Synthetic
// — "Bengal" (6 chars) used to be the real-world example of this case back
// when the floor was 12, but the floor is 5 now (see tpm_split_title()'s
// docblock), so "Bengal:" legitimately splits below and this needs a shorter
// stand-in to keep testing the floor itself.
$r = tpm_split_title( 'Hi: A Beautiful Cat That Comes With Homework' );
tpm_assert_same( 'Hi: A Beautiful Cat That Comes With Homework', $r['h1'], 'short h1 (<5 chars): not split' );
tpm_assert_same( '', $r['deck'], 'short h1 (<5 chars): deck stays empty' );

// Deck side under 3 chars: same rejection, opposite side. Synthetic — no
// real title is this short after its separator.
$r = tpm_split_title( 'Complete Guide to Puppy Training: Go' );
tpm_assert_same( 'Complete Guide to Puppy Training: Go', $r['h1'], 'short deck (<3 chars): not split' );
tpm_assert_same( '', $r['deck'], 'short deck (<3 chars): deck stays empty' );

// Leftmost separator candidate sits inside a parenthetical aside opened
// earlier: must NOT be used. Synthetic — the function's own docblock notes
// no title in the current content set hits this today.
$r = tpm_split_title( 'Best Practices (Ideally: Before You Need Them) for New Pet Owners' );
tpm_assert_same( 'Best Practices (Ideally: Before You Need Them) for New Pet Owners', $r['h1'], 'separator inside parens: not used as a split point' );
tpm_assert_same( '', $r['deck'], 'separator inside parens: deck stays empty' );

// Multibyte: a curly apostrophe BEFORE the separator must not shift the
// split point (byte slicing would cut mid-character). Synthetic — the real
// corpus only uses straight apostrophes.
$r = tpm_split_title( "Alex\xe2\x80\x99s Guide to Senior Cats: What Actually Changes" );
tpm_assert_same( "Alex\xe2\x80\x99s Guide to Senior Cats", $r['h1'], 'multibyte before separator: h1 preserved byte-for-byte' );
tpm_assert_same( 'What Actually Changes', $r['deck'], 'multibyte before separator: deck correct' );

// Multibyte: an en dash INSIDE the deck must survive verbatim.
$r = tpm_split_title( "Crate Training Basics: A Short Primer \xe2\x80\x93 Read This First" );
tpm_assert_same( 'Crate Training Basics', $r['h1'], 'multibyte in deck: h1 side' );
tpm_assert_same( "A Short Primer \xe2\x80\x93 Read This First", $r['deck'], 'multibyte in deck: en dash preserved byte-for-byte' );

// --- Real titles, read live from tools/wxr/publish-registry.json (never
// written to — CLAUDE.md §13: that file is state the WXR importer depends
// on). Located by an ASCII-safe substring rather than retyped in full, so a
// transcription slip can't silently assert the wrong thing for a title that
// itself contains a multibyte character (see "French Bulldog" below).
$registry_path    = __DIR__ . '/../wxr/publish-registry.json';
$registry         = json_decode( (string) file_get_contents( $registry_path ), true );
$registry_titles  = array();
if ( is_array( $registry ) ) {
	foreach ( $registry as $url => $entry ) {
		if ( '_readme' !== $url && is_array( $entry ) && isset( $entry['title'] ) ) {
			$registry_titles[] = (string) $entry['title'];
		}
	}
}
function tpm_find_registry_title( array $titles, $needle ) {
	$matches = array_values( array_filter( $titles, static function ( $t ) use ( $needle ) {
		return false !== strpos( $t, $needle );
	} ) );
	return 1 === count( $matches ) ? $matches[0] : null;
}

// needle (to locate the real title) => assertions to run against the split.
$registry_fixtures = array(
	'About ThePawManual'       => function ( $r ) {
		tpm_assert_same( 'About ThePawManual', $r['h1'], 'registry "About ThePawManual": no separator, not split' );
		tpm_assert_same( '', $r['deck'], 'registry "About ThePawManual": deck empty' );
	},
	'Privacy Policy'           => function ( $r ) {
		tpm_assert_same( 'Privacy Policy', $r['h1'], 'registry "Privacy Policy": no separator, not split' );
	},
	'Why Does My Cat Bite Me?' => function ( $r ) {
		tpm_assert_same( 'Why Does My Cat Bite Me?', $r['h1'], 'registry "Why Does My Cat Bite Me?": "?" split' );
		tpm_assert_same( 'A Field Guide to Every Bite', $r['deck'], 'registry: deck correct' );
	},
	'Why Does My Cat Lick Me?' => function ( $r ) {
		tpm_assert_same( 'Why Does My Cat Lick Me?', $r['h1'], 'registry "Why Does My Cat Lick Me?": "?" split' );
		tpm_assert_same( "You've Been Accepted Into the Colony", $r['deck'], 'registry: apostrophe in deck untouched' );
	},
	// The 5-character floor (lowered from 12 — see tpm_split_title()'s
	// docblock) is exactly what lets these short-hook titles split at all;
	// each of the fixtures below this one is a real title that a 12-char
	// floor silently left unsplit, including two LIVE pages (/tools/ and
	// /pet-guides/) at the time that defect was found.
	'Bengal:'                  => function ( $r ) {
		tpm_assert_same( 'Bengal', $r['h1'], 'registry "Bengal": 6-char h1, split' );
		tpm_assert_same( 'A Beautiful Cat That Comes With Homework', $r['deck'], 'registry "Bengal": deck correct' );
	},
	'Poodle:'                  => function ( $r ) {
		tpm_assert_same( 'Poodle', $r['h1'], 'registry "Poodle": 6-char h1, split' );
		tpm_assert_same( "The Smartest Dog You're Underestimating", $r['deck'], 'registry "Poodle": deck correct' );
	},
	'Maine Coon:'              => function ( $r ) {
		tpm_assert_same( 'Maine Coon', $r['h1'], 'registry "Maine Coon": 10-char h1, split' );
	},
	'Persian:'                 => function ( $r ) {
		tpm_assert_same( 'Persian', $r['h1'], 'registry "Persian": 7-char h1, split' );
	},
	'Ragdoll:'                 => function ( $r ) {
		tpm_assert_same( 'Ragdoll', $r['h1'], 'registry "Ragdoll": 7-char h1, split' );
	},
	'Siamese:'                 => function ( $r ) {
		tpm_assert_same( 'Siamese', $r['h1'], 'registry "Siamese": 7-char h1, split' );
	},
	'Kitten Food:'             => function ( $r ) {
		tpm_assert_same( 'Kitten Food', $r['h1'], 'registry "Kitten Food": 11-char h1, split' );
	},
	'Cat Health:'              => function ( $r ) {
		tpm_assert_same( 'Cat Health', $r['h1'], 'registry "Cat Health": 10-char h1, split' );
	},
	// Needle disambiguated from "8-Week-Old Kitten Care: …" (a different
	// live title that also contains the bare substring "Kitten Care:").
	'Kitten Care: The First Months' => function ( $r ) {
		tpm_assert_same( 'Kitten Care', $r['h1'], 'registry "Kitten Care": 11-char h1, split' );
	},
	'Puppy Food:'              => function ( $r ) {
		tpm_assert_same( 'Puppy Food', $r['h1'], 'registry "Puppy Food": 10-char h1, split' );
	},
	'Dog Health:'              => function ( $r ) {
		tpm_assert_same( 'Dog Health', $r['h1'], 'registry "Dog Health": 10-char h1, split' );
	},
	'Puppy Care:'              => function ( $r ) {
		tpm_assert_same( 'Puppy Care', $r['h1'], 'registry "Puppy Care": 10-char h1, split' );
	},
	// The task brief's own worked example of the defect: the live /tools/
	// and /pet-guides/ pages, whose 5- and 10-char hooks were exactly what
	// the old 12-char floor rejected.
	'Pet Guides:'              => function ( $r ) {
		tpm_assert_same( 'Pet Guides', $r['h1'], 'registry "Pet Guides": 10-char h1, split (was live-broken at the 12-char floor)' );
		tpm_assert_same( 'The Decisions That Apply to Every Pet', $r['deck'], 'registry "Pet Guides": deck correct' );
	},
	'Tools:'                   => function ( $r ) {
		tpm_assert_same( 'Tools', $r['h1'], 'registry "Tools": 5-char h1, split (was live-broken at the 12-char floor)' );
		tpm_assert_same( 'Free Pet Calculators', $r['deck'], 'registry "Tools": deck correct' );
	},
	// These two titles carried an em dash in the deck when this fixture was
	// written (proving a multibyte char past the split point survives
	// intact); the 2026-09-15 em-dash purge rewrote both, per CLAUDE.md §9's
	// house-style rule, so the assertions below check today's actual wording
	// instead. The multibyte-survival case itself is still covered above by
	// the synthetic curly-apostrophe and en-dash-in-deck fixtures, which
	// aren't real content and so aren't subject to the house-style rule.
	'French Bulldog:'          => function ( $r ) {
		tpm_assert_same( 'French Bulldog', $r['h1'], 'registry "French Bulldog": colon wins as the leftmost separator' );
		tpm_assert_same( "America's #1 Breed, and Its Most Complicated", $r['deck'], 'registry "French Bulldog": deck matches the post-purge title' );
	},
	// Same shape: "?" is the leftmost separator, so it wins.
	'Can Cats Eat Tuna?'       => function ( $r ) {
		tpm_assert_same( 'Can Cats Eat Tuna?', $r['h1'], 'registry "Can Cats Eat Tuna?": "?" wins as the leftmost separator' );
		tpm_assert_same( 'Yes, As a Treat, Never as a Diet', $r['deck'], 'registry "Can Cats Eat Tuna?": deck matches the post-purge title' );
	},
	// A real parenthetical that is NOT the parens-skip case: the colon
	// sits well before the "(" opens, so it splits normally.
	'Recall Training:'         => function ( $r ) {
		tpm_assert_same( 'Recall Training', $r['h1'], 'registry "Recall Training": colon before any parens' );
		tpm_assert_contains( '(and Why Yours', $r['deck'], 'registry "Recall Training": parenthetical preserved in deck' );
	},
	'Low-Shedding Dog Breeds:' => function ( $r ) {
		tpm_assert_same( 'Low-Shedding Dog Breeds', $r['h1'], 'registry "Low-Shedding Dog Breeds": colon split' );
		tpm_assert_contains( 'Hypoallergenic', $r['deck'], 'registry: deck text intact' );
	},
	// The exact example named in the task brief (SPEC.md requirement 5).
	'How Much Does a Pet Cost?' => function ( $r ) {
		tpm_assert_same( 'How Much Does a Pet Cost?', $r['h1'], 'registry: matches the task brief example' );
	},
);
foreach ( $registry_fixtures as $needle => $check ) {
	$title = tpm_find_registry_title( $registry_titles, $needle );
	if ( null === $title ) {
		tpm_test_report( false, "registry fixture found for: {$needle}", 'no unique match in publish-registry.json' );
		continue;
	}
	$check( tpm_split_title( $title ) );
}

/* ==================== tpm_prepare_article_content() ===================== */

echo "== tpm_prepare_article_content() ==\n";

// The regression test for CLAUDE.md §13 / script-protection.php: a
// realistic article — long lead paragraph, two H2s (one is the FAQ
// heading), three H3/P question pairs, and a JSON-LD <script> whose text
// contains a literal "</div>" — the exact failure mode script-protection's
// own docblock names as the one that used to eat the rest of the page.
$lead       = 'Bringing home a new puppy changes your daily routine more than most owners expect going in, and understanding the basics up front makes the first weeks far less stressful for both of you.';
$last_para  = 'This is the final answer text, unique and identifiable so we can assert it survived the transform.';
$faq_script = '<script type="application/ld+json">{"@context":"https://schema.org","@type":"FAQPage","mainEntity":[{"@type":"Question","name":"Is it normal for a puppy to whine the first few nights?","acceptedAnswer":{"@type":"Answer","text":"Most puppies whine a little at first. If yours has not settled after two weeks </div> talk to your vet to rule out anything medical."}}]}</script>';

$article_html = '<p>' . $lead . '</p>' . "\n"
	. "<h2>Getting Ready Before Pickup Day</h2>\n"
	. "<p>Have crates, bowls, and a vet appointment lined up before your puppy arrives so the first few days go smoothly.</p>\n"
	. "<h2>Frequently asked questions</h2>\n"
	. "<h3>How long does it take a puppy to settle in?</h3>\n"
	. "<p>Most puppies settle into a household routine within the first one to two weeks, though every dog is different.</p>\n"
	. "<h3>What supplies do I actually need on day one?</h3>\n"
	. "<p>A crate, food and water bowls, a collar with an ID tag, and food matched to your vet's recommendation.</p>\n"
	. '<h3>Is it normal for a puppy to whine the first few nights?</h3>' . "\n"
	. '<p>' . $last_para . '</p>' . "\n"
	. $faq_script;

$result = tpm_prepare_article_content( $article_html, 1 );

tpm_assert_same( $lead, $result['takeaway'], 'realistic fragment: lead paragraph lifted out verbatim' );
tpm_assert_not_contains( $lead, $result['content'], 'realistic fragment: lead paragraph removed from content' );
tpm_assert_contains( 'Getting Ready Before Pickup Day', $result['content'], 'realistic fragment: first H2 section untouched' );
tpm_assert( 3 === substr_count( $result['content'], '<details' ), 'realistic fragment: three FAQ items became <details>' );
tpm_assert( 3 === substr_count( $result['content'], '<summary' ), 'realistic fragment: three <summary> wrappers' );
tpm_assert_contains( 'How long does it take a puppy to settle in?', $result['content'], 'realistic fragment: Q1 text byte-identical' );
tpm_assert_contains( 'What supplies do I actually need on day one?', $result['content'], 'realistic fragment: Q2 text byte-identical' );
tpm_assert_contains( 'Is it normal for a puppy to whine the first few nights?', $result['content'], 'realistic fragment: Q3 text byte-identical' );
tpm_assert_contains( $faq_script, $result['content'], 'realistic fragment: JSON-LD <script> restored VERBATIM, embedded </div> and all' );
tpm_assert_contains( $last_para, $result['content'], "realistic fragment: NO content lost — last paragraph's text survives" );

// Fail-closed: an unclosed <script> tag (a plausible hand-edit mistake)
// must leave the content completely untouched. tpm_extract_script_blocks()
// can't PROVE the whole script body is protected, so the DOM pass must not
// run at all — the exact corruption this file exists to avoid.
$unbalanced_html = '<p>This lead paragraph is intentionally long enough to normally qualify as a takeaway candidate for this fail-closed test case.</p>' . "\n"
	. "<h2>Some Section</h2>\n"
	. '<p>Body copy that would normally stay exactly where it is.</p>' . "\n"
	. '<script type="application/ld+json">{"@context":"https://schema.org","@type":"FAQPage"}';

$result = tpm_prepare_article_content( $unbalanced_html, 1 );
tpm_assert_same( $unbalanced_html, $result['content'], 'unclosed <script>: content returned completely unchanged (fail closed)' );
tpm_assert_same( '', $result['takeaway'], 'unclosed <script>: takeaway is empty, not a partial extraction' );

// Fail-closed: a stray, unbalanced closing tag (a plausible hand-edit
// mistake — an extra </div> typed while rearranging a table) makes libxml's
// HTML5 recovery close the wrapping scaffold <div> EARLY. Unlike the
// unclosed-<script> case above, loadHTML() still returns true and
// documentElement is still the wrapping 'div', so neither of THOSE guards
// catches it — only tpm_content_blocks_lost_too_much_text() does. This is
// the exact CLAUDE.md §11 (2026-08-14) pre-deploy-review finding: everything
// after the stray "</div>" used to silently vanish from the page with no
// error at all.
$truncating_html = '<p>' . str_repeat( 'This paragraph is long enough to qualify as a takeaway candidate. ', 2 ) . '</p>' . "\n"
	. "<h2>Section A</h2>\n"
	. "<p>Body text for section A.</p>\n"
	. "</div>\n"
	. "<h2>Section B</h2>\n"
	. '<p>ENDMARK — this text must survive.</p>';

$result = tpm_prepare_article_content( $truncating_html, 1 );
tpm_assert_same( $truncating_html, $result['content'], 'stray closing tag: content returned completely unchanged (fail closed)' );
tpm_assert_same( '', $result['takeaway'], 'stray closing tag: takeaway is empty, not a partial extraction' );

// First element is an H2, not a P: nothing qualifies as a takeaway, and —
// with no "Frequently asked questions" H2 or inline-tool config registered
// in this harness — nothing else in the pass has anything to do either.
$h2_first_html = "<h2>Choosing the Right Crate Size</h2>\n<p>Pick a crate sized for your dog's adult size, not their current puppy size, using a divider panel if needed.</p>";
$result        = tpm_prepare_article_content( $h2_first_html, 1 );
tpm_assert_same( '', $result['takeaway'], 'h2-first fragment: takeaway is empty' );
tpm_assert_same( $h2_first_html, $result['content'], 'h2-first fragment: content unchanged' );

/* ========== FINDING 1 — entity-neutral truncation guard (regression) ==== */

echo "== FINDING 1: entity-neutral truncation guard ==\n";

// Pre-fix, both tpm_content_blocks_lost_too_much_text() and its
// content-nav.php twin compared mb_strlen( wp_strip_all_tags( $x ) )
// directly — no entity decoding. A DOMDocument round-trip decodes
// &#39;/&quot; (which the markdown -> WXR conversion writes for every
// apostrophe/quote in imported post_content) to literal '/" while
// re-serializing, so the "after" side legitimately shrank by 4 chars per
// &#39; and 5 per &quot; with ZERO content lost. This is the exact defect
// that false-positived the content-blocks.php guard on 7 of the 17 wave-1
// pages when replayed through the real the_content pipeline (round 3
// verification; CLAUDE.md §13's round-2 note originally said "7 of 18,"
// unreproduced — corrected once it was) — every fixture in the suite above
// used only ASCII apostrophes, so 70/70 passed WITH the bug present. This
// section is the blind spot closed.
//
// The pre-fix comparison is replicated inline (not called — it no longer
// exists) purely to document and prove the regression this section guards
// against: on this exact fixture it returns TRUE (fires — the bug), while
// both the current, fixed guards return FALSE (do not fire).
function tpm_test_pre_fix_lost_too_much_text( $protected_content, $serialized ) {
	$before    = mb_strlen( wp_strip_all_tags( (string) $protected_content ) );
	$after     = mb_strlen( wp_strip_all_tags( (string) $serialized ) );
	$tolerance = max( 20, (int) ceil( $before * 0.02 ) );
	return ( $after + $tolerance ) < $before;
}

// A realistic pre-parse string next to its post-DOMDocument-round-trip
// equivalent: the entities decode, nothing else changes at all — exactly
// what tpm_prepare_article_content() feeds its own guard in production once
// the takeaway/FAQ restructuring has run over real, apostrophe-heavy copy.
$entity_before = str_repeat(
	"A puppy&#39;s routine and a family&#39;s patience both matter, and quotes like &quot;settle in&quot; show up often in copy like this. ",
	4
);
$entity_after = str_repeat(
	'A puppy\'s routine and a family\'s patience both matter, and quotes like "settle in" show up often in copy like this. ',
	4
);

tpm_assert(
	tpm_test_pre_fix_lost_too_much_text( $entity_before, $entity_after ),
	'entity drift regression: the OLD (pre-fix) comparison FALSE-POSITIVES on lossless entity decoding',
	'this assertion documents the bug being fixed — it must be true, proving the fixture reproduces it'
);
tpm_assert(
	! tpm_content_blocks_lost_too_much_text( $entity_before, $entity_after ),
	'entity drift regression: the FIXED content-blocks.php guard does NOT fire on the same input'
);
tpm_assert(
	! tpm_content_nav_lost_too_much_text( $entity_before, $entity_after ),
	'entity drift regression: the FIXED content-nav.php guard (twin function) does NOT fire on the same input'
);

// End-to-end: run entity-heavy, realistic article content through the real
// pipeline — tpm_prepare_article_content() then tpm_inject_heading_ids(), the
// same order page.php uses — and confirm the takeaway/FAQ/TOC all still get
// built, not silently dropped to the untouched-original fallback.
$entity_lead = "A puppy&#39;s first weeks at home set the tone for the rest of your dog&#39;s life, and getting the basics right from day one &mdash; feeding, sleep, and a predictable routine &mdash; makes the transition easier for your whole family&#39;s daily rhythm.";

$entity_article_html = '<p>' . $entity_lead . '</p>' . "\n"
	. "<h2>Getting Ready Before Pickup Day</h2>\n"
	. "<p>Have crates, bowls, and a vet appointment lined up so your puppy&#39;s first days go smoothly, and don&#39;t forget a &quot;go bag&quot; for the car ride home.</p>\n"
	. "<h2>Frequently asked questions</h2>\n"
	. "<h3>How long does it take a puppy to settle in?</h3>\n"
	. "<p>Most puppies settle into a household&#39;s routine within one to two weeks, though every dog&#39;s pace is different.</p>\n"
	. "<h3>What supplies do I actually need on day one?</h3>\n"
	. '<p>A crate, food and water bowls, and a &quot;starter&quot; bag of your vet&#39;s recommended food.</p>';

$entity_result = tpm_prepare_article_content( $entity_article_html, 1 );

tpm_assert( '' !== $entity_result['takeaway'], 'entity-heavy article: takeaway was extracted (guard did not false-fire)' );
tpm_assert( 2 === substr_count( $entity_result['content'], '<details' ), 'entity-heavy article: both FAQ items became <details> (guard did not false-fire)' );

$entity_toc = tpm_inject_heading_ids( $entity_result['content'] );
tpm_assert( 2 === count( $entity_toc['items'] ), 'entity-heavy article: both H2s made it into the TOC (content-nav guard did not false-fire)', 'got ' . count( $entity_toc['items'] ) . ' items' );

// Genuine truncation must still be caught even in entity-heavy content — the
// normalization fix must not mask a real loss. Same shape as the "stray
// closing tag" fixture above, with entities threaded through every
// paragraph, proving the fix isn't just "raise the tolerance until entity
// noise fits under it."
$entity_truncating_html = '<p>' . str_repeat( "This paragraph has an apostrophe like a puppy&#39;s routine and a &quot;quoted&quot; phrase in it, repeated so it is long enough to qualify as a takeaway candidate. ", 2 ) . '</p>' . "\n"
	. "<h2>Section A</h2>\n"
	. "<p>Body text for section A, with a family&#39;s patience and a &quot;settle in&quot; phrase thrown in.</p>\n"
	. "</div>\n"
	. "<h2>Section B</h2>\n"
	. '<p>ENDMARK &#39;&#39; &quot;&quot; &mdash; this text must survive.</p>';

$entity_truncated_result = tpm_prepare_article_content( $entity_truncating_html, 1 );
tpm_assert_same( $entity_truncating_html, $entity_truncated_result['content'], 'entity-heavy stray closing tag: content returned unchanged — genuine truncation still caught, not masked by entity normalization' );
tpm_assert_same( '', $entity_truncated_result['takeaway'], 'entity-heavy stray closing tag: takeaway is empty, not a partial extraction' );

/* ============ FINDING 5 — .pm-calc widget heading excluded from TOC ===== */

echo "== FINDING 5: .pm-calc widget heading excluded from the TOC ==\n";

// Simulates a page whose body contains the [pawmanual_calculator] shortcode
// output (plugin: templates/calculator-shell.php) between two real article
// H2s — the exact shape of /tools/dog-age-calculator/ and
// /tools/cat-age-calculator/. getElementsByTagName( 'h2' ) walks the WHOLE
// subtree, not just $root's direct children, so without the fix the
// widget's own "Dog Age Calculator" <h2 class="pm-calc__title"> becomes a
// spurious first TOC entry — it titles the CALCULATOR, not a section of the
// article.
$calc_widget_html = "<h2>Getting Ready Before Pickup Day</h2>\n"
	. "<p>Have crates, bowls, and a vet appointment lined up before your puppy arrives.</p>\n"
	. '<div class="pm-calc" data-calculator="dog-age"><div class="pm-calc__card"><h2 class="pm-calc__title">Dog Age Calculator</h2><form class="pm-calc__form"></form></div></div>' . "\n"
	. "<h2>Frequently asked questions</h2>\n"
	. "<h3>How long does it take a puppy to settle in?</h3>\n"
	. '<p>Most puppies settle into a household routine within the first one to two weeks.</p>';

$calc_toc       = tpm_inject_heading_ids( $calc_widget_html );
$calc_toc_texts = array_column( $calc_toc['items'], 'text' );

tpm_assert( 2 === count( $calc_toc['items'] ), 'pm-calc heading skip: exactly the two real article H2s are collected, not three', 'got ' . count( $calc_toc['items'] ) . ' items: ' . implode( ', ', $calc_toc_texts ) );
tpm_assert( ! in_array( 'Dog Age Calculator', $calc_toc_texts, true ), 'pm-calc heading skip: the widget title text is NOT in the TOC items' );
tpm_assert( in_array( 'Getting Ready Before Pickup Day', $calc_toc_texts, true ), 'pm-calc heading skip: real first H2 still collected' );
tpm_assert( in_array( 'Frequently asked questions', $calc_toc_texts, true ), 'pm-calc heading skip: real second H2 still collected' );
tpm_assert_not_contains( 'id="dog-age-calculator"', $calc_toc['content'], 'pm-calc heading skip: the widget h2 itself got no id attribute stamped on it' );

/* ===== ROUND 3, FINDING 1 — the /tools/ h1 override reaches the <h1> ==== */

echo "== ROUND 3, FINDING 1: article-header.php reads the resolved title_parts ==\n";

// This is the one case in this suite that requires the actual TEMPLATE
// (template-parts/content/article-header.php), not just the inc/ files —
// a unit test of tpm_get_title_parts() alone can't catch this regression,
// because that function was already correct; the bug was that
// article-header.php (the file that actually renders the <h1>) never read
// its result. The stubs below exist ONLY to let this one template run
// standalone outside WordPress, the same "stub only what's called" posture
// as every stub above — none of them are needed by the four inc/ files
// this suite otherwise tests.
function get_the_title( $post_id = 0 ) {
	return 'Tools: Free Pet Calculators'; // The stored WP post title — unchanged by the 2026-09-14 H1 decision below; only the RENDERED h1 changes.
}
function get_page_uri( $post_id = 0 ) {
	return 'tools'; // The one path actually seeded in tpm_get_title_part_overrides().
}
function get_post( $post_id = null ) {
	return null; // No parent post -> tpm_get_eyebrow() returns null early; nothing else in article-header.php needs a real WP_Post here.
}
function get_the_date( $format = '', $post = null ) {
	return '1700000000';
}
function get_the_modified_date( $format = '', $post = null ) {
	return '1700000000'; // Same instant as get_the_date() above -> tpm_get_last_updated_label() takes its "Published", not "Updated", branch; irrelevant to this test's assertion either way.
}
function get_option( $option, $default = false ) {
	return 'F j, Y';
}
function get_template_part( $slug, $name = null, $part_args = array() ) {
	// Only RECORDS which partials the header asks for, in order: the h1
	// assertions here don't need their output (takeaway is ''), and the
	// page-image section below reads this to prove the image comes first.
	$GLOBALS['tpm_test_template_parts'][] = $slug;
}
if ( ! defined( 'DAY_IN_SECONDS' ) ) {
	define( 'DAY_IN_SECONDS', 86400 );
}

// tpm_get_title_parts() IS the override-map lookup under test: with
// get_page_uri() stubbed to 'tools' above, this exercises the exact same
// map lookup production hits for the live /tools/ page. Asserting on ITS
// return value guards the override map itself — if a future edit bypassed
// the map (e.g. called tpm_split_title() unconditionally instead — which
// would produce h1 "Tools", deck "Free Pet Calculators", the FLIPPED
// shape — this fails), independent of the template below.
//
// 2026-09-14, client design decision: the override now hand-authors
// h1 "Free Pet Calculators" / deck "" instead of keeping the WP post title
// whole. "Tools" is a navigation word, not a search phrase, and /tools/ is
// not yet indexed, so there is no ranking on the old wording to protect
// (see inc/article-meta.php's tpm_get_title_part_overrides() docblock).
$title_parts = tpm_get_title_parts( 4242 );
tpm_assert_same( 'Free Pet Calculators', $title_parts['h1'], 'tpm_get_title_parts(): override-map path returns the hand-authored short h1 (fails if the override map is bypassed or reverted)' );
tpm_assert_same( '', $title_parts['deck'], 'tpm_get_title_parts(): override-map path: deck stays empty' );

// The actual regression guard: render the real template with the SAME
// $args shape page.php passes (title_parts already resolved), exactly the
// way [pawmanual_calculator]-free wp_head()-less request would. A plain
// `include`/`require` shares this scope's locals, so `$args` below is what
// article-header.php's own `isset( $args['title_parts'] )` check sees.
$article_header_path = __DIR__ . '/../../wp-content/themes/thepawmanual/template-parts/content/article-header.php';
$args                = array(
	'post_id'     => 4242,
	'is_hub'      => true,
	'takeaway'    => '',
	'title_parts' => $title_parts,
);
ob_start();
require $article_header_path;
$rendered_header = ob_get_clean();

tpm_assert_not_contains( 'Tools: Free Pet Calculators', $rendered_header, 'article-header.php: does NOT render the full stored post title (this is what regresses to the whole title if $args[\'title_parts\'] stops being read, or the override reverts to `true`)' );

// Extract the <h1>...</h1> inner text specifically (trimmed, whitespace
// collapsed — the template echoes it on its own indented line) rather than
// a raw substring match, so this can't be fooled by incidental formatting
// the way a plain tpm_assert_not_contains() on the literal tag text would be.
preg_match( '#<h1[^>]*>(.*?)</h1>#s', $rendered_header, $h1_match );
$h1_text = isset( $h1_match[1] ) ? trim( preg_replace( '/\s+/', ' ', $h1_match[1] ) ) : null;
tpm_assert_same( 'Free Pet Calculators', $h1_text, 'article-header.php: h1 text is EXACTLY "Free Pet Calculators" — short, no deck clause' );

tpm_assert_not_contains( 'tpm-article-deck', $rendered_header, 'article-header.php: no deck paragraph rendered (this override\'s deck is empty)' );

/* == ROUND 3, FINDING 2 — script-placeholder guard, wpautop-wrapped shape = */

echo "== ROUND 3, FINDING 2: FAQ JSON-LD placeholder recognised once wpautop wraps it ==\n";

// The pre-fix version of tpm_dom_is_script_placeholder_node() tested only
// XML_TEXT_NODE — which is why 83 green assertions in this suite (the
// "realistic fragment" case above, and every entity-heavy one after it)
// never caught this: every fixture above builds its FAQ script as a bare
// top-level node, i.e. the exact shape the ORIGINAL predicate expected, and
// NEVER the shape wpautop() actually produces against real content, which
// wraps any bare placeholder line sitting between two blank lines in a
// `<p>` — the same way it wraps ordinary prose. This fixture closes that
// blind spot: the script sits on its own line, blank-line-separated, same
// as every real wave-1 page, so tpm_extract_script_blocks()'s placeholder
// ends up wrapped in a `<p>` by the time this pass's own DOMDocument parse
// sees it — without needing wpautop() itself in this dependency-free
// harness, since a `<p>...</p>` written directly here is byte-identical to
// what wpautop() would have produced.
$wrapped_faq_html = "<p>{$lead}</p>\n"
	. "<h2>Getting Ready Before Pickup Day</h2>\n"
	. "<p>Have crates, bowls, and a vet appointment lined up before your puppy arrives so the first few days go smoothly.</p>\n"
	. "<h2>Frequently asked questions</h2>\n"
	. "<h3>How long does it take a puppy to settle in?</h3>\n"
	. "<p>Most puppies settle into a household routine within the first one to two weeks, though every dog is different.</p>\n"
	. "<h3>What supplies do I actually need on day one?</h3>\n"
	. "<p>A crate, food and water bowls, a collar with an ID tag, and food matched to your vet's recommendation.</p>\n"
	. '<h3>Is it normal for a puppy to whine the first few nights?</h3>' . "\n"
	. '<p>' . $last_para . '</p>' . "\n"
	. "\n"                     // The blank line wpautop() leaves between paragraphs...
	. '<p>' . $faq_script . '</p>' . "\n"; // ...is exactly what makes wpautop() wrap the placeholder in its own <p>.

$wrapped_result = tpm_prepare_article_content( $wrapped_faq_html, 1 );

tpm_assert_contains( $faq_script, $wrapped_result['content'], 'wpautop-wrapped placeholder: JSON-LD <script> restored, byte-identical' );
tpm_assert( 3 === substr_count( $wrapped_result['content'], '<details' ), 'wpautop-wrapped placeholder: still exactly three FAQ items became <details>' );
tpm_assert_contains( $last_para, $wrapped_result['content'], 'wpautop-wrapped placeholder: last FAQ answer text intact' );

// The actual regression guard: the restored <script> must NOT be inside a
// <details> — parse the real output and check ancestry, rather than a
// string-position heuristic, so this can't be fooled by coincidental text
// ordering.
$script_dom = new DOMDocument();
libxml_use_internal_errors( true );
$script_dom->loadHTML( '<?xml encoding="utf-8" ?><div>' . $wrapped_result['content'] . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD );
libxml_clear_errors();

$script_nested_in_details = false;
foreach ( $script_dom->getElementsByTagName( 'script' ) as $script_node ) {
	$ancestor = $script_node->parentNode;
	while ( $ancestor instanceof DOMElement ) {
		if ( 'details' === $ancestor->tagName ) {
			$script_nested_in_details = true;
			break 2;
		}
		$ancestor = $ancestor->parentNode;
	}
}
tpm_assert( ! $script_nested_in_details, 'wpautop-wrapped placeholder: restored <script> is NOT nested inside a <details> (this is the regression the unwrapped fixture above could never catch)' );

/* == ROUND 4, FINDING 1 — inline CTA dedup must see the LEAD paragraph's == */

echo "== ROUND 4, FINDING 1: inline tool CTA dedup sees a link in the lead paragraph ==\n";

// tpm_get_inline_tool_for_post() (inc/tools-data.php) is a different
// worker's file and isn't loaded by this harness; stub it exactly the way
// content-blocks.php's own function_exists() guard expects — one
// BUILT+PUBLISHED tool row for a single dedicated post ID, so this fixture
// can't change the outcome of any OTHER tpm_prepare_article_content() call
// above (every one of which passes post_id 1, not 9001).
function tpm_get_inline_tool_for_post( $post_id ) {
	if ( 9001 !== $post_id ) {
		return null;
	}
	return array(
		'title'       => 'Pet Cost Calculator',
		'description' => 'Estimate what a new pet actually costs, month to month and year one.',
		'url'         => '/tools/pet-cost-calculator/',
		'icon'        => 'calculator',
	);
}

// The exact live shape CLAUDE.md §13 names: /pet-guides/pet-costs/'s OWN
// lead paragraph already links the tool this page would otherwise earn a
// second, redundant CTA for. Two real H2s follow it, neither the FAQ
// heading, so tpm_dom_inject_inline_tool_cta() has somewhere it WOULD
// insert a card if the dedup check failed to see that link — which is
// exactly what happened pre-fix: tpm_dom_extract_lead_paragraph() (mutator
// (a)) detaches this paragraph from $root before the dedup check
// (mutator (c)) ever runs, so a check re-scanning $root at that point finds
// no link at all, and duplicates the CTA the lead already gave the reader.
$dedup_lead = 'Owning a pet costs far more than the adoption fee, and our <a href="/tools/pet-cost-calculator/">pet cost calculator</a> breaks down food, vet care, and everyday supplies so you know the real number before you bring an animal home.';

$dedup_html = '<p>' . $dedup_lead . '</p>' . "\n"
	. "<h2>What Pet Ownership Really Costs</h2>\n"
	. "<p>Food, litter, toys, and routine vet care add up fast in the first year of ownership.</p>\n"
	. "<h2>Budgeting For the Unexpected</h2>\n"
	. "<p>An emergency fund matters as much as the predictable monthly costs do.</p>\n";

$dedup_result = tpm_prepare_article_content( $dedup_html, 9001 );

tpm_assert_contains( 'pet cost calculator', $dedup_result['takeaway'], "inline CTA dedup: the lead paragraph's own tool link survives into the takeaway" );
tpm_assert_not_contains( 'tpm-tool-cta', $dedup_result['content'], 'inline CTA dedup: NO second CTA injected for a tool the lead paragraph already links (the regression this guards: pre-fix, the dedup check ran against $root AFTER the lead paragraph was already detached from it, so it never saw this link and injected a duplicate CTA)' );

/* ================= The page image (a Page's Featured image) ================ */

echo "== PAGE IMAGE: sits under the title and above the key takeaway ==\n";

// The ordering requirement: image between the title/deck and the answer box.
// article-header.php was rendered above with get_template_part() recording
// the partials it asks for, in order; the h1/deck are echoed inline, so their
// position relative to the image is checked against the template's source.
$header_parts = $GLOBALS['tpm_test_template_parts'];
$image_at     = array_search( 'template-parts/content/page-image', $header_parts, true );
$takeaway_at  = array_search( 'template-parts/content/key-takeaway', $header_parts, true );
tpm_assert( false !== $image_at && false !== $takeaway_at, 'article-header.php asks for both the page image and the key takeaway', 'saw: ' . implode( ', ', $header_parts ) );
tpm_assert( false !== $image_at && false !== $takeaway_at && $image_at < $takeaway_at, 'article-header.php: the page image is requested BEFORE the key takeaway (fails if the image is moved below the answer box)' );
$header_src = (string) file_get_contents( $article_header_path );
$h1_at    = strpos( $header_src, '</h1>' );
$deck_at  = strpos( $header_src, 'tpm-article-deck' );
$image_in = strpos( $header_src, 'content/page-image' );
tpm_assert( false !== $h1_at && false !== $image_in && $h1_at < $image_in, 'article-header.php: the page image comes after the h1' );
tpm_assert( false !== $deck_at && false !== $image_in && $deck_at < $image_in, 'article-header.php: the page image comes after the deck' );

echo "== PAGE IMAGE: tpm_get_page_image_slot() ==\n";

// Dimensions the helper reads from the attachment's metadata. WordPress stores
// the scaled copy's size there, so 2560x1429 is what a 4 MB 16:9 upload looks
// like once core has scaled it down.
function wp_get_attachment_metadata( $attachment_id = 0 ) {
	return $GLOBALS['tpm_test_attachment_meta'][ $attachment_id ] ?? false;
}
$GLOBALS['tpm_test_attachment_meta'] = array(
	11 => array( 'width' => 1024, 'height' => 1024 ),
	12 => array( 'width' => 2560, 'height' => 1429 ),
	13 => array( 'width' => 4000, 'height' => 1000 ),
	14 => array( 'width' => 1000, 'height' => 3000 ),
	15 => array(),
	16 => array( 'width' => 300, 'height' => 300 ),
	17 => array( 'width' => 100, 'height' => 100 ),
);
tpm_assert_same( 448, tpm_get_page_image_slot( 11 ), 'slot: a square image is drawn 448px wide, so 448px tall (what the first three images got)' );
tpm_assert_same( 803, tpm_get_page_image_slot( 12 ), 'slot: a 2560x1429 image is drawn 803px wide, still 448px tall (a fixed 448px width would leave it a 250px sliver)' );
tpm_assert_same( 960, tpm_get_page_image_slot( 13 ), 'slot: a very wide image is held to the 960px column' );
tpm_assert_same( 240, tpm_get_page_image_slot( 14 ), 'slot: a very tall image is not drawn narrower than 240px' );
tpm_assert_same( 448, tpm_get_page_image_slot( 15 ), 'slot: an attachment with empty dimensions is treated as a square, not guessed wider' );
tpm_assert_same( 448, tpm_get_page_image_slot( 99 ), 'slot: an attachment with no metadata at all is treated as a square' );
tpm_assert_same( 300, tpm_get_page_image_slot( 16 ), 'slot: a 300px upload is drawn 300px wide, not stretched to 448px' );
tpm_assert_same( 240, tpm_get_page_image_slot( 17 ), 'slot: a tiny upload still gets the 240px minimum rather than a postage stamp' );

echo "== PAGE IMAGE: tpm_get_page_image() ==\n";

// Stubs for the WP calls the helper makes. get_the_post_thumbnail() reports
// what the srcset-cap filter would have returned at the moment core builds the
// srcset, which is the one thing worth observing, and can be told to return
// nothing (a deleted attachment) or to throw.
function get_post_thumbnail_id( $post = null ) {
	return $GLOBALS['tpm_test_thumb_id'] ?? 0;
}
function get_the_post_thumbnail( $post = null, $size = 'post-thumbnail', $attr = '' ) {
	$cap = null;
	foreach ( $GLOBALS['tpm_test_filters']['max_srcset_image_width'] ?? array() as $callback ) {
		$cap = $callback( 2048, array( 768, 768 ) );
		foreach ( $GLOBALS['tpm_test_observe_incoming'] ?? array() as $incoming ) {
			$GLOBALS['tpm_test_observed'][] = $callback( $incoming, array( 768, 768 ) );
		}
	}
	$GLOBALS['tpm_test_thumb_call'] = array(
		'size' => $size,
		'attr' => $attr,
		'cap'  => $cap,
	);
	if ( ! empty( $GLOBALS['tpm_test_thumb_throws'] ) ) {
		throw new RuntimeException( 'boom' );
	}
	return $GLOBALS['tpm_test_thumb_html'] ?? '<img src="x-768x768.webp" alt="">';
}

$GLOBALS['tpm_test_thumb_id']     = 0;
$GLOBALS['tpm_test_thumb_call']   = null;
$GLOBALS['tpm_test_thumb_throws'] = false;
tpm_assert_same( array(), tpm_get_page_image( 7 ), 'tpm_get_page_image(): a Page without a Featured image returns an empty array' );
tpm_assert_same( null, $GLOBALS['tpm_test_thumb_call'], 'tpm_get_page_image(): without a Featured image core is never even asked for markup' );

$GLOBALS['tpm_test_thumb_id'] = 11;
$image                        = tpm_get_page_image( 7 );
$call                         = $GLOBALS['tpm_test_thumb_call'];
tpm_assert_contains( '<img', $image['html'], 'tpm_get_page_image(): with a Featured image, returns core\'s markup' );
tpm_assert_same( 448, $image['slot'], 'tpm_get_page_image(): a square image gets a 448px slot' );
tpm_assert_same( 'medium_large', $call['size'], 'tpm_get_page_image(): asks for the 768px size, so the plain src is a light file, not the master' );
tpm_assert_same( 896, $call['cap'], 'tpm_get_page_image(): the srcset is capped at twice the slot (896px; the 400px desktop column is narrower) while core builds it, which keeps the multi-MB original out' );
tpm_assert_same( 'eager', $call['attr']['loading'], 'tpm_get_page_image(): loading="eager" (the image is the likely LCP element)' );
tpm_assert_same( 'high', $call['attr']['fetchpriority'], 'tpm_get_page_image(): fetchpriority="high"' );
tpm_assert_contains( 'w-full', $call['attr']['class'], 'tpm_get_page_image(): fills its figure' );
tpm_assert_same( '(min-width: 1024px) 400px, (min-width: 480px) 448px, calc(100vw - 32px)', $call['attr']['sizes'], 'tpm_get_page_image(): sizes says 400px in the desktop column, 448px below lg once the viewport is wide enough, the viewport minus the 16px gutters before that' );
tpm_assert( empty( $GLOBALS['tpm_test_filters']['max_srcset_image_width'] ), 'tpm_get_page_image(): the srcset cap is removed again afterwards (a leaked cap would degrade every later image)' );

$GLOBALS['tpm_test_thumb_id'] = 12;
$wide                         = tpm_get_page_image( 7 );
$wide_call                    = $GLOBALS['tpm_test_thumb_call'];
tpm_assert_same( 803, $wide['slot'], 'tpm_get_page_image(): a 16:9 image gets its wider 803px slot' );
tpm_assert_same( 1606, $wide_call['cap'], 'tpm_get_page_image(): the srcset cap follows the slot (2 x 803), so the 1536px file stays available and the 2048px one does not' );
tpm_assert_same( '(min-width: 1024px) 400px, (min-width: 835px) 803px, calc(100vw - 32px)', $wide_call['attr']['sizes'], 'tpm_get_page_image(): sizes follows the slot below lg too' );

// The cap lowers a limit, it never raises one: a stricter site-wide srcset
// limit set by something else must still win, and 0 ("no limit") is replaced.
// get_the_post_thumbnail() above feeds each incoming limit to the registered
// filter and records what it returns.
$GLOBALS['tpm_test_thumb_id']         = 12;
$GLOBALS['tpm_test_observe_incoming'] = array( 1024, 0, 4000 );
$GLOBALS['tpm_test_observed']         = array();
tpm_get_page_image( 7 );
tpm_assert_same( array( 1024, 1606, 1606 ), $GLOBALS['tpm_test_observed'], 'tpm_get_page_image(): the srcset cap only LOWERS a limit (1024 stays 1024), replaces "no limit" (0) and caps a looser one (4000 becomes 1606)' );
unset( $GLOBALS['tpm_test_observe_incoming'], $GLOBALS['tpm_test_observed'] );

$GLOBALS['tpm_test_thumb_html'] = '';
tpm_assert_same( array(), tpm_get_page_image( 7 ), 'tpm_get_page_image(): an attachment core cannot render (deleted file) returns an empty array, not a figure around nothing' );
unset( $GLOBALS['tpm_test_thumb_html'] );

$GLOBALS['tpm_test_thumb_throws'] = true;
$thrown                           = false;
try {
	tpm_get_page_image( 7 );
} catch ( RuntimeException $e ) {
	$thrown = true;
}
tpm_assert( $thrown, 'tpm_get_page_image(): an error inside core still propagates (nothing is swallowed)' );
tpm_assert( empty( $GLOBALS['tpm_test_filters']['max_srcset_image_width'] ), 'tpm_get_page_image(): the srcset cap is removed even when core throws' );
$GLOBALS['tpm_test_thumb_throws'] = false;

// The partial itself: a figure whose width follows the image when there is
// one, nothing at all when there is not (so every page without an image
// renders exactly as it did before). Rendered inside a function so the
// partial's own variables ($post_id, $image, $args) stay out of the suite's
// global scope.
function tpm_test_render_part( $path, $part_args ) {
	$args = $part_args;
	ob_start();
	require $path;
	return ob_get_clean();
}
$part_path = __DIR__ . '/../../wp-content/themes/thepawmanual/template-parts/content/page-image.php';

$GLOBALS['tpm_test_thumb_id'] = 11;
$with_square                  = tpm_test_render_part( $part_path, array( 'post_id' => 7 ) );
tpm_assert_contains( '<figure class="tpm-page-image', $with_square, 'page-image.php: renders the figure when the Page has a Featured image' );
tpm_assert_contains( 'x-768x768.webp', $with_square, 'page-image.php: the figure holds the image markup' );
tpm_assert_contains( 'style="--tpm-page-image-max: 448px"', $with_square, 'page-image.php: below lg, a square image\'s figure is 448px at most' );
tpm_assert_contains( 'max-w-[var(--tpm-page-image-max)]', $with_square, 'page-image.php: the figure actually applies that width below lg' );
tpm_assert_contains( 'lg:max-w-none', $with_square, 'page-image.php: from lg up the figure fills the desktop image column' );

$GLOBALS['tpm_test_thumb_id'] = 12;
$with_wide                    = tpm_test_render_part( $part_path, array( 'post_id' => 7 ) );
tpm_assert_contains( 'style="--tpm-page-image-max: 803px"', $with_wide, 'page-image.php: below lg, a 16:9 image\'s figure is 803px at most' );

$GLOBALS['tpm_test_thumb_id'] = 0;
$without_image                = tpm_test_render_part( $part_path, array( 'post_id' => 7 ) );
tpm_assert_same( '', trim( $without_image ), 'page-image.php: prints nothing when the Page has no Featured image' );

$GLOBALS['tpm_test_thumb_id'] = 11; // a thumbnail exists, so only the post_id guard can keep this blank
$no_post                      = tpm_test_render_part( $part_path, array() );
tpm_assert_same( '', trim( $no_post ), 'page-image.php: prints nothing without a post_id' );

// The desktop layout: with an image, the title block and the image share a
// row from `lg` up (and only from `lg` up, so phones and tablets keep the
// stacked layout); without one, the header carries no grid at all.
$header_args = array(
	'post_id'     => 4242,
	'is_hub'      => true,
	'takeaway'    => '',
	'title_parts' => array( 'h1' => 'Pet Guides', 'deck' => 'The Decisions That Apply to Every Pet' ),
);
$GLOBALS['tpm_test_thumb_id'] = 11;
$header_with_image            = tpm_test_render_part( $article_header_path, $header_args );
tpm_assert_contains( 'lg:grid lg:grid-cols-[minmax(0,1fr)_400px]', $header_with_image, 'article-header.php: with an image, title and image sit side by side from lg up, the image in a 400px column' );
tpm_assert_same( 0, preg_match_all( '/(?<![\w:-])grid(?![\w-])/', $header_with_image ), 'article-header.php: no unprefixed "grid" class, so below lg everything still stacks as before' );
tpm_assert( TPM_PAGE_IMAGE_DESKTOP_WIDTH === 400 && false !== strpos( $header_with_image, '_400px]' ), 'article-header.php: the grid column matches TPM_PAGE_IMAGE_DESKTOP_WIDTH, the width the image\'s sizes promises' );

$GLOBALS['tpm_test_thumb_id'] = 0;
$header_without_image         = tpm_test_render_part( $article_header_path, $header_args );
tpm_assert_not_contains( 'lg:grid', $header_without_image, 'article-header.php: without an image there is no two-column grid (no empty right-hand column)' );

// The partial degrades to "no image" if the helper is missing, so a dropped
// require_once would fail silently. The suite never loads functions.php, so
// pin the wiring here.
$functions_src = (string) file_get_contents( __DIR__ . '/../../wp-content/themes/thepawmanual/functions.php' );
tpm_assert( false !== strpos( $functions_src, "inc/page-image.php'" ), 'functions.php: loads inc/page-image.php (without it every page silently loses its image)' );

/* ============================== Summary ================================ */

$pass = $GLOBALS['tpm_test_pass'];
$fail = $GLOBALS['tpm_test_fail'];
echo "\n{$pass} passed, {$fail} failed\n";
exit( $fail > 0 ? 1 : 0 );
