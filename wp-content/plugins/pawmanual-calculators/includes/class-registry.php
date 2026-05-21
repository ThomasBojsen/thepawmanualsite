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
	 * @return array<string, array{title: string, script: string}>
	 */
	public static function all() {
		return array(
			'dog-age' => array(
				'title'  => __( 'Dog Age Calculator', 'pawmanual-calculators' ),
				'script' => 'calculators/dog-age/dog-age.js',
			),
			'cat-age' => array(
				'title'  => __( 'Cat Age Calculator', 'pawmanual-calculators' ),
				'script' => 'calculators/cat-age/cat-age.js',
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
	 * @return array{title: string, script: string}|null
	 */
	public static function get( $id ) {
		$all = self::all();
		return isset( $all[ $id ] ) ? $all[ $id ] : null;
	}
}
