<?php
/**
 * The site footer: link columns, copyright, and the closing document tags.
 *
 * @package thepawmanual
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<footer class="mt-16 border-t border-neutral-200 sm:mt-24">
	<div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
		<div class="grid animate-on-scroll grid-cols-1 gap-8 [animation:fadeSlideIn_1s_ease-out_0.1s_both] sm:grid-cols-2 lg:grid-cols-4">

			<?php
			/*
			 * Every footer link goes through tpm_get_published_page_url() and is
			 * simply omitted until its target page is actually published — the
			 * same rule the rest of the theme follows (CLAUDE.md §13).
			 *
			 * This is not hypothetical here. The five trust pages carrying
			 * [PLACEHOLDER] copy (about, contact, terms, privacy-policy,
			 * affiliate-disclosure) are deliberately held back as Drafts at
			 * wave-1 launch until a human fills in real names, a real address
			 * and a real legal entity. Hardcoding home_url() would have put
			 * five 404s in the footer of every page on the site for as long as
			 * that takes. A column with nothing published yet renders nothing.
			 *
			 * No email address is hardcoded either — the previous
			 * "hello@thepawmanual.com" mailto was invented, and the real
			 * address is still a [PLACEHOLDER] in content/contact.md.
			 */
			$tpm_footer_columns = array(
				array(
					'heading' => get_bloginfo( 'name' ),
					'intro'   => __( 'Vet-informed dog and cat care guides and free calculators.', 'thepawmanual' ),
					'links'   => array(
						'contact' => __( 'Contact', 'thepawmanual' ),
					),
				),
				// Aura's "Social" column is dropped rather than linking to accounts that don't exist (CLAUDE.md §10).
				array(
					'heading' => __( 'ThePawManual', 'thepawmanual' ),
					'links'   => array(
						'about'             => __( 'About Us', 'thepawmanual' ),
						'editorial-process' => __( 'Editorial Process', 'thepawmanual' ),
					),
				),
				array(
					'heading' => __( 'Terms & Policy', 'thepawmanual' ),
					'links'   => array(
						'terms'                => __( 'Terms of Use', 'thepawmanual' ),
						'privacy-policy'       => __( 'Privacy Policy', 'thepawmanual' ),
						'affiliate-disclosure' => __( 'Affiliate Disclosure', 'thepawmanual' ),
					),
				),
			);

			foreach ( $tpm_footer_columns as $tpm_column ) :
				$tpm_live_links = array();
				foreach ( $tpm_column['links'] as $tpm_path => $tpm_label ) {
					$tpm_url = tpm_get_published_page_url( $tpm_path );
					if ( $tpm_url ) {
						$tpm_live_links[ $tpm_url ] = $tpm_label;
					}
				}

				// An intro paragraph is the column's own copy, so it stands even with no links yet.
				if ( ! $tpm_live_links && empty( $tpm_column['intro'] ) ) {
					continue;
				}
				?>
				<div>
					<p class="font-geist text-sm font-semibold tracking-tight"><?php echo esc_html( $tpm_column['heading'] ); ?></p>
					<?php if ( ! empty( $tpm_column['intro'] ) ) : ?>
						<p class="mt-3 font-geist text-sm text-neutral-600"><?php echo esc_html( $tpm_column['intro'] ); ?></p>
					<?php endif; ?>
					<?php if ( $tpm_live_links ) : ?>
						<ul class="mt-3 space-y-2 text-sm text-neutral-600">
							<?php foreach ( $tpm_live_links as $tpm_url => $tpm_label ) : ?>
								<li class="font-geist">
									<a href="<?php echo esc_url( $tpm_url ); ?>" class="transition-colors hover:text-brand-700"><?php echo esc_html( $tpm_label ); ?></a>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</div>
				<?php
			endforeach;
			?>

			<?php
			/*
			 * Column 4: Pet Emergency links. The two vet-signs pages ship in
			 * wave 3 (dogs) and wave 6 (cats) of the content rollout — weeks
			 * to months after wave 1 launch (content/SEO-STRATEGY.md §4).
			 * This footer renders on every page from day one, so it cannot
			 * hardcode those URLs without 404ing sitewide until each wave
			 * ships. tpm_get_published_page_url() only returns a URL once
			 * the target Page exists and is published; the whole column
			 * disappears until at least one link is live.
			 */
			$tpm_dog_vet_signs_url = tpm_get_published_page_url( 'dogs/health/signs-your-dog-needs-a-vet' );
			$tpm_cat_vet_signs_url = tpm_get_published_page_url( 'cats/health/signs-your-cat-needs-a-vet' );
			?>
			<?php
			/*
			 * `text-caution-700`, not `text-brand-600` (deploy-gate finding,
			 * 2026-09-14). This heading is not a navigational label like the
			 * three columns beside it — it carries the exact "Pet emergency?"
			 * wording emergency-callout.php uses for the SAME two vet-signs
			 * pages, rendered sitewide (including pages with no inline
			 * callout at all, e.g. the homepage). That makes it the site's
			 * second emergency affordance, not a rebrand of the first, so it
			 * should read with the same weight `caution` gives the callout,
			 * not the ordinary brand colour every other footer heading gets.
			 * `caution-700` (not `-900`): this is a small `text-sm` label,
			 * not the callout's large bold heading, and 8.02:1 against white
			 * already clears AA with room to spare.
			 *
			 * tailwind.config.js's `caution` comment still says
			 * emergency-callout.php is the only place this scale is used —
			 * that statement is now one file out of date. This file does not
			 * own tailwind.config.js, so widening it is a config-owner edit,
			 * escalated rather than made here (see this task's report).
			 */
			?>
			<?php if ( $tpm_dog_vet_signs_url || $tpm_cat_vet_signs_url ) : ?>
				<div>
					<p class="font-geist text-sm font-semibold tracking-tight text-caution-700"><?php esc_html_e( 'Pet Emergency?', 'thepawmanual' ); ?></p>
					<ul class="mt-3 space-y-2 text-sm text-neutral-600">
						<?php if ( $tpm_dog_vet_signs_url ) : ?>
							<li class="font-geist">
								<a href="<?php echo esc_url( $tpm_dog_vet_signs_url ); ?>" class="transition-colors hover:text-brand-700"><?php esc_html_e( 'Signs your dog needs a vet', 'thepawmanual' ); ?></a>
							</li>
						<?php endif; ?>
						<?php if ( $tpm_cat_vet_signs_url ) : ?>
							<li class="font-geist">
								<a href="<?php echo esc_url( $tpm_cat_vet_signs_url ); ?>" class="transition-colors hover:text-brand-700"><?php esc_html_e( 'Signs your cat needs a vet', 'thepawmanual' ); ?></a>
							</li>
						<?php endif; ?>
					</ul>
				</div>
			<?php endif; ?>

		</div>

		<?php
		/*
		 * A single, correct copyright line. The Aura export's duplicated second
		 * copyright (which read "Pawsome") and its invalid repeated id="y" have
		 * been dropped; the year is rendered in PHP (CLAUDE.md §10).
		 */
		?>
		<div class="mt-12 border-t border-neutral-200 pt-8 text-center">
			<p class="font-geist text-xs text-neutral-500">
				&copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>
			</p>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
