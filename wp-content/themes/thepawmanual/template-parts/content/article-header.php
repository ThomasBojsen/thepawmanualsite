<?php
/**
 * Article header: eyebrow chip + date + read time, the (split) H1, the deck
 * line, the page image (when the Page has a Featured Image), and the
 * key-takeaway box.
 *
 * Replaces the plain "h1 + last-updated" block that used to sit directly in
 * page.php's <header> — tpm_breadcrumbs() stays a separate call in page.php,
 * ABOVE this template part, because the RankMath-deference check it does
 * belongs with the other "am I even showing a breadcrumb trail" logic, not
 * with article metadata.
 *
 * Task brief, requirement 5: several page titles are really an H1 plus a
 * subtitle clause typed as one string, which reads as H1+H2 to a visitor and
 * dilutes keyword focus for a crawler. tpm_get_title_parts() (inc/article-meta.php)
 * is what separates the two — checking the per-path override map first,
 * tpm_split_title() otherwise — and page.php resolves it once, per request,
 * and passes the result down as $args['title_parts']; this file is only
 * responsible for putting the two halves in the right elements — the short
 * half in the actual <h1>, the rest in a non-heading deck paragraph so it can
 * carry editorial voice without competing for the page's one heading slot.
 *
 * Expects $args['post_id'] (int) and $args['is_hub'] (bool).
 * Expects $args['title_parts'] — { 'h1' => string, 'deck' => string },
 * page.php's own tpm_get_title_parts( $post_id ) result. Falls back to
 * resolving it locally (same function, then tpm_split_title() if even that
 * isn't loaded) so this template still renders correctly if it's ever called
 * from somewhere that doesn't pass it.
 * Expects $args['takeaway'] — pre-filtered HTML from the content pipeline
 * (inc/content-blocks.php's tpm_prepare_article_content()), may be empty;
 * passed straight through to template-parts/content/key-takeaway.php.
 *
 * @package thepawmanual
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post_id = isset( $args['post_id'] ) ? (int) $args['post_id'] : 0;
if ( ! $post_id ) {
	return;
}

$is_hub   = ! empty( $args['is_hub'] );
$takeaway = isset( $args['takeaway'] ) ? (string) $args['takeaway'] : '';

$eyebrow = tpm_get_eyebrow( $post_id );

// Prefer the value page.php already resolved (it's computed once per request
// there, through the per-path override map) — falling back to resolving it
// here only if a future call site skips that step, and to the raw split as
// the last resort so this template never fatals on a missing article-meta.php.
$split = isset( $args['title_parts'] ) && is_array( $args['title_parts'] )
	? $args['title_parts']
	: ( function_exists( 'tpm_get_title_parts' )
		? tpm_get_title_parts( $post_id )
		: tpm_split_title( get_the_title( $post_id ) ) );

// Looked up here, not only in the partial, because it decides the layout:
// with an image, the title block and the image share a row from `lg` up.
$image = function_exists( 'tpm_get_page_image' ) ? tpm_get_page_image( $post_id ) : array();

// Read time only means something for an article a visitor is about to read
// top to bottom; a hub page is a menu of other pages, not itself a read.
$read_time = $is_hub ? 0 : tpm_get_read_time( $post_id );
?>
<?php
/*
 * With a page image, from `lg` up the title block and the image share a row,
 * the image in a 400px column on the right (TPM_PAGE_IMAGE_DESKTOP_WIDTH,
 * inc/page-image.php), so a square picture fills the header instead of
 * sitting alone beside empty space. Two variants:
 *
 * - With a deck, title and deck are centred against the image and the key
 *   takeaway follows below the row at full width.
 * - Without a deck, a lone title centred against a 400px picture leaves a
 *   large empty band above and below it, so the key takeaway moves up into
 *   the left column under the title and both columns start at the top.
 *
 * Only the grid placement differs: the HTML order is title, image, takeaway
 * in every case, so crawlers and screen readers read the page exactly the
 * same way and the answer still comes straight after the title. Below `lg`
 * the wrapper is a plain block and everything stacks as it always has.
 * Without an image the wrapper has no classes at all.
 */
