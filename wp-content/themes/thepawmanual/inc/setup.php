<?php
/**
 * Theme setup — registers the features WordPress and the block editor rely on.
 *
 * @package thepawmanual
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register theme support flags.
 *
 * SEO meta (the contents of the <title> tag, meta description, Open Graph,
 * canonical URLs) is owned by the RankMath plugin — see CLAUDE.md §9. The theme
 * only opts into `title-tag` so WordPress/RankMath can manage the document
 * title element; it never hardcodes meta tags.
 */
function tpm_theme_setup() {
	// Let WordPress (and RankMath) manage the <title> element.
	add_theme_support( 'title-tag' );

	// Output clean HTML5 markup for core features instead of dated XHTML.
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' )
	);

	// Featured images, used by tool pages and future content.
	add_theme_support( 'post-thumbnails' );

	// Allow a logo to be set in the Customizer; the header falls back to the
	// site-title text when no logo is set.
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 48,
			'width'       => 200,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);
}
add_action( 'after_setup_theme', 'tpm_theme_setup' );
