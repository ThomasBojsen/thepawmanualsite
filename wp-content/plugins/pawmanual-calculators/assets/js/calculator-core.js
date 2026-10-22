/**
 * PawManual Calculators: shared engine.
 *
 * Loaded once on any page that uses the [pawmanual_calculator] shortcode. Each
 * calculator's config file (calculators/<id>/<id>.js) calls
 * PawManual.register(config); this engine then renders that calculator's form
 * fields, validates input, runs config.calculate(), and shows the result.
 *
 * Adding a calculator never requires editing this file.
 *
 * Config shape (every key except id/fields/calculate is optional):
 *   {
 *     id: 'dog-age',
 *     submitLabel: 'Find my breeds',     // replaces the shell's "Calculate"
 *     fields: [
 *       { name, label, type: 'number' | 'select', required,
 *         min, max, step, unit, help, placeholder,
 *         default,                         // value, or function returning one
 *         options: [ { value, label } ],   // select fields only
 *         units: [                         // number fields only: a unit switch
 *           { value: 'lb', label: 'lb', system: 'imperial',
 *             toBase: 0.45359237, min, max, step },
 *           { value: 'kg', label: 'kg', system: 'metric',
 *             toBase: 1, min, max, step }
 *         ]
 *       }
 *     ],
 *     calculate: function (values) {
 *       // values maps each field name to a number (number fields, already
 *       // converted to the base unit when the field has `units`) or a string
 *       // (select fields). A field with units also adds values[name + 'Unit'].
 *       return {
 *         headline: 'string',
 *         summary: 'string',
 *         details: [ { heading } | { label, value } ],   // a rows table
 *         list: [ { title, path, meta, text } ],         // a ranked list
 *         notes: [ 'string' ]
 *       };
 *     }
 *   }
 *
 * Everything the engine writes goes through textContent, never innerHTML, so
 * a config can't inject markup by accident.
 */
