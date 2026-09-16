# ThePawManual: Content Plan & Sitemap Tracker

> One markdown file per page in `content/`, mirroring the URL structure.
> Frontmatter carries the RankMath fields (SEO title, meta description).
> Status: ⬜ pending · ✍️ draft written · ✅ approved/published.
>
> Pages marked **NEW** were not in the original sitemap: added for SEO/E-E-A-T reasons
> (rationale noted inline).
>
> **LIVE (2026-09-10):** all 18 wave-1 URLs are published (see `docs/LAUNCH-RUNBOOK.md` and
> CLAUDE.md §11); the ✅ status below reflects that, not a repo-import state. **Indexing is a
> separate, unverified claim:** published only means the page exists and returns 200 on
> thepawmanual.com. Whether Google has actually indexed any given URL is not something this
> repo or its tooling can determine; only Google Search Console's Pages / URL Inspection
> report can say that, and it has not been checked page-by-page. Don't read the ✅ status below,
> or "live," as an indexing claim either way; check GSC before relying on indexing state.
>
> Pages marked **GEO drafted (repo only) 2026-09-14** or **GEO drafted (repo only) 2026-09-15**
> have had the answer-first / FAQ-schema GEO rewrite described in `content/README.md` rules
> 15–17 (question-shaped H2s, an answer-first paragraph under every H2, matching FAQPage
> JSON-LD) applied to the *markdown in this repo*. The 2026-09-14 date marks the 11 wave-1
> URLs (all already live): WordPress's importer recognises an already-imported page by
> title+date and skips a re-import (CLAUDE.md §13), so editing those bodies does **not** change
> the live pages, and the rewrite reaches them only by hand-pasting each page's body into WP
> admin (Block editor → Code editor), page by page. Until that paste happens the live page
> still runs the old, pre-GEO copy: do not read that marker as "this page is GEO-optimized on
> the live site." The 2026-09-15 date marks the 95 pages the wave-2 pass actually rewrote
> (re-verified 2026-09-15 by diffing all 109 wave-2 target pages against HEAD: a page counts
> only with a converted heading, a new/expanded answer-first paragraph, or a new FAQPage
> entity, not merely an em-dash-to-other-punctuation substitution); none of them
> are live yet, so for these the repo copy *is* the only copy, and the rewrite ships to
> WordPress the normal way, on the WXR import for whichever wave publishes them. **14 pages are
> still unmarked** (listed in the Dogs and Cats tables below): their only diffs from HEAD are
> punctuation substitutions, so they have not had the GEO pass at
> all yet. Apply it (in the repo) before treating them as launch-ready for wave 2+.
>
> **Live-vs-repo drift (2026-09-14):** an em-dash-to-colon house-style pass was applied
> directly on the live pages at some point after wave-1 import and was never brought back into
> `content/`. This edit re-synced the 11 GEO-drafted pages' bodies and all six trust pages to
> that style (see `content/README.md`'s Style paragraph), but the 11 GEO-drafted pages still
> differ from live in structure (new headings, new FAQ entries) because the hand-paste above
> hasn't happened. Don't assume `content/` is strictly newer than live for any wave-1 page:
> diff before overwriting either direction.

## Homepage

| Page | URL | Status |
|---|---|---|
| Homepage (Pets) | `/` | ✅ published; live 2026-09-10; `content/home.md` |

## Trust & E-E-A-T pages (NEW, required set)

Google treats pet health/nutrition as YMYL-adjacent. A site with no identifiable
publisher, no editorial policy, and affiliate product pages with no disclosure
struggles to rank on health queries. These six pages are cheap to produce and
load-bearing for everything else:

| Page | URL | Status | Why |
|---|---|---|---|
| About ThePawManual | `/about/` | ✅ published; live 2026-09-10; `content/about.md` | Entity/publisher identity: cited by every E-E-A-T guideline |
| Editorial & Review Process | `/editorial-process/` | ✅ published; live 2026-09-10; `content/editorial-process.md` | How content is sourced/updated; linked from every health page |
| Contact | `/contact/` | ✅ published; live 2026-09-10; `content/contact.md` | Trust signal + AdSense/Mediavine requirement |
| Privacy Policy | `/privacy-policy/` | ✅ published; live 2026-09-10; `content/privacy-policy.md` | Legal + ad network requirement |
| Terms of Use | `/terms/` | ✅ published; live 2026-09-10; `content/terms.md` ⚠️ Danish governing-law clause on a US-directed site: still worth a legal review | Legal |
| Affiliate Disclosure | `/affiliate-disclosure/` | ✅ published; live 2026-09-10; `content/affiliate-disclosure.md` | FTC requirement: the `/products/` sections are affiliate content |

## Dogs: `/dogs/` (65 pages)

| Page | URL | Status |
|---|---|---|
| Dogs hub | `/dogs/` | ✅ published; live 2026-09-10; `content/dogs/index.md` · GEO drafted (repo only) 2026-09-14; NOT yet live |
| Dog Breeds hub | `/dogs/breeds/` | ✍️ draft; `content/dogs/breeds/index.md` · GEO drafted (repo only) 2026-09-15 |
| Golden Retriever | `/dogs/breeds/golden-retriever/` | ✍️ draft; `content/dogs/breeds/golden-retriever.md` · GEO drafted (repo only) 2026-09-15 |
| Labrador Retriever | `/dogs/breeds/labrador-retriever/` | ✍️ draft; `content/dogs/breeds/labrador-retriever.md` · GEO drafted (repo only) 2026-09-15 |
| French Bulldog | `/dogs/breeds/french-bulldog/` | ✍️ draft; `content/dogs/breeds/french-bulldog.md` · GEO drafted (repo only) 2026-09-15 |
| German Shepherd | `/dogs/breeds/german-shepherd/` | ✍️ draft; `content/dogs/breeds/german-shepherd.md` · GEO drafted (repo only) 2026-09-15 |
| Poodle | `/dogs/breeds/poodle/` | ✍️ draft; `content/dogs/breeds/poodle.md` · GEO drafted (repo only) 2026-09-15 |
| Small Dogs | `/dogs/breeds/small-dogs/` | ✍️ draft; `content/dogs/breeds/small-dogs.md` · GEO drafted (repo only) 2026-09-15 |
| Family Dogs | `/dogs/breeds/family-dogs/` | ✍️ draft; `content/dogs/breeds/family-dogs.md` · GEO drafted (repo only) 2026-09-15 |
| Apartment Dogs | `/dogs/breeds/apartment-dogs/` | ✍️ draft; `content/dogs/breeds/apartment-dogs.md` · GEO drafted (repo only) 2026-09-15 |
| Low-Shedding Dogs | `/dogs/breeds/low-shedding-dogs/` | ✍️ draft; `content/dogs/breeds/low-shedding-dogs.md` · GEO drafted (repo only) 2026-09-15 |
| Large Dogs (NEW 24-07) | `/dogs/breeds/large-dogs/` | ✍️ draft; `content/dogs/breeds/large-dogs.md` · GEO drafted (repo only) 2026-09-15 |
| Guard Dogs (NEW 24-07) | `/dogs/breeds/guard-dogs/` | ✍️ draft; `content/dogs/breeds/guard-dogs.md` · GEO drafted (repo only) 2026-09-15 |
| Best Dogs for Seniors (NEW 24-07) | `/dogs/breeds/best-dogs-for-seniors/` | ✍️ draft; `content/dogs/breeds/best-dogs-for-seniors.md` · GEO drafted (repo only) 2026-09-15 |
| Dog Food hub | `/dogs/food/` | ✍️ draft; `content/dogs/food/index.md` · GEO drafted (repo only) 2026-09-15 |
| Best Dog Food | `/dogs/food/best-dog-food/` | ✍️ draft; `content/dogs/food/best-dog-food.md` · GEO drafted (repo only) 2026-09-15 |
| Puppy Food | `/dogs/food/puppy-food/` | ✍️ draft; `content/dogs/food/puppy-food.md` · GEO drafted (repo only) 2026-09-15 |
| Senior Dog Food | `/dogs/food/senior-dog-food/` | ✍️ draft; `content/dogs/food/senior-dog-food.md` · GEO drafted (repo only) 2026-09-15 |
| Dog Food for Allergies | `/dogs/food/dog-food-for-allergies/` | ✍️ draft; `content/dogs/food/dog-food-for-allergies.md` · GEO drafted (repo only) 2026-09-15 |
| How Much Should I Feed My Dog? | `/dogs/food/how-much-should-i-feed-my-dog/` | ✍️ draft; `content/dogs/food/how-much-should-i-feed-my-dog.md` · GEO drafted (repo only) 2026-09-15 |
| Can Dogs Eat Rice? | `/dogs/food/can-dogs-eat-rice/` | ✍️ draft; `content/dogs/food/can-dogs-eat-rice.md` · GEO drafted (repo only) 2026-09-15 |
| Can Dogs Eat Chicken? | `/dogs/food/can-dogs-eat-chicken/` | ✍️ draft; `content/dogs/food/can-dogs-eat-chicken.md` · GEO drafted (repo only) 2026-09-15 |
| Can Dogs Eat Grapes? (NEW 24-07, danger-first) | `/dogs/food/can-dogs-eat-grapes/` | ✍️ draft; `content/dogs/food/can-dogs-eat-grapes.md` · GEO drafted (repo only) 2026-09-15 |
| Can Dogs Eat Chocolate? (NEW 24-07, danger-first) | `/dogs/food/can-dogs-eat-chocolate/` | ✍️ draft; `content/dogs/food/can-dogs-eat-chocolate.md` · GEO drafted (repo only) 2026-09-15 |
| Dog Health hub | `/dogs/health/` | ✍️ draft; `content/dogs/health/index.md` |
| Dog Symptoms | `/dogs/health/symptoms/` | ✍️ draft; `content/dogs/health/symptoms.md` · GEO drafted (repo only) 2026-09-15 |
| Dog Allergies | `/dogs/health/allergies/` | ✍️ draft; `content/dogs/health/allergies.md` · GEO drafted (repo only) 2026-09-15 |
| Dog Dental Care | `/dogs/health/dental-care/` | ✍️ draft; `content/dogs/health/dental-care.md` · GEO drafted (repo only) 2026-09-15 |
| Skin & Coat | `/dogs/health/skin-and-coat/` | ✍️ draft; `content/dogs/health/skin-and-coat.md` · GEO drafted (repo only) 2026-09-15 |
| Why Is My Dog Itching? | `/dogs/health/why-is-my-dog-itching/` | ✍️ draft; `content/dogs/health/why-is-my-dog-itching.md` |
| Why Is My Dog Throwing Up? | `/dogs/health/why-is-my-dog-throwing-up/` | ✍️ draft; `content/dogs/health/why-is-my-dog-throwing-up.md` · GEO drafted (repo only) 2026-09-15 |
| Signs Your Dog Needs a Vet | `/dogs/health/signs-your-dog-needs-a-vet/` | ✍️ draft; `content/dogs/health/signs-your-dog-needs-a-vet.md` · GEO drafted (repo only) 2026-09-15 |
| Fleas, Ticks & Worms (NEW 24-07) | `/dogs/health/fleas-ticks-and-worms/` | ✍️ draft; `content/dogs/health/fleas-ticks-and-worms.md` · GEO drafted (repo only) 2026-09-15 |
| Dog Ear Infections (NEW 24-07) | `/dogs/health/ear-infections/` | ✍️ draft; `content/dogs/health/ear-infections.md` · GEO drafted (repo only) 2026-09-15 |
| Dog Training hub | `/dogs/training/` | ✍️ draft; `content/dogs/training/index.md` · GEO drafted (repo only) 2026-09-15 |
| Puppy Training | `/dogs/training/puppy-training/` | ✍️ draft; `content/dogs/training/puppy-training.md` · GEO drafted (repo only) 2026-09-15 |
| Potty Training | `/dogs/training/potty-training/` | ✍️ draft; `content/dogs/training/potty-training.md` · GEO drafted (repo only) 2026-09-15 |
| Crate Training | `/dogs/training/crate-training/` | ✍️ draft; `content/dogs/training/crate-training.md` · GEO drafted (repo only) 2026-09-15 |
| Leash Training | `/dogs/training/leash-training/` | ✍️ draft; `content/dogs/training/leash-training.md` · GEO drafted (repo only) 2026-09-15 |
| How to Stop Dog Barking | `/dogs/training/how-to-stop-dog-barking/` | ✍️ draft; `content/dogs/training/how-to-stop-dog-barking.md` · GEO drafted (repo only) 2026-09-15 |
| Separation Anxiety | `/dogs/training/separation-anxiety-in-dogs/` | ✍️ draft; `content/dogs/training/separation-anxiety-in-dogs.md` · GEO drafted (repo only) 2026-09-15 |
| Recall Training (NEW 24-07) | `/dogs/training/recall-training/` | ✍️ draft; `content/dogs/training/recall-training.md` |
| Puppy Care hub | `/dogs/puppy-care/` | ✍️ draft; `content/dogs/puppy-care/index.md` · GEO drafted (repo only) 2026-09-15 |
| New Puppy Checklist | `/dogs/puppy-care/new-puppy-checklist/` | ✍️ draft; `content/dogs/puppy-care/new-puppy-checklist.md` · GEO drafted (repo only) 2026-09-15 |
| 8-Week-Old Puppy Schedule | `/dogs/puppy-care/8-week-old-puppy-schedule/` | ✍️ draft; `content/dogs/puppy-care/8-week-old-puppy-schedule.md` · GEO drafted (repo only) 2026-09-15 |
| Puppy Vaccination Schedule | `/dogs/puppy-care/puppy-vaccination-schedule/` | ✍️ draft; `content/dogs/puppy-care/puppy-vaccination-schedule.md` · GEO drafted (repo only) 2026-09-15 |
| First Night With Puppy | `/dogs/puppy-care/first-night-with-puppy/` | ✍️ draft; `content/dogs/puppy-care/first-night-with-puppy.md` · GEO drafted (repo only) 2026-09-15 |
| Puppy Teething Guide | `/dogs/puppy-care/puppy-teething-guide/` | ✍️ draft; `content/dogs/puppy-care/puppy-teething-guide.md` · GEO drafted (repo only) 2026-09-15 |
| How to Stop Puppy Biting (NEW 24-07) | `/dogs/puppy-care/how-to-stop-puppy-biting/` | ✍️ draft; `content/dogs/puppy-care/how-to-stop-puppy-biting.md` · GEO drafted (repo only) 2026-09-15 |
| Puppy Socialization (NEW 24-07) | `/dogs/puppy-care/puppy-socialization/` | ✍️ draft; `content/dogs/puppy-care/puppy-socialization.md` · GEO drafted (repo only) 2026-09-15 |
| Dog Products hub | `/dogs/products/` | ✍️ draft; `content/dogs/products/index.md` |
| Best Dog Beds | `/dogs/products/best-dog-beds/` | ✍️ draft; `content/dogs/products/best-dog-beds.md` · GEO drafted (repo only) 2026-09-15 |
| Best Dog Crates | `/dogs/products/best-dog-crates/` | ✍️ draft; `content/dogs/products/best-dog-crates.md` · GEO drafted (repo only) 2026-09-15 |
| Best Dog Harnesses | `/dogs/products/best-dog-harnesses/` | ✍️ draft; `content/dogs/products/best-dog-harnesses.md` |
| Best Dog Toys for Chewers | `/dogs/products/best-dog-toys-for-chewers/` | ✍️ draft; `content/dogs/products/best-dog-toys-for-chewers.md` · GEO drafted (repo only) 2026-09-15 |
| Dog GPS Trackers | `/dogs/products/best-gps-dog-trackers/` | ✍️ draft; `content/dogs/products/best-gps-dog-trackers.md` · GEO drafted (repo only) 2026-09-15 |
| Dog Behavior hub | `/dogs/behavior/` | ✍️ draft; `content/dogs/behavior/index.md` · GEO drafted (repo only) 2026-09-15 |
| Why Does My Dog Lick Me? | `/dogs/behavior/why-does-my-dog-lick-me/` | ✍️ draft; `content/dogs/behavior/why-does-my-dog-lick-me.md` |
| Why Does My Dog Eat Grass? | `/dogs/behavior/why-does-my-dog-eat-grass/` | ✍️ draft; `content/dogs/behavior/why-does-my-dog-eat-grass.md` |
| Why Does My Dog Follow Me Everywhere? | `/dogs/behavior/why-does-my-dog-follow-me-everywhere/` | ✍️ draft; `content/dogs/behavior/why-does-my-dog-follow-me-everywhere.md` |
| Why Does My Dog Shake? | `/dogs/behavior/why-does-my-dog-shake/` | ✍️ draft; `content/dogs/behavior/why-does-my-dog-shake.md` · GEO drafted (repo only) 2026-09-15 |
| Dog Zoomies (NEW 24-07) | `/dogs/behavior/dog-zoomies/` | ✍️ draft; `content/dogs/behavior/dog-zoomies.md` · GEO drafted (repo only) 2026-09-15 |
| Why Does My Dog Stare at Me? (NEW 24-07) | `/dogs/behavior/why-does-my-dog-stare-at-me/` | ✍️ draft; `content/dogs/behavior/why-does-my-dog-stare-at-me.md` · GEO drafted (repo only) 2026-09-15 |
| Why Does My Dog Dig? (NEW 24-07) | `/dogs/behavior/why-does-my-dog-dig/` | ✍️ draft; `content/dogs/behavior/why-does-my-dog-dig.md` · GEO drafted (repo only) 2026-09-15 |
| **Dog Grooming hub (NEW 24-07, new 8th pillar)** | `/dogs/grooming/` | ✍️ draft; `content/dogs/grooming/index.md` · GEO drafted (repo only) 2026-09-15 |

## Cats: `/cats/` (46 pages)

| Page | URL | Status |
|---|---|---|
| Cats hub | `/cats/` | ✅ published; live 2026-09-10; `content/cats/index.md` · GEO drafted (repo only) 2026-09-14; NOT yet live |
| Cat Breeds hub | `/cats/breeds/` | ✍️ draft; `content/cats/breeds/index.md` · GEO drafted (repo only) 2026-09-15 |
| Maine Coon | `/cats/breeds/maine-coon/` | ✍️ draft; `content/cats/breeds/maine-coon.md` · GEO drafted (repo only) 2026-09-15 |
| Ragdoll | `/cats/breeds/ragdoll/` | ✍️ draft; `content/cats/breeds/ragdoll.md` · GEO drafted (repo only) 2026-09-15 |
| Siamese | `/cats/breeds/siamese/` | ✍️ draft; `content/cats/breeds/siamese.md` · GEO drafted (repo only) 2026-09-15 |
| British Shorthair | `/cats/breeds/british-shorthair/` | ✍️ draft; `content/cats/breeds/british-shorthair.md` · GEO drafted (repo only) 2026-09-15 |
| Bengal | `/cats/breeds/bengal/` | ✍️ draft; `content/cats/breeds/bengal.md` · GEO drafted (repo only) 2026-09-15 |
| Persian | `/cats/breeds/persian/` | ✍️ draft; `content/cats/breeds/persian.md` · GEO drafted (repo only) 2026-09-15 |
| Hypoallergenic Cats | `/cats/breeds/hypoallergenic-cats/` | ✍️ draft; `content/cats/breeds/hypoallergenic-cats.md` · GEO drafted (repo only) 2026-09-15 |
| Apartment Cats | `/cats/breeds/apartment-cats/` | ✍️ draft; `content/cats/breeds/apartment-cats.md` · GEO drafted (repo only) 2026-09-15 |
| Family Cats | `/cats/breeds/family-cats/` | ✍️ draft; `content/cats/breeds/family-cats.md` · GEO drafted (repo only) 2026-09-15 |
| Cat Food hub | `/cats/food/` | ✍️ draft; `content/cats/food/index.md` · GEO drafted (repo only) 2026-09-15 |
| Best Cat Food | `/cats/food/best-cat-food/` | ✍️ draft; `content/cats/food/best-cat-food.md` · GEO drafted (repo only) 2026-09-15 |
| Kitten Food | `/cats/food/kitten-food/` | ✍️ draft; `content/cats/food/kitten-food.md` · GEO drafted (repo only) 2026-09-15 |
| Senior Cat Food | `/cats/food/senior-cat-food/` | ✍️ draft; `content/cats/food/senior-cat-food.md` · GEO drafted (repo only) 2026-09-15 |
| Wet Cat Food | `/cats/food/wet-cat-food/` | ✍️ draft; `content/cats/food/wet-cat-food.md` · GEO drafted (repo only) 2026-09-15 |
| Dry Cat Food | `/cats/food/dry-cat-food/` | ✍️ draft; `content/cats/food/dry-cat-food.md` · GEO drafted (repo only) 2026-09-15 |
| Cat Food for Sensitive Stomachs | `/cats/food/cat-food-for-sensitive-stomachs/` | ✍️ draft; `content/cats/food/cat-food-for-sensitive-stomachs.md` · GEO drafted (repo only) 2026-09-15 |
| How Much Should I Feed My Cat? | `/cats/food/how-much-should-i-feed-my-cat/` | ✍️ draft; `content/cats/food/how-much-should-i-feed-my-cat.md` · GEO drafted (repo only) 2026-09-15 |
| Can Cats Eat Tuna? | `/cats/food/can-cats-eat-tuna/` | ✍️ draft; `content/cats/food/can-cats-eat-tuna.md` · GEO drafted (repo only) 2026-09-15 |
| Cat Health hub | `/cats/health/` | ✍️ draft; `content/cats/health/index.md` · GEO drafted (repo only) 2026-09-15 |
| Cat Symptoms | `/cats/health/symptoms/` | ✍️ draft; `content/cats/health/symptoms.md` · GEO drafted (repo only) 2026-09-15 |
| Cat Allergies | `/cats/health/allergies/` | ✍️ draft; `content/cats/health/allergies.md` |
| Cat Dental Care | `/cats/health/dental-care/` | ✍️ draft; `content/cats/health/dental-care.md` · GEO drafted (repo only) 2026-09-15 |
| Hairballs | `/cats/health/hairballs/` | ✍️ draft; `content/cats/health/hairballs.md` · GEO drafted (repo only) 2026-09-15 |
| Why Is My Cat Throwing Up? | `/cats/health/why-is-my-cat-throwing-up/` | ✍️ draft; `content/cats/health/why-is-my-cat-throwing-up.md` · GEO drafted (repo only) 2026-09-15 |
| Why Is My Cat Sneezing? | `/cats/health/why-is-my-cat-sneezing/` | ✍️ draft; `content/cats/health/why-is-my-cat-sneezing.md` · GEO drafted (repo only) 2026-09-15 |
| Signs Your Cat Needs a Vet | `/cats/health/signs-your-cat-needs-a-vet/` | ✍️ draft; `content/cats/health/signs-your-cat-needs-a-vet.md` · GEO drafted (repo only) 2026-09-15 |
| Cat Behavior hub | `/cats/behavior/` | ✍️ draft; `content/cats/behavior/index.md` |
| Why Does My Cat Meow So Much? | `/cats/behavior/why-does-my-cat-meow-so-much/` | ✍️ draft; `content/cats/behavior/why-does-my-cat-meow-so-much.md` · GEO drafted (repo only) 2026-09-15 |
| Why Does My Cat Knead? | `/cats/behavior/why-does-my-cat-knead/` | ✍️ draft; `content/cats/behavior/why-does-my-cat-knead.md` · GEO drafted (repo only) 2026-09-15 |
| Why Does My Cat Lick Me? | `/cats/behavior/why-does-my-cat-lick-me/` | ✍️ draft; `content/cats/behavior/why-does-my-cat-lick-me.md` · GEO drafted (repo only) 2026-09-15 |
| Why Does My Cat Bite Me? | `/cats/behavior/why-does-my-cat-bite-me/` | ✍️ draft; `content/cats/behavior/why-does-my-cat-bite-me.md` |
| Why Is My Cat Hiding? | `/cats/behavior/why-is-my-cat-hiding/` | ✍️ draft; `content/cats/behavior/why-is-my-cat-hiding.md` |
| Kitten Care hub | `/cats/kitten-care/` | ✍️ draft; `content/cats/kitten-care/index.md` · GEO drafted (repo only) 2026-09-15 |
| New Kitten Checklist | `/cats/kitten-care/new-kitten-checklist/` | ✍️ draft; `content/cats/kitten-care/new-kitten-checklist.md` · GEO drafted (repo only) 2026-09-15 |
| 8-Week-Old Kitten Care | `/cats/kitten-care/8-week-old-kitten-care/` | ✍️ draft; `content/cats/kitten-care/8-week-old-kitten-care.md` |
| Kitten Feeding Schedule | `/cats/kitten-care/kitten-feeding-schedule/` | ✍️ draft; `content/cats/kitten-care/kitten-feeding-schedule.md` |
| Kitten Vaccination Schedule | `/cats/kitten-care/kitten-vaccination-schedule/` | ✍️ draft; `content/cats/kitten-care/kitten-vaccination-schedule.md` · GEO drafted (repo only) 2026-09-15 |
| How to Litter Train a Kitten | `/cats/kitten-care/how-to-litter-train-a-kitten/` | ✍️ draft; `content/cats/kitten-care/how-to-litter-train-a-kitten.md` · GEO drafted (repo only) 2026-09-15 |
| Cat Products hub | `/cats/products/` | ✍️ draft; `content/cats/products/index.md` · GEO drafted (repo only) 2026-09-15 |
| Best Cat Litter | `/cats/products/best-cat-litter/` | ✍️ draft; `content/cats/products/best-cat-litter.md` · GEO drafted (repo only) 2026-09-15 |
| Best Litter Boxes | `/cats/products/best-litter-boxes/` | ✍️ draft; `content/cats/products/best-litter-boxes.md` · GEO drafted (repo only) 2026-09-15 |
| Best Cat Trees | `/cats/products/best-cat-trees/` | ✍️ draft; `content/cats/products/best-cat-trees.md` · GEO drafted (repo only) 2026-09-15 |
| Best Cat Toys | `/cats/products/best-cat-toys/` | ✍️ draft; `content/cats/products/best-cat-toys.md` · GEO drafted (repo only) 2026-09-15 |
| Best Cat Carriers | `/cats/products/best-cat-carriers/` | ✍️ draft; `content/cats/products/best-cat-carriers.md` · GEO drafted (repo only) 2026-09-15 |

## Pet Guides: `/pet-guides/` (6 pages)

| Page | URL | Status |
|---|---|---|
| Pet Guides hub | `/pet-guides/` | ✅ published; live 2026-09-10; `content/pet-guides/index.md` · GEO drafted (repo only) 2026-09-14; NOT yet live |
| Pet Insurance | `/pet-guides/pet-insurance/` | ✅ published; live 2026-09-10; `content/pet-guides/pet-insurance.md` · GEO drafted (repo only) 2026-09-14; NOT yet live |
| Pet Costs | `/pet-guides/pet-costs/` | ✅ published; live 2026-09-10; `content/pet-guides/pet-costs.md` · GEO drafted (repo only) 2026-09-14; NOT yet live |
| Pet Safety | `/pet-guides/pet-safety/` | ✅ published; live 2026-09-10; `content/pet-guides/pet-safety.md` · GEO drafted (repo only) 2026-09-14; NOT yet live |
| Pet Travel | `/pet-guides/pet-travel/` | ✅ published; live 2026-09-10; `content/pet-guides/pet-travel.md` · GEO drafted (repo only) 2026-09-14; NOT yet live |
| New Pet Checklist | `/pet-guides/new-pet-checklist/` | ✅ published; live 2026-09-10; `content/pet-guides/new-pet-checklist.md` · GEO drafted (repo only) 2026-09-14; NOT yet live |

## Tools: `/tools/` (9 pages)

| Page | URL | Status |
|---|---|---|
| Tools hub | `/tools/` | ✅ published; live 2026-09-10; `content/tools/index.md` · GEO drafted (repo only) 2026-09-14; NOT yet live |
| Dog Age Calculator | `/tools/dog-age-calculator/` | ✅ published; live 2026-09-10; `content/tools/dog-age-calculator.md` · GEO drafted (repo only) 2026-09-14; NOT yet live |
| Cat Age Calculator | `/tools/cat-age-calculator/` | ✅ published; live 2026-09-10; `content/tools/cat-age-calculator.md` · GEO drafted (repo only) 2026-09-14; NOT yet live |
| Dog Food Calculator | `/tools/dog-food-calculator/` | ⬜ (calculator not built yet) |
| Cat Food Calculator | `/tools/cat-food-calculator/` | ⬜ (calculator not built yet) |
| Pet Cost Calculator | `/tools/pet-cost-calculator/` | ⬜ (calculator not built yet) |
| Dog Breed Selector | `/tools/dog-breed-selector/` | ⬜ (tool not built yet) |
| Cat Breed Selector | `/tools/cat-breed-selector/` | ⬜ (tool not built yet) |
| Pet Name Generator | `/tools/pet-name-generator/` | ⬜ (tool not built yet) |

## Cluster-level expansion candidates

Proposed cluster by cluster as hubs are written. **Dogs proposals: ALL 14 WRITTEN 24-07-2026**: moved into the Dogs table above as "(NEW 24-07)" rows. Remaining dog-side candidates for later:

| Proposed page | Cluster | Priority |
|---|---|---|
| `/dogs/food/can-dogs-eat-bananas/apples/eggs/peanut-butter/` etc. | Food | Medium: continue the series; build `/dogs/food/can-dogs-eat/` index at 6+ pages (now at 4) |
| `/dogs/grooming/nail-trimming/`, `/bathing/`, `/deshedding/` | Grooming | Low: children for the new hub when volume justifies |
| Breed profiles: Bernese, Newfoundland, Great Dane, Rottweiler, Greyhound, Cavalier, Doberman | Breeds | Medium: unlinked mentions in the new lists; programmatic template ready |

**Cats (proposed when cat hubs are written):**

- `/cats/grooming/`: brushing, nail trims, bathing, matted fur
- `/cats/behavior/litter-box-problems/`: one of the highest-volume cat behavior topics; currently missing
- `/pet-guides/pet-adoption/`: "adopt vs. buy", shelter checklist; strong E-E-A-T topic

**Total original sitemap: 113 pages · with trust set: 119 · with Dogs expansion (24-07): 133**

## Publishing note (important)

Do NOT publish all ~119 pages in one batch on a fresh domain. Google's scaled-content
policies flag large single-shot dumps of templated content. Publish in cluster waves
(e.g., trust set + tools + one dog cluster first), 10–20 pages per wave, and let each
wave get indexed before the next. Full rollout plan comes with the final SEO strategy
deliverable.
