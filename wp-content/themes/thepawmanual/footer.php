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

			<?php // Column 1: brand and contact. ?>
			<div>
				<p class="font-geist text-sm font-semibold tracking-tight"><?php bloginfo( 'name' ); ?></p>
				<ul class="mt-3 space-y-2 text-sm text-neutral-600">
					<li class="font-geist">
						<a href="mailto:hello@thepawmanual.com" class="transition-colors hover:text-neutral-900">hello@thepawmanual.com</a>
					</li>
					<li class="font-geist">About Us</li>
					<li class="font-geist">Contact</li>
				</ul>
			</div>

			<?php // Column 2: social. ?>
			<div>
				<p class="font-geist text-sm font-semibold tracking-tight"><?php esc_html_e( 'Social', 'thepawmanual' ); ?></p>
				<ul class="mt-3 space-y-2 text-sm text-neutral-600">
					<li class="font-geist">Instagram</li>
					<li class="font-geist">Twitter</li>
					<li class="font-geist">TikTok</li>
				</ul>
			</div>

			<?php // Column 3: company. ?>
			<div>
				<p class="font-geist text-sm font-semibold tracking-tight"><?php esc_html_e( 'Company', 'thepawmanual' ); ?></p>
				<ul class="mt-3 space-y-2 text-sm text-neutral-600">
					<li class="font-geist">Our Mission</li>
					<li class="font-geist">Veterinary Reviewers</li>
					<li class="font-geist">Editorial Guidelines</li>
				</ul>
			</div>

			<?php // Column 4: terms and policy. ?>
			<div>
				<p class="font-geist text-sm font-semibold tracking-tight"><?php esc_html_e( 'Terms & Policy', 'thepawmanual' ); ?></p>
				<ul class="mt-3 space-y-2 text-sm text-neutral-600">
					<li class="font-geist">Terms of Use</li>
					<li class="font-geist">Privacy Policy</li>
					<li class="font-geist">Disclaimer</li>
				</ul>
			</div>

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