$has_image       = ! empty( $image['html'] );
$takeaway_beside = $has_image && '' === $split['deck'];

if ( $takeaway_beside ) {
	$header_grid = 'lg:grid lg:grid-cols-[minmax(0,1fr)_400px] lg:grid-rows-[auto_1fr] lg:gap-x-10';
} elseif ( $has_image ) {
	$header_grid = 'lg:grid lg:grid-cols-[minmax(0,1fr)_400px] lg:items-center lg:gap-10';
} else {
	$header_grid = '';
}
?>
<div class="<?php echo esc_attr( $header_grid ); ?>">
<div class="<?php echo $takeaway_beside ? 'lg:col-start-1 lg:row-start-1' : ''; ?>">
<div class="tpm-article-meta mt-4 flex flex-wrap items-center gap-x-2 gap-y-1 font-geist text-sm text-neutral-500">
	<?php if ( $eyebrow ) : ?>
		<?php // .tpm-chip is the shared eyebrow/category pill (src/tailwind.css) — using it here instead of a hand-rolled utility string is what keeps it present in the compiled build at all (Tailwind's content scan only emits a @layer components rule when its class name literally appears in a scanned template). ?>
		<a
			href="<?php echo esc_url( $eyebrow['url'] ); ?>"
			class="tpm-chip"
		>
			<?php // Uppercase is this class's `uppercase` transform, not the stored string — see tpm_get_eyebrow()'s docblock for why that matters to a screen reader. ?>
			<?php echo esc_html( $eyebrow['label'] ); ?>
		</a>
		<span aria-hidden="true">&middot;</span>
	<?php endif; ?>

	<time datetime="<?php echo esc_attr( get_the_date( 'c', $post_id ) ); ?>">
		<?php echo esc_html( tpm_get_last_updated_label( $post_id ) ); ?>
	</time>

	<?php if ( ! $is_hub ) : ?>
		<span aria-hidden="true">&middot;</span>
		<span>
			<?php
			printf(
				/* translators: %d: estimated reading time, in whole minutes. */
				esc_html__( '%d min read', 'thepawmanual' ),
				(int) $read_time
			);
			?>
		</span>
	<?php endif; ?>
</div>

<h1 class="mt-3 font-playfair text-3xl font-medium tracking-tight sm:text-5xl">
	<?php echo esc_html( $split['h1'] ); ?>
</h1>

<?php if ( '' !== $split['deck'] ) : ?>
	<?php // A deck, never a heading — it carries the dropped subtitle clause's editorial voice without adding a second heading to the page's outline. ?>
	<p class="tpm-article-deck mt-2 max-w-3xl font-playfair text-2xl italic text-brand-600 sm:text-4xl">
		<?php echo esc_html( $split['deck'] ); ?>
	</p>
<?php endif; ?>

</div>

<?php // The page image comes here, after the title and deck and before the takeaway, so it is above the answer box on every page, hub or article, at every width. It prints nothing when the Page has no Featured Image. In the takeaway-beside layout it spans both rows of the right column, and lg:mt-4 lines its top up with the meta line's own mt-4. ?>
<div class="<?php echo $takeaway_beside ? 'lg:col-start-2 lg:row-span-2 lg:row-start-1 lg:mt-4' : ''; ?>">
	<?php
	get_template_part(
		'template-parts/content/page-image',
		null,
		array(
			'post_id' => $post_id,
			'image'   => $image,
		)
	);
	?>
</div>

<?php if ( $takeaway_beside ) : ?>
	<div class="lg:col-start-1 lg:row-start-2">
		<?php get_template_part( 'template-parts/content/key-takeaway', null, array( 'takeaway' => $takeaway ) ); ?>
	</div>
<?php endif; ?>
</div>

<?php if ( ! $takeaway_beside ) : ?>
	<?php get_template_part( 'template-parts/content/key-takeaway', null, array( 'takeaway' => $takeaway ) ); ?>
<?php endif; ?>
