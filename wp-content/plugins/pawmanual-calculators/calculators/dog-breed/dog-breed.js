/**
 * Dog Breed Selector: configuration, scoring and breed data.
 *
 * A questionnaire, not a formula: every answer becomes one or more limits on
 * a breed trait ("energy at most 2", "good with young children at least 4"),
 * each breed collects a penalty for every limit it misses (weighted by how
 * far it misses), and the five breeds with the smallest penalty are shown.
 * A size preference and "minimal shedding" are hard filters instead: a
 * near-miss there is still the wrong dog.
 *
 * BREEDS below: facts (weight, lifespan) come from the breed standards and
 * breed-club / veterinary breed pages; trait scores 1–5 are ThePawManual's
 * own editorial ratings, cross-checked against several sources and not
 * copied from any one of them. content/tools/dog-breed-selector.md lists the
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
		{"id":"french-bulldog","name":"French Bulldog","size":"small","weightLb":[16,28],"weightKg":[7.5,12.5],"lifespan":[10,12],"traits":{"energy":2,"exercise":2,"shedding":2,"grooming":2,"barking":2,"kids":4,"apartment":5,"trainability":3,"firstTimeOwner":4,"aloneTolerance":2,"otherDogs":4},"blurb":"A compact, bat-eared companion that needs little exercise, but its flat face makes it prone to breathing trouble and overheating.","path":"/dogs/breeds/french-bulldog/"},
		{"id":"labrador-retriever","name":"Labrador Retriever","size":"large","weightLb":[55,80],"weightKg":[25,36],"lifespan":[11,13],"traits":{"energy":5,"exercise":5,"shedding":4,"grooming":2,"barking":2,"kids":5,"apartment":2,"trainability":5,"firstTimeOwner":4,"aloneTolerance":2,"otherDogs":5},"blurb":"A friendly, food-motivated gundog that is easy to train and great with families, but needs plenty of daily exercise.","path":"/dogs/breeds/labrador-retriever/"},
		{"id":"golden-retriever","name":"Golden Retriever","size":"large","weightLb":[55,75],"weightKg":[25,34],"lifespan":[10,12],"traits":{"energy":4,"exercise":5,"shedding":5,"grooming":3,"barking":2,"kids":5,"apartment":2,"trainability":5,"firstTimeOwner":5,"aloneTolerance":2,"otherDogs":5},"blurb":"A gentle, eager-to-please retriever that suits families and novices, though it sheds heavily and needs long daily walks.","path":"/dogs/breeds/golden-retriever/"},
		{"id":"german-shepherd","name":"German Shepherd","size":"large","weightLb":[50,90],"weightKg":[23,41],"lifespan":[12,14],"traits":{"energy":5,"exercise":5,"shedding":5,"grooming":3,"barking":3,"kids":4,"apartment":1,"trainability":5,"firstTimeOwner":2,"aloneTolerance":2,"otherDogs":3},"blurb":"A highly trainable, protective working dog that needs an experienced owner, lots of exercise, and tolerance for heavy shedding.","path":"/dogs/breeds/german-shepherd/"},
		{"id":"dachshund","name":"Dachshund (Standard)","size":"medium","weightLb":[16,32],"weightKg":[7.5,14.5],"lifespan":[12,16],"traits":{"energy":3,"exercise":2,"shedding":2,"grooming":2,"barking":4,"kids":3,"apartment":4,"trainability":2,"firstTimeOwner":3,"aloneTolerance":3,"otherDogs":3},"blurb":"A bold, long-backed hound in three coat types; it is vocal and stubborn, and its long spine is prone to disc disease."},
		{"id":"poodle","name":"Poodle (Standard)","size":"large","weightLb":[40,70],"weightKg":[18,32],"lifespan":[12,14],"traits":{"energy":4,"exercise":4,"shedding":1,"grooming":5,"barking":3,"kids":4,"apartment":2,"trainability":5,"firstTimeOwner":3,"aloneTolerance":2,"otherDogs":4},"blurb":"A clever, athletic dog with a low-shedding curly coat that needs professional clipping every four to eight weeks.","path":"/dogs/breeds/poodle/"},
		{"id":"miniature-poodle","name":"Poodle (Miniature)","size":"small","weightLb":[10,15],"weightKg":[4.5,7],"lifespan":[13,15],"traits":{"energy":4,"exercise":3,"shedding":1,"grooming":5,"barking":4,"kids":4,"apartment":4,"trainability":5,"firstTimeOwner":4,"aloneTolerance":2,"otherDogs":4},"blurb":"A bright, lively mid-sized Poodle that learns quickly and sheds very little, but needs regular professional clipping."},
		{"id":"toy-poodle","name":"Poodle (Toy)","size":"small","weightLb":[4,6],"weightKg":[2,2.5],"lifespan":[12,14],"traits":{"energy":3,"exercise":2,"shedding":1,"grooming":5,"barking":4,"kids":2,"apartment":5,"trainability":5,"firstTimeOwner":4,"aloneTolerance":2,"otherDogs":3},"blurb":"The smallest Poodle variety: smart, affectionate and low-shedding, but fragile around rough play and young children."},
		{"id":"beagle","name":"Beagle","size":"medium","weightLb":[18,30],"weightKg":[8,13.5],"lifespan":[10,15],"traits":{"energy":4,"exercise":4,"shedding":3,"grooming":1,"barking":5,"kids":5,"apartment":2,"trainability":2,"firstTimeOwner":3,"aloneTolerance":2,"otherDogs":5},"blurb":"A merry, sociable scent hound that loves children and other dogs, but follows its nose, howls, and can be hard to recall."},
		{"id":"rottweiler","name":"Rottweiler","size":"giant","weightLb":[80,135],"weightKg":[36,61],"lifespan":[9,10],"traits":{"energy":3,"exercise":4,"shedding":3,"grooming":1,"barking":2,"kids":3,"apartment":1,"trainability":4,"firstTimeOwner":1,"aloneTolerance":3,"otherDogs":2},"blurb":"A powerful, confident guardian that is devoted to its family and needs early socialization and firm, consistent training."},
		{"id":"german-shorthaired-pointer","name":"German Shorthaired Pointer","size":"large","weightLb":[45,70],"weightKg":[20,32],"lifespan":[10,12],"traits":{"energy":5,"exercise":5,"shedding":3,"grooming":1,"barking":3,"kids":4,"apartment":1,"trainability":4,"firstTimeOwner":2,"aloneTolerance":1,"otherDogs":4},"blurb":"A tireless, versatile hunting dog that suits very active households and becomes restless without hours of daily exercise."},
		{"id":"bulldog","name":"Bulldog","size":"medium","weightLb":[40,50],"weightKg":[18,23],"lifespan":[8,10],"traits":{"energy":1,"exercise":1,"shedding":3,"grooming":2,"barking":2,"kids":4,"apartment":4,"trainability":2,"firstTimeOwner":3,"aloneTolerance":4,"otherDogs":3},"blurb":"A calm, sturdy companion with low exercise needs whose flat face and heavy build bring breathing, heat and skin problems."},
		{"id":"cane-corso","name":"Cane Corso","size":"giant","weightLb":[88,110],"weightKg":[40,50],"lifespan":[9,12],"traits":{"energy":3,"exercise":4,"shedding":2,"grooming":1,"barking":2,"kids":2,"apartment":1,"trainability":3,"firstTimeOwner":1,"aloneTolerance":3,"otherDogs":2},"blurb":"A massive Italian guardian breed that is loyal and protective; it suits experienced owners who socialize and train it early."},
		{"id":"cavalier-king-charles-spaniel","name":"Cavalier King Charles Spaniel","size":"small","weightLb":[13,18],"weightKg":[6,8],"lifespan":[12,15],"traits":{"energy":3,"exercise":3,"shedding":3,"grooming":3,"barking":2,"kids":5,"apartment":5,"trainability":4,"firstTimeOwner":5,"aloneTolerance":1,"otherDogs":5},"blurb":"A gentle, affectionate toy spaniel that suits novices and families; the breed has a high rate of inherited heart valve disease."},
		{"id":"yorkshire-terrier","name":"Yorkshire Terrier","size":"small","weightLb":[4,7],"weightKg":[2,3],"lifespan":[11,15],"traits":{"energy":4,"exercise":2,"shedding":1,"grooming":4,"barking":4,"kids":2,"apartment":5,"trainability":3,"firstTimeOwner":4,"aloneTolerance":2,"otherDogs":3},"blurb":"A tiny, spirited terrier with a silky, low-shedding coat that needs daily brushing or a regular short trim."},
		{"id":"australian-shepherd","name":"Australian Shepherd","size":"medium","weightLb":[40,65],"weightKg":[18,29],"lifespan":[12,15],"traits":{"energy":5,"exercise":5,"shedding":4,"grooming":3,"barking":4,"kids":4,"apartment":1,"trainability":5,"firstTimeOwner":2,"aloneTolerance":1,"otherDogs":3},"blurb":"A smart, intense herding dog that excels at dog sports but needs a job, hours of activity, and an engaged owner."},
		{"id":"doberman-pinscher","name":"Doberman Pinscher","size":"large","weightLb":[60,100],"weightKg":[27,45],"lifespan":[10,12],"traits":{"energy":5,"exercise":5,"shedding":3,"grooming":1,"barking":3,"kids":3,"apartment":2,"trainability":5,"firstTimeOwner":2,"aloneTolerance":2,"otherDogs":2},"blurb":"A sleek, athletic and loyal guardian that is highly trainable but needs vigorous exercise and an experienced handler."},
		{"id":"pembroke-welsh-corgi","name":"Pembroke Welsh Corgi","size":"medium","weightLb":[24,30],"weightKg":[11,13.5],"lifespan":[12,13],"traits":{"energy":4,"exercise":3,"shedding":5,"grooming":2,"barking":4,"kids":3,"apartment":4,"trainability":4,"firstTimeOwner":3,"aloneTolerance":3,"otherDogs":4},"blurb":"A short-legged, big-hearted herding dog that is smart and vocal, sheds heavily, and gains weight easily."},
		{"id":"miniature-schnauzer","name":"Miniature Schnauzer","size":"small","weightLb":[11,20],"weightKg":[5,9],"lifespan":[12,15],"traits":{"energy":3,"exercise":3,"shedding":2,"grooming":4,"barking":5,"kids":4,"apartment":4,"trainability":4,"firstTimeOwner":4,"aloneTolerance":3,"otherDogs":3},"blurb":"A sturdy, bearded little dog with a low-shedding wiry coat; it is alert, vocal, and keen to please."},
		{"id":"boxer","name":"Boxer","size":"large","weightLb":[50,80],"weightKg":[23,36],"lifespan":[10,12],"traits":{"energy":5,"exercise":4,"shedding":3,"grooming":1,"barking":3,"kids":4,"apartment":2,"trainability":3,"firstTimeOwner":3,"aloneTolerance":2,"otherDogs":3},"blurb":"A bouncy, playful and protective family dog that stays puppyish for years and needs plenty of exercise and training."},
		{"id":"pomeranian","name":"Pomeranian","size":"small","weightLb":[3,7],"weightKg":[1.5,3],"lifespan":[12,16],"traits":{"energy":3,"exercise":1,"shedding":4,"grooming":3,"barking":5,"kids":2,"apartment":5,"trainability":3,"firstTimeOwner":3,"aloneTolerance":2,"otherDogs":3},"blurb":"A tiny, fox-faced spitz with a thick double coat; it is bold, alert, and quick to bark at anything new."},
		{"id":"bernese-mountain-dog","name":"Bernese Mountain Dog","size":"giant","weightLb":[70,115],"weightKg":[32,52],"lifespan":[7,10],"traits":{"energy":3,"exercise":3,"shedding":5,"grooming":3,"barking":3,"kids":5,"apartment":1,"trainability":4,"firstTimeOwner":3,"aloneTolerance":2,"otherDogs":5},"blurb":"A big, gentle Swiss farm dog that is great with families but sheds heavily and has a short average lifespan."},
		{"id":"shih-tzu","name":"Shih Tzu","size":"small","weightLb":[9,16],"weightKg":[4,7.5],"lifespan":[10,18],"traits":{"energy":2,"exercise":1,"shedding":1,"grooming":5,"barking":3,"kids":4,"apartment":5,"trainability":3,"firstTimeOwner":5,"aloneTolerance":3,"otherDogs":5},"blurb":"A cheerful, affectionate lapdog with a long, low-shedding coat that needs daily brushing or a short clip."},
		{"id":"great-dane","name":"Great Dane","size":"giant","weightLb":[110,175],"weightKg":[50,79],"lifespan":[7,10],"traits":{"energy":3,"exercise":3,"shedding":3,"grooming":1,"barking":2,"kids":4,"apartment":2,"trainability":3,"firstTimeOwner":3,"aloneTolerance":3,"otherDogs":4},"blurb":"A gentle giant that is calm indoors but needs space, a large car, and a budget sized for one of the tallest breeds."},
		{"id":"boston-terrier","name":"Boston Terrier","size":"small","weightLb":[12,25],"weightKg":[5.5,11.5],"lifespan":[11,13],"traits":{"energy":3,"exercise":2,"shedding":2,"grooming":1,"barking":2,"kids":5,"apartment":5,"trainability":4,"firstTimeOwner":5,"aloneTolerance":3,"otherDogs":4},"blurb":"A friendly, compact companion in a tuxedo coat that adapts well to city life; its short muzzle makes it sensitive to heat."},
		{"id":"chihuahua","name":"Chihuahua","size":"small","weightLb":[2,6],"weightKg":[1,2.5],"lifespan":[14,16],"traits":{"energy":3,"exercise":1,"shedding":2,"grooming":1,"barking":5,"kids":1,"apartment":5,"trainability":3,"firstTimeOwner":3,"aloneTolerance":2,"otherDogs":2},"blurb":"The smallest recognized breed: loyal to its person, wary of strangers, and too fragile for homes with young children."},
		{"id":"havanese","name":"Havanese","size":"small","weightLb":[7,13],"weightKg":[3,6],"lifespan":[14,16],"traits":{"energy":3,"exercise":2,"shedding":1,"grooming":4,"barking":3,"kids":5,"apartment":5,"trainability":4,"firstTimeOwner":5,"aloneTolerance":1,"otherDogs":5},"blurb":"A sociable, playful Cuban toy breed with a soft, low-shedding coat; it bonds closely and dislikes being left alone."},
		{"id":"border-collie","name":"Border Collie","size":"medium","weightLb":[30,55],"weightKg":[13.5,25],"lifespan":[12,15],"traits":{"energy":5,"exercise":5,"shedding":3,"grooming":3,"barking":3,"kids":3,"apartment":1,"trainability":5,"firstTimeOwner":2,"aloneTolerance":1,"otherDogs":3},"blurb":"A workaholic herding dog famed for its intelligence that needs hours of physical and mental work every day."},
		{"id":"english-springer-spaniel","name":"English Springer Spaniel","size":"medium","weightLb":[40,50],"weightKg":[18,23],"lifespan":[12,14],"traits":{"energy":5,"exercise":5,"shedding":3,"grooming":3,"barking":3,"kids":4,"apartment":2,"trainability":5,"firstTimeOwner":3,"aloneTolerance":1,"otherDogs":5},"blurb":"An enthusiastic, people-loving gundog with boundless energy that thrives on long walks, swimming, and retrieving games."},
		{"id":"shetland-sheepdog","name":"Shetland Sheepdog","size":"small","weightLb":[15,25],"weightKg":[7,11.5],"lifespan":[12,14],"traits":{"energy":4,"exercise":3,"shedding":4,"grooming":4,"barking":5,"kids":4,"apartment":3,"trainability":5,"firstTimeOwner":4,"aloneTolerance":2,"otherDogs":4},"blurb":"A small, sensitive herding dog that is quick to learn and devoted to its family, with a dense coat and a loud bark."},
		{"id":"siberian-husky","name":"Siberian Husky","size":"medium","weightLb":[35,60],"weightKg":[16,27],"lifespan":[12,14],"traits":{"energy":5,"exercise":5,"shedding":5,"grooming":3,"barking":4,"kids":4,"apartment":1,"trainability":2,"firstTimeOwner":2,"aloneTolerance":1,"otherDogs":4},"blurb":"A friendly, independent sled dog built to run; it howls rather than barks, sheds heavily, and is known for escaping yards."},
		{"id":"english-cocker-spaniel","name":"English Cocker Spaniel","size":"medium","weightLb":[26,34],"weightKg":[12,15.5],"lifespan":[12,14],"traits":{"energy":4,"exercise":4,"shedding":3,"grooming":4,"barking":3,"kids":5,"apartment":3,"trainability":4,"firstTimeOwner":4,"aloneTolerance":2,"otherDogs":5},"blurb":"Called simply the Cocker Spaniel in Europe: a merry, affectionate gundog whose silky coat and long ears need regular care."},
		{"id":"belgian-malinois","name":"Belgian Malinois","size":"large","weightLb":[40,80],"weightKg":[18,36],"lifespan":[14,16],"traits":{"energy":5,"exercise":5,"shedding":3,"grooming":2,"barking":3,"kids":2,"apartment":1,"trainability":5,"firstTimeOwner":1,"aloneTolerance":1,"otherDogs":2},"blurb":"An intense, driven working dog used by police and armed forces worldwide; it is a poor fit for casual pet homes."},
		{"id":"vizsla","name":"Vizsla","size":"large","weightLb":[44,60],"weightKg":[20,27],"lifespan":[12,14],"traits":{"energy":5,"exercise":5,"shedding":2,"grooming":1,"barking":2,"kids":4,"apartment":2,"trainability":4,"firstTimeOwner":3,"aloneTolerance":1,"otherDogs":4},"blurb":"A sleek, golden-rust Hungarian pointer nicknamed the Velcro dog for sticking close to its owners; it needs lots of exercise."},
		{"id":"maltese","name":"Maltese","size":"small","weightLb":[4,7],"weightKg":[2,3],"lifespan":[12,15],"traits":{"energy":3,"exercise":1,"shedding":1,"grooming":5,"barking":3,"kids":2,"apartment":5,"trainability":3,"firstTimeOwner":4,"aloneTolerance":2,"otherDogs":4},"blurb":"A gentle, playful toy breed with a long, silky white coat that sheds little but tangles without daily brushing."},
		{"id":"pug","name":"Pug","size":"small","weightLb":[14,18],"weightKg":[6.5,8],"lifespan":[13,15],"traits":{"energy":2,"exercise":2,"shedding":5,"grooming":2,"barking":2,"kids":5,"apartment":5,"trainability":3,"firstTimeOwner":4,"aloneTolerance":2,"otherDogs":4},"blurb":"A charming, sociable companion that sheds more than its short coat suggests; its flat face limits exercise in warm weather."},
		{"id":"staffordshire-bull-terrier","name":"Staffordshire Bull Terrier","size":"medium","weightLb":[24,38],"weightKg":[11,17],"lifespan":[12,15],"traits":{"energy":4,"exercise":3,"shedding":3,"grooming":1,"barking":2,"kids":4,"apartment":3,"trainability":3,"firstTimeOwner":3,"aloneTolerance":2,"otherDogs":2},"blurb":"A muscular, people-loving terrier known for its affection with family; it can be intolerant of unfamiliar dogs."},
		{"id":"jack-russell-terrier","name":"Jack Russell Terrier","size":"small","weightLb":[9,15],"weightKg":[4,7],"lifespan":[12,14],"traits":{"energy":5,"exercise":4,"shedding":3,"grooming":2,"barking":4,"kids":3,"apartment":3,"trainability":3,"firstTimeOwner":2,"aloneTolerance":2,"otherDogs":2},"blurb":"A small, fearless hunting terrier with a strong prey drive; it digs, chases, and needs far more activity than its size suggests."},
		{"id":"west-highland-white-terrier","name":"West Highland White Terrier","size":"small","weightLb":[15,20],"weightKg":[7,9],"lifespan":[13,15],"traits":{"energy":4,"exercise":3,"shedding":3,"grooming":4,"barking":4,"kids":3,"apartment":4,"trainability":3,"firstTimeOwner":4,"aloneTolerance":3,"otherDogs":3},"blurb":"A confident, cheerful white terrier with a harsh coat that needs stripping or clipping; the breed is prone to skin allergies."},
		{"id":"coton-de-tulear","name":"Coton de Tulear","size":"small","weightLb":[8,15],"weightKg":[3.5,7],"lifespan":[15,19],"traits":{"energy":3,"exercise":2,"shedding":1,"grooming":4,"barking":2,"kids":5,"apartment":5,"trainability":4,"firstTimeOwner":5,"aloneTolerance":1,"otherDogs":5},"blurb":"A cotton-coated companion from Madagascar that is gentle, playful and adaptable; its long coat mats without daily brushing."},
		{"id":"danish-swedish-farmdog","name":"Danish-Swedish Farmdog","size":"small","weightLb":[15,20],"weightKg":[7,9],"lifespan":[11,13],"traits":{"energy":4,"exercise":3,"shedding":2,"grooming":1,"barking":3,"kids":5,"apartment":3,"trainability":4,"firstTimeOwner":4,"aloneTolerance":2,"otherDogs":4},"blurb":"A small, lively farm dog from Denmark and Sweden with an easy-care coat; it is friendly, alert, and quick to learn."},
		{"id":"border-terrier","name":"Border Terrier","size":"small","weightLb":[11.5,15.5],"weightKg":[5,7],"lifespan":[12,15],"traits":{"energy":4,"exercise":4,"shedding":2,"grooming":2,"barking":3,"kids":4,"apartment":3,"trainability":3,"firstTimeOwner":4,"aloneTolerance":2,"otherDogs":4},"blurb":"A tough, good-natured working terrier with an otter-shaped head and a wiry coat that needs hand-stripping twice a year."},
		{"id":"whippet","name":"Whippet","size":"medium","weightLb":[25,40],"weightKg":[11.5,18],"lifespan":[12,15],"traits":{"energy":3,"exercise":3,"shedding":2,"grooming":1,"barking":1,"kids":4,"apartment":4,"trainability":3,"firstTimeOwner":4,"aloneTolerance":4,"otherDogs":5},"blurb":"A gentle sighthound that sprints in short bursts and then naps for hours; it feels the cold and may chase small animals."},
		{"id":"bichon-frise","name":"Bichon Frise","size":"small","weightLb":[12,18],"weightKg":[5.5,8],"lifespan":[14,15],"traits":{"energy":3,"exercise":2,"shedding":1,"grooming":5,"barking":3,"kids":5,"apartment":5,"trainability":4,"firstTimeOwner":5,"aloneTolerance":1,"otherDogs":5},"blurb":"A cheerful, fluffy white companion with a low-shedding coat that needs professional grooming every four to six weeks."},
		{"id":"newfoundland","name":"Newfoundland","size":"giant","weightLb":[100,150],"weightKg":[45,68],"lifespan":[9,10],"traits":{"energy":2,"exercise":2,"shedding":5,"grooming":4,"barking":1,"kids":5,"apartment":1,"trainability":3,"firstTimeOwner":3,"aloneTolerance":3,"otherDogs":5},"blurb":"A giant, sweet-natured water rescue dog that drools heavily and sheds a thick coat; it is famously patient with children."},
	];
	/* BREEDS-DATA-END */

	var SIZE_LABELS = { small: 'Small', medium: 'Medium', large: 'Large', giant: 'Giant' };

	// What a met limit says about a breed, per trait and direction.
	var GOOD = {
		apartment: { min: 'Adapts well to apartment life' },
		energy: { max: 'Calm at home', min: 'Full of energy for an active life' },
		exercise: { max: 'Modest exercise needs', min: 'Loves long walks, runs and hikes' },
		aloneTolerance: { min: 'Copes better than most with time alone' },
		firstTimeOwner: { min: 'A good choice for a first dog' },
		trainability: { min: 'Eager to learn' },
		kids: { min: 'Known for being good with children' },
		otherDogs: { min: 'Usually gets on with other dogs' },
		shedding: { max: 'Sheds little' },
		grooming: { max: 'Easy coat care' },
		barking: { max: 'Usually quiet' },
	};

	// What a missed limit means, per trait and direction.
	var WATCH = {
		apartment: { min: 'Needs more space than an apartment gives' },
		energy: { max: 'More energy than you described', min: 'Calmer than you may want' },
		exercise: { max: 'Needs more exercise than you described', min: 'Needs less exercise than you plan' },
		aloneTolerance: { min: 'Does not like being left alone for long' },
		firstTimeOwner: { min: 'Better with an experienced owner' },
		trainability: { min: 'Can be stubborn to train' },
		kids: { min: 'Needs care around young children' },
		otherDogs: { min: 'Can be selective with other dogs' },
		shedding: { max: 'Sheds more than you want' },
		grooming: { max: 'Needs regular grooming' },
		barking: { max: 'Can be vocal' },
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

		if ( 'apartment' === v.home ) {
			add( 'apartment', 'min', 4, 2 );
		} else if ( 'small-yard' === v.home ) {
			add( 'apartment', 'min', 2, 0.5 );
		}

		if ( 'low' === v.activity ) {
			add( 'energy', 'max', 2, 2 );
			add( 'exercise', 'max', 2, 2 );
		} else if ( 'moderate' === v.activity ) {
			add( 'energy', 'max', 4, 1.5 );
			add( 'exercise', 'max', 3, 1.5 );
		} else if ( 'high' === v.activity ) {
			add( 'energy', 'min', 4, 1.5 );
			add( 'exercise', 'min', 4, 1.5 );
		}

		if ( 'some' === v.alone ) {
			add( 'aloneTolerance', 'min', 3, 2 );
		} else if ( 'long' === v.alone ) {
			add( 'aloneTolerance', 'min', 4, 2 );
		}

		if ( 'first' === v.experience ) {
			add( 'firstTimeOwner', 'min', 4, 2 );
			add( 'trainability', 'min', 3, 1 );
		} else if ( 'some' === v.experience ) {
			add( 'firstTimeOwner', 'min', 3, 1 );
		}

		if ( 'young' === v.kids ) {
			add( 'kids', 'min', 4, 3 );
		} else if ( 'older' === v.kids ) {
			add( 'kids', 'min', 3, 2 );
		}

		if ( 'yes' === v.otherDogs ) {
			add( 'otherDogs', 'min', 3, 2 );
		}

		if ( 'minimal' === v.shedding ) {
			add( 'shedding', 'max', 2, 3 );
		} else if ( 'some' === v.shedding ) {
			add( 'shedding', 'max', 3, 1 );
		}

		if ( 'minimal' === v.grooming ) {
			add( 'grooming', 'max', 2, 2 );
		} else if ( 'moderate' === v.grooming ) {
			add( 'grooming', 'max', 3, 1 );
		}

		if ( 'quiet' === v.noise ) {
			add( 'barking', 'max', 2, 2 );
		} else if ( 'some' === v.noise ) {
			add( 'barking', 'max', 3, 1 );
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
			} else if ( limit.weight >= 1.5 && GOOD[ limit.trait ] && GOOD[ limit.trait ][ dir ] ) {
				good.push( { weight: limit.weight, text: GOOD[ limit.trait ][ dir ] } );
			}
		} );

		good.sort( function ( a, b ) {
			return b.weight - a.weight;
		} );

		return { breed: breed, penalty: penalty, good: good, worst: worst };
	}

	function passesFilters( breed, v ) {
		if ( v.size && 'any' !== v.size ) {
			var wanted = 'large' === v.size ? [ 'large', 'giant' ] : [ v.size ];
			if ( wanted.indexOf( breed.size ) === -1 ) {
				return false;
			}
		}
		if ( 'minimal' === v.shedding && breed.traits.shedding > 3 ) {
			return false;
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
		return SIZE_LABELS[ breed.size ] + ', ' + weight + ', lives ' + range( breed.lifespan, 'years' );
	}

	function sentence( items ) {
		return items.length ? items.join( '. ' ) + '.' : '';
	}

	window.PawManual.register( {
		id: 'dog-breed',
		submitLabel: 'Find my breeds',
		fields: [
			{
				name: 'home',
				label: 'Where will your dog live?',
				type: 'select',
				required: true,
				options: [
					{ value: 'apartment', label: 'Apartment or flat' },
					{ value: 'small-yard', label: 'House with a small yard' },
					{ value: 'big-yard', label: 'House with a large yard or land' },
				],
			},
			{
				name: 'activity',
				label: 'How active are you?',
				type: 'select',
				required: true,
				options: [
					{ value: 'low', label: 'Relaxed: short walks are enough' },
					{ value: 'moderate', label: 'Moderate: 30 to 60 minutes of walks a day' },
					{ value: 'high', label: 'Very active: runs, hikes, 1 to 2 hours a day' },
				],
			},
			{
				name: 'alone',
				label: 'How long is the dog alone on a normal weekday?',
				type: 'select',
				required: true,
				options: [
					{ value: 'short', label: 'Under 4 hours' },
					{ value: 'some', label: '4 to 6 hours' },
					{ value: 'long', label: 'More than 6 hours' },
				],
			},
			{
				name: 'experience',
				label: 'Your experience with dogs',
				type: 'select',
				required: true,
				options: [
					{ value: 'first', label: 'This will be my first dog' },
					{ value: 'some', label: 'I have had a dog before' },
					{ value: 'experienced', label: 'Very experienced, including training' },
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
				name: 'otherDogs',
				label: 'Other dogs in the home?',
				type: 'select',
				required: true,
				default: 'no',
				options: [
					{ value: 'no', label: 'No' },
					{ value: 'yes', label: 'Yes' },
				],
			},
			{
				name: 'shedding',
				label: 'How much dog hair can you live with?',
				type: 'select',
				required: true,
				options: [
					{ value: 'minimal', label: 'As little as possible' },
					{ value: 'some', label: 'Some is fine' },
					{ value: 'lots', label: 'Hair everywhere is fine' },
				],
			},
			{
				name: 'grooming',
				label: 'Time for brushing and grooming',
				type: 'select',
				required: true,
				options: [
					{ value: 'minimal', label: 'A quick brush once a week' },
					{ value: 'moderate', label: 'A few brushing sessions a week' },
					{ value: 'lots', label: 'Daily brushing or a groomer is fine' },
				],
			},
			{
				name: 'noise',
				label: 'Barking',
				type: 'select',
				required: true,
				options: [
					{ value: 'quiet', label: 'I need a quiet dog (neighbours, thin walls)' },
					{ value: 'some', label: 'Some barking is fine' },
					{ value: 'any', label: 'Barking does not bother me' },
				],
			},
			{
				name: 'size',
				label: 'Preferred size',
				type: 'select',
				required: true,
				default: 'any',
				options: [
					{ value: 'any', label: 'No preference' },
					{ value: 'small', label: 'Small (up to 9 kg / 20 lb)' },
					{ value: 'medium', label: 'Medium (10–22 kg / 21–50 lb)' },
					{ value: 'large', label: 'Large or giant (over 22 kg / 50 lb)' },
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
					summary: 'No breed in our list fits every answer. Try "No preference" for size or allow a little more shedding.',
				};
			}

			var notes = [];
			if ( 'long' === values.alone ) {
				notes.push( 'No dog should be alone for a full working day every day. Plan for a dog walker, daycare or a midday visit, whatever breed you choose.' );
			}
			if ( 'minimal' === values.shedding ) {
				notes.push( 'No dog is truly hypoallergenic. Low-shedding breeds leave less hair around, but if anyone in the home has allergies, spend time with the breed before you commit.' );
			}
			notes.push( 'Breed is a tendency, not a promise: every dog is an individual. Mixed-breed dogs from shelters can fit these answers just as well, so ask the shelter about temperament.' );

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
