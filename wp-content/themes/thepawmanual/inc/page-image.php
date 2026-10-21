<?php
/**
 * The page image: a Page's Featured Image, shown between the title and the
 * key-takeaway box.
 *
 * Which image belongs to which page is NOT stored in the theme. An editor sets
 * the Page's "Featured image" in WP admin and article-header.php renders it.
 * That keeps upload URLs out of the code (they differ per environment and
 * break when a file is re-uploaded), lets the SEO specialist swap an image
 * without a deploy, and means a new page gets its image by filling in one
 * field, with no template edit. `post-thumbnails` support is declared in
 * inc/setup.php.
 *
 * The assignment lives in the database only. The WXR import does not carry
 * it, so a delete-and-reimport of a page drops its image (see
 * docs/PAGE-IMAGES.md).
 *
 * @package thepawmanual
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * No page image is drawn taller than this on desktop, in CSS pixels.
 *
 * Uploads come in different shapes (square and 16:9 both occur), and one fixed
 * width suits only one of them: 448px wide is right for a square but leaves a
 * 16:9 picture a 250px sliver. Bounding the HEIGHT instead lets a landscape
 * image use the width it needs, and a square stays 448px. This is also the
 * knob for how far the page image pushes the key-takeaway box down.
 */
define( 'TPM_PAGE_IMAGE_MAX_HEIGHT', 448 );

/**
 * Narrowest and widest the page image is ever drawn. The widest is page.php's
 * 960px column. The narrowest keeps a very tall image from shrinking to a
 * strip, at the cost of the height bound above (it only holds for images wider
 * than about 1:1.9 portrait).
 */
define( 'TPM_PAGE_IMAGE_MIN_WIDTH', 240 );
define( 'TPM_PAGE_IMAGE_MAX_WIDTH', 960 );

/**
 * Width of the page image from Tailwind's `lg` breakpoint (1024px) up, where
 * it stands in its own column to the right of the title and deck instead of
 * under them (template-parts/content/article-header.php). Shown whole, never
 * cropped: the owner ruled out cropping, and on 2026-10-21 chose this layout
 * over a full-width image so the picture fills the header without being cut
 * or zoomed. The same number is written literally into article-header.php's
 * grid class, since Tailwind only sees class names spelled out in templates:
 * change both together.
 */
define( 'TPM_PAGE_IMAGE_DESKTOP_WIDTH', 400 );

/**
 * Widest this attachment is drawn, in CSS pixels: the width at which it is
 * TPM_PAGE_IMAGE_MAX_HEIGHT tall, kept inside the column and never wider than
 * the file itself (so a small upload is not stretched soft), except that
 * nothing is drawn narrower than TPM_PAGE_IMAGE_MIN_WIDTH: an upload under
 * 240px is stretched to 240px.
 *
 * Read from the attachment's own dimensions, not from the file: WordPress
 * stores the scaled copy's size as the attachment's main size, which is the
 * shape that matters here.
 *
 * @param int $attachment_id Featured-image attachment ID.
 * @return int Width in CSS pixels.
 */
function tpm_get_page_image_slot( $attachment_id ) {
	$meta   = wp_get_attachment_metadata( $attachment_id );
	$width  = isset( $meta['width'] ) ? (int) $meta['width'] : 0;
	$height = isset( $meta['height'] ) ? (int) $meta['height'] : 0;

	if ( $width < 1 || $height < 1 ) {
		// No dimensions on record: treat it as a square, the shape the first
		// images had, rather than guess a wider one.
		return TPM_PAGE_IMAGE_MAX_HEIGHT;
	}

	$slot = (int) round( TPM_PAGE_IMAGE_MAX_HEIGHT * $width / $height );

	return max( TPM_PAGE_IMAGE_MIN_WIDTH, min( TPM_PAGE_IMAGE_MAX_WIDTH, $slot, $width ) );
}

/**
 * A Page's Featured Image as responsive-image markup plus the width it is
 * drawn at, or an empty array when the Page has none.
 *
 * Built for phones first:
 *
 * - `sizes` states the 400px desktop column from 1024px up, below that the
 *   slot width, and the viewport minus the gutters while the viewport is
 *   narrower than the slot, so the browser does not assume the
 *   image fills the screen and over-fetch. It models the 16px phone gutter
 *   only; from 640px page.php's gutter is 24px, which makes the hint a few
 *   pixels generous there and never changes which file is chosen at the usual
 *   pixel densities.
 * - The srcset is capped at twice the wider of the slot and the desktop
 *   column. Editors upload large masters, and
 *   WordPress puts every size up to 2048px wide into the srcset, including the
 *   full-size file when it is that small, which can be ten times the bytes of
 *   the sub-size the slot needs. A 3x phone would pick it. The cap only drops
 *   files wider than twice the slot, and only for this one call, so every
 *   other image keeps WordPress's defaults. It depends on WordPress's 768px
 *   size existing (`medium_large`): core never drops the file used as `src`,
 *   so if that size is disabled the full-size file becomes `src` and stays in
 *   the srcset.
 * - It is eager with `fetchpriority="high"`: it is the largest element above
 *   the fold and so most likely the page's Largest Contentful Paint, which
 *   lazy-loading would delay. Caveat: page.php builds the body before the
 *   header renders, so a large image in the page body gets `fetchpriority="high"`
 *   from core as well and the two compete. Give any in-body image
 *   `loading="lazy"` (core then skips the high priority) or `fetchpriority="low"`.
 * - Width and height attributes come from WordPress, so the browser reserves
 *   the box before the file arrives and the text below does not jump.
 *
 * Alt text is exactly what the Media Library's "Alternative Text" field holds.
 * WordPress does not fall back to the caption or the title: an empty field
 * renders alt="" (a decorative image), so set it when attaching the image.
 *
 * @param int $post_id Page ID.
 * @return array{html:string,slot:int}|array{} Core's escaped <img> markup and
 *                                             the width to draw it at below
 *                                             the `lg` breakpoint.
 */
