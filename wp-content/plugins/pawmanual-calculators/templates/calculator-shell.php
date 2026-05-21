<?php
/**
 * Shared calculator shell — the identical markup wrapper for every calculator.
 *
 * Included by the shortcode. Expects two variables in scope:
 *   $pmc_calc_id    — the calculator id (e.g. 'dog-age')
 *   $pmc_calc_title — the calculator's display title
 *
 * The <form> is intentionally empty: calculator-core.js injects the input
 * fields from the calculator's config at runtime. This file is shared by every
 * calculator and must never need per-calculator changes (CLAUDE.md §6).
 *
 * @package pawmanual-calculators
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$pmc_calc_id    = isset( $pmc_calc_id ) ? $pmc_calc_id : '';
$pmc_calc_title = isset( $pmc_calc_title ) ? $pmc_calc_title : '';
?>
<div class="pm-calc" data-calculator="<?php echo esc_attr( $pmc_calc_id ); ?>">
	<div class="pm-calc__card">
		<h2 class="pm-calc__title"><?php echo esc_html( $pmc_calc_title ); ?></h2>

		<form class="pm-calc__form" novalidate>
			<?php // Input fields are injected here by calculator-core.js. ?>
			<div class="pm-calc__fields" data-pm-fields></div>

			<div class="pm-calc__actions">
				<button type="submit" class="pm-calc__button pm-calc__button--primary" data-pm-calculate>
					<?php esc_html_e( 'Calculate', 'pawmanual-calculators' ); ?>
				</button>
				<button type="reset" class="pm-calc__button pm-calc__button--ghost" data-pm-reset>
					<?php esc_html_e( 'Reset', 'pawmanual-calculators' ); ?>
				</button>
			</div>
		</form>

		<div class="pm-calc__result" data-pm-result role="status" aria-live="polite"></div>

		<p class="pm-calc__disclaimer">
			<?php esc_html_e( 'This tool gives general guidance only and is not a substitute for professional veterinary advice.', 'pawmanual-calculators' ); ?>
		</p>

		<noscript>
			<p class="pm-calc__noscript">
				<?php esc_html_e( 'This calculator needs JavaScript to be enabled in your browser.', 'pawmanual-calculators' ); ?>
			</p>
		</noscript>
	</div>
</div>
