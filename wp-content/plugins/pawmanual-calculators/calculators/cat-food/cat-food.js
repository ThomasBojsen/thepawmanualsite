/**
 * Cat Food Calculator: configuration and formula.
 *
 * Same energy model as the dog calculator: RER = 70 × (body weight in
 * kg)^0.75, times a feline life-stage and lifestyle factor (NRC 2006, Merck
 * Veterinary Manual, WSAVA/AAHA calorie guidance; sources in
 * content/tools/cat-food-calculator.md). The published table in
 * content/cats/food/how-much-should-i-feed-my-cat.md is RER × 0.8–1.2 for an
 * indoor neutered adult, which is this file's 1.0 factor ± 20%: change one,
 * change the other.
 *
 * No weight-loss mode on purpose: crash-dieting a fat cat can trigger hepatic
 * lipidosis, so an overweight answer adds a vet note instead of a smaller
 * number.
 *
 * Configuration only: the shared engine (calculator-core.js) does the rest.
 */
( function () {
	'use strict';

	if ( ! window.PawManual || typeof window.PawManual.register !== 'function' ) {
		return;
	}

	var LB_TO_KG = 0.45359237;

	// MER factors (× RER) for adult and senior cats, by neuter status and
	// lifestyle. Neutered 1.2, intact 1.4, inactive/indoor 1.0: Merck and the
	// Pet Nutrition Alliance (AAHA 2021: neutered 1.2–1.4). Indoor neutered
	// cats often need less than the standard factor (FEDIAF; AAHA notes
	// sedentary pets may need less), hence 1.0 for "indoor, mostly calm".
	// Seniors: no guideline gives a number, and AAHA's 2023 senior guideline
	// says needs rise again after about 10, so a senior is never set below
	// the adult factor.
	var ADULT_FACTORS = {
		neutered: { indoor: 1.0, active: 1.2 },
		intact: { indoor: 1.2, active: 1.4 },
	};
	var SENIOR_FACTORS = {
		neutered: { indoor: 1.1, active: 1.2 },
		intact: { indoor: 1.2, active: 1.4 },
	};

	// Growing kittens: Merck and AAHA 2021 give a flat 2.5 × RER (the Pet
	// Nutrition Alliance 2–3). Lifestyle and neuter status don't change it.
	var KITTEN_FACTORS = {
		kitten: 2.5,
	};

	// The published range around the point estimate (0.8–1.2 around 1.0).
	var RANGE = 0.2;

	function round5( n ) {
		return Math.round( n / 5 ) * 5;
	}

	function fmt( n, decimals ) {
		return Number( n ).toLocaleString( 'en-US', {
			minimumFractionDigits: decimals || 0,
			maximumFractionDigits: decimals || 0,
		} );
	}

	function weightText( kg, unit ) {
		return 'lb' === unit ? fmt( kg / LB_TO_KG, 1 ) + ' lb' : fmt( kg, 1 ) + ' kg';
	}

	/**
	 * Resting energy requirement in kcal/day.
	 *
	 * @param {number} kg Body weight in kg.
	 * @return {number}
	 */
	function rer( kg ) {
		return 70 * Math.pow( kg, 0.75 );
	}

	/**
	 * The MER factor and a short description of how it was chosen.
	 */
	function pickFactor( values ) {
		if ( KITTEN_FACTORS[ values.stage ] ) {
			return {
				factor: KITTEN_FACTORS[ values.stage ],
				label: 'kitten under 12 months',
			};
		}
		var table = 'senior' === values.stage ? SENIOR_FACTORS : ADULT_FACTORS;
		var status = 'no' === values.neutered ? 'intact' : 'neutered';
		var lifestyle = 'active' === values.lifestyle ? 'active' : 'indoor';
		return {
			factor: table[ status ][ lifestyle ],
			label: status + ' ' + ( 'senior' === values.stage ? 'senior' : 'adult' ) + ', ' + ( 'active' === lifestyle ? 'active or outdoor access' : 'indoor and calm' ),
		};
	}

	/**
	 * A "per day (per meal)" amount for one food, or '' without a calorie figure.
	 */
	function portion( kcalPerDay, energy, unit, meals ) {
		if ( ! energy ) {
			return '';
		}
		var daily;
		var text;

		if ( 'cup' === unit || 'can' === unit ) {
			daily = kcalPerDay / energy;
			var noun = 'cup' === unit ? 'cup' : 'can';
			text = fmt( daily, 'cup' === unit ? 2 : 1 ) + ' ' + noun + ( daily === 1 ? '' : 's' ) + ' a day';
			if ( meals > 1 ) {
				text += ' (' + fmt( daily / meals, 2 ) + ' per meal)';
			}
			return text;
		}

		// Energy per 100 g (European labels): answer in grams.
		daily = kcalPerDay / energy * 100;
		text = fmt( Math.round( daily ), 0 ) + ' g a day';
		if ( meals > 1 ) {
			text += ' (' + fmt( Math.round( daily / meals ), 0 ) + ' g per meal)';
		}
		return text;
	}

	window.PawManual.register( {
		id: 'cat-food',
		fields: [
			{
				name: 'weight',
				label: "Cat's current weight",
				type: 'number',
				required: true,
				placeholder: 'e.g. 10',
				units: [
					{ value: 'lb', label: 'lb', system: 'imperial', toBase: LB_TO_KG, min: 1, max: 35, step: 0.1 },
					{ value: 'kg', label: 'kg', system: 'metric', toBase: 1, min: 0.5, max: 16, step: 0.1 },
				],
			},
			{
				name: 'stage',
				label: 'Life stage',
				type: 'select',
				required: true,
				default: 'adult',
				options: [
					{ value: 'kitten', label: 'Kitten (under 12 months)' },
					{ value: 'adult', label: 'Adult (1 to 10 years)' },
					{ value: 'senior', label: 'Senior (11 years and up)' },
				],
			},
			{
				name: 'neutered',
				label: 'Spayed or neutered?',
				type: 'select',
				required: true,
				default: 'yes',
				options: [
					{ value: 'yes', label: 'Yes' },
					{ value: 'no', label: 'No' },
				],
			},
			{
				name: 'lifestyle',
				label: 'Lifestyle',
				type: 'select',
				required: true,
				default: 'indoor',
				options: [
					{ value: 'indoor', label: 'Indoor, mostly calm' },
					{ value: 'active', label: 'Very playful, or goes outdoors' },
				],
			},
			{
				name: 'condition',
				label: 'Body condition right now',
				type: 'select',
				required: true,
				default: 'ideal',
				options: [
					{ value: 'ideal', label: 'Ideal: ribs easy to feel, visible waist' },
					{ value: 'over', label: 'Overweight: ribs hard to feel, round belly' },
					{ value: 'under', label: 'Underweight: ribs, spine or hips stand out' },
				],
			},
			{
				name: 'dry',
				label: 'Dry food calories (optional)',
				type: 'number',
				placeholder: 'e.g. 400',
				help: 'Printed on the bag or can. Leave empty to see calories only.',
				units: [
					{ value: 'cup', label: 'kcal/cup', system: 'imperial', min: 150, max: 800, step: 1 },
					{ value: '100g', label: 'kcal/100 g', system: 'metric', min: 150, max: 700, step: 1 },
				],
			},
			{
				name: 'wet',
				label: 'Wet food calories (optional)',
				type: 'number',
				placeholder: 'e.g. 90',
				units: [
					{ value: 'can', label: 'kcal/can', system: 'imperial', min: 20, max: 400, step: 1 },
					{ value: '100g', label: 'kcal/100 g', system: 'metric', min: 40, max: 300, step: 1 },
				],
			},
			{
				name: 'meals',
				label: 'Meals per day',
				type: 'select',
				required: true,
				default: '2',
				options: [
					{ value: '2', label: '2' },
					{ value: '3', label: '3' },
					{ value: '4', label: '4' },
				],
			},
		],
		calculate: function ( values ) {
			var kg = values.weight;
			var meals = parseInt( values.meals, 10 ) || 2;
			var base = rer( kg );
			var pick = pickFactor( values );
			var mer = base * pick.factor;
			var low = round5( mer * ( 1 - RANGE ) );
			var high = round5( mer * ( 1 + RANGE ) );
			var isKitten = !! KITTEN_FACTORS[ values.stage ];

			var details = [
				{ heading: 'Daily energy' },
				{ label: 'Calories per day', value: fmt( round5( mer ) ) + ' kcal' },
				{ label: 'Typical range', value: fmt( low ) + '–' + fmt( high ) + ' kcal' },
				{ label: 'Resting energy (RER)', value: fmt( round5( base ) ) + ' kcal' },
				{ label: 'Factor used', value: pick.factor.toFixed( 1 ) + ' × RER' },
				{ label: 'Treats (max 10%)', value: 'up to ' + fmt( round5( mer * 0.1 ) ) + ' kcal' },
			];

			var dry = portion( mer, values.dry, values.dryUnit, meals );
			var wet = portion( mer, values.wet, values.wetUnit, meals );
			if ( dry || wet ) {
				details.push( { heading: 'How much to feed' } );
				if ( dry ) {
					details.push( { label: 'Dry food only', value: dry } );
				}
				if ( wet ) {
					details.push( { label: 'Wet food only', value: wet } );
				}
			}

			var notes = [];
			if ( dry && wet ) {
				notes.push( 'Feeding both? Split the calories between them, for example half from each, rather than adding the two amounts together.' );
			}
			if ( 'over' === values.condition ) {
				notes.push( 'This number maintains the current weight. Never put a cat on a crash diet: losing weight too fast can cause hepatic lipidosis, a serious liver disease. Ask your vet for a slow, supervised plan.' );
			} else if ( 'under' === values.condition ) {
				notes.push( 'Unexplained weight loss is an early sign of illness in cats, especially older cats. Have a vet check your cat before you change portions.' );
			}
			if ( isKitten ) {
				notes.push( 'Kittens grow fast: weigh every 2 weeks and recalculate. Very young kittens can need up to 3 times their resting energy, so if a kitten seems hungry or stops gaining, ask your vet rather than holding to this number. Most kittens do best with 3 to 4 small meals a day.' );
			}
			if ( 'senior' === values.stage ) {
				notes.push( 'Some older cats need more calories, not fewer, because digestion becomes less efficient. Weigh monthly and follow the scale, not the birthday.' );
			}
			notes.push( 'Treats count towards the daily total. Recheck body condition every 2 to 3 weeks and adjust portions by about 10% at a time.' );

			return {
				headline: '≈ ' + fmt( round5( mer ) ) + ' kcal per day',
				summary: 'For a ' + weightText( kg, values.weightUnit ) + ' cat: ' + pick.label + '. Most cats like this need ' + fmt( low ) + '–' + fmt( high ) + ' kcal a day.',
				details: details,
				notes: notes,
			};
		},
	} );
} )();