function tpm_get_page_image( $post_id ) {
	$attachment_id = (int) get_post_thumbnail_id( $post_id );
	if ( ! $attachment_id ) {
		return array();
	}

	$slot = tpm_get_page_image_slot( $attachment_id );

	// A cap, never a raise: a lower site-wide limit set elsewhere still wins,
	// and 0 ("no limit") is replaced by ours.
	$cap_srcset = static function ( $max_width ) use ( $slot ) {
		$cap = max( $slot, TPM_PAGE_IMAGE_DESKTOP_WIDTH ) * 2;
		return $max_width ? min( (int) $max_width, $cap ) : $cap;
	};

	add_filter( 'max_srcset_image_width', $cap_srcset );
	try {
		$html = get_the_post_thumbnail(
			$post_id,
			// 768px is the plain `src` fallback: light for every shape, and
			// smaller uploads simply use their full size.
			'medium_large',
			array(
				'class'         => 'w-full',
				'sizes'         => '(min-width: 1024px) ' . TPM_PAGE_IMAGE_DESKTOP_WIDTH . 'px, (min-width: ' . ( $slot + 32 ) . 'px) ' . $slot . 'px, calc(100vw - 32px)',
				'loading'       => 'eager',
				'fetchpriority' => 'high',
			)
		);
	} finally {
		// Not left to chance: a leaked cap would silently lower the size of
		// every later image on the request.
		remove_filter( 'max_srcset_image_width', $cap_srcset );
	}

	if ( '' === $html ) {
		return array();
	}

	return array(
		'html' => $html,
		'slot' => $slot,
	);
}

/**
 * A page's Featured Image as a card thumbnail, for the Related Reading cards
 * (template-parts/content/related-reading.php) and the hub child grid
 * (template-parts/content/child-grid.php). Empty string when the page has
 * none, so the card can show its placeholder instead.
 *
 * Unlike the page image above, this is never the page's main picture:
 * - `loading="lazy"`: the cards sit at the foot of the page, far below the
 *   fold, and must not compete with the page image for bandwidth.
 * - `alt=""`: the card's own title already names the link, so describing the
 *   picture too would make a screen reader announce every card twice.
 * - `sizes` matches the card layouts: an 80px thumbnail beside the text on
 *   phones, about half the column on tablets, and at most 260px in the three-
 *   and four-column desktop grids.
 * - The srcset is capped at 768px, which covers a 2x tablet card and keeps
 *   the large masters out.
 *
 * The wrapper in card-image.php is square and the image covers it, so a
 * square upload (all of them so far) is shown whole; a 16:9 one loses a
 * little at the sides.
 *
 * @param int $post_id Page ID.
 * @return string Core's escaped <img> markup, or ''.
 */
function tpm_get_card_image( $post_id ) {
	if ( ! get_post_thumbnail_id( $post_id ) ) {
		return '';
	}

	$cap_srcset = static function ( $max_width ) {
		return $max_width ? min( (int) $max_width, 768 ) : 768;
	};

	add_filter( 'max_srcset_image_width', $cap_srcset );
	try {
		$html = get_the_post_thumbnail(
			$post_id,
			'medium',
			array(
				'class'   => 'h-full w-full object-cover',
				'alt'     => '',
				'sizes'   => '(min-width: 1024px) 260px, (min-width: 640px) 45vw, 80px',
				'loading' => 'lazy',
			)
		);
	} finally {
		remove_filter( 'max_srcset_image_width', $cap_srcset );
	}

	return (string) $html;
}

/**
 * Load the Featured Image data for a whole card grid in two queries, before
 * the cards render.
 *
 * Without this every card looks up its own thumbnail ID, attachment and
 * attachment metadata one by one: three queries per card, which on a hub
 * with twenty children is sixty queries for pictures.
 *
 * @param WP_Post[] $posts The pages about to be shown as cards.
 */
function tpm_prime_card_images( $posts ) {
	$ids = array();
	foreach ( (array) $posts as $post ) {
		if ( $post instanceof WP_Post ) {
			$ids[] = (int) $post->ID;
		}
	}
	if ( empty( $ids ) || ! function_exists( 'update_meta_cache' ) ) {
		return;
	}

	update_meta_cache( 'post', $ids );

	$thumb_ids = array();
	foreach ( $ids as $id ) {
		$thumb_id = (int) get_post_thumbnail_id( $id );
		if ( $thumb_id ) {
			$thumb_ids[] = $thumb_id;
		}
	}
	if ( $thumb_ids && function_exists( '_prime_post_caches' ) ) {
		_prime_post_caches( $thumb_ids, false, true );
	}
}
