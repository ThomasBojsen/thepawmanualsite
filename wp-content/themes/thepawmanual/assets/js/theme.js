/**
 * ThePawManual — theme front-end behaviour.
 *
 * Three small progressive enhancements: the mobile-navigation toggle, the
 * scroll-reveal effect that plays Aura's fadeSlideIn animation as sections
 * enter the viewport, and forcing the in-article table of contents open on
 * desktop. The site is fully usable with this script disabled.
 */
( function () {
	'use strict';

	document.addEventListener( 'DOMContentLoaded', function () {
		initMobileNav();
		initScrollReveal();
		initTocDesktopOpen();
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

	/**
	 * Force the in-article table of contents (template-parts/content/toc.php)
	 * open on desktop.
	 *
	 * The <details> ships with no `open` attribute, so it defaults to closed
	 * — exactly right for the stacked-above-the-article mobile layout — but
	 * desktop's sticky-sidebar TOC (page.php's `lg:` grid) is meant to read
	 * as permanently open. src/tailwind.css's `@screen lg` rules already get
	 * the VISUAL result right with no JS at all (including the
	 * `::details-content` override Chrome's newer <details> rendering
	 * needs — see that file), but CSS can't flip the actual `open`
	 * attribute, so the `aria-expanded` a screen reader reads off it would
	 * still say "closed" on desktop without this. See toc.php's docblock
	 * for the full "why not CSS alone" writeup.
	 *
	 * No-ops immediately on the pages — most of them — that have no TOC:
	 * page.php only renders one on articles with 3+ H2s, but this script
	 * loads unconditionally on every front-end page.
	 */
	function initTocDesktopOpen() {
		var details = document.querySelector( '.tpm-toc details' );
		if ( ! details ) {
			return;
		}

		if ( isLgBreakpoint() ) {
			details.open = true;
		}
	}

	/**
	 * Whether the viewport is at/above Tailwind's `lg` breakpoint.
	 *
	 * Reads the `--tpm-is-lg` custom property src/tailwind.css sets via
	 * `@screen lg`, rather than hardcoding `1024px` a second time here —
	 * tailwind.config.js (Tailwind's `lg` screen) stays the one place that
	 * number is defined. A browser too old for CSS custom properties simply
	 * reads an empty string here and this returns false, which just means
	 * initTocDesktopOpen() leaves the TOC as the CSS already rendered it —
	 * never a broken or stuck state.
	 */
	function isLgBreakpoint() {
		var value = getComputedStyle( document.documentElement ).getPropertyValue( '--tpm-is-lg' );
		return '1' === value.trim();
	}
} )();
