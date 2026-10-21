<?php
/**
 * ThePawManual theme bootstrap.
 *
 * This file is intentionally thin: each concern lives in its own file under
 * inc/ and is simply loaded here (see CLAUDE.md §9).
 *
 * @package thepawmanual
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Prevent direct access.
}

/** Absolute path to the theme directory, with a trailing slash. */
define( 'TPM_THEME_DIR', trailingslashit( get_template_directory() ) );

/** Theme version — used to cache-bust enqueued assets. */
define( 'TPM_THEME_VERSION', '0.1.0' );

require_once TPM_THEME_DIR . 'inc/setup.php';
require_once TPM_THEME_DIR . 'inc/enqueue.php';
require_once TPM_THEME_DIR . 'inc/nav-menus.php';
require_once TPM_THEME_DIR . 'inc/icons.php';
require_once TPM_THEME_DIR . 'inc/tools-data.php';
require_once TPM_THEME_DIR . 'inc/breadcrumbs.php';
require_once TPM_THEME_DIR . 'inc/article-meta.php';
require_once TPM_THEME_DIR . 'inc/search.php';
// Listed first as documentation of the dependency, not because the order
// is actually load-bearing: content-nav.php, internal-links.php and
// content-blocks.php all call tpm_extract_script_blocks()/
// tpm_restore_script_blocks(), but only from inside their own function
// bodies, invoked at render time - by then every require_once below has
// already run, so PHP would resolve the call correctly regardless of which
// of these files loaded first. content-blocks.php's inline-tool-CTA mutator
// reaches into tools-data.php the same way, at the same render-time remove
// (function_exists()-guarded there, since that require_once happens above,
// before this block, and the guard costs nothing either way).
require_once TPM_THEME_DIR . 'inc/script-protection.php';
require_once TPM_THEME_DIR . 'inc/content-nav.php';
require_once TPM_THEME_DIR . 'inc/internal-links.php';
require_once TPM_THEME_DIR . 'inc/content-blocks.php';
require_once TPM_THEME_DIR . 'inc/page-image.php';
