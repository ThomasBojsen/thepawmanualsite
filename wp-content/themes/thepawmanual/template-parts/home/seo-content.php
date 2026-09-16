<?php
/**
 * Homepage section: SEO content block.
 *
 * Everything below the tools grid carries the copy from content/home.md —
 * the hub summaries (Dogs / Cats / Free tools / Pet guides), the "Why trust
 * ThePawManual" block, the veterinary disclaimer, and the FAQ. Before this
 * template part existed, front-page.php was almost entirely hero imagery and
 * card labels with no real body text, so the homepage had nothing for search
 * engines to rank on (SEO-STRATEGY.md §1 item 0.4 — the single highest-value
 * pre-launch blocker on the list).
 *
 * Copy is carried faithfully from content/home.md: it was written for
 * featured snippets and AI answer engines (BLUF structure, specific figures)
 * and must not be paraphrased or trimmed. Two spots deliberately diverge from
 * the source text — see the inline comments below — because two of the four
 * tools and the dog-breed-selector tool are not built yet, and shipping a
 * link to a page that doesn't exist is the thin-content signal
 * SEO-STRATEGY.md is trying to avoid; see this theme's task report for the
 * full explanation.
 *
 * @package thepawmanual
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Build an internal link, styled to match the body copy around it.
 *
 * Links only render as links once their target page is actually published.
 * The homepage is live from day 0, but the content ships in waves over
 * several months (SEO-STRATEGY.md §4), so a hardcoded link to a wave-2 sub-hub
 * would be a 404 on the site's most authoritative page for weeks. When the
 * target is missing or still a draft, the sentence keeps its wording and
 * simply loses the anchor — the copy still reads correctly, and the link
 * appears by itself the moment that wave publishes. No template edit needed
 * per wave, which matters because the person publishing waves is an SEO
 * specialist, not a developer.
 *
 * @param string $path Site-relative path, e.g. '/dogs/breeds/'.
 * @param string $text Visible link text (untranslated; callers pass a
 *                      translated string so this stays a plain formatter).
 */
function tpm_seo_internal_link( $path, $text ) {
	$url = function_exists( 'tpm_get_published_page_url' ) ? tpm_get_published_page_url( $path ) : home_url( $path );

	if ( ! $url ) {
		return esc_html( $text );
	}

	return sprintf(
		'<a href="%1$s" class="text-neutral-900 underline decoration-neutral-300 underline-offset-2 hover:decoration-neutral-900">%2$s</a>',
		esc_url( $url ),
		esc_html( $text )
	);
}

/*
 * No external-link helper here: AVMA, AKC, and APPA are each mentioned for
 * the first time in the hero intro paragraph (template-parts/home/hero.php),
 * so their nofollow-noopener links live there (home.md implementation note
 * 3 — link only the first mention). Their later mentions in the "Why trust"
 * block below stay plain text on purpose.
 */
