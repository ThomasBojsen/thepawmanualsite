/**
 * Dog Food Calculator: configuration and formula.
 *
 * The standard veterinary energy model: resting energy requirement
 * RER = 70 × (body weight in kg)^0.75, multiplied by a life-stage and
 * activity factor to get the daily maintenance energy requirement (MER).
 * Factors follow the NRC (2006), the Merck Veterinary Manual and the
 * WSAVA/AAHA calorie guidance; content/tools/dog-food-calculator.md lists
 * the sources. The published feeding table in
 * content/dogs/food/how-much-should-i-feed-my-dog.md is exactly RER × 1.4–1.8
 * for a neutered adult, which is this file's 1.6 factor ± 12.5%:
 * change one, change the other.
 *
 * Deliberately no weight-loss mode: a calorie target for an overweight dog
 * belongs with a vet (the article says so too), so an overweight or
 * underweight answer adds a note instead of a smaller number.
 *
 * Configuration only: the shared engine (calculator-core.js) does the rest.
 */
( function () {
	'use strict';

	if ( ! window.PawManual || typeof window.PawManual.register !== 'function' ) {
		return;
	}

	var LB_TO_KG = 0.45359237;

	// MER factors (× RER) for adult and senior dogs, by neuter status and activity.
	// Neutered 1.6 / intact 1.8 / inactive 1.2: Merck, Pet Nutrition Alliance,
	// Ohio State (AAHA 2021 gives 1.4–1.6 neutered, 1.0–1.2 inactive). Active
	// 2.0 and working 3.0 sit inside AAHA's light (1.6–2.0) and moderate
	// (2.0–5.0) work bands; hard work (5.0+) is deliberately not offered, it
	// needs a vet. Seniors: no guideline gives one number; FEDIAF puts dogs
	// over 7 at about 1.36 × RER, so 1.4 for a neutered senior.
	var ADULT_FACTORS = {
		neutered: { low: 1.2, normal: 1.6, active: 2.0, working: 3.0 },
		intact: { low: 1.4, normal: 1.8, active: 2.0, working: 3.0 },
	};
	var SENIOR_FACTORS = {
		neutered: { low: 1.2, normal: 1.4, active: 1.6, working: 2.0 },
		intact: { low: 1.4, normal: 1.6, active: 1.8, working: 2.0 },
	};

	// Growing puppies (Merck, AAHA 2021): activity and neuter status don't change the factor.
	var PUPPY_FACTORS = {
		'puppy-young': 3.0,
		'puppy-older': 2.0,
	};

	// The published range around the point estimate (1.4–1.8 around 1.6).
	var RANGE = 0.125;

	var ACTIVITY_LABELS = {
		low: 'low activity',
		normal: 'normal activity',
		active: 'very active',
		working: 'working',
	};

	function round10( n ) {
		return Math.round( n / 10 ) * 10;
	}

	function fmt( n, decimals ) {
		return Number( n ).toLocaleString( 'en-US', {
			minimumFractionDigits: decimals || 0,
			maximumFractionDigits: decimals || 0,
		} );
	}

	function weightText( kg, unit ) {
		return 'lb' === unit ? fmt( kg / LB_TO_KG, 0 ) + ' lb' : fmt( kg, kg < 10 ? 1 : 0 ) + ' kg';
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
		if ( PUPPY_FACTORS[ values.stage ] ) {
			return {
				factor: PUPPY_FACTORS[ values.stage ],
				label: 'puppy-young' === values.stage ? 'puppy under 4 months' : 'puppy 4 to 12 months',
			};
		}
		var table = 'senior' === values.stage ? SENIOR_FACTORS : ADULT_FACTORS;
		var status = 'no' === values.neutered ? 'intact' : 'neutered';
		var factor = table[ status ][ values.activity ] || table[ status ].normal;
		return {
			factor: factor,
			label: status + ' ' + ( 'senior' === values.stage ? 'senior' : 'adult' ) + ', ' + ( ACTIVITY_LABELS[ values.activity ] || 'normal activity' ),
		};
	}

	/**
	 * A "per day (per meal)" amount for one food, or '' without a calorie figure.
	 */
	function portion( kcalPerDay, energy, unit, meals ) {
		if ( ! energy ) {
			return '';
		}
		var perMeal = meals > 1 ? meals : 0;
		var daily;
		var text;

		if ( 'cup' === unit || 'can' === unit ) {
			daily = kcalPerDay / energy;
			var noun = 'cup' === unit ? 'cup' : 'can';
			text = fmt( daily, 1 ) + ' ' + noun + ( daily === 1 ? '' : 's' ) + ' a day';
			if ( perMeal ) {
				text += ' (' + fmt( daily / meals, 1 ) + ' per meal)';
			}
			return text;
		}

		// Energy per 100 g (European labels): answer in grams.
		daily = kcalPerDay / energy * 100;
		text = fmt( Math.round( daily ), 0 ) + ' g a day';
		if ( perMeal ) {
			text += ' (' + fmt( Math.round( daily / meals ), 0 ) + ' g per meal)';
		}
		return text;
	}

	window.PawManual.register( {
		id: 'dog-food',
		fields: [
			{
				name: 'weight',
				label: "Dog's current weight",
				type: 'number',
				required: true,
				placeholder: 'e.g. 30',
				units: [
					{ value: 'lb', label: 'lb', system: 'imperial', toBase: LB_TO_KG, min: 2, max: 250, step: 0.1 },
					{ value: 'kg', label: 'kg', system: 'metric', toBase: 1, min: 1, max: 115, step: 0.1 },
				],
			},
			{
				name: 'stage',
				label: 'Life stage',
				type: 'select',
				required: true,
				default: 'adult',
				options: [
					{ value: 'puppy-young', label: 'Puppy, under 4 months' },
					{ value: 'puppy-older', label: 'Puppy, 4 to 12 months' },
					{ value: 'adult', label: 'Adult' },
					{ value: 'senior', label: 'Senior' },
				],
				help: 'Large and giant breeds count as seniors from about 6 to 7 years, small breeds from about 9.',
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
				name: 'activity',
				label: 'Activity level',
				type: 'select',
				required: true,
				default: 'normal',
				options: [
					{ value: 'low', label: 'Low: mostly resting, short walks' },
					{ value: 'normal', label: 'Normal: daily walks and play' },
					{ value: 'active', label: 'Very active: long runs, hikes, dog sports' },
					{ value: 'working', label: 'Working dog: herding, sledding, all-day work' },
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
					{ value: 'over', label: 'Overweight: ribs hard to feel, no waist' },
					{ value: 'under', label: 'Underweight: ribs, spine or hips stand out' },
				],
			},
			{
				name: 'dry',
				label: 'Dry food calories (optional)',
				type: 'number',
				placeholder: 'e.g. 380',
				help: 'On the bag, usually next to the feeding guide. Leave empty to see calories only.',
				units: [
					{ value: 'cup', label: 'kcal/cup', system: 'imperial', min: 150, max: 800, step: 1 },
					{ value: '100g', label: 'kcal/100 g', system: 'metric', min: 150, max: 700, step: 1 },
				],
			},
			{
				name: 'wet',
				label: 'Wet food calories (optional)',
				type: 'number',
				placeholder: 'e.g. 400',
				units: [
					{ value: 'can', label: 'kcal/can', system: 'imperial', min: 30, max: 1200, step: 1 },
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
					{ value: '1', label: '1' },
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
			var low = round10( mer * ( 1 - RANGE ) );
			var high = round10( mer * ( 1 + RANGE ) );
			var isPuppy = !! PUPPY_FACTORS[ values.stage ];

			var details = [
				{ heading: 'Daily energy' },
				{ label: 'Calories per day', value: fmt( round10( mer ) ) + ' kcal' },
				{ label: 'Typical range', value: fmt( low ) + '–' + fmt( high ) + ' kcal' },
				{ label: 'Resting energy (RER)', value: fmt( round10( base ) ) + ' kcal' },
				{ label: 'Factor used', value: pick.factor.toFixed( 1 ) + ' × RER' },
				{ label: 'Treats (max 10%)', value: 'up to ' + fmt( round10( mer * 0.1 ) ) + ' kcal' },
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
				notes.push( 'This number maintains the current weight. A weight-loss plan should be set with your vet, who will usually work from your dog\'s ideal weight and recheck every few weeks.' );
			} else if ( 'under' === values.condition ) {
				notes.push( 'An underweight dog should see a vet before you raise portions: weight loss can be a sign of illness, and the vet can set a safe target.' );
			}
			if ( isPuppy ) {
				notes.push( 'Puppies need frequent recalculation as they grow: weigh every 2 weeks. Large and giant breed puppies should grow slowly on a large-breed puppy food, so check portions with your vet.' );
			}
			notes.push( 'Treats count towards the daily total, so subtract them from the food. Recheck body condition every 2 to 3 weeks and adjust portions by about 10% at a time.' );

			return {
				headline: '≈ ' + fmt( round10( mer ) ) + ' kcal per day',
				summary: 'For a ' + weightText( kg, values.weightUnit ) + ' dog: ' + pick.label + '. Most dogs like this need ' + fmt( low ) + '–' + fmt( high ) + ' kcal a day.',
				details: details,
				notes: notes,
			};
		},
	} );
} )();
