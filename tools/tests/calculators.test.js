#!/usr/bin/env node
/**
 * Formula tests for the calculator config files.
 *
 * Run: node tools/tests/calculators.test.js
 *
 * Each config in wp-content/plugins/pawmanual-calculators/calculators/ is
 * loaded into a sandbox with a stub PawManual.register(), and its calculate()
 * is checked against the numbers the site publishes elsewhere. That is the
 * point of these tests: a calculator and the article it is paired with must
 * never disagree, so a change to either one fails here until both agree.
 */
'use strict';

const fs = require( 'fs' );
const path = require( 'path' );
const vm = require( 'vm' );

const CALC_DIR = path.join( __dirname, '..', '..', 'wp-content', 'plugins', 'pawmanual-calculators', 'calculators' );
const LB = 0.45359237;

let passed = 0;
let failed = 0;

function check( name, condition, detail ) {
	if ( condition ) {
		passed++;
	} else {
		failed++;
		console.log( 'FAIL: ' + name + ( detail ? '\n      ' + detail : '' ) );
	}
}

/**
 * Load one calculator config and return what it registered.
 */
function load( id, region ) {
	const configs = {};
	const sandbox = {
		window: {
			PawManual: {
				register: ( config ) => { configs[ config.id ] = config; },
				getRegion: () => region || 'US',
				prefersImperial: () => ( region || 'US' ) === 'US',
				isPublished: () => false,
			},
		},
		console,
	};
	vm.createContext( sandbox );
	const file = path.join( CALC_DIR, id, id + '.js' );
	vm.runInContext( fs.readFileSync( file, 'utf8' ), sandbox, { filename: file } );
	return configs[ id ];
}

/** The first integer in a string like "≈ 820 kcal per day" ("1,300" counts). */
function firstNumber( text ) {
	const m = String( text ).match( /[\d,]+(\.\d+)?/ );
	return m ? Number( m[ 0 ].replace( /,/g, '' ) ) : NaN;
}

/** Every user-visible string in a result, for the em-dash rule. */
function resultText( r ) {
	const parts = [ r.headline, r.summary ];
	( r.details || [] ).forEach( ( d ) => parts.push( d.heading, d.label, d.value ) );
	( r.list || [] ).forEach( ( d ) => parts.push( d.title, d.meta, d.text ) );
	( r.notes || [] ).forEach( ( n ) => parts.push( n ) );
	return parts.filter( Boolean ).join( ' ' );
}

function noEmDash( name, text ) {
	check( name + ': no em dash', ! /—/.test( text ), text.match( /.{0,30}—.{0,30}/ ) );
}

/** Every user-visible string in a config's fields. */
function fieldText( config ) {
	const parts = [ config.submitLabel ];
	config.fields.forEach( ( f ) => {
		parts.push( f.label, f.help, f.placeholder );
		( f.options || [] ).forEach( ( o ) => parts.push( o.label ) );
		( f.units || [] ).forEach( ( u ) => parts.push( u.label ) );
	} );
	return parts.filter( Boolean ).join( ' ' );
}

