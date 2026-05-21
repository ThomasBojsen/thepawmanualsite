/**
 * Cat Age Calculator — configuration and formula.
 *
 * The standard feline model: a cat's first year is about 15 human years, the
 * second year brings the total to 24, and every year after that adds 4. Cats
 * have negligible size variation, so a single input (age) is all that is needed.
 *
 * This file is the proof of the reusable calculator pattern (CLAUDE.md §6): it
 * adds an entire working calculator as nothing more than a new config file —
 * with no changes to the shared engine, shell, or styles.
 */
( function () {
	'use strict';

	if ( ! window.PawManual || typeof window.PawManual.register !== 'function' ) {
		return;
	}

	// Human years added per cat-year once the cat is older than two.
	var YEARS_AFTER_TWO = 4;

	// Cat-age (in years) at which a cat is treated as a senior.
	var SENIOR_AGE = 11;

	/**
	 * Convert a cat's age to approximate human years.
	 *
	 * @param {number} age Cat's age in years.
	 * @return {number} Approximate age in human years.
	 */
	function toHumanYears( age ) {
		var human;

		if ( age <= 1 ) {
			human = age * 15;
		} else if ( age <= 2 ) {
			human = 15 + ( age - 1 ) * 9;
		} else {
			human = 24 + ( age - 2 ) * YEARS_AFTER_TWO;
		}

		return Math.round( human );
	}

	/**
	 * A short note on the cat's life stage.
	 *
	 * @param {number} age Cat's age in years.
	 * @return {string}
	 */
	function lifeStageNote( age ) {
		if ( age < 1 ) {
			return 'Still a kitten — a stage of rapid growth and learning.';
		}
		if ( age >= SENIOR_AGE ) {
			return 'Now a senior cat — more frequent vet check-ups are a good idea.';
		}
		return 'In the prime adult years.';
	}

	window.PawManual.register( {
		id: 'cat-age',
		fields: [
			{
				name: 'age',
				label: "Cat's age",
				type: 'number',
				min: 0,
				max: 30,
				step: 0.5,
				required: true,
				unit: 'years',
				placeholder: 'e.g. 4',
			},
		],
		calculate: function ( values ) {
			var age = values.age;
			var human = toHumanYears( age );

			return {
				headline: '≈ ' + human + ' human years',
				summary: lifeStageNote( age ),
			};
		},
	} );
} )();
