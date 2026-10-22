/**
 * Pet Cost Calculator: configuration, price tables and arithmetic.
 *
 * Adds up the same line items as content/pet-guides/pet-costs.md (one-time
 * setup, yearly costs, and the occasional big bills) as low–high ranges,
 * in US dollars or euros. The two currencies are separate price sets, not an
 * exchange-rate conversion: vet care, food and insurance simply cost
 * different amounts in the US and in Western Europe.
 *
 * PRICES is the only place the numbers live. The USD set must match the
 * article's tables (tools/tests/calculators.test.js checks the yearly totals
 * against the article's headline ranges); sources for both sets, and which
 * lines are our own estimates, are in docs/calculator-sources/costs.md.
 * Review both once a year: inflation ages cost figures fast.
 *
 * Configuration only: the shared engine (calculator-core.js) does the rest.
 */
( function () {
	'use strict';

	if ( ! window.PawManual || typeof window.PawManual.register !== 'function' ) {
		return;
	}

	// [low, high] per year unless the key says otherwise.
	var PRICES = {
		usd: {
			code: 'USD',
			adopt: { dog: [ 50, 500 ], cat: [ 50, 200 ] },
			breeder: { dog: [ 1000, 3000 ], cat: [ 500, 1500 ] },
			spay: { dog: [ 150, 600 ], cat: [ 100, 400 ] },
			vaccines: { dog: [ 200, 500 ], cat: [ 150, 400 ] },
			setup: { dog: [ 200, 500 ], cat: [ 100, 300 ] },
			microchip: { dog: [ 50, 150 ], cat: [ 50, 100 ] },
			dogFood: { small: [ 200, 450 ], medium: [ 350, 650 ], large: [ 550, 950 ], giant: [ 800, 1400 ] },
			catFood: { dry: [ 150, 350 ], mixed: [ 300, 700 ], wet: [ 600, 1300 ] },
			vet: { dog: [ 300, 700 ], cat: [ 200, 500 ] },
			insurance: { dog: [ 450, 1000 ], cat: [ 250, 550 ] },
			litter: [ 150, 350 ],
			toys: { dog: [ 100, 300 ], cat: [ 50, 150 ] },
			groomingVisit: [ 60, 150 ],
			boardingNight: { dog: [ 25, 85 ], cat: [ 25, 85 ] },
			dental: { dog: [ 300, 800 ], cat: [ 300, 800 ] },
			emergency: [ 800, 5000 ],
			emergencyFund: [ 2000, 5000 ],
		},
		eur: {
			code: 'EUR',
			adopt: { dog: [ 150, 450 ], cat: [ 80, 200 ] },
			breeder: { dog: [ 1000, 2500 ], cat: [ 600, 1500 ] },
			spay: { dog: [ 200, 650 ], cat: [ 70, 250 ] },
			vaccines: { dog: [ 120, 300 ], cat: [ 100, 250 ] },
			setup: { dog: [ 150, 450 ], cat: [ 120, 350 ] },
			microchip: { dog: [ 50, 120 ], cat: [ 40, 100 ] },
			dogFood: { small: [ 150, 350 ], medium: [ 250, 550 ], large: [ 400, 850 ], giant: [ 600, 1200 ] },
			catFood: { dry: [ 150, 400 ], mixed: [ 350, 750 ], wet: [ 450, 900 ] },
			vet: { dog: [ 200, 450 ], cat: [ 120, 300 ] },
			insurance: { dog: [ 250, 750 ], cat: [ 120, 420 ] },
			litter: [ 80, 200 ],
			toys: { dog: [ 80, 250 ], cat: [ 40, 120 ] },
			groomingVisit: [ 45, 110 ],
			boardingNight: { dog: [ 18, 45 ], cat: [ 10, 25 ] },
			dental: { dog: [ 150, 500 ], cat: [ 150, 400 ] },
			emergency: [ 500, 3000 ],
			emergencyFund: [ 1500, 3000 ],
		},
	};

	// Wet food costs about five times as much per calorie as dry for dogs
	// (Tufts Petfoodology 2022); "mixed" assumes about a quarter of the
	// calories come from wet food. Cats have their own measured bands above.
	var DOG_FOOD_MULTIPLIER = { dry: 1, mixed: 2, wet: 5 };

	// Professional grooming every 4 to 6 weeks.
	var GROOMING_VISITS = [ 9, 13 ];

	// Typical life span in years, by pet (VetCompass UK 2024 for dog sizes and
	// cats; giant breeds from breed-level medians). Used for the lifetime total.
	var LIFESPAN = {
		small: [ 12, 16 ],
		medium: [ 11, 14 ],
		large: [ 10, 13 ],
		giant: [ 7, 10 ],
		cat: [ 12, 18 ],
	};

	// Regions whose visitors start in euros; everyone else starts in dollars.
	var EURO_REGIONS = [ 'AT', 'BE', 'CH', 'CY', 'CZ', 'DE', 'DK', 'EE', 'ES', 'FI', 'FR', 'GB', 'GR', 'HR', 'HU', 'IE', 'IS', 'IT', 'LT', 'LU', 'LV', 'MT', 'NL', 'NO', 'PL', 'PT', 'RO', 'SE', 'SI', 'SK' ];

	function money( n, code ) {
		return Number( n ).toLocaleString( 'en-US', {
			style: 'currency',
			currency: code,
			maximumFractionDigits: 0,
		} );
	}

	function roundTo( n, step ) {
		return Math.round( n / step ) * step;
	}

	function span( pair, code ) {
		return money( roundTo( pair[ 0 ], 10 ), code ) + '–' + money( roundTo( pair[ 1 ], 10 ), code );
	}

	function add( a, b ) {
		return [ a[ 0 ] + b[ 0 ], a[ 1 ] + b[ 1 ] ];
	}

	function times( pair, n ) {
		return [ pair[ 0 ] * n, pair[ 1 ] * n ];
	}

	window.PawManual.register( {
		id: 'pet-cost',
		fields: [
			{
				name: 'currency',
				label: 'Currency and prices',
				type: 'select',
				required: true,
				default: function () {
					return EURO_REGIONS.indexOf( window.PawManual.getRegion() ) !== -1 ? 'eur' : 'usd';
				},
				options: [
					{ value: 'usd', label: 'US dollars (US prices)' },
					{ value: 'eur', label: 'Euros (Western European prices)' },
				],
			},
			{
				name: 'pet',
				label: 'Your pet',
				type: 'select',
				required: true,
				options: [
					{ value: 'small', label: 'Dog, small (up to 9 kg / 20 lb)' },
					{ value: 'medium', label: 'Dog, medium (10–22 kg / 22–48 lb)' },
					{ value: 'large', label: 'Dog, large (23–40 kg / 50–88 lb)' },
					{ value: 'giant', label: 'Dog, giant (over 40 kg / 88 lb)' },
					{ value: 'cat', label: 'Cat' },
				],
			},
			{
				name: 'source',
				label: 'Where is your pet coming from?',
				type: 'select',
				required: true,
				default: 'adopt',
				options: [
					{ value: 'adopt', label: 'Shelter or rescue' },
					{ value: 'breeder', label: 'Breeder' },
				],
			},
			{
				name: 'neutered',
				label: 'Already spayed or neutered?',
				type: 'select',
				required: true,
				default: 'no',
				options: [
					{ value: 'no', label: 'No, budget for it' },
					{ value: 'yes', label: 'Yes (common with shelter pets)' },
				],
			},
			{
				name: 'food',
				label: 'Main food',
				type: 'select',
				required: true,
				default: 'dry',
				options: [
					{ value: 'dry', label: 'Mostly dry food' },
					{ value: 'mixed', label: 'Mix of dry and wet' },
					{ value: 'wet', label: 'Mostly wet or fresh food' },
				],
			},
			{
				name: 'insurance',
				label: 'Pet insurance (accident and illness)?',
				type: 'select',
				required: true,
				default: 'no',
				options: [
					{ value: 'no', label: 'No' },
					{ value: 'yes', label: 'Yes' },
				],
			},
			{
				name: 'grooming',
				label: 'Professional grooming every 4 to 6 weeks?',
				type: 'select',
				required: true,
				default: 'no',
				help: 'Usual for Poodles, doodles, Bichons, Shih Tzus and Persian cats.',
				options: [
					{ value: 'no', label: 'No' },
					{ value: 'yes', label: 'Yes' },
				],
			},
			{
				name: 'boarding',
				label: 'Nights in boarding or with a sitter per year (optional)',
				type: 'number',
				min: 0,
				max: 365,
				step: 1,
				placeholder: 'e.g. 14',
			},
			{
				name: 'rent',
				label: 'Pet rent per month (optional)',
				type: 'number',
				min: 0,
				max: 1000,
				step: 1,
				placeholder: 'e.g. 35',
				help: 'Common in US rentals, rare in Europe. In your chosen currency.',
			},
			{
				name: 'other',
				label: 'Other yearly costs (optional)',
				type: 'number',
				min: 0,
				max: 10000,
				step: 1,
				placeholder: 'e.g. 100',
				help: 'Dog tax, licence or required liability insurance. In your chosen currency.',
			},
		],
		calculate: function ( values ) {
			var p = PRICES[ values.currency ] || PRICES.usd;
			var code = p.code;
			var isCat = 'cat' === values.pet;
			var species = isCat ? 'cat' : 'dog';
			var rows = [];

			// One-time, first-year costs.
			var setup = [ 0, 0 ];
			function oneTime( label, pair ) {
				setup = add( setup, pair );
				rows.push( { label: label, value: span( pair, code ) } );
			}
			rows.push( { heading: 'One-time setup (first year)' } );
			oneTime( 'breeder' === values.source ? 'Breeder price' : 'Adoption fee', p[ 'breeder' === values.source ? 'breeder' : 'adopt' ][ species ] );
			if ( 'yes' !== values.neutered ) {
				oneTime( 'Spay or neuter', p.spay[ species ] );
			}
			oneTime( 'Vaccines and first vet visits', p.vaccines[ species ] );
			oneTime( 'Setup gear', p.setup[ species ] );
			oneTime( 'Microchip and registration', p.microchip[ species ] );

			// Yearly costs.
			var yearly = [ 0, 0 ];
			function annual( label, pair ) {
				yearly = add( yearly, pair );
				rows.push( { label: label, value: span( pair, code ) } );
			}
			rows.push( { heading: 'Every year' } );
			annual( 'Food', isCat ? p.catFood[ values.food ] || p.catFood.dry : times( p.dogFood[ values.pet ], DOG_FOOD_MULTIPLIER[ values.food ] || 1 ) );
			annual( 'Routine vet care and parasite prevention', p.vet[ species ] );
			if ( 'yes' === values.insurance ) {
				annual( 'Pet insurance', p.insurance[ species ] );
			}
			if ( isCat ) {
				annual( 'Litter', p.litter );
			}
			annual( 'Toys, treats and replacement gear', p.toys[ species ] );
			if ( 'yes' === values.grooming ) {
				annual( 'Professional grooming', [ p.groomingVisit[ 0 ] * GROOMING_VISITS[ 0 ], p.groomingVisit[ 1 ] * GROOMING_VISITS[ 1 ] ] );
			}
			if ( values.boarding ) {
				annual( 'Boarding or sitter (' + values.boarding + ' nights)', times( p.boardingNight[ species ], values.boarding ) );
			}
			if ( values.rent ) {
				annual( 'Pet rent', [ values.rent * 12, values.rent * 12 ] );
			}
			if ( values.other ) {
				annual( 'Other yearly costs', [ values.other, values.other ] );
			}

			var firstYear = add( setup, yearly );
			var life = LIFESPAN[ values.pet ] || LIFESPAN.medium;
			var lifetime = [ firstYear[ 0 ] + yearly[ 0 ] * ( life[ 0 ] - 1 ), firstYear[ 1 ] + yearly[ 1 ] * ( life[ 1 ] - 1 ) ];
			var yearlyMid = ( yearly[ 0 ] + yearly[ 1 ] ) / 2;

			var summaryRows = [
				{ heading: 'Totals' },
				{ label: 'First year', value: span( firstYear, code ) },
				{ label: 'Every year after', value: span( yearly, code ) },
				{ label: 'Per month, on average', value: span( times( yearly, 1 / 12 ), code ) },
				{ label: 'Lifetime (' + life[ 0 ] + '–' + life[ 1 ] + ' years)', value: span( [ roundTo( lifetime[ 0 ], 100 ), roundTo( lifetime[ 1 ], 100 ) ], code ) },
			];

			var occasional = [
				{ heading: 'Budget for, now and then' },
				{ label: 'Dental cleaning under anesthesia', value: span( p.dental[ species ], code ) + ' or more' },
				{ label: 'An emergency vet visit', value: span( p.emergency, code ) + ' or more' },
				{ label: 'Suggested emergency fund', value: span( p.emergencyFund, code ) },
			];

			var notes = [];
			if ( 'yes' !== values.insurance ) {
				notes.push( 'Without insurance, an emergency fund is the plan: one serious illness or accident can cost more than a year of routine care.' );
			}
			if ( 'eur' === values.currency ) {
				notes.push( 'Euro prices are typical ranges for Western Europe (Germany, the Netherlands, France, Ireland and the Nordic countries) and vary by country and city. Some countries add a dog tax or require liability insurance for dogs: add those under other yearly costs.' );
			}
			notes.push( 'Ranges are typical, not quotes: senior years usually cost more, and breed health problems can add a lot. Prices were last reviewed in October 2026.' );

			return {
				headline: '≈ ' + money( roundTo( yearlyMid, 50 ), code ) + ' a year',
				summary: 'Most owners in this situation spend ' + span( yearly, code ) + ' a year, or ' + span( times( yearly, 1 / 12 ), code ) + ' a month. The first year costs ' + span( firstYear, code ) + ' with setup included.',
				details: summaryRows.concat( rows, occasional ),
				notes: notes,
			};
		},
	} );
} )();