?>
<section class="mt-16 sm:mt-24" aria-label="<?php esc_attr_e( 'About ThePawManual', 'thepawmanual' ); ?>">
	<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
		<div class="grid gap-6 sm:grid-cols-2 lg:gap-8">

			<?php // --- Dog care hub ------------------------------------------------ ?>
			<div class="rounded-3xl border border-neutral-200 bg-white p-6 animate-on-scroll [animation:fadeSlideIn_0.8s_ease-out_both] sm:p-8">
				<h2 class="font-playfair text-2xl font-medium tracking-tight sm:text-3xl">
					<?php esc_html_e( 'Dog care, from first night home to senior years', 'thepawmanual' ); ?>
				</h2>
				<p class="mt-3 font-geist text-sm text-neutral-600">
					<?php
					printf(
						/* translators: %s: "dog care hub" link to /dogs/. */
						__( 'Everything for dog owners lives in our %s, organized so you can go from "which breed?" to "why is he doing that?" without leaving the site:', 'thepawmanual' ),
						tpm_seo_internal_link( '/dogs/', __( 'dog care hub', 'thepawmanual' ) )
					); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fragment built via tpm_seo_internal_link(), which escapes internally.
					?>
				</p>
				<ul class="mt-4 space-y-3 font-geist text-sm text-neutral-700">
					<li>
						<?php
						printf(
							/* translators: %s: "Dog breeds" hub link. */
							__( '%s: honest profiles of popular breeds like the Golden Retriever and French Bulldog, plus lifestyle lists like the best family dogs and apartment-friendly dogs.', 'thepawmanual' ),
							'<strong>' . tpm_seo_internal_link( '/dogs/breeds/', __( 'Dog breeds', 'thepawmanual' ) ) . '</strong>'
						); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						?>
					</li>
					<li>
						<?php
						printf(
							/* translators: %s: "Dog food & nutrition" hub link. */
							__( '%s, from choosing the best dog food to the question every owner googles: how much should I feed my dog?', 'thepawmanual' ),
							'<strong>' . tpm_seo_internal_link( '/dogs/food/', __( 'Dog food & nutrition', 'thepawmanual' ) ) . '</strong>'
						); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						?>
					</li>
					<li>
						<?php
						printf(
							/* translators: %s: "Dog health" hub link. */
							__( '%s: symptom guides written with clear veterinary sourcing, including signs your dog needs a vet.', 'thepawmanual' ),
							'<strong>' . tpm_seo_internal_link( '/dogs/health/', __( 'Dog health', 'thepawmanual' ) ) . '</strong>'
						); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						?>
					</li>
					<li>
						<?php
						printf(
							/* translators: %s: "Dog training" hub link. */
							__( '%s: step-by-step help with potty training, crate training, and excessive barking.', 'thepawmanual' ),
							'<strong>' . tpm_seo_internal_link( '/dogs/training/', __( 'Dog training', 'thepawmanual' ) ) . '</strong>'
						); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						?>
					</li>
					<li>
						<?php
						printf(
							/* translators: %s: "Puppy care" hub link. */
							__( '%s: start with the new puppy checklist and the puppy vaccination schedule.', 'thepawmanual' ),
							'<strong>' . tpm_seo_internal_link( '/dogs/puppy-care/', __( 'Puppy care', 'thepawmanual' ) ) . '</strong>'
						); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						?>
					</li>
				</ul>
			</div>

			<?php // --- Cat care hub ------------------------------------------------ ?>
			<div class="rounded-3xl border border-neutral-200 bg-white p-6 animate-on-scroll [animation:fadeSlideIn_0.8s_ease-out_both] sm:p-8">
				<h2 class="font-playfair text-2xl font-medium tracking-tight sm:text-3xl">
					<?php esc_html_e( 'Cat care that respects how cats actually work', 'thepawmanual' ); ?>
				</h2>
				<p class="mt-3 font-geist text-sm text-neutral-600">
					<?php
					printf(
						/* translators: %s: "cat care hub" link to /cats/. */
						__( "Cats aren't small dogs; our %s treats them accordingly:", 'thepawmanual' ),
						tpm_seo_internal_link( '/cats/', __( 'cat care hub', 'thepawmanual' ) )
					); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					?>
				</p>
				<ul class="mt-4 space-y-3 font-geist text-sm text-neutral-700">
					<li>
						<?php
						printf(
							/* translators: %s: "Cat breeds" hub link. */
							__( '%s: profiles of the Maine Coon, Ragdoll, Siamese, and more, plus lists like hypoallergenic cats.', 'thepawmanual' ),
							'<strong>' . tpm_seo_internal_link( '/cats/breeds/', __( 'Cat breeds', 'thepawmanual' ) ) . '</strong>'
						); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						?>
					</li>
					<li>
						<?php
						printf(
							/* translators: %s: "Cat food & nutrition" hub link. */
							__( '%s: including the wet vs. dry food decision and how much to feed your cat.', 'thepawmanual' ),
							'<strong>' . tpm_seo_internal_link( '/cats/food/', __( 'Cat food & nutrition', 'thepawmanual' ) ) . '</strong>'
						); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						?>
					</li>
					<li>
						<?php
						printf(
							/* translators: %s: "Cat health" hub link. */
							__( "%s: practical guides to hairballs, sneezing, and when a vet visit can't wait.", 'thepawmanual' ),
							'<strong>' . tpm_seo_internal_link( '/cats/health/', __( 'Cat health', 'thepawmanual' ) ) . '</strong>'
						); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						?>
					</li>
					<li>
						<?php
						printf(
							/* translators: %s: "Cat behavior" hub link. */
							__( '%s: decode kneading, constant meowing, and hiding.', 'thepawmanual' ),
							'<strong>' . tpm_seo_internal_link( '/cats/behavior/', __( 'Cat behavior', 'thepawmanual' ) ) . '</strong>'
						); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						?>
					</li>
					<li>
						<?php
						printf(
							/* translators: %s: "Kitten care" hub link. */
							__( '%s: from the new kitten checklist to litter training.', 'thepawmanual' ),
							'<strong>' . tpm_seo_internal_link( '/cats/kitten-care/', __( 'Kitten care', 'thepawmanual' ) ) . '</strong>'
						); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						?>
					</li>
				</ul>
			</div>

			<?php // --- Free tools ---------------------------------------------------- ?>
			<div class="rounded-3xl border border-neutral-200 bg-white p-6 animate-on-scroll [animation:fadeSlideIn_0.8s_ease-out_both] sm:p-8">
				<h2 class="font-playfair text-2xl font-medium tracking-tight sm:text-3xl">
					<?php esc_html_e( 'Free pet calculators and tools', 'thepawmanual' ); ?>
				</h2>
				<p class="mt-3 font-geist text-sm text-neutral-600">
					<?php
					printf(
						/* translators: %s: "free tools" link to /tools/. */
						__( 'Our %s turn veterinary guidelines into instant, personalized answers. No signup, no paywall. The math runs right in your browser. Start with the two most popular:', 'thepawmanual' ),
						tpm_seo_internal_link( '/tools/', __( 'free tools', 'thepawmanual' ) )
					); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					?>
				</p>
				<?php
				/*
				 * home.md lists this section as a 4-row table (Dog Age, Cat
				 * Age, Pet Cost, Dog Breed Selector). Rendered here as a list
				 * to match the other three hub sections' shape. Only Dog Age
				 * and Cat Age are built — the other two are named exactly as
				 * in home.md but intentionally left unlinked and marked
				 * "coming soon", matching the tools-grid cards above and the
				 * "no dead links to unbuilt tools" rule from this theme's
				 * task brief.
				 */
				?>
				<ul class="mt-4 space-y-3 font-geist text-sm text-neutral-700">
					<li>
						<?php
						printf(
							/* translators: 1: Dog Age Calculator link, 2: description. */
							__( '%1$s: %2$s', 'thepawmanual' ),
							'<strong>' . tpm_seo_internal_link( '/tools/dog-age-calculator/', __( 'Dog Age Calculator', 'thepawmanual' ) ) . '</strong>',
							esc_html__( "Your dog's age in human years, adjusted for breed size", 'thepawmanual' )
						); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						?>
					</li>
					<li>
						<?php
						printf(
							/* translators: 1: Cat Age Calculator link, 2: description. */
							__( '%1$s: %2$s', 'thepawmanual' ),
							'<strong>' . tpm_seo_internal_link( '/tools/cat-age-calculator/', __( 'Cat Age Calculator', 'thepawmanual' ) ) . '</strong>',
							esc_html__( "Your cat's age in human years, using the standard feline model", 'thepawmanual' )
						); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						?>
					</li>
					<li class="text-neutral-500">
						<?php
						printf(
							/* translators: 1: Pet Cost Calculator name, 2: description. */
							__( '%1$s: %2$s (coming soon)', 'thepawmanual' ),
							'<strong>' . esc_html__( 'Pet Cost Calculator', 'thepawmanual' ) . '</strong>',
							esc_html__( 'What a dog or cat will realistically cost you per year', 'thepawmanual' )
						); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						?>
					</li>
					<li class="text-neutral-500">
						<?php
						printf(
							/* translators: 1: Dog Breed Selector name, 2: description. */
							__( '%1$s: %2$s (coming soon)', 'thepawmanual' ),
							'<strong>' . esc_html__( 'Dog Breed Selector', 'thepawmanual' ) . '</strong>',
							esc_html__( 'Which breeds fit your home, schedule, and activity level', 'thepawmanual' )
						); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						?>
					</li>
				</ul>
				<p class="mt-4 font-geist text-sm text-neutral-600">
					<?php esc_html_e( 'Fun fact the tools are built on: the old "multiply by 7" rule is wrong. A 1-year-old dog is developmentally closer to a 15-year-old human, and small breeds age more slowly in later life than giant breeds, which is why our calculator asks for size.', 'thepawmanual' ); ?>
				</p>
			</div>

			<?php // --- Pet guides --------------------------------------------------- ?>
			<div class="rounded-3xl border border-neutral-200 bg-white p-6 animate-on-scroll [animation:fadeSlideIn_0.8s_ease-out_both] sm:p-8">
				<h2 class="font-playfair text-2xl font-medium tracking-tight sm:text-3xl">
					<?php esc_html_e( 'Practical guides for every pet household', 'thepawmanual' ); ?>
				</h2>
				<p class="mt-3 font-geist text-sm text-neutral-700">
					<?php
					printf(
						/* translators: 1: "pet guides" hub link, 2: pet insurance link, 3: pet costs link, 4: pet safety link, 5: pet travel link, 6: new pet checklist link. */
						__( 'Some decisions apply whether you have a dog, a cat, or both. Our %1$s cover the big ones: %2$s, %3$s (plan on roughly $1,000–$2,000 per year for a dog, somewhat less for a cat), %4$s, %5$s, and the %6$s for first-time owners.', 'thepawmanual' ),
						tpm_seo_internal_link( '/pet-guides/', __( 'pet guides', 'thepawmanual' ) ),
						tpm_seo_internal_link( '/pet-guides/pet-insurance/', __( 'is pet insurance worth it?', 'thepawmanual' ) ),
						tpm_seo_internal_link( '/pet-guides/pet-costs/', __( 'what a pet really costs', 'thepawmanual' ) ),
						tpm_seo_internal_link( '/pet-guides/pet-safety/', __( 'pet-proofing your home', 'thepawmanual' ) ),
						tpm_seo_internal_link( '/pet-guides/pet-travel/', __( 'traveling with pets', 'thepawmanual' ) ),
						tpm_seo_internal_link( '/pet-guides/new-pet-checklist/', __( 'new pet checklist', 'thepawmanual' ) )
					); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					?>
				</p>
			</div>

		</div>
	</div>