// ---------------------------------------------------------------------------
// Dog Food: content/dogs/food/how-much-should-i-feed-my-dog.md's table.
// ---------------------------------------------------------------------------
( function () {
	const dog = load( 'dog-food' );
	noEmDash( 'dog-food fields', fieldText( dog ) );

	const table = [ [ 10, 300, 400 ], [ 30, 700, 900 ], [ 50, 1000, 1300 ], [ 70, 1300, 1700 ], [ 90, 1600, 2000 ] ];
	table.forEach( ( [ lb, lo, hi ] ) => {
		const r = dog.calculate( { weight: lb * LB, weightUnit: 'lb', stage: 'adult', neutered: 'yes', activity: 'normal', condition: 'ideal', dry: '', wet: '', meals: '2' } );
		const kcal = firstNumber( r.headline );
		check( `dog-food ${lb} lb neutered adult inside the article's ${lo}–${hi}`, kcal >= lo && kcal <= hi, r.headline );
		noEmDash( `dog-food ${lb} lb`, resultText( r ) );
	} );

	const cups = dog.calculate( { weight: 30 * LB, weightUnit: 'lb', stage: 'adult', neutered: 'yes', activity: 'normal', condition: 'ideal', dry: 400, dryUnit: 'cup', wet: '', meals: '2' } );
	const cupRow = cups.details.find( ( d ) => d.label === 'Dry food only' );
	check( 'dog-food converts kcal/cup to cups', cupRow && /^2\.0 cups a day \(1\.0 per meal\)$/.test( cupRow.value ), cupRow && cupRow.value );

	const grams = dog.calculate( { weight: 13.6, weightUnit: 'kg', stage: 'adult', neutered: 'yes', activity: 'normal', condition: 'ideal', dry: 370, dryUnit: '100g', wet: '', meals: '2' } );
	const gRow = grams.details.find( ( d ) => d.label === 'Dry food only' );
	check( 'dog-food converts kcal/100 g to grams', gRow && / g a day/.test( gRow.value ), gRow && gRow.value );

	const over = dog.calculate( { weight: 30 * LB, weightUnit: 'lb', stage: 'adult', neutered: 'yes', activity: 'normal', condition: 'over', dry: '', wet: '', meals: '2' } );
	check( 'dog-food overweight keeps the maintenance number and adds a vet note',
		firstNumber( over.headline ) === firstNumber( cups.headline ) && over.notes.some( ( n ) => /vet/.test( n ) ) );

	const puppy = dog.calculate( { weight: 10 * LB, weightUnit: 'lb', stage: 'puppy-young', neutered: 'no', activity: 'normal', condition: 'ideal', dry: '', wet: '', meals: '3' } );
	check( 'dog-food young puppy uses 3 × RER', /3\.0 × RER/.test( puppy.details.find( ( d ) => d.label === 'Factor used' ).value ) );
}() );

// ---------------------------------------------------------------------------
// Cat Food: content/cats/food/how-much-should-i-feed-my-cat.md's table.
// ---------------------------------------------------------------------------
( function () {
	const cat = load( 'cat-food' );
	noEmDash( 'cat-food fields', fieldText( cat ) );

	const table = [ [ 8, 150, 200 ], [ 10, 180, 250 ], [ 12, 230, 320 ], [ 14, 230, 320 ] ];
	table.forEach( ( [ lb, lo, hi ] ) => {
		const r = cat.calculate( { weight: lb * LB, weightUnit: 'lb', stage: 'adult', neutered: 'yes', lifestyle: 'indoor', condition: 'ideal', dry: '', wet: '', meals: '2' } );
		const kcal = firstNumber( r.headline );
		check( `cat-food ${lb} lb indoor neutered inside the article's ${lo}–${hi}`, kcal >= lo && kcal <= hi, r.headline );
		noEmDash( `cat-food ${lb} lb`, resultText( r ) );
	} );

	const over = cat.calculate( { weight: 14 * LB, weightUnit: 'lb', stage: 'adult', neutered: 'yes', lifestyle: 'indoor', condition: 'over', dry: '', wet: '', meals: '2' } );
	check( 'cat-food overweight warns about hepatic lipidosis', over.notes.some( ( n ) => /hepatic lipidosis/.test( n ) ) );
}() );

// ---------------------------------------------------------------------------
// Pet Cost: content/pet-guides/pet-costs.md's headline yearly ranges
// (dog $1,000–$2,000, cat $800–$1,200) must contain the calculator's typical
// yearly estimate for a typical household (adopted, dry food, no extras).
// ---------------------------------------------------------------------------
( function () {
	const cost = load( 'pet-cost' );
	noEmDash( 'pet-cost fields', fieldText( cost ) );

	const base = { currency: 'usd', source: 'adopt', neutered: 'no', food: 'dry', insurance: 'no', grooming: 'no', boarding: '', rent: '', other: '' };
	[ [ 'small', 1000, 2000 ], [ 'medium', 1000, 2000 ], [ 'large', 1000, 2000 ], [ 'giant', 1000, 2000 ], [ 'cat', 800, 1200 ] ].forEach( ( [ pet, lo, hi ] ) => {
		const r = cost.calculate( Object.assign( {}, base, { pet } ) );
		const yearly = firstNumber( r.headline );
		check( `pet-cost ${ pet } yearly estimate inside the article's $${ lo }–$${ hi }`, yearly >= lo && yearly <= hi, r.headline );
		noEmDash( `pet-cost ${ pet }`, resultText( r ) );
	} );

	const eur = cost.calculate( Object.assign( {}, base, { currency: 'eur', pet: 'medium', boarding: 10, other: 100 } ) );
	check( 'pet-cost euros render with the euro sign', /^≈ €/.test( eur.headline ), eur.headline );
	check( 'pet-cost euros add the Western Europe note', eur.notes.some( ( n ) => /Western Europe/.test( n ) ) );
	check( 'pet-cost optional lines appear when filled in', eur.details.some( ( d ) => /10 nights/.test( d.label || '' ) ) && eur.details.some( ( d ) => d.label === 'Other yearly costs' ) );

	const insured = cost.calculate( Object.assign( {}, base, { pet: 'medium', insurance: 'yes' } ) );
	check( 'pet-cost insurance raises the yearly estimate', firstNumber( insured.headline ) > firstNumber( cost.calculate( Object.assign( {}, base, { pet: 'medium' } ) ).headline ) );

	const neutered = cost.calculate( Object.assign( {}, base, { pet: 'cat', neutered: 'yes' } ) );
	check( 'pet-cost skips spay/neuter when already done', ! neutered.details.some( ( d ) => d.label === 'Spay or neuter' ) );
}() );

