<?php
/**
 * Inline SVG icon helper.
 *
 * The Lucide icons used across the theme, collected in one place so templates
 * stay readable and no icon is ever duplicated. The SVGs are inlined — never
 * loaded from a CDN (CLAUDE.md §10).
 *
 * @package thepawmanual
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Output an inline SVG icon.
 *
 * @param string $name  Icon name — a key of the $icons map below.
 * @param string $class CSS classes applied to the <svg> element.
 */
function tpm_icon( $name, $class = 'h-5 w-5' ) {
	$icons = array(
		'arrow-right'        => '<path d="M5 12h14"/><path d="m12 5 7 7-7 7"/>',
		'search'             => '<path d="m21 21-4.34-4.34"/><circle cx="11" cy="11" r="8"/>',
		'sliders-horizontal' => '<path d="M10 5H3"/><path d="M12 19H3"/><path d="M14 3v4"/><path d="M16 17v4"/><path d="M21 12h-9"/><path d="M21 19h-5"/><path d="M21 5h-7"/><path d="M8 10v4"/><path d="M8 12H3"/>',
		'chevron-down'       => '<path d="m6 9 6 6 6-6"/>',
		'calculator'         => '<rect width="16" height="20" x="4" y="2" rx="2"/><line x1="8" x2="16" y1="6" y2="6"/><line x1="16" x2="16" y1="14" y2="18"/><path d="M16 10h.01"/><path d="M12 10h.01"/><path d="M8 10h.01"/><path d="M12 14h.01"/><path d="M8 14h.01"/><path d="M12 18h.01"/><path d="M8 18h.01"/>',
		'paw'                => '<ellipse cx="12" cy="15.5" rx="4.2" ry="3.4"/><ellipse cx="6" cy="10.5" rx="1.9" ry="2.2"/><ellipse cx="10.3" cy="7" rx="2" ry="2.4"/><ellipse cx="13.7" cy="7" rx="2" ry="2.4"/><ellipse cx="18" cy="10.5" rx="1.9" ry="2.2"/>',
		'gauge'              => '<path d="m12 14 4-4"/><path d="M3.34 19a10 10 0 1 1 17.32 0"/>',
		'bowl'               => '<path d="M12 21a9 9 0 0 0 9-9H3a9 9 0 0 0 9 9Z"/><path d="M12 3v9"/>',
		'calendar'           => '<rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/>',
		'droplet'            => '<path d="M12 22a7 7 0 0 0 7-7c0-2-1-3.9-3-5.5s-3.5-4-4-6.5c-.5 2.5-2 4.9-4 6.5C6 11.1 5 13 5 15a7 7 0 0 0 7 7z"/>',
	);

	if ( ! isset( $icons[ $name ] ) ) {
		return;
	}

	printf(
		'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="%s" aria-hidden="true" focusable="false">%s</svg>',
		esc_attr( $class ),
		$icons[ $name ] // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static, trusted SVG markup.
	);
}