</section>

<section class="mt-16 sm:mt-24">
	<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
		<div class="rounded-3xl border border-neutral-200 bg-white p-6 animate-on-scroll [animation:fadeSlideIn_0.8s_ease-out_both] sm:p-10">

			<h2 class="font-playfair text-2xl font-medium tracking-tight sm:text-3xl">
				<?php esc_html_e( 'Why trust ThePawManual', 'thepawmanual' ); ?>
			</h2>

			<ul class="mt-4 space-y-3 font-geist text-sm text-neutral-700">
				<li>
					<strong><?php esc_html_e( 'Sourced, not opinionated.', 'thepawmanual' ); ?></strong>
					<?php esc_html_e( 'Health and nutrition claims cite veterinary authorities (the AVMA, the AKC, and peer-reviewed veterinary research) with references linked on every page.', 'thepawmanual' ); ?>
				</li>
				<li>
					<strong><?php esc_html_e( 'Honest about our limits.', 'thepawmanual' ); ?></strong>
					<?php esc_html_e( "We're an educational resource, not a vet. Every health page carries a clear disclaimer, and we tell you when symptoms warrant professional care.", 'thepawmanual' ); ?>
				</li>
				<li>
					<strong><?php esc_html_e( 'Kept current.', 'thepawmanual' ); ?></strong>
					<?php esc_html_e( 'Guides carry a visible last-updated date and are reviewed on a schedule, because feeding guidelines and vaccination protocols change.', 'thepawmanual' ); ?>
				</li>
			</ul>

			<p class="mt-4 font-geist text-sm text-neutral-600">
				<?php
				/*
				 * /about/ and /editorial-process/ are new trust pages
				 * (content/about.md, content/editorial-process.md) that must
				 * be published before or at launch — both this block and the
				 * footer (SEO-STRATEGY §1 item 0.8) link them, per home.md's
				 * own body copy. Flagged in the task report as a launch
				 * dependency, not a theme bug.
				 */
				printf(
					/* translators: 1: "who we are" link to /about/, 2: "how we research and review our content" link to /editorial-process/. */
					__( 'Read more about %1$s and %2$s.', 'thepawmanual' ),
					tpm_seo_internal_link( '/about/', __( 'who we are', 'thepawmanual' ) ),
					tpm_seo_internal_link( '/editorial-process/', __( 'how we research and review our content', 'thepawmanual' ) )
				); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				?>
			</p>

			<blockquote class="mt-6 rounded-2xl border border-neutral-200 bg-neutral-50 p-5 font-geist text-sm leading-relaxed text-neutral-700">
				<p>
					<strong><?php esc_html_e( 'Veterinary disclaimer:', 'thepawmanual' ); ?></strong>
					<?php esc_html_e( 'ThePawManual provides general educational information for pet owners. It is not a substitute for professional veterinary advice, diagnosis, or treatment. If your pet shows signs of illness or distress, contact a licensed veterinarian.', 'thepawmanual' ); ?>
				</p>
			</blockquote>

		</div>
	</div>
