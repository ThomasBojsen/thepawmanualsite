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
			 * Column 1: brand and contact. No hardcoded email address here —
			 * the previous "hello@thepawmanual.com" mailto was invented; the
			 * real contact address is still a [PLACEHOLDER] in
			 * content/contact.md, so this links to the /contact/ page
			 * instead (SEO-STRATEGY.md §1 items 0.2 and 0.8).
			 */
			?>
			<div>
				<p class="font-geist text-sm font-semibold tracking-tight"><?php bloginfo( 'name' ); ?></p>
				<p class="mt-3 font-geist text-sm text-neutral-600">
					<?php esc_html_e( 'Vet-informed dog and cat care guides and free calculators.', 'thepawmanual' ); ?>
				</p>
				<ul class="mt-3 space-y-2 text-sm text-neutral-600">
					<li class="font-geist">
						<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="transition-colors hover:text-neutral-900"><?php esc_html_e( 'Contact', 'thepawmanual' ); ?></a>
					</li>
				</ul>
			</div>

			<?php // Column 2: about the site. Aura's social column is dropped rather than linking to accounts that don't exist (CLAUDE.md §10). ?>
			<div>
				<p class="font-geist text-sm font-semibold tracking-tight"><?php esc_html_e( 'ThePawManual', 'thepawmanual' ); ?></p>
				<ul class="mt-3 space-y-2 text-sm text-neutral-600">
					<li class="font-geist">
						<a href="<?php echo esc_url( home_url( '/about/' ) ); ?>" class="transition-colors hover:text-neutral-900"><?php esc_html_e( 'About Us', 'thepawmanual' ); ?></a>
					</li>
					<li class="font-geist">
						<a href="<?php echo esc_url( home_url( '/editorial-process/' ) ); ?>" class="transition-colors hover:text-neutral-900"><?php esc_html_e( 'Editorial Process', 'thepawmanual' ); ?></a>
					</li>
				</ul>
			</div>

			<?php // Column 3: terms and policy. ?>
			<div>
				<p class="font-geist text-sm font-semibold tracking-tight"><?php esc_html_e( 'Terms & Policy', 'thepawmanual' ); ?></p>
				<ul class="mt-3 space-y-2 text-sm text-neutral-600">
					<li class="font-geist">
						<a href="<?php echo esc_url( home_url( '/terms/' ) ); ?>" class="transition-colors hover:text-neutral-900"><?php esc_html_e( 'Terms of Use', 'thepawmanual' ); ?></a>
					</li>
					<li class="font-geist">
						<a href="<?php echo esc_url( home_url( '/privacy-policy/' ) ); ?>" class="transition-colors hover:text-neutral-900"><?php esc_html_e( 'Privacy Policy', 'thepawmanual' ); ?></a>
					</li>
					<li class="font-geist">
						<a href="<?php echo esc_url( home_url( '/affiliate-disclosure/' ) ); ?>" class="transition-colors hover:text-neutral-900"><?php esc_html_e( 'Affiliate Disclosure', 'thepawmanual' ); ?></a>
					</li>
				</ul>
			</div>

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
			<?php if ( $tpm_dog_vet_signs_url || $tpm_cat_vet_signs_url ) : ?>
				<div>
					<p class="font-geist text-sm font-semibold tracking-tight text-amber-600"><?php esc_html_e( 'Pet Emergency?', 'thepawmanual' ); ?></p>
					<ul class="mt-3 space-y-2 text-sm text-neutral-600">
						<?php if ( $tpm_dog_vet_signs_url ) : ?>
							<li class="font-geist">
								<a href="<?php echo esc_url( $tpm_dog_vet_signs_url ); ?>" class="transition-colors hover:text-neutral-900"><?php esc_html_e( 'Signs your dog needs a vet', 'thepawmanual' ); ?></a>
							</li>
						<?php endif; ?>
						<?php if ( $tpm_cat_vet_signs_url ) : ?>
							<li class="font-geist">
								<a href="<?php echo esc_url( $tpm_cat_vet_signs_url ); ?>" class="transition-colors hover:text-neutral-900"><?php esc_html_e( 'Signs your cat needs a vet', 'thepawmanual' ); ?></a>
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
