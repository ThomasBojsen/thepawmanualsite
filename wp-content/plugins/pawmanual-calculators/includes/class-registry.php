<?php
/**
 * Calculator registry.
 *
 * The single source of truth for which calculators exist. Each entry pairs a
 * calculator id with its display title and the path to its config script.
 *
 * Adding a calculator = add an entry here, then create the matching config
 * file at calculators/<id>/<id>.js. Nothing else in the plugin changes.
 *
 * Optional `link_parents`: parent page paths (e.g. 'dogs/breeds') whose
 * published child pages the calculator's results may link to. The plugin
 * prints exactly those published paths for the engine (includes/enqueue.php),
 * so a result never links to a page that hasn't shipped yet.
 *
 * @package pawmanual-calculators
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Static registry of available calculators.
 */
class PMC_Registry {

	/**
	 * All registered calculators, keyed by id.
	 *
	 * @return array<string, array{title: string, script: string, link_parents?: string[]}>
	 */
	public static function all() {
		return array(
			'dog-age'   => array(
				'title'  => __( 'Dog Age Calculator', 'pawmanual-calculators' ),
				'script' => 'calculators/dog-age/dog-age.js',
			),
			'cat-age'   => array(
				'title'  => __( 'Cat Age Calculator', 'pawmanual-calculators' ),
				'script' => 'calculators/cat-age/cat-age.js',
			),
			'dog-food'  => array(
				'title'  => __( 'Dog Food Calculator', 'pawmanual-calculators' ),
				'script' => 'calculators/dog-food/dog-food.js',
			),
			'cat-food'  => array(
				'title'  => __( 'Cat Food Calculator', 'pawmanual-calculators' ),
				'script' => 'calculators/cat-food/cat-food.js',
			),
			'pet-cost'  => array(
				'title'  => __( 'Pet Cost Calculator', 'pawmanual-calculators' ),
				'script' => 'calculators/pet-cost/pet-cost.js',
			),
			'dog-breed' => array(
				'title'        => __( 'Dog Breed Selector', 'pawmanual-calculators' ),
				'script'       => 'calculators/dog-breed/dog-breed.js',
				'link_parents' => array( 'dogs/breeds' ),
			),
			'cat-breed' => array(
				'title'        => __( 'Cat Breed Selector', 'pawmanual-calculators' ),
				'script'       => 'calculators/cat-breed/cat-breed.js',
				'link_parents' => array( 'cats/breeds' ),
			),
		);
	}

	/**
	 * Whether a calculator id is registered.
	 *
	 * @param string $id Calculator id.
	 * @return bool
	 */
	public static function exists( $id ) {
		return array_key_exists( $id, self::all() );
	}

	/**
	 * Get a single calculator definition.
	 *
	 * @param string $id Calculator id.
	 * @return array{title: string, script: string, link_parents?: string[]}|null
	 */
	public static function get( $id ) {
		$all = self::all();
		return isset( $all[ $id ] ) ? $all[ $id ] : null;
	}
}