</section>

<section class="mt-16 sm:mt-24">
	<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

		<h2 class="font-playfair text-2xl font-medium tracking-tight sm:text-3xl">
			<?php esc_html_e( 'Frequently asked questions', 'thepawmanual' ); ?>
		</h2>

		<?php
		/*
		 * FAQ JSON-LD (FAQPage schema) is intentionally NOT output here.
		 * CLAUDE.md §9 gives SEO meta/schema ownership to RankMath, not the
		 * theme. The ready-to-paste schema block lives at the bottom of
		 * content/home.md (below its "Implementation notes" heading) —
		 * paste it via RankMath (Schema → Custom) or an HTML block on the
		 * static "Home" page. The visible answers below must stay identical
		 * to that schema's `text` fields through every future edit.
		 */
		?>

		<dl class="mt-6 divide-y divide-neutral-200 rounded-3xl border border-neutral-200 bg-white animate-on-scroll [animation:fadeSlideIn_0.8s_ease-out_both]">

			<div class="p-6 sm:p-8">
				<dt class="font-geist text-base font-semibold tracking-tight text-neutral-900">
					<?php esc_html_e( 'How old is my dog in human years?', 'thepawmanual' ); ?>
				</dt>
				<dd class="mt-2 font-geist text-sm leading-relaxed text-neutral-600">
					<?php
					printf(
						/* translators: %s: "dog age calculator" link to /tools/dog-age-calculator/. */
						__( 'A 1-year-old dog is roughly as mature as a 15-year-old human, and aging speed depends on breed size: the old "7 dog years" rule is inaccurate. Use our free %s for a size-adjusted estimate based on veterinary aging models.', 'thepawmanual' ),
						tpm_seo_internal_link( '/tools/dog-age-calculator/', __( 'dog age calculator', 'thepawmanual' ) )
					); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					?>
				</dd>
			</div>

			<div class="p-6 sm:p-8">
				<dt class="font-geist text-base font-semibold tracking-tight text-neutral-900">
					<?php esc_html_e( 'How much does it cost to own a dog or cat?', 'thepawmanual' ); ?>
				</dt>
				<dd class="mt-2 font-geist text-sm leading-relaxed text-neutral-600">
					<?php
					printf(
						/* translators: %s: "pet cost guide" link to /pet-guides/pet-costs/. */
						__( 'Most U.S. dog owners spend roughly $1,000–$2,000 per year on food, routine veterinary care, and supplies; cats typically cost somewhat less. The first year runs higher due to one-time purchases and vaccinations. Our %s breaks down every line item.', 'thepawmanual' ),
						tpm_seo_internal_link( '/pet-guides/pet-costs/', __( 'pet cost guide', 'thepawmanual' ) )
					); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					?>
				</dd>
			</div>

			<div class="p-6 sm:p-8">
				<dt class="font-geist text-base font-semibold tracking-tight text-neutral-900">
					<?php esc_html_e( 'How do I choose the right dog or cat breed for my lifestyle?', 'thepawmanual' ); ?>
				</dt>
				<dd class="mt-2 font-geist text-sm leading-relaxed text-neutral-600">
					<?php
					/*
					 * home.md links "the dog breed selector" to
					 * /tools/dog-breed-selector/ — that tool isn't built yet
					 * (SITEMAP.md), so per the "no dead links to unbuilt
					 * tools" rule the phrase stays but the link is dropped;
					 * everything else is unchanged.
					 */
					printf(
						/* translators: 1: "dog breed guides" link, 2: "cat breed guides" link. */
						__( "Start from your living situation, not the breed's looks: space, hours alone per day, activity level, allergies, and kids. Our %1\$s and %2\$s rate each breed on exactly those factors, and the dog breed selector narrows it down in under a minute.", 'thepawmanual' ),
						tpm_seo_internal_link( '/dogs/breeds/', __( 'dog breed guides', 'thepawmanual' ) ),
						tpm_seo_internal_link( '/cats/breeds/', __( 'cat breed guides', 'thepawmanual' ) )
					); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					?>
				</dd>
			</div>

			<div class="p-6 sm:p-8">
				<dt class="font-geist text-base font-semibold tracking-tight text-neutral-900">
					<?php esc_html_e( 'Does ThePawManual replace veterinary advice?', 'thepawmanual' ); ?>
				</dt>
				<dd class="mt-2 font-geist text-sm leading-relaxed text-neutral-600">
					<?php esc_html_e( 'No. Our content is educational and helps you ask better questions, spot warning signs, and handle routine care confidently, but diagnosis and treatment belong with a licensed veterinarian. When in doubt, call your vet.', 'thepawmanual' ); ?>
				</dd>
			</div>

		</dl>
	</div>
</section>
