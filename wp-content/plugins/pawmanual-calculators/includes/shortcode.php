<?php
/**
 * The [pawmanual_calculator] shortcode.
 *
 * Usage: [pawmanual_calculator id="dog-age"]
 *
 * @package pawmanual-calculators
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the [pawmanual_calculator] shortcode.
 *
 * Outputs the shared calculator shell for the requested calculator. The actual
 * fields and logic are supplied client-side by the engine and the calculator's
 * config script (enqueued by pmc_enqueue_assets()).
 *
 * @param array|string $atts Shortcode attributes.
 * @return string Calculator HTML.
 */
function pmc_calculator_shortcode( $atts ) {
	$atts = shortcode_atts(
		array( 'id' => '' ),
		$atts,
		'pawmanual_calculator'
	);

	$id = sanitize_key( $atts['id'] );

	if ( '' === $id || ! PMC_Registry::exists( $id ) ) {
		// Show a hint to logged-in editors; output nothing for visitors.
		if ( current_user_can( 'edit_posts' ) ) {
			return '<p class="pm-calc-notice">' . sprintf(
				/* translators: %s: the calculator id supplied to the shortcode. */
				esc_html__( 'PawManual Calculators: unknown calculator id "%s".', 'pawmanual-calculators' ),
				esc_html( $id )
			) . '</p>';
		}
		return '';
	}

	$calc = PMC_Registry::get( $id );

	// Variables consumed by the shell template.
	$pmc_calc_id    = $id;
	$pmc_calc_title = $calc['title'];

	ob_start();
	include PMC_PLUGIN_DIR . 'templates/calculator-shell.php';
	return ob_get_clean();
}
add_shortcode( 'pawmanual_calculator', 'pmc_calculator_shortcode' );
