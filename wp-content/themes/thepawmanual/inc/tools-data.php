<?php
/**
 * Homepage tool-card data.
 *
 * The list of tools shown in the homepage tools grid. Each entry drives one
 * card rendered by template-parts/tool-card.php — adding a tool here adds a
 * card, with no template changes needed.
 *
 * @package thepawmanual
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return the tools shown on the homepage.
 *
 * `status` is either 'available' (a real, linked calculator page) or
 * 'coming-soon' (shown as a card but not yet built). URLs and the six-tool
 * set match content/SITEMAP.md's `/tools/` section — only the Dog Age and
 * Cat Age calculators are actually built, so the other four are deliberately
 * non-linked "coming soon" cards (tool-card.php never renders a link unless
 * status is 'available' AND a url is set) rather than dead links to pages
 * that don't exist yet. Shipping working links to unbuilt tools is exactly
 * the thin-content signal a fresh domain can't afford at launch.
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
		),
		array(
			'title'       => __( 'Cat Age Calculator', 'thepawmanual' ),
			'description' => __( 'Find out how old your cat is in human years with the standard feline model.', 'thepawmanual' ),
			'icon'        => 'paw',
			'status'      => 'available',
			'url'         => home_url( '/tools/cat-age-calculator/' ),
		),
		array(
			'title'       => __( 'Dog Food Calculator', 'thepawmanual' ),
			'description' => __( "Work out the right daily food portion for your dog's weight and activity.", 'thepawmanual' ),
			'icon'        => 'bowl',
			'status'      => 'coming-soon',
			'url'         => '',
		),
		array(
			'title'       => __( 'Cat Food Calculator', 'thepawmanual' ),
			'description' => __( "Work out the right daily food portion for your cat's weight and activity.", 'thepawmanual' ),
			'icon'        => 'bowl',
			'status'      => 'coming-soon',
			'url'         => '',
		),
		array(
			'title'       => __( 'Pet Cost Calculator', 'thepawmanual' ),
			'description' => __( 'See what a dog or cat will realistically cost you per year.', 'thepawmanual' ),
			'icon'        => 'gauge',
			'status'      => 'coming-soon',
			'url'         => '',
		),
		array(
			'title'       => __( 'Dog Breed Selector', 'thepawmanual' ),
			'description' => __( 'Find which breeds fit your home, schedule, and activity level.', 'thepawmanual' ),
			'icon'        => 'sliders-horizontal',
			'status'      => 'coming-soon',
			'url'         => '',
		),
	);
}