// ---------------------------------------------------------------------------
// Breed selectors: sane rankings for clear-cut households, filters respected.
// ---------------------------------------------------------------------------
( function () {
	const dog = load( 'dog-breed' );
	noEmDash( 'dog-breed fields', fieldText( dog ) );

	const names = ( r ) => r.list.map( ( i ) => i.title );

	const apartment = dog.calculate( { home: 'apartment', activity: 'low', alone: 'short', experience: 'first', kids: 'none', otherDogs: 'no', shedding: 'some', grooming: 'moderate', noise: 'quiet', size: 'any' } );
	check( 'dog-breed returns five matches', apartment.list.length === 5, names( apartment ) );
	check( 'dog-breed calm apartment match excludes high-energy working breeds',
		! names( apartment ).some( ( n ) => /Border Collie|Malinois|Husky|Australian Shepherd/.test( n ) ), names( apartment ) );
	noEmDash( 'dog-breed result', resultText( apartment ) );

	const runner = dog.calculate( { home: 'big-yard', activity: 'high', alone: 'short', experience: 'experienced', kids: 'none', otherDogs: 'no', shedding: 'lots', grooming: 'lots', noise: 'any', size: 'large' } );
	check( 'dog-breed size filter keeps only large/giant breeds', runner.list.every( ( i ) => /^(Large|Giant)/.test( i.meta ) ), runner.list.map( ( i ) => i.meta ) );

	const lowShed = dog.calculate( { home: 'small-yard', activity: 'moderate', alone: 'some', experience: 'some', kids: 'older', otherDogs: 'yes', shedding: 'minimal', grooming: 'lots', noise: 'some', size: 'any' } );
	check( 'dog-breed minimal shedding adds the no-hypoallergenic note', lowShed.notes.some( ( n ) => /hypoallergenic/.test( n ) ) );
	check( 'dog-breed only the five guide-page breeds carry a path',
		dog.calculate( { home: 'big-yard', activity: 'moderate', alone: 'short', experience: 'some', kids: 'none', otherDogs: 'no', shedding: 'lots', grooming: 'lots', noise: 'any', size: 'any' } )
			.list.every( ( i ) => ! i.path || /^\/dogs\/breeds\/(french-bulldog|german-shepherd|golden-retriever|labrador-retriever|poodle)\/$/.test( i.path ) ) );

	const cat = load( 'cat-breed' );
	noEmDash( 'cat-breed fields', fieldText( cat ) );
	const lap = cat.calculate( { activity: 'calm', affection: 'cuddly', vocal: 'quiet', coat: 'any', grooming: 'lots', shedding: 'lots', kids: 'young', otherPets: 'no', alone: 'short', experience: 'first' } );
	check( 'cat-breed returns five matches', lap.list.length === 5, names( lap ) );
	check( 'cat-breed calm quiet lap cat excludes Siamese and Bengal', ! names( lap ).some( ( n ) => /Siamese|Bengal|Oriental/.test( n ) ), names( lap ) );
	noEmDash( 'cat-breed result', resultText( lap ) );
	const shortCoat = cat.calculate( { activity: 'lively', affection: 'some', vocal: 'any', coat: 'short', grooming: 'minimal', shedding: 'some', kids: 'none', otherPets: 'yes', alone: 'some', experience: 'some' } );
	check( 'cat-breed coat filter keeps short or hairless coats', shortCoat.list.every( ( i ) => /^(Short coat|Hairless)/.test( i.meta ) ), shortCoat.list.map( ( i ) => i.meta ) );
}() );

console.log( `\n${ passed } passed, ${ failed } failed` );
if ( failed ) {
	process.exitCode = 1;
}