( function ( window, document ) {
	'use strict';

	var PawManual = window.PawManual || {};
	PawManual._configs = PawManual._configs || {};
	PawManual._published = PawManual._published || {};

	/**
	 * Register a calculator configuration. Called by each calculator config file.
	 *
	 * @param {Object} config Calculator configuration (see the file header).
	 */
	PawManual.register = function ( config ) {
		if ( config && config.id ) {
			PawManual._configs[ config.id ] = config;
		}
	};

	/**
	 * The visitor's region code from the browser language ('US', 'DK', …), or
	 * '' when the language carries none. Used only to pick sensible defaults
	 * (lb vs kg, USD vs EUR); the visitor can always switch.
	 *
	 * @return {string}
	 */
	PawManual.getRegion = function () {
		var langs = ( window.navigator && window.navigator.languages && window.navigator.languages.length ) ?
			window.navigator.languages :
			[ ( window.navigator && window.navigator.language ) || '' ];

		for ( var i = 0; i < langs.length; i++ ) {
			var parts = String( langs[ i ] ).split( '-' );
			if ( parts.length > 1 && /^[a-z]{2}$/i.test( parts[ parts.length - 1 ] ) ) {
				return parts[ parts.length - 1 ].toUpperCase();
			}
		}
		return '';
	};

	/**
	 * Whether the visitor most likely measures in pounds. Only the US (plus
	 * Liberia and Myanmar) defaults to imperial; everyone else gets metric.
	 *
	 * @return {boolean}
	 */
	PawManual.prefersImperial = function () {
		return [ 'US', 'LR', 'MM' ].indexOf( PawManual.getRegion() ) !== -1;
	};

	/**
	 * Whether a site path is a published page. The plugin prints the list of
	 * published paths a calculator may link to (includes/enqueue.php), so a
	 * result never links to a page that hasn't shipped yet: the same rule the
	 * theme enforces for post-body links.
	 *
	 * @param {string} path Site-relative path, e.g. '/dogs/breeds/poodle/'.
	 * @return {boolean}
	 */
	PawManual.isPublished = function ( path ) {
		return !! PawManual._published[ normalizePath( path ) ];
	};

	window.PawManual = PawManual;

	function normalizePath( path ) {
		var p = String( path || '' ).toLowerCase().replace( /[?#].*$/, '' );
		p = p.replace( /^\/+|\/+$/g, '' );
		return p ? '/' + p + '/' : '/';
	}

	/**
	 * Resolve a field's default value (a plain value or a function).
	 */
	function resolveDefault( field ) {
		if ( typeof field.default === 'function' ) {
			try {
				return field.default();
			} catch ( error ) {
				return undefined;
			}
		}
		return field.default;
	}

	/**
	 * Pick the unit a field starts in: the one matching the visitor's
	 * measurement system, else the first.
	 */
	function initialUnit( units ) {
		var system = PawManual.prefersImperial() ? 'imperial' : 'metric';
		for ( var i = 0; i < units.length; i++ ) {
			if ( units[ i ].system === system ) {
				return units[ i ];
			}
		}
		return units[ 0 ];
	}

	function findUnit( units, value ) {
		for ( var i = 0; i < units.length; i++ ) {
			if ( units[ i ].value === value ) {
				return units[ i ];
			}
		}
		return units[ 0 ];
	}

	/**
	 * Apply a unit's min/max/step to the number input, so the browser's own
	 * spinner and the engine's validation agree.
	 */
	function applyUnitLimits( control, unit, field ) {
		var min = typeof unit.min === 'number' ? unit.min : field.min;
		var max = typeof unit.max === 'number' ? unit.max : field.max;
		var step = unit.step || field.step;

		if ( typeof min === 'number' ) { control.min = String( min ); } else { control.removeAttribute( 'min' ); }
		if ( typeof max === 'number' ) { control.max = String( max ); } else { control.removeAttribute( 'max' ); }
		if ( step ) { control.step = String( step ); } else { control.removeAttribute( 'step' ); }
	}

	/**
	 * Build one field group: a label, the control, optional help text, and an
	 * error slot.
	 *
	 * @param {string} calcId Calculator id, used to namespace element ids.
	 * @param {Object} field  Field definition from the config.
	 * @return {Object} { wrap, control, error, field, unitControl? }
	 */
	function createField( calcId, field ) {
		var wrap = document.createElement( 'div' );
		wrap.className = 'pm-calc__field';

		var inputId = 'pm-' + calcId + '-' + field.name;
		var errorId = inputId + '-error';
		var helpId = inputId + '-help';
		var hasUnits = field.type === 'number' && field.units && field.units.length > 1;
		var unitControl = null;
		var startDefault = resolveDefault( field );

		var label = document.createElement( 'label' );
		label.className = 'pm-calc__label';
		label.setAttribute( 'for', inputId );
		label.textContent = field.label || field.name;
		wrap.appendChild( label );

		var control;

		if ( field.type === 'select' ) {
			control = document.createElement( 'select' );
			control.className = 'pm-calc__select';

			var placeholder = document.createElement( 'option' );
			placeholder.value = '';
			placeholder.textContent = field.placeholder || 'Choose…';
			control.appendChild( placeholder );

			( field.options || [] ).forEach( function ( opt ) {
				var option = document.createElement( 'option' );
				option.value = opt.value;
				option.textContent = opt.label;
				// defaultSelected (not just selected) so the form's own Reset
				// restores the default instead of the empty placeholder.
				if ( startDefault !== undefined && String( startDefault ) === String( opt.value ) ) {
					option.defaultSelected = true;
					option.selected = true;
				}
				control.appendChild( option );
			} );
		} else {
			control = document.createElement( 'input' );
			control.className = 'pm-calc__input';
			control.type = ( field.type === 'number' ) ? 'number' : 'text';

			if ( ! hasUnits ) {
				applyUnitLimits( control, {}, field );
			}
			if ( field.placeholder ) { control.placeholder = field.placeholder; }
			if ( field.type === 'number' ) { control.inputMode = 'decimal'; }
			if ( startDefault !== undefined && startDefault !== null && startDefault !== '' ) {
				control.defaultValue = String( startDefault );
			}
		}

		control.id = inputId;
		control.name = field.name;
		control.setAttribute( 'aria-describedby', ( field.help ? helpId + ' ' : '' ) + errorId );
		if ( field.required ) { control.setAttribute( 'aria-required', 'true' ); }

		if ( hasUnits ) {
			// A number input plus its own unit <select>. The select carries an
			// aria-label because the visible <label> already belongs to the input.
			var unitsWrap = document.createElement( 'div' );
			unitsWrap.className = 'pm-calc__control pm-calc__control--units';
			unitsWrap.appendChild( control );

			unitControl = document.createElement( 'select' );
			unitControl.className = 'pm-calc__select pm-calc__unit-select';
			unitControl.id = inputId + '-unit';
			unitControl.name = field.name + 'Unit';
			unitControl.setAttribute( 'aria-label', ( field.label || field.name ) + ': unit' );

			var startUnit = initialUnit( field.units );
			field.units.forEach( function ( unit ) {
				var option = document.createElement( 'option' );
				option.value = unit.value;
				option.textContent = unit.label;
				if ( unit === startUnit ) {
					option.defaultSelected = true;
					option.selected = true;
				}
				unitControl.appendChild( option );
			} );
			applyUnitLimits( control, startUnit, field );

			// Switching units converts what's already typed, so "20 lb" becomes
			// "9.1 kg" instead of silently turning into 20 kg. Units without a
			// toBase (kcal/cup vs kcal/100 g measure different things) can't
			// convert, so the old value is cleared rather than reinterpreted.
			unitControl.setAttribute( 'data-pm-previous', startUnit.value );
			unitControl.addEventListener( 'change', function () {
				var from = findUnit( field.units, unitControl.getAttribute( 'data-pm-previous' ) );
				var to = findUnit( field.units, unitControl.value );
				var current = Number( control.value );

				if ( control.value !== '' ) {
					if ( isFinite( current ) && from.toBase && to.toBase ) {
						var converted = current * from.toBase / to.toBase;
						control.value = String( Math.round( converted * 10 ) / 10 );
					} else if ( from !== to ) {
						control.value = '';
					}
				}
				applyUnitLimits( control, to, field );
				unitControl.setAttribute( 'data-pm-previous', to.value );
			} );

			unitsWrap.appendChild( unitControl );
			wrap.appendChild( unitsWrap );
		} else if ( field.unit ) {
			var unitWrap = document.createElement( 'div' );
			unitWrap.className = 'pm-calc__control pm-calc__control--unit';
			unitWrap.appendChild( control );
			var unit = document.createElement( 'span' );
			unit.className = 'pm-calc__unit';
			unit.textContent = field.unit;
			unitWrap.appendChild( unit );
			wrap.appendChild( unitWrap );
		} else {
			wrap.appendChild( control );
		}

		if ( field.help ) {
			var help = document.createElement( 'p' );
			help.className = 'pm-calc__help';
			help.id = helpId;
			help.textContent = field.help;
			wrap.appendChild( help );
		}

		var error = document.createElement( 'p' );
		error.className = 'pm-calc__error';
		error.id = errorId;
		error.hidden = true;
		wrap.appendChild( error );

		return { wrap: wrap, control: control, error: error, field: field, unitControl: unitControl };
	}

	/**
	 * Show a validation message on a field.
	 */
	function setFieldError( item, message ) {
		item.error.textContent = message;
		item.error.hidden = false;
		item.control.setAttribute( 'aria-invalid', 'true' );
		item.wrap.classList.add( 'pm-calc__field--invalid' );
	}

	/**
	 * Clear any validation message from a field.
	 */
	function clearFieldError( item ) {
		item.error.textContent = '';
		item.error.hidden = true;
		item.control.removeAttribute( 'aria-invalid' );
		item.wrap.classList.remove( 'pm-calc__field--invalid' );
	}

	/**
	 * Validate one field's current value.
	 *
	 * @return {Object} { valid: boolean, value?, unit? }: value is present when
	 *                  valid, already converted to the base unit for a field
	 *                  with `units`.
	 */
	function validateField( item ) {
		var field = item.field;
		var raw = ( item.control.value != null ) ? String( item.control.value ).trim() : '';
		var unit = item.unitControl ? findUnit( field.units, item.unitControl.value ) : null;
		var suffix = unit ? ' ' + unit.label : '';

		clearFieldError( item );

		if ( raw === '' ) {
			if ( field.required ) {
				setFieldError( item, 'Please fill in this field.' );
				return { valid: false };
			}
			return { valid: true, value: '', unit: unit ? unit.value : undefined };
		}

		if ( field.type === 'number' ) {
			var num = Number( raw );
			var min = ( unit && typeof unit.min === 'number' ) ? unit.min : field.min;
			var max = ( unit && typeof unit.max === 'number' ) ? unit.max : field.max;

			if ( ! isFinite( num ) ) {
				setFieldError( item, 'Please enter a number.' );
				return { valid: false };
			}
			if ( typeof min === 'number' && num < min ) {
				setFieldError( item, 'Please enter ' + min + suffix + ' or more.' );
				return { valid: false };
			}
			if ( typeof max === 'number' && num > max ) {
				setFieldError( item, 'Please enter ' + max + suffix + ' or less.' );
				return { valid: false };
			}
			if ( unit && unit.toBase ) {
				num = num * unit.toBase;
			}
			return { valid: true, value: num, unit: unit ? unit.value : undefined };
		}

		return { valid: true, value: raw };
	}

	/**
	 * Empty the result region.
	 */
	function clearResult( resultEl ) {
		resultEl.textContent = '';
		resultEl.classList.remove( 'pm-calc__result--filled', 'pm-calc__result--error' );
	}

	function appendText( parent, tag, className, text ) {
		var el = document.createElement( tag );
		el.className = className;
		el.textContent = text;
		parent.appendChild( el );
		return el;
	}

	/**
	 * A label/value table. A row with only `heading` starts a new group.
	 */
	function renderDetails( resultEl, rows ) {
		var dl = document.createElement( 'dl' );
		dl.className = 'pm-calc__details';

		rows.forEach( function ( row ) {
			if ( ! row ) {
				return;
			}
			if ( row.heading ) {
				appendText( dl, 'div', 'pm-calc__details-heading', row.heading );
				return;
			}
			var line = document.createElement( 'div' );
			line.className = 'pm-calc__details-row';
			appendText( line, 'dt', 'pm-calc__details-label', row.label );
			appendText( line, 'dd', 'pm-calc__details-value', row.value );
			dl.appendChild( line );
		} );

		resultEl.appendChild( dl );
	}

	/**
	 * A ranked list. An item links to its `path` only when that page is
	 * published (PawManual.isPublished); otherwise the title is plain text.
	 */
	function renderList( resultEl, items ) {
		var ol = document.createElement( 'ol' );
		ol.className = 'pm-calc__list';

		items.forEach( function ( item ) {
			if ( ! item ) {
				return;
			}
			var li = document.createElement( 'li' );
			li.className = 'pm-calc__list-item';

			var title;
			if ( item.path && PawManual.isPublished( item.path ) ) {
				title = document.createElement( 'a' );
				title.href = normalizePath( item.path );
				title.className = 'pm-calc__list-title pm-calc__list-title--link';
				title.textContent = item.title;
				li.appendChild( title );
			} else {
				appendText( li, 'span', 'pm-calc__list-title', item.title );
			}

			if ( item.meta ) {
				appendText( li, 'span', 'pm-calc__list-meta', item.meta );
			}
			if ( item.text ) {
				appendText( li, 'p', 'pm-calc__list-text', item.text );
			}
			ol.appendChild( li );
		} );

		resultEl.appendChild( ol );
	}

	/**
	 * Render a successful result.
	 */
	function showResult( resultEl, result ) {
		resultEl.textContent = '';

		appendText( resultEl, 'div', 'pm-calc__result-value', result.headline );

		if ( result.summary ) {
			appendText( resultEl, 'div', 'pm-calc__result-summary', result.summary );
		}
		if ( result.details && result.details.length ) {
			renderDetails( resultEl, result.details );
		}
		if ( result.list && result.list.length ) {
			renderList( resultEl, result.list );
		}
		if ( result.notes && result.notes.length ) {
			result.notes.forEach( function ( note ) {
				if ( note ) {
					appendText( resultEl, 'p', 'pm-calc__note', note );
				}
			} );
		}

		resultEl.classList.add( 'pm-calc__result--filled' );
		resultEl.classList.remove( 'pm-calc__result--error' );
	}

	/**
	 * Render an error message in the result region.
	 */
	function showResultError( resultEl, message ) {
		resultEl.textContent = '';
		appendText( resultEl, 'div', 'pm-calc__result-value', message );
		resultEl.classList.add( 'pm-calc__result--filled', 'pm-calc__result--error' );
	}

	/**
	 * Render one calculator: inject its fields and wire up Calculate / Reset.
	 */
	function renderCalculator( root, config ) {
		var form = root.querySelector( '.pm-calc__form' );
		var fieldsHost = root.querySelector( '[data-pm-fields]' );
		var resultEl = root.querySelector( '[data-pm-result]' );
		var submit = root.querySelector( '[data-pm-calculate]' );

		if ( ! form || ! fieldsHost || ! resultEl ) {
			return;
		}

		if ( config.submitLabel && submit ) {
			submit.textContent = config.submitLabel;
		}

		var items = ( config.fields || [] ).map( function ( field ) {
			var item = createField( config.id, field );
			fieldsHost.appendChild( item.wrap );
			return item;
		} );

		form.addEventListener( 'submit', function ( event ) {
			event.preventDefault();
			clearResult( resultEl );

			var values = {};
			var valid = true;
			var firstInvalid = null;

			items.forEach( function ( item ) {
				var outcome = validateField( item );
				if ( ! outcome.valid ) {
					valid = false;
					if ( ! firstInvalid ) {
						firstInvalid = item.control;
					}
				} else {
					values[ item.field.name ] = outcome.value;
					if ( outcome.unit !== undefined ) {
						values[ item.field.name + 'Unit' ] = outcome.unit;
					}
				}
			} );

			if ( ! valid ) {
				if ( firstInvalid ) {
					firstInvalid.focus();
				}
				return;
			}

			var result;
			try {
				result = config.calculate( values );
			} catch ( error ) {
				result = null;
			}

			if ( result && result.headline ) {
				showResult( resultEl, result );
			} else {
				showResultError( resultEl, 'Sorry, we could not calculate a result. Please check your input.' );
			}
		} );

		form.addEventListener( 'reset', function () {
			items.forEach( clearFieldError );
			clearResult( resultEl );

			// The browser restores each unit <select> to its default on reset,
			// but only after this handler runs: re-sync the input's limits then.
			window.setTimeout( function () {
				items.forEach( function ( item ) {
					if ( item.unitControl ) {
						var unit = findUnit( item.field.units, item.unitControl.value );
						applyUnitLimits( item.control, unit, item.field );
						item.unitControl.setAttribute( 'data-pm-previous', unit.value );
					}
				} );
			}, 0 );
		} );
	}

	/**
	 * Find every calculator shell on the page and activate it.
	 */
	function init() {
		var roots = document.querySelectorAll( '.pm-calc[data-calculator]' );

		Array.prototype.forEach.call( roots, function ( root ) {
			if ( root.getAttribute( 'data-pm-ready' ) === '1' ) {
				return;
			}
			root.setAttribute( 'data-pm-ready', '1' );

			var id = root.getAttribute( 'data-calculator' );
			var config = PawManual._configs[ id ];

			if ( ! config ) {
				var resultEl = root.querySelector( '[data-pm-result]' );
				if ( resultEl ) {
					showResultError( resultEl, 'This calculator could not be loaded.' );
				}
				return;
			}

			renderCalculator( root, config );
		} );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
} )( window, document );
