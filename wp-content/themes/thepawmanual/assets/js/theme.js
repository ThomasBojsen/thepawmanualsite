/**
 * ThePawManual — theme front-end behaviour.
 *
 * Two small progressive enhancements: the mobile-navigation toggle, and the
 * scroll-reveal effect that plays Aura's fadeSlideIn animation as sections
 * enter the viewport. The site is fully usable with this script disabled.
 */
( function () {
	'use strict';

	document.addEventListener( 'DOMContentLoaded', function () {
		initMobileNav();
		initScrollReveal();
	} );

	/**
	 * Wire the header's mobile-menu button to show/hide the navigation panel.
	 */
	function initMobileNav() {
		var toggle = document.querySelector( '[data-tpm-nav-toggle]' );
		var panel = document.getElementById( 'tpm-mobile-nav' );

		if ( ! toggle || ! panel ) {
			return;
		}

		toggle.addEventListener( 'click', function () {
			var isOpen = panel.classList.toggle( 'hidden' ) === false;
			toggle.setAttribute( 'aria-expanded', String( isOpen ) );
		} );
	}

	/**
	 * Reveal elements marked `.animate-on-scroll` as they scroll into view.
	 */
	function initScrollReveal() {
		var elements = document.querySelectorAll( '.animate-on-scroll' );

		if ( ! elements.length ) {
			return;
		}

		// Without IntersectionObserver (very old browsers), just show them all.
		if ( ! ( 'IntersectionObserver' in window ) ) {
			elements.forEach( function ( el ) {
				el.classList.add( 'is-visible' );
			} );
			return;
		}

		var observer = new IntersectionObserver(
			function ( entries ) {
				entries.forEach( function ( entry ) {
					if ( entry.isIntersecting ) {
						entry.target.classList.add( 'is-visible' );
						observer.unobserve( entry.target );
					}
				} );
			},
			{ threshold: 0.15, rootMargin: '0px 0px -10% 0px' }
		);

		elements.forEach( function ( el ) {
			observer.observe( el );
		} );
	}
} )();
