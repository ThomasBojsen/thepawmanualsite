/**
 * Dog Age Calculator — configuration and formula.
 *
 * A size-aware veterinary model (AKC-style): the first year of a dog's life
 * counts as roughly 15 human years, the second year adds about 9 more, and
 * every year after that adds 4–5.5 depending on breed size — smaller breeds
 * age more slowly, giant breeds faster.
 *
 * This is a configuration file only. It defines the input fields and the
 * calculate() function, then registers them with the shared engine
 * (calculator-core.js). Adding or changing a calculator must never require
 * editing a shared file (CLAUDE.md §6).
 */
( function () {
	'use strict';

	if ( ! window.PawManual || typeof window.PawManual.register !== 'function' ) {
		return;
	}

	// Human years added per dog-year once the dog is older than two.
	var YEARS_PER_SIZE = {
		small: 4,
		medium: 4.5,
		large: 5,
		giant: 5.5,
	};

	// Dog-age (in years) at which a dog is treated as a senior, by breed size.
	var SENIOR_AGE = {
		small: 9,
		medium: 8,
		large: 7,
		giant: 6,
	};

	var SIZE_LABELS = {
		small: 'small',
		medium: 'medium-sized',
		large: 'large',
		giant: 'giant',
	};

	/**
	 * Convert a dog's age to approximate human years.
	 *
	 * @param {number} age  Dog's age in years.
	 * @param {string} size Breed-size key.
	 * @return {number} Approximate age in human years.
	 */
	function toHumanYears( age, size ) {
		var perYear = YEARS_PER_SIZE[ size ] || YEARS_PER_SIZE.medium;
		var human;

		if ( age <= 1 ) {
			human = age * 15;
		} else if ( age <= 2 ) {
			human = 15 + ( age - 1 ) * 9;
		} else {
			human = 24 + ( age - 2 ) * perYear;
		}

		return Math.round( human );
	}

	/**
	 * A short note on the dog's life stage.
	 *
	 * @param {number} age  Dog's age in years.
	 * @param {string} size Breed-size key.
	 * @return {string}
	 */
	function lifeStageNote( age, size ) {
		if ( age < 1 ) {
			return 'Still a puppy: a stage of fast growth, training and socialisation.';
		}
		var seniorAt = SENIOR_AGE[ size ] || SENIOR_AGE.medium;
		if ( age >= seniorAt ) {
			return 'Now a senior for this breed size: more frequent vet check-ups are a good idea.';
		}
		return 'In the prime adult years.';
	}

	window.PawManual.register( {
		id: 'dog-age',
		fields: [
			{
				name: 'age',
				label: "Dog's age",
				type: 'number',
				min: 0,
				max: 30,
				step: 0.5,
				required: true,
				unit: 'years',
				placeholder: 'e.g. 3',
			},
			{
				name: 'size',
				label: 'Breed size',
				type: 'select',
				required: true,
				options: [
					{ value: 'small', label: 'Small (0–9 kg)' },
					{ value: 'medium', label: 'Medium (10–22 kg)' },
					{ value: 'large', label: 'Large (23–40 kg)' },
					{ value: 'giant', label: 'Giant (40+ kg)' },
				],
			},
		],
		calculate: function ( values ) {
			var age = values.age;
			var size = values.size;
			var human = toHumanYears( age, size );

			return {
				headline: '≈ ' + human + ' human years',
				summary: 'Based on a ' + ( SIZE_LABELS[ size ] || 'medium-sized' ) + ' breed. ' + lifeStageNote( age, size ),
			};
		},
	} );
} )();
