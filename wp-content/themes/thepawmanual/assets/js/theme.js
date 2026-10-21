/**
 * ThePawManual — theme front-end behaviour.
 *
 * Progressive enhancements only, every one of them: the mobile-navigation
 * toggle, the scroll-reveal effect that plays Aura's fadeSlideIn animation
 * as sections enter the viewport, forcing the in-article table of contents
 * open on desktop, the header search panel, the TOC's scroll-spy highlight,
 * and opening a deep-linked FAQ answer. The site is fully usable — search
 * included, via searchform.php's plain `method="get"` form and header.php's
 * <noscript> fallback — with this script disabled.
 */
( function () {
	'use strict';

	document.addEventListener( 'DOMContentLoaded', function () {
		initMobileNav();
		initScrollReveal();
		initTocDesktopOpen();
		initSearchPanel();
		initTocScrollSpy();
		initFaqDeepLink();
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

		initMobileSubmenus( panel );
	}

	/**
	 * Fold the mobile menu's sub-menus in and out.
	 *
	 * inc/nav-menus.php renders a chevron button next to every parent item;
	 * src/tailwind.css keeps the sub-menu hidden until its <li> carries
	 * `.is-open`. The parent link keeps working as a link to its hub page, so
	 * the button is the only thing that opens the list.
	 */
	function initMobileSubmenus( panel ) {
		var buttons = panel.querySelectorAll( '[data-tpm-submenu-toggle]' );

		buttons.forEach( function ( button, index ) {
			var item = button.parentElement;
			var submenu = item.querySelector( ':scope > .sub-menu' );

			if ( ! submenu ) {
				button.hidden = true;
				return;
			}

			if ( ! submenu.id ) {
				submenu.id = 'tpm-mobile-submenu-' + index;
			}
			button.setAttribute( 'aria-controls', submenu.id );

			button.addEventListener( 'click', function () {
				var isOpen = item.classList.toggle( 'is-open' );
				button.setAttribute( 'aria-expanded', String( isOpen ) );
			} );
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

	/**
	 * Wire the header's search button (header.php, `[data-tpm-search-toggle]`)
	 * to open/close `#tpm-search-panel`.
	 *
	 * Toggles the `hidden` PROPERTY, never `style.display` — header.php ships
	 * the panel with the `hidden` ATTRIBUTE (the SHARED CLASS/ID CONTRACT
	 * requires #tpm-search-panel to start hidden), and the `hidden` property
	 * is the live reflection of that same attribute, so this can never drift
	 * out of sync with what the server actually rendered the way toggling a
	 * separate CSS class could.
	 *
	 * No-ops if either element is missing rather than assuming both always
	 * exist — matches every other init*() in this file, and means a future
	 * header.php edit that drops one half can't throw here.
	 */
	function initSearchPanel() {
		var toggle = document.querySelector( '[data-tpm-search-toggle]' );
		var panel = document.getElementById( 'tpm-search-panel' );

		if ( ! toggle || ! panel ) {
			return;
		}

		var field = panel.querySelector( 'input[type="search"]' );

		function openPanel() {
			panel.hidden = false;
			toggle.setAttribute( 'aria-expanded', 'true' );

			// The whole point of a click-to-open search field is to type into
			// it immediately — without this a keyboard/screen-reader user gets
			// an opened panel but focus left behind on the button, one extra
			// Tab away from where they obviously want to be.
			if ( field ) {
				field.focus();
			}
		}

		// `returnFocus` defaults true (Escape and the toggle re-closing its
		// own panel both want it), but an outside click passes it
		// conditionally — see that listener below. `click` fires after the
		// browser has already moved focus to whatever the user clicked, so
		// unconditionally re-focusing the toggle here would rip focus back
		// off an element the user deliberately clicked into.
		function closePanel( returnFocus ) {
			if ( panel.hidden ) {
				return;
			}
			panel.hidden = true;
			toggle.setAttribute( 'aria-expanded', 'false' );

			// Returning focus to the trigger — on Escape, on the toggle button
			// re-closing its own panel (a harmless re-focus of the element
			// that's already focused there), or on an outside click that would
			// otherwise strand focus on a now-hidden field — is what keeps
			// this an accessible disclosure pattern rather than one that
			// merely LOOKS like it.
			if ( false !== returnFocus ) {
				toggle.focus();
			}
		}

		toggle.addEventListener( 'click', function () {
			if ( panel.hidden ) {
				openPanel();
			} else {
				closePanel( true );
			}
		} );

		document.addEventListener( 'keydown', function ( event ) {
			if ( 'Escape' === event.key ) {
				closePanel( true );
			}
		} );

		// A click anywhere that is neither inside the panel nor the button
		// that opens it counts as "outside" and closes it — the standard
		// expectation for any dropdown/disclosure control. Only return focus
		// to the toggle if it would otherwise be stranded on the now-hidden
		// panel: a visitor who clicked elsewhere on the page (a calculator
		// field, the "refine your search" input on the results page, a FAQ
		// <summary>, a TOC link) has already moved focus exactly where they
		// meant to, and snapping it back to the header button would throw
		// their next keystroke away.
		document.addEventListener( 'click', function ( event ) {
			if ( panel.hidden ) {
				return;
			}
			if ( panel.contains( event.target ) || toggle.contains( event.target ) ) {
				return;
			}
			closePanel( panel.contains( document.activeElement ) );
		} );
	}

	/**
	 * Highlight the table-of-contents link for whichever H2 section is
	 * currently under the reading position (template-parts/content/toc.php,
	 * page.php's TOC branch).
	 *
	 * Targets are derived from the TOC's own links (`a.tpm-toc__link`,
	 * SHARED CLASS/ID CONTRACT) rather than a bare `querySelectorAll('h2[id]')`
	 * over the whole article: every id this walks is one the TOC itself
	 * already linked to, so there's no way to highlight a link for a heading
	 * the TOC doesn't list, or observe a heading nothing points back to.
	 *
	 * No motion of its own — `setActive()` only ever toggles a class name.
	 * Any transition on `.is-active` is src/tailwind.css's decision, already
	 * covered there by the same `prefers-reduced-motion` override the
	 * fadeSlideIn scroll-reveal above answers to; this function has nothing
	 * further to gate.
	 */
	function initTocScrollSpy() {
		var toc = document.querySelector( '.tpm-toc' );
		if ( ! toc || ! ( 'IntersectionObserver' in window ) ) {
			return;
		}

		var links = toc.querySelectorAll( 'a.tpm-toc__link' );
		if ( ! links.length ) {
			return;
		}

		var linkById = {};
		var targets = [];

		links.forEach( function ( link ) {
			var href = link.getAttribute( 'href' ) || '';
			if ( '#' !== href.charAt( 0 ) ) {
				return;
			}

			var id = href.slice( 1 );
			var heading = id ? document.getElementById( id ) : null;
			if ( ! heading ) {
				return;
			}

			linkById[ id ] = link;
			targets.push( heading );
		} );

		if ( ! targets.length ) {
			return;
		}

		var activeId = null;

		function setActive( id ) {
			if ( id === activeId ) {
				return;
			}
			if ( activeId && linkById[ activeId ] ) {
				linkById[ activeId ].classList.remove( 'is-active' );
			}
			if ( id && linkById[ id ] ) {
				linkById[ id ].classList.add( 'is-active' );
			}
			activeId = id;
		}

		var observer = new IntersectionObserver(
			function ( entries ) {
				// More than one heading can intersect the band at once (a
				// short section). The one nearest the top of the viewport is
				// what a reader would call "current" — not, say, whichever
				// IntersectionObserver entry happened to fire last.
				var visible = entries.filter( function ( entry ) {
					return entry.isIntersecting;
				} );

				if ( ! visible.length ) {
					return;
				}

				visible.sort( function ( a, b ) {
					return a.boundingClientRect.top - b.boundingClientRect.top;
				} );

				setActive( visible[ 0 ].target.id );
			},
			// A band near the top of the viewport, not the whole viewport: a
			// heading only counts as "current" once it has scrolled up near
			// the actual reading position, matching how a sticky TOC reads
			// against the article running alongside it.
			{ rootMargin: '-15% 0px -70% 0px', threshold: 0 }
		);

		targets.forEach( function ( heading ) {
			observer.observe( heading );
		} );
	}

	/**
	 * Open a deep-linked FAQ answer (template-parts/content/*, `.tpm-faq`,
	 * SHARED CLASS/ID CONTRACT) and scroll it into view.
	 *
	 * `<details>` closed by default is exactly right for a visitor reading
	 * top to bottom, but it is exactly wrong for one arriving from a link
	 * straight to `#some-question` — the browser's native same-page-anchor
	 * scroll still fires, but scrolls to a closed box with the answer text
	 * still hidden inside it. This runs once at load, matching how a visitor
	 * actually reaches an FAQ deep link (an external link or a search result
	 * carrying the hash already, not an in-page click this script would also
	 * need to watch `hashchange` for).
	 *
	 * `scrollIntoView()` is called with no `behavior` — leaving it at the
	 * spec default of 'auto' — rather than hardcoding 'smooth', so it defers
	 * to the `scroll-behavior` CSS property instead of overriding it: <html>
	 * carries Tailwind's `scroll-smooth` class, but src/tailwind.css's base
	 * layer already forces `scroll-behavior: auto` under
	 * `prefers-reduced-motion: reduce`. An explicit 'smooth' argument here
	 * would bypass that CSS rule entirely (per spec, an explicit smooth
	 * request does not consult scroll-behavior), reintroducing the exact
	 * animated motion that override exists to remove.
	 */
	function initFaqDeepLink() {
		if ( ! location.hash || location.hash.length < 2 ) {
			return;
		}

		var target;
		try {
			target = document.querySelector( location.hash );
		} catch ( error ) {
			// A hash containing characters invalid in a CSS selector — rare,
			// but not impossible in hand-authored content — throws here
			// rather than simply matching nothing. Same as "no match".
			return;
		}

		if ( ! target ) {
			return;
		}

		var details = target.closest( 'details.tpm-faq__item' );
		if ( ! details ) {
			return;
		}

		details.open = true;
		details.scrollIntoView( { block: 'start' } );
	}
} )();
