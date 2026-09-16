/**
 * PawManual Calculators — shared engine.
 *
 * Loaded once on any page that uses the [pawmanual_calculator] shortcode. Each
 * calculator's config file (calculators/<id>/<id>.js) calls
 * PawManual.register(config); this engine then renders that calculator's form
 * fields, validates input, runs config.calculate(), and shows the result.
 *
 * Adding a calculator never requires editing this file.
 *
 * Config shape:
 *   {
 *     id: 'dog-age',
 *     fields: [
 *       { name, label, type: 'number' | 'select', required,
 *         min, max, step, unit, help, placeholder,
 *         options: [ { value, label } ]   // select fields only
 *       }
 *     ],
 *     calculate: function (values) {
 *       // values maps each field name to a number (number fields)
 *       // or a string (select fields).
 *       return { headline: 'string', summary: 'string' };
 *     }
 *   }
 */
( function ( window, document ) {
	'use strict';

	var PawManual = window.PawManual || {};
	PawManual._configs = PawManual._configs || {};

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

	window.PawManual = PawManual;

	/**
	 * Build one field group: a label, the control, optional help text, and an
	 * error slot.
	 *
	 * @param {string} calcId Calculator id, used to namespace element ids.
	 * @param {Object} field  Field definition from the config.
	 * @return {Object} { wrap, control, error, field }
	 */
	function createField( calcId, field ) {
		var wrap = document.createElement( 'div' );
		wrap.className = 'pm-calc__field';

		var inputId = 'pm-' + calcId + '-' + field.name;
		var errorId = inputId + '-error';

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
				control.appendChild( option );
			} );
		} else {
			control = document.createElement( 'input' );
			control.className = 'pm-calc__input';
			control.type = ( field.type === 'number' ) ? 'number' : 'text';

			if ( typeof field.min === 'number' ) { control.min = String( field.min ); }
			if ( typeof field.max === 'number' ) { control.max = String( field.max ); }
			if ( field.step ) { control.step = String( field.step ); }
			if ( field.placeholder ) { control.placeholder = field.placeholder; }
			if ( field.type === 'number' ) { control.inputMode = 'decimal'; }
		}

		control.id = inputId;
		control.name = field.name;
		control.setAttribute( 'aria-describedby', errorId );
		if ( field.required ) { control.setAttribute( 'aria-required', 'true' ); }

		if ( field.unit ) {
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
			help.textContent = field.help;
			wrap.appendChild( help );
		}

		var error = document.createElement( 'p' );
		error.className = 'pm-calc__error';
		error.id = errorId;
		error.hidden = true;
		wrap.appendChild( error );

		return { wrap: wrap, control: control, error: error, field: field };
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
	 * @return {Object} { valid: boolean, value? } — value is present when valid.
	 */
	function validateField( item ) {
		var field = item.field;
		var raw = ( item.control.value != null ) ? String( item.control.value ).trim() : '';

		clearFieldError( item );

		if ( raw === '' ) {
			if ( field.required ) {
				setFieldError( item, 'Please fill in this field.' );
				return { valid: false };
			}
			return { valid: true, value: '' };
		}

		if ( field.type === 'number' ) {
			var num = Number( raw );
			if ( ! isFinite( num ) ) {
				setFieldError( item, 'Please enter a number.' );
				return { valid: false };
			}
			if ( typeof field.min === 'number' && num < field.min ) {
				setFieldError( item, 'Please enter ' + field.min + ' or more.' );
				return { valid: false };
			}
			if ( typeof field.max === 'number' && num > field.max ) {
				setFieldError( item, 'Please enter ' + field.max + ' or less.' );
				return { valid: false };
			}
			return { valid: true, value: num };
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

	/**
	 * Render a successful result.
	 */
	function showResult( resultEl, result ) {
		resultEl.textContent = '';

		var value = document.createElement( 'div' );
		value.className = 'pm-calc__result-value';
		value.textContent = result.headline;
		resultEl.appendChild( value );

		if ( result.summary ) {
			var summary = document.createElement( 'div' );
			summary.className = 'pm-calc__result-summary';
			summary.textContent = result.summary;
			resultEl.appendChild( summary );
		}

		resultEl.classList.add( 'pm-calc__result--filled' );
		resultEl.classList.remove( 'pm-calc__result--error' );
	}

	/**
	 * Render an error message in the result region.
	 */
	function showResultError( resultEl, message ) {
		resultEl.textContent = '';
		var value = document.createElement( 'div' );
		value.className = 'pm-calc__result-value';
		value.textContent = message;
		resultEl.appendChild( value );
		resultEl.classList.add( 'pm-calc__result--filled', 'pm-calc__result--error' );
	}

	/**
	 * Render one calculator: inject its fields and wire up Calculate / Reset.
	 */
	function renderCalculator( root, config ) {
		var form = root.querySelector( '.pm-calc__form' );
		var fieldsHost = root.querySelector( '[data-pm-fields]' );
		var resultEl = root.querySelector( '[data-pm-result]' );

		if ( ! form || ! fieldsHost || ! resultEl ) {
			return;
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
