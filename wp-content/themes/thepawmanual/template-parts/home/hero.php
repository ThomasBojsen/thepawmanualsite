<?php
/**
 * Homepage section: hero.
 *
 * The giant wordmark, a feature image with category pills, and the
 * "Featured Tool" card promoting the Dog Age Calculator.
 *
 * @package thepawmanual
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<section class="relative">
	<div class="mx-auto max-w-7xl px-4 pt-10 sm:px-6 sm:pt-14 lg:px-8">

		<?php
		/*
		 * The page's one and only H1 (SEO-STRATEGY §1 item 0.4 / CLAUDE.md
		 * "single H1" rule). This used to just repeat the site name at giant
		 * display size — a wordmark, not a headline, and duplicated the
		 * header's own logo text with zero indexable meaning. It now carries
		 * the real headline from content/home.md, so the clamp() max is
		 * dialled back from 8rem (fine for one word) to a size a full
		 * sentence can occupy without overflowing on small screens; the
		 * brand name itself stays visible via the header wordmark.
		 */
		?>
		<h1 class="font-playfair text-[clamp(1.875rem,5.5vw,4.5rem)] font-medium leading-[1.05] tracking-tight text-neutral-900 [animation:fadeSlideIn_1s_ease-out_0.1s_both]">
			<?php esc_html_e( "The Owner's Manual for Happy, Healthy Dogs and Cats", 'thepawmanual' ); ?>
		</h1>

		<p class="mt-6 max-w-3xl font-geist text-base leading-relaxed text-neutral-700 [animation:fadeSlideIn_1s_ease-out_0.15s_both] sm:text-lg">
			<?php
			/*
			 * The homepage intro paragraph from content/home.md. Built from
			 * pre-sanitized link fragments (esc_url()/esc_html() applied
			 * before substitution) rather than escaping the whole sentence,
			 * since the sentence itself must carry real <a> markup.
			 *
			 * AVMA, AKC, and APPA are linked here because this is their
			 * first mention on the page (home.md implementation note 3) —
			 * rel="nofollow noopener" on all three, per the same note.
			 */
			$tpm_hero_dog_age_link = sprintf(
				'<a href="%1$s" class="underline decoration-neutral-300 underline-offset-2 hover:decoration-neutral-900">%2$s</a>',
				esc_url( home_url( '/tools/dog-age-calculator/' ) ),
				esc_html__( 'dog age calculator', 'thepawmanual' )
			);
			$tpm_hero_appa_link = sprintf(
				'<a href="%1$s" rel="nofollow noopener" target="_blank" class="underline decoration-neutral-300 underline-offset-2 hover:decoration-neutral-900">%2$s</a>',
				esc_url( 'https://www.americanpetproducts.org/' ),
				esc_html__( 'American Pet Products Association', 'thepawmanual' )
			);
			$tpm_hero_avma_link = sprintf(
				'<a href="%1$s" rel="nofollow noopener" target="_blank" class="underline decoration-neutral-300 underline-offset-2 hover:decoration-neutral-900">%2$s</a>',
				esc_url( 'https://www.avma.org/' ),
				esc_html__( 'American Veterinary Medical Association (AVMA)', 'thepawmanual' )
			);
			$tpm_hero_akc_link = sprintf(
				'<a href="%1$s" rel="nofollow noopener" target="_blank" class="underline decoration-neutral-300 underline-offset-2 hover:decoration-neutral-900">%2$s</a>',
				esc_url( 'https://www.akc.org/' ),
				esc_html__( 'American Kennel Club (AKC)', 'thepawmanual' )
			);

			printf(
				/* translators: 1: dog age calculator link, 2: American Pet Products Association link, 3: AVMA link, 4: AKC link. */
				__( 'ThePawManual gives dog and cat owners clear, research-backed answers about breeds, food, health, behavior, and training, paired with free interactive tools like our %1$s. About 87 million U.S. households (66%%) own a pet, according to the %2$s, yet much of the pet advice online is contradictory or outdated. We fix that by grounding every guide in veterinary sources such as the %3$s and the %4$s, and by telling you plainly when a question belongs at your vet\'s office instead of a search bar.', 'thepawmanual' ),
				$tpm_hero_dog_age_link,
				$tpm_hero_appa_link,
				$tpm_hero_avma_link,
				$tpm_hero_akc_link
			); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- every substituted fragment is pre-built with esc_url()/esc_html__() above.
			?>
		</p>

		<div class="mt-8 grid gap-8 lg:grid-cols-12">

			<?php // Feature image with the "Find a Tool" button and category pills. ?>
			<div class="relative overflow-hidden rounded-3xl [animation:fadeSlideIn_1s_ease-out_0.2s_both] lg:col-span-8">
				<img
					src="<?php echo esc_url( get_theme_file_uri( 'assets/images/hero-dog.jpg' ) ); ?>"
					alt="<?php esc_attr_e( 'A happy dog running outdoors', 'thepawmanual' ); ?>"
					class="h-[300px] w-full object-cover sm:h-[440px]"
					fetchpriority="high"
					decoding="async"
				>
				<?php
				/*
				 * "Find a Tool" and the category quick links are plain links to
				 * #tools, so without JavaScript they still scroll to the full
				 * grid. theme.js (initToolFilters()) additionally sets the grid's
				 * category filter from data-tpm-filter-link and marks the active
				 * quick link. On phones "Find a Tool" sits top-left so it never
				 * collides with the centred quick links along the bottom.
				 */
				?>
				<div class="absolute left-4 top-4 sm:bottom-4 sm:top-auto">
					<a href="#tools" data-tpm-filter-link="all" class="inline-flex items-center gap-2 rounded-full bg-brand-600 px-4 py-2 font-geist text-sm text-white shadow transition hover:bg-brand-700">
						<?php esc_html_e( 'Find a Tool', 'thepawmanual' ); ?>
						<?php tpm_icon( 'arrow-right', 'h-4 w-4' ); ?>
					</a>
				</div>
				<?php if ( function_exists( 'tpm_get_tool_categories' ) ) : ?>
					<nav class="absolute bottom-4 left-1/2 -translate-x-1/2 sm:left-auto sm:right-4 sm:translate-x-0" aria-label="<?php esc_attr_e( 'Tools by category', 'thepawmanual' ); ?>">
						<ul class="flex items-center gap-1 rounded-full bg-white/85 p-2 shadow backdrop-blur">
							<?php foreach ( tpm_get_tool_categories() as $tpm_key => $tpm_label ) : ?>
								<li>
									<a href="#tools" data-tpm-filter-link="<?php echo esc_attr( $tpm_key ); ?>" class="tpm-hero-pill"><?php echo esc_html( $tpm_label ); ?></a>
								</li>
							<?php endforeach; ?>
						</ul>
					</nav>
				<?php endif; ?>
			</div>

			<?php // Featured-tool card. ?>
			<div class="tpm-card [animation:fadeSlideIn_1s_ease-out_0.3s_both] rounded-3xl p-5 sm:p-6 lg:col-span-4">
				<div>
					<h2 class="font-playfair text-2xl font-medium tracking-tight sm:text-3xl">
						<?php esc_html_e( 'Featured Tool', 'thepawmanual' ); ?>
					</h2>
					<p class="mt-2 font-geist text-sm text-neutral-600">
						<?php esc_html_e( "The Dog Age Calculator converts your dog's age into human years using a breed-size-aware veterinary model.", 'thepawmanual' ); ?>
					</p>
				</div>
				<div class="tpm-tool-cta mt-5 flex flex-grow flex-col items-center justify-center gap-3 p-6 text-center">
					<div class="flex h-12 w-12 items-center justify-center rounded-full border border-brand-200 bg-white text-brand-600 shadow-sm">
						<?php tpm_icon( 'calculator', 'h-6 w-6' ); ?>
					</div>
					<div>
						<p class="font-geist text-sm font-semibold text-neutral-900"><?php esc_html_e( 'Breed-aware result', 'thepawmanual' ); ?></p>
						<p class="mt-1 font-geist text-xs text-neutral-500"><?php esc_html_e( 'Based on veterinary guidelines', 'thepawmanual' ); ?></p>
					</div>
				</div>
				<div class="mt-4">
					<a href="<?php echo esc_url( home_url( '/tools/dog-age-calculator/' ) ); ?>" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-brand-600 px-4 py-2.5 font-geist text-sm text-white transition hover:bg-brand-700">
						<?php esc_html_e( 'Open Calculator', 'thepawmanual' ); ?>
						<?php tpm_icon( 'arrow-right', 'h-4 w-4' ); ?>
					</a>
				</div>
			</div>

		</div>
	</div>
</section>
