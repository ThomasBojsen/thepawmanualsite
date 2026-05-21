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
 * 'coming-soon' (shown as a card but not yet built — the POC ships only the
 * Dog Age and Cat Age calculators).
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
			'url'         => home_url( '/dog-age-calculator/' ),
		),
		array(
			'title'       => __( 'Cat Age Calculator', 'thepawmanual' ),
			'description' => __( 'Find out how old your cat is in human years with the standard feline model.', 'thepawmanual' ),
			'icon'        => 'paw',
			'status'      => 'available',
			'url'         => home_url( '/cat-age-calculator/' ),
		),
		array(
			'title'       => __( 'Puppy Weight Predictor', 'thepawmanual' ),
			'description' => __( "Estimate your puppy's adult weight from their current age and breed size.", 'thepawmanual' ),
			'icon'        => 'gauge',
			'status'      => 'coming-soon',
			'url'         => '',
		),
		array(
			'title'       => __( 'Food Portion Calculator', 'thepawmanual' ),
			'description' => __( "Work out the right daily food portion for your pet's weight and activity.", 'thepawmanual' ),
			'icon'        => 'bowl',
			'status'      => 'coming-soon',
			'url'         => '',
		),
		array(
			'title'       => __( 'Pregnancy Due Date', 'thepawmanual' ),
			'description' => __( 'Estimate the delivery date for a pregnant dog or cat from the mating date.', 'thepawmanual' ),
			'icon'        => 'calendar',
			'status'      => 'coming-soon',
			'url'         => '',
		),
		array(
			'title'       => __( 'Water Intake Calculator', 'thepawmanual' ),
			'description' => __( 'Check how much water your pet should drink each day to stay healthy.', 'thepawmanual' ),
			'icon'        => 'droplet',
			'status'      => 'coming-soon',
			'url'         => '',
		),
	);
}
