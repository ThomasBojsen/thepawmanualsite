<?php
/**
 * The canonical search form — what get_search_form() returns everywhere it
 * is called: the header's slide-down panel (header.php), its <noscript>
 * fallback, and the "refine your search" / empty-state fields on search.php.
 *
 * A plain `method="get"` form to the site root with a single `s` field.
 * Nothing here depends on JavaScript — assets/js/theme.js only ever toggles
 * the PANEL this form happens to sit inside (initSearchPanel()), never the
 * form's own submit behaviour, so search keeps working with scripting off.
 *
 * Duplicate ids are an accessibility bug, and this template is genuinely
 * rendered more than once on the same page in practice: the header panel is
 * in the DOM on every single page — search.php included, since get_header()
 * always runs it — and search.php also calls get_search_form() itself for
 * its own "refine your search" / empty-state field. Rendering the canonical
 * #tpm-search-field id twice on that one page is exactly the bug the SHARED
 * CLASS/ID CONTRACT warns about. get_search_form( $args ) requires this file
 * straight into its own function body, so the merged $args array it built is
 * already in scope here as $args — no `search_form_args` filter needed to
 * reach it. An 'id_suffix' entry in that array appends "-$id_suffix" to the
 * field's id/for pair; leave it unset (the header panel's call does) for the
 * bare #tpm-search-field the contract fixes, and pass one everywhere else
 * the form repeats on a page that already has that canonical field.
 *
 * The field's VISUAL styling — including the focus ring — is keyed on the
 * `.tpm-search-field` CLASS (src/tailwind.css), never on the id above. An id
 * selector only ever matches the one unsuffixed instance; a previous version
 * of this file relied on `#tpm-search-field` for the focus ring and every
 * suffixed instance silently fell back to a near-invisible default (deploy-
 * gate finding, 2026-09-14 — WCAG 2.4.7 / 1.4.11). The class is on the input
 * unconditionally, so every instance this file ever renders gets the same
 * 5:1+ ring regardless of which id it was given.
 *
 * get_search_query() defaults to pre-escaped output ($escaped = true, via
 * esc_attr() inside WordPress core) — passing that through esc_attr()/
 * esc_html() again here would double-encode any "&" a visitor actually
 * searched for into "&amp;amp;". Calling get_search_query( false ) for the
 * raw value and escaping it exactly once, here, keeps this file consistent
 * with the rest of the theme's "escape at the point of output" rule instead
 * of leaning on a WordPress default a future edit could easily reintroduce
 * a double-escape around.
 *
 * @package thepawmanual
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$id_suffix = isset( $args['id_suffix'] ) ? sanitize_html_class( (string) $args['id_suffix'] ) : '';
$field_id  = '' !== $id_suffix ? 'tpm-search-field-' . $id_suffix : 'tpm-search-field';
?>
<form role="search" method="get" class="tpm-search-form flex items-center gap-2" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label for="<?php echo esc_attr( $field_id ); ?>" class="sr-only">
		<?php esc_html_e( 'Search ThePawManual', 'thepawmanual' ); ?>
	</label>
	<input
		type="search"
		id="<?php echo esc_attr( $field_id ); ?>"
		name="s"
		value="<?php echo esc_attr( get_search_query( false ) ); ?>"
		placeholder="<?php esc_attr_e( 'Search guides, tools, breeds...', 'thepawmanual' ); ?>"
		class="tpm-search-field min-w-0 flex-1"
	/>
	<button type="submit" class="inline-flex shrink-0 items-center justify-center rounded-full bg-neutral-900 p-2.5 text-white transition hover:bg-neutral-800">
		<span class="sr-only"><?php esc_html_e( 'Submit search', 'thepawmanual' ); ?></span>
		<?php tpm_icon( 'search', 'h-4 w-4' ); ?>
	</button>
</form>
