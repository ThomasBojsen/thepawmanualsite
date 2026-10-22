/**
 * Cat Breed Selector: configuration, scoring and breed data.
 *
 * Same method as the Dog Breed Selector (calculators/dog-breed/dog-breed.js):
 * every answer becomes a weighted limit on a breed trait, each breed collects
 * a penalty for the limits it misses, and the five smallest penalties win. A
 * coat-length preference is a hard filter. The two files share the approach,
 * not code, so each stays a self-contained config (CLAUDE.md §6).
 *
 * BREEDS below: facts (weight, lifespan) come from cat registries (CFA, TICA,
 * GCCF, FIFe) and veterinary breed pages; trait scores 1–5 are ThePawManual's
 * own editorial ratings, cross-checked against several sources and not copied
 * from any one of them. content/tools/cat-breed-selector.md lists the
 * sources. `path` is set only for breeds with a guide page; the engine links
 * it only once that page is published (PawManual.isPublished).
 *
 * Configuration only: the shared engine (calculator-core.js) does the rest.
 */
( function () {
	'use strict';

	if ( ! window.PawManual || typeof window.PawManual.register !== 'function' ) {
		return;
	}

	/* BREEDS-DATA-START */
	var BREEDS = [
		{"id":"domestic-shorthair","name":"Domestic Shorthair (Mixed-Breed Cat)","weightLb":[8,12],"weightKg":[3.6,5.4],"lifespan":[12,15],"traits":{"energy":3,"playfulness":3,"affection":3,"vocal":3,"shedding":3,"grooming":1,"kids":4,"otherPets":3,"aloneTolerance":4,"firstTimeOwner":5},"coat":"short","blurb":"The everyday mixed-breed cat: personality and size vary by individual, and they make up most cats waiting in shelters."},
		{"id":"maine-coon","name":"Maine Coon","weightLb":[10,20],"weightKg":[4.5,9.1],"lifespan":[12,15],"traits":{"energy":3,"playfulness":4,"affection":4,"vocal":2,"shedding":4,"grooming":3,"kids":5,"otherPets":5,"aloneTolerance":4,"firstTimeOwner":4},"coat":"long","blurb":"A large, slow-maturing North American breed with a shaggy water-resistant coat, known for chirping trills rather than loud meows.","path":"/cats/breeds/maine-coon/"},
		{"id":"ragdoll","name":"Ragdoll","weightLb":[10,20],"weightKg":[4.5,9.1],"lifespan":[12,17],"traits":{"energy":2,"playfulness":3,"affection":5,"vocal":1,"shedding":3,"grooming":3,"kids":5,"otherPets":5,"aloneTolerance":3,"firstTimeOwner":5},"coat":"medium","blurb":"A large, blue-eyed pointed breed with a semi-long silky coat, bred for a placid temperament and a habit of going limp when held.","path":"/cats/breeds/ragdoll/"},
		{"id":"persian","name":"Persian","weightLb":[7,12],"weightKg":[3.2,5.4],"lifespan":[12,17],"traits":{"energy":1,"playfulness":2,"affection":4,"vocal":1,"shedding":5,"grooming":5,"kids":3,"otherPets":4,"aloneTolerance":4,"firstTimeOwner":2},"coat":"long","blurb":"A calm, flat-faced longhair whose dense coat needs daily combing; its short muzzle can bring breathing and eye problems.","path":"/cats/breeds/persian/"},
		{"id":"exotic-shorthair","name":"Exotic Shorthair","weightLb":[7,14],"weightKg":[3.2,6.4],"lifespan":[12,15],"traits":{"energy":2,"playfulness":3,"affection":4,"vocal":1,"shedding":3,"grooming":2,"kids":4,"otherPets":4,"aloneTolerance":3,"firstTimeOwner":4},"coat":"short","blurb":"Essentially a Persian in a short, plush coat: the same quiet nature and flat face, with far less grooming."},
		{"id":"devon-rex","name":"Devon Rex","weightLb":[6,9],"weightKg":[2.7,4.1],"lifespan":[12,16],"traits":{"energy":5,"playfulness":5,"affection":5,"vocal":3,"shedding":1,"grooming":2,"kids":4,"otherPets":4,"aloneTolerance":2,"firstTimeOwner":3},"coat":"short","blurb":"A small, large-eared cat with a soft wavy coat, famous for climbing onto shoulders and wanting to be involved in everything."},
		{"id":"abyssinian","name":"Abyssinian","weightLb":[8,10],"weightKg":[3.6,4.5],"lifespan":[12,16],"traits":{"energy":5,"playfulness":5,"affection":3,"vocal":2,"shedding":2,"grooming":1,"kids":4,"otherPets":3,"aloneTolerance":2,"firstTimeOwner":3},"coat":"short","blurb":"A lithe, ticked-coat breed that prefers exploring and climbing to lap time, and is usually quiet, with a soft chirrup."},
		{"id":"british-shorthair","name":"British Shorthair","weightLb":[7,17],"weightKg":[3.2,7.7],"lifespan":[12,17],"traits":{"energy":2,"playfulness":2,"affection":3,"vocal":1,"shedding":4,"grooming":2,"kids":4,"otherPets":5,"aloneTolerance":4,"firstTimeOwner":5},"coat":"short","blurb":"A stocky, round-faced cat with a dense plush coat, calm and undemanding, but often dislikes being picked up and carried.","path":"/cats/breeds/british-shorthair/"},
		{"id":"siberian","name":"Siberian","weightLb":[8,18],"weightKg":[3.6,8.2],"lifespan":[12,15],"traits":{"energy":4,"playfulness":4,"affection":4,"vocal":2,"shedding":4,"grooming":4,"kids":5,"otherPets":5,"aloneTolerance":3,"firstTimeOwner":4},"coat":"long","blurb":"A sturdy Russian forest cat with a thick triple coat that sheds heavily in spring and autumn; playful, quiet and family-minded."},
		{"id":"american-shorthair","name":"American Shorthair","weightLb":[6,15],"weightKg":[2.7,6.8],"lifespan":[15,20],"traits":{"energy":3,"playfulness":3,"affection":3,"vocal":2,"shedding":3,"grooming":1,"kids":5,"otherPets":5,"aloneTolerance":5,"firstTimeOwner":5},"coat":"short","blurb":"A robust, easygoing working-cat breed that tolerates children, other pets and time alone better than most pedigreed cats."},
		{"id":"russian-blue","name":"Russian Blue","weightLb":[7,12],"weightKg":[3.2,5.4],"lifespan":[12,15],"traits":{"energy":3,"playfulness":3,"affection":4,"vocal":2,"shedding":2,"grooming":1,"kids":3,"otherPets":4,"aloneTolerance":4,"firstTimeOwner":4},"coat":"short","blurb":"A fine-boned cat with a dense silver-blue double coat, reserved with strangers but affectionate once it trusts its people."},
		{"id":"bengal","name":"Bengal","weightLb":[8,15],"weightKg":[3.6,6.8],"lifespan":[12,16],"traits":{"energy":5,"playfulness":5,"affection":4,"vocal":4,"shedding":2,"grooming":1,"kids":4,"otherPets":3,"aloneTolerance":2,"firstTimeOwner":2},"coat":"short","blurb":"A muscular, spotted or marbled breed descended from the Asian leopard cat, very active and in need of daily play and climbing space.","path":"/cats/breeds/bengal/"},
		{"id":"siamese","name":"Siamese","weightLb":[8,12],"weightKg":[3.6,5.4],"lifespan":[12,20],"traits":{"energy":4,"playfulness":4,"affection":5,"vocal":5,"shedding":2,"grooming":1,"kids":3,"otherPets":3,"aloneTolerance":1,"firstTimeOwner":3},"coat":"short","blurb":"A slender, blue-eyed pointed breed with a loud, frequent voice and a strong need for company; it copes poorly alone all day.","path":"/cats/breeds/siamese/"},
		{"id":"sphynx","name":"Sphynx","weightLb":[6,11],"weightKg":[2.7,5],"lifespan":[8,15],"traits":{"energy":4,"playfulness":4,"affection":5,"vocal":4,"shedding":1,"grooming":4,"kids":4,"otherPets":4,"aloneTolerance":1,"firstTimeOwner":2},"coat":"hairless","blurb":"A hairless, warm-to-the-touch cat that needs regular baths and ear cleaning, and is not hypoallergenic despite the lack of coat."},
		{"id":"birman","name":"Birman","weightLb":[10,15],"weightKg":[4.5,6.8],"lifespan":[12,16],"traits":{"energy":3,"playfulness":3,"affection":5,"vocal":2,"shedding":3,"grooming":2,"kids":5,"otherPets":5,"aloneTolerance":2,"firstTimeOwner":5},"coat":"medium","blurb":"A colourpointed semi-longhair with white \"gloved\" paws and a silky coat that rarely mats; gentle, people-focused and soft-voiced."},
		{"id":"burmese","name":"Burmese","weightLb":[6,14],"weightKg":[2.7,6.4],"lifespan":[10,17],"traits":{"energy":4,"playfulness":4,"affection":5,"vocal":3,"shedding":2,"grooming":1,"kids":5,"otherPets":4,"aloneTolerance":3,"firstTimeOwner":4},"coat":"short","blurb":"A compact, surprisingly heavy cat with a short satin coat, intensely sociable and among the longer-lived breeds in UK vet data."},
		{"id":"norwegian-forest-cat","name":"Norwegian Forest Cat","weightLb":[9,16],"weightKg":[4.1,7.3],"lifespan":[14,16],"traits":{"energy":4,"playfulness":3,"affection":3,"vocal":2,"shedding":4,"grooming":3,"kids":4,"otherPets":4,"aloneTolerance":4,"firstTimeOwner":4},"coat":"long","blurb":"A large Scandinavian climber with a water-resistant double coat and full ruff, friendly but less demanding of attention than many breeds."},
		{"id":"oriental-shorthair","name":"Oriental Shorthair","weightLb":[8,14],"weightKg":[3.6,6.4],"lifespan":[12,15],"traits":{"energy":5,"playfulness":5,"affection":5,"vocal":5,"shedding":2,"grooming":1,"kids":4,"otherPets":4,"aloneTolerance":1,"firstTimeOwner":3},"coat":"short","blurb":"A slim, large-eared relative of the Siamese in many colours and patterns, just as talkative and dependent on company."},
		{"id":"cornish-rex","name":"Cornish Rex","weightLb":[5,10],"weightKg":[2.3,4.5],"lifespan":[9,16],"traits":{"energy":5,"playfulness":5,"affection":5,"vocal":2,"shedding":1,"grooming":2,"kids":4,"otherPets":4,"aloneTolerance":2,"firstTimeOwner":3},"coat":"short","blurb":"A slender, curly-coated athlete with only a soft undercoat, very active and people-oriented, and keen on warm laps and high shelves."},
		{"id":"tonkinese","name":"Tonkinese","weightLb":[6,12],"weightKg":[2.7,5.4],"lifespan":[10,16],"traits":{"energy":4,"playfulness":5,"affection":5,"vocal":4,"shedding":2,"grooming":1,"kids":5,"otherPets":5,"aloneTolerance":2,"firstTimeOwner":4},"coat":"short","blurb":"A Siamese and Burmese cross breed with a medium build, chatty and outgoing with guests, and happiest with company at home."},
		{"id":"ragamuffin","name":"RagaMuffin","weightLb":[10,20],"weightKg":[4.5,9.1],"lifespan":[12,16],"traits":{"energy":2,"playfulness":3,"affection":5,"vocal":1,"shedding":3,"grooming":3,"kids":5,"otherPets":4,"aloneTolerance":3,"firstTimeOwner":5},"coat":"medium","blurb":"A large, docile Ragdoll relative in many colours, with a rabbit-soft semi-long coat that needs brushing once or twice a week."},
		{"id":"selkirk-rex","name":"Selkirk Rex","weightLb":[9,16],"weightKg":[4.1,7.3],"lifespan":[12,15],"traits":{"energy":2,"playfulness":3,"affection":4,"vocal":2,"shedding":3,"grooming":3,"kids":4,"otherPets":4,"aloneTolerance":3,"firstTimeOwner":4},"coat":"medium","blurb":"A sturdy, curly-coated breed in short and long versions, relaxed and patient; its curls need gentle combing rather than brushing."},
		{"id":"turkish-van","name":"Turkish Van","weightLb":[7,20],"weightKg":[3.2,9.1],"lifespan":[12,14],"traits":{"energy":5,"playfulness":5,"affection":3,"vocal":3,"shedding":3,"grooming":2,"kids":3,"otherPets":4,"aloneTolerance":3,"firstTimeOwner":3},"coat":"medium","blurb":"A large, mostly white semi-longhair with colour on the head and tail, very athletic, often fond of water, and not keen on being carried."},
		{"id":"chartreux","name":"Chartreux","weightLb":[11,18],"weightKg":[5,8.2],"lifespan":[12,15],"traits":{"energy":2,"playfulness":3,"affection":4,"vocal":1,"shedding":4,"grooming":2,"kids":4,"otherPets":4,"aloneTolerance":4,"firstTimeOwner":5},"coat":"short","blurb":"A robust French blue-grey cat with a dense woolly coat, quiet and calm, often bonding most closely with one person."},
		{"id":"somali","name":"Somali","weightLb":[8,12],"weightKg":[3.6,5.4],"lifespan":[12,14],"traits":{"energy":5,"playfulness":5,"affection":4,"vocal":3,"shedding":3,"grooming":3,"kids":4,"otherPets":3,"aloneTolerance":2,"firstTimeOwner":3},"coat":"medium","blurb":"The semi-longhaired Abyssinian, with a bushy fox-like tail; busy and curious, it is more of a playmate than a lap cat."},
	];
	/* BREEDS-DATA-END */

	var COAT_LABELS = { short: 'Short coat', medium: 'Medium coat', long: 'Long coat', hairless: 'Hairless' };

	// What a met limit says about a breed, per trait and direction.
	var GOOD = {
		energy: { max: 'Calm and easy-going', min: 'Lively and athletic' },
		playfulness: { min: 'Very playful' },
		affection: { min: 'Affectionate, often a lap cat' },
		vocal: { max: 'Usually quiet' },
		shedding: { max: 'Sheds little' },
		grooming: { max: 'Easy coat care' },
		kids: { min: 'Known for being good with children' },
		otherPets: { min: 'Usually gets on with other pets' },
		aloneTolerance: { min: 'Copes well with time alone' },
		firstTimeOwner: { min: 'A good choice for a first cat' },
	};

	// What a missed limit means, per trait and direction.
	var WATCH = {
		energy: { max: 'More energy than you described', min: 'Calmer than you may want' },
		playfulness: { min: 'Less playful than you may want' },
		affection: { min: 'More independent than cuddly' },
		vocal: { max: 'Can be talkative' },
		shedding: { max: 'Sheds more than you want' },
		grooming: { max: 'Needs regular grooming' },
		kids: { min: 'Prefers a calmer home than one with small children' },
		otherPets: { min: 'Can be choosy about other pets' },
		aloneTolerance: { min: 'Does not like being left alone for long' },
		firstTimeOwner: { min: 'Better with an experienced owner' },
	};

	/**
	 * Turn the answers into weighted trait limits.
	 *
	 * @param {Object} v Answers.
	 * @return {Array} [ { trait, min?, max?, weight } ]
	 */
	function limitsFor( v ) {
		var limits = [];
		function add( trait, dir, value, weight ) {
			var limit = { trait: trait, weight: weight };
			limit[ dir ] = value;
			limits.push( limit );
		}

		if ( 'calm' === v.activity ) {
			add( 'energy', 'max', 2, 2 );
		} else if ( 'balanced' === v.activity ) {
			add( 'energy', 'max', 4, 1 );
			add( 'energy', 'min', 2, 1 );
		} else if ( 'lively' === v.activity ) {
			add( 'energy', 'min', 4, 1.5 );
			add( 'playfulness', 'min', 4, 1.5 );
		}

		if ( 'cuddly' === v.affection ) {
			add( 'affection', 'min', 4, 2 );
		} else if ( 'some' === v.affection ) {
			add( 'affection', 'min', 3, 1 );
		}

		if ( 'quiet' === v.vocal ) {
			add( 'vocal', 'max', 2, 2 );
		}

		if ( 'minimal' === v.grooming ) {
			add( 'grooming', 'max', 2, 2 );
		} else if ( 'moderate' === v.grooming ) {
			add( 'grooming', 'max', 3, 1 );
		}

		if ( 'minimal' === v.shedding ) {
			add( 'shedding', 'max', 2, 2 );
		} else if ( 'some' === v.shedding ) {
			add( 'shedding', 'max', 3, 1 );
		}

		if ( 'young' === v.kids ) {
			add( 'kids', 'min', 4, 3 );
		} else if ( 'older' === v.kids ) {
			add( 'kids', 'min', 3, 2 );
		}

		if ( 'yes' === v.otherPets ) {
			add( 'otherPets', 'min', 4, 2 );
		}

		if ( 'long' === v.alone ) {
			add( 'aloneTolerance', 'min', 4, 2 );
		} else if ( 'some' === v.alone ) {
			add( 'aloneTolerance', 'min', 3, 1.5 );
		}

		if ( 'first' === v.experience ) {
			add( 'firstTimeOwner', 'min', 4, 2 );
		}

		return limits;
	}

	/**
	 * Score one breed: total penalty plus what it does well and its worst miss.
	 */
	function scoreBreed( breed, limits ) {
		var penalty = 0;
		var good = [];
		var worst = null;
		var seen = {};

		limits.forEach( function ( limit ) {
			var value = breed.traits[ limit.trait ];
			if ( typeof value !== 'number' ) {
				return;
			}
			var dir = typeof limit.min === 'number' ? 'min' : 'max';
			var miss = 'min' === dir ? limit.min - value : value - limit.max;

			if ( miss > 0 ) {
				var cost = miss * limit.weight;
				penalty += cost;
				if ( ! worst || cost > worst.cost ) {
					worst = { cost: cost, text: WATCH[ limit.trait ][ dir ] };
				}
			} else if ( limit.weight >= 1.5 && GOOD[ limit.trait ] && GOOD[ limit.trait ][ dir ] && ! seen[ limit.trait ] ) {
				seen[ limit.trait ] = true;
				good.push( { weight: limit.weight, text: GOOD[ limit.trait ][ dir ] } );
			}
		} );

		good.sort( function ( a, b ) {
			return b.weight - a.weight;
		} );

		return { breed: breed, penalty: penalty, good: good, worst: worst };
	}

	function passesFilters( breed, v ) {
		if ( 'short' === v.coat ) {
			return 'short' === breed.coat || 'hairless' === breed.coat;
		}
		if ( 'long' === v.coat ) {
			return 'long' === breed.coat || 'medium' === breed.coat;
		}
		return true;
	}

	function range( pair, unit ) {
		return pair[ 0 ] === pair[ 1 ] ? pair[ 0 ] + ' ' + unit : pair[ 0 ] + '–' + pair[ 1 ] + ' ' + unit;
	}

	function describe( breed ) {
		var weight = window.PawManual.prefersImperial && window.PawManual.prefersImperial() ?
			range( breed.weightLb, 'lb' ) :
			range( breed.weightKg, 'kg' );
		return ( COAT_LABELS[ breed.coat ] || 'Coat varies' ) + ', ' + weight + ', lives ' + range( breed.lifespan, 'years' );
	}

	function sentence( items ) {
		return items.length ? items.join( '. ' ) + '.' : '';
	}

	window.PawManual.register( {
		id: 'cat-breed',
		submitLabel: 'Find my breeds',
		fields: [
			{
				name: 'activity',
				label: 'How lively a cat do you want?',
				type: 'select',
				required: true,
				options: [
					{ value: 'calm', label: 'Calm: happy to nap most of the day' },
					{ value: 'balanced', label: 'Balanced: some play, some lounging' },
					{ value: 'lively', label: 'Lively: climbs, plays and explores a lot' },
				],
			},
			{
				name: 'affection',
				label: 'How cuddly should your cat be?',
				type: 'select',
				required: true,
				options: [
					{ value: 'independent', label: 'Independent is fine' },
					{ value: 'some', label: 'Friendly, but not clingy' },
					{ value: 'cuddly', label: 'A real lap cat' },
				],
			},
			{
				name: 'vocal',
				label: 'Talkative cats',
				type: 'select',
				required: true,
				options: [
					{ value: 'quiet', label: 'I prefer a quiet cat' },
					{ value: 'any', label: 'A chatty cat is fine' },
				],
			},
			{
				name: 'coat',
				label: 'Coat length',
				type: 'select',
				required: true,
				default: 'any',
				options: [
					{ value: 'any', label: 'No preference' },
					{ value: 'short', label: 'Short coat' },
					{ value: 'long', label: 'Medium or long coat' },
				],
			},
			{
				name: 'grooming',
				label: 'Time for brushing',
				type: 'select',
				required: true,
				options: [
					{ value: 'minimal', label: 'A quick brush once a week' },
					{ value: 'moderate', label: 'A few times a week' },
					{ value: 'lots', label: 'Daily brushing is fine' },
				],
			},
			{
				name: 'shedding',
				label: 'How much cat hair can you live with?',
				type: 'select',
				required: true,
				options: [
					{ value: 'minimal', label: 'As little as possible' },
					{ value: 'some', label: 'Some is fine' },
					{ value: 'lots', label: 'Hair everywhere is fine' },
				],
			},
			{
				name: 'kids',
				label: 'Children in the home?',
				type: 'select',
				required: true,
				options: [
					{ value: 'none', label: 'No children' },
					{ value: 'older', label: 'Children aged 6 and up' },
					{ value: 'young', label: 'Children under 6' },
				],
			},
			{
				name: 'otherPets',
				label: 'Other pets in the home?',
				type: 'select',
				required: true,
				default: 'no',
				options: [
					{ value: 'no', label: 'No' },
					{ value: 'yes', label: 'Yes, a dog or another cat' },
				],
			},
			{
				name: 'alone',
				label: 'How long is the cat alone on a normal weekday?',
				type: 'select',
				required: true,
				options: [
					{ value: 'short', label: 'Under 4 hours' },
					{ value: 'some', label: '4 to 8 hours' },
					{ value: 'long', label: 'More than 8 hours' },
				],
			},
			{
				name: 'experience',
				label: 'Your experience with cats',
				type: 'select',
				required: true,
				options: [
					{ value: 'first', label: 'This will be my first cat' },
					{ value: 'some', label: 'I have had cats before' },
				],
			},
		],
		calculate: function ( values ) {
			var limits = limitsFor( values );
			var ranked = BREEDS.filter( function ( breed ) {
				return passesFilters( breed, values );
			} ).map( function ( breed ) {
				return scoreBreed( breed, limits );
			} ).sort( function ( a, b ) {
				return a.penalty - b.penalty || a.breed.name.localeCompare( b.breed.name );
			} ).slice( 0, 5 );

			if ( ! ranked.length ) {
				return {
					headline: 'No close matches',
					summary: 'No breed in our list fits every answer. Try "No preference" for coat length.',
				};
			}

			var notes = [];
			if ( 'minimal' === values.shedding ) {
				notes.push( 'No cat breed is truly hypoallergenic: the main cat allergen comes from saliva and skin, not just hair. If anyone in the home has allergies, spend time with the breed before you commit.' );
			}
			if ( 'long' === values.alone ) {
				notes.push( 'Cats left alone for long days do best in pairs or with plenty of play and climbing space at home.' );
			}
			notes.push( 'Breed is a tendency, not a promise: every cat is an individual. Most cats are mixed-breed, and shelters can match an adult cat\'s known personality to these answers.' );

			return {
				headline: 'Your top ' + ranked.length + ' breed matches',
				summary: 'Ranked by how well each breed fits all of your answers. Best match first.',
				list: ranked.map( function ( r ) {
					var text = sentence( r.good.slice( 0, 2 ).map( function ( g ) {
						return g.text;
					} ) );
					if ( r.worst ) {
						text += ( text ? ' ' : '' ) + 'Keep in mind: ' + r.worst.text.charAt( 0 ).toLowerCase() + r.worst.text.slice( 1 ) + '.';
					}
					return {
						title: r.breed.name,
						path: r.breed.path,
						meta: describe( r.breed ),
						text: ( r.breed.blurb ? r.breed.blurb + ' ' : '' ) + text,
					};
				} ),
				notes: notes,
			};
		},
	} );
} )();
