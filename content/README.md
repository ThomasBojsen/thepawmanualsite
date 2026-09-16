# ThePawManual content system: handover & source of truth

> **Read this first.** This document explains how the `content/` workstream is built, the
> editorial rules every page follows, and what remains. It's written for whoever continues the
> work, human or Claude, without access to the conversation that produced it.
> The repo's `CLAUDE.md` (repo root) is the source of truth for the **WordPress theme/plugin
> build**; this file is the source of truth for the **content**. They deliberately don't overlap.

## What this is

Complete SEO content for **thepawmanual.com**: a US pet site (dogs + cats primary), WordPress
on Simply.com, monetized via display ads + affiliate, with free calculators as the product
(see CLAUDE.md for that architecture). Content was drafted 2026-07 by Claude for Seekings,
in American English, against a 113-page sitemap expanded to 119 with a trust set.

**Status: 110 of 119 pages drafted.** Remaining: 9 Tools pages, deliberately on hold: only 2
of 8 calculators are built, and tool pages without working tools are thin content. See
[`SEO-STRATEGY.md`](SEO-STRATEGY.md) §0 for the recommendation.

## The three control documents

| File | Role |
|---|---|
| [`SITEMAP.md`](SITEMAP.md) | Page-by-page tracker: every URL, status, file path, plus the **expansion table** of proposed-but-unwritten pages with priorities |
| [`SEO-STRATEGY.md`](SEO-STRATEGY.md) | Launch checklist, GSC setup, the **wave publishing plan** (never bulk-publish, scaled-content risk), link building, ops calendar |
| This `README.md` | How the system works + the editorial constitution |

## How the files work

- **Structure mirrors URLs:** `content/dogs/food/puppy-food.md` → `/dogs/food/puppy-food/`. Hub pages are `index.md` in their folder. Trust pages sit at `content/` root.
- **Frontmatter = RankMath fields:** `url`, `seo-title` (≤60 chars, final string, some include "| ThePawManual", so RankMath's template must be `%title%` only, no auto-suffix), `meta-description` (≤155), `status`. Char counts are annotated in comments.
- **Body = the page copy**, markdown. H1 included (first line after frontmatter).
- **FAQ section + JSON-LD:** every content page ends with 3–5 FAQs whose visible text is mirrored in a ```html code block of FAQPage schema. **These must stay synchronized through every edit**: that's a hard rule, not a preference.
- **"Notes (not page copy)" / "Expansion suggestions (not page copy)"** at the bottom of most files: implementation notes, citation lists, and **fact-check flags** for the SEO specialist. **Never publish these sections.**
- **The homepage is the exception:** `home.md`'s copy maps onto theme template parts (`front-page.php` is hardcoded, see the implementation notes inside `home.md`). Every other page is a normal WP Page.
- **Import path:** generate a WXR file from these markdown files (pages as drafts, parent/child hierarchy from the URL paths, RankMath meta as postmeta) rather than hand-pasting 110 pages. Claude can generate this on request.

## The editorial constitution (the rules every page follows, keep them)

**Format & GEO:**
1. **BLUF:** the direct answer lives in the first 2–3 sentences, often bolded, built for featured snippets and AI engines. Don't bury it in edits.
2. Scannable structure: H2/H3, tables for comparisons, bullets where they help.
3. Dense internal linking with descriptive anchors; hubs link every child; articles cross-link related clusters. The two vet-signs pages are deliberately the most-linked pages on the site.

**E-E-A-T & sourcing:**
4. Source hierarchy: AVMA, AAHA, AAFP, AAFCO, WSAVA, AVSAB, VOHC, AVDC, Cornell Feline Health Center, Merck Veterinary Manual, ASPCA APCC, peer-reviewed literature. External authority links get `rel="nofollow"`. Marketing claims are never sources.
5. Every health/nutrition page carries a **veterinary disclaimer** and escalation guidance ("when to call the vet" is a feature, not fine print).
6. **No medication brand names, no dosages**: class-level mentions only ("modern prescription anti-itch medication"). This is deliberate scope-of-practice discipline.
7. **No fabricated experience:** product pages are honest *buying guides* (spec criteria, design classes like "front-clip harness") with empty "tested picks" slots reserved for when real first-hand testing exists. Adding fake "we tested" claims is the highest-risk edit anyone could make (Google Reviews-update logic).
8. **No fabricated humans:** the trust pages never invent names, credentials, or reviewers. As of 2026-09, `about.md`, `contact.md`, `affiliate-disclosure.md`, `privacy-policy.md`, `terms.md`, and `editorial-process.md` all carry real details and are published/live. None of them hold `[PLACEHOLDERS]` any more. Never imply veterinary review that doesn't happen.

**Editorial stances (consistent site-wide, don't contradict them):**
9. Training content is **positive-reinforcement only** per AVSAB: no shock/prong/choke collars, no alpha/dominance framing (explicitly debunked on the puppy-training page). Declawing is condemned, per AAFP. Surgical debarking/devocalization is discouraged and permitted only as a licensed veterinarian's last resort after behavior modification has failed, per AVMA policy: it is not a blanket condemnation, so don't "fix" it back to that framing. Scottish Fold deliberately not recommended (cartilage disorder).
10. **Punishment doesn't work on cats**; environment design does. Recurring line, keep it.
11. **"Behavior change = pain until proven otherwise"**: the bridge rule between behavior and health clusters, both species.
12. **Species-true contrasts** are load-bearing: cats can never fast (hepatic lipidosis, 24-hour rule), open-mouth breathing in cats = emergency, male-cat urinary straining = THE cat emergency, never pull visible string; dogs get the bland-diet/food-rest protocol cats don't. Don't cross-contaminate the species clusters.
13. **Myth-corrections are the GEO strategy:** hypoallergenic pets don't exist (Fel d 1 / Can f 1), kibble doesn't clean teeth, "cats just vomit" is false, hairball-hacking-without-hairballs is often asthma, anesthesia-free dentals are cosmetic, grain-free isn't better, the 7-dog-years rule is wrong. These direct corrections are what AI engines quote. Protect them in edits.
14. Affiliate transparency: product pages open with a disclosure line linking `/affiliate-disclosure/` (FTC: disclosure near the links, not just a footer page). The **insurance page takes no affiliate money, ever**: its Notes explain why.

**GEO / AI-answer-engine formatting (added 2026-09-14):**

15. **Answer-first paragraphs are what get quoted.** Under every H2, open with a 1–3 sentence paragraph that gives the verdict or the number first, phrased to stand alone as a quote. An AI answer engine lifts a paragraph out of its surrounding context to answer a user's question: a section that opens "It depends on several factors" hands it nothing worth citing, while one that opens with the number or the verdict is the one that gets quoted and attributed. This is judgment applied to a section, not a rule forced on it: a heading **may be rewritten** into a real search-style question where that reads naturally, never **must**, and a punchy opening is never worth inventing a number that isn't already on the page (see "when a claim needs a number you can't verify," below). Keep headings to roughly 70 characters or under: they double as entries in the sticky "In This Article" TOC, a narrow sidebar rail that wraps badly past that length.
16. **A visible FAQ answer and its JSON-LD twin are one fact written twice: keep them identical.** `acceptedAnswer.text` must match the visible answer paragraph word for word. Editing one without the other doesn't fail the build: it silently breaks the schema, since Google requires the visible text and the structured data to match. This has already happened once on this project, and a 2026-09-15 re-measurement (item 2 below) found it open on 118 of 121 pages, not the 25 previously quoted. Treat every FAQ edit as two edits, not one, and don't call a wave launch-ready until both copies agree everywhere in it.
17. **This FAQPage schema will not earn rich results here: that isn't why it's there.** Google restricted FAQPage rich snippets (the accordion/star treatment in search results) to government and health-authority sites in 2023, so this markup will not produce rich results for a site like this one, full stop. Don't go looking for them in Search Console. It's on every page anyway for two reasons that have nothing to do with rich results: AI answer engines (ChatGPT, Perplexity, Google AI Overviews) read FAQPage JSON-LD directly, and the answer-first visible structure it forces (rule 15) is what earns ordinary featured snippets and AI citations, independent of the schema. A future writer seeing zero FAQ rich results in GSC isn't looking at a bug.

**GEO rewrite guardrails (added 2026-09-14, from the wave-1 hedge-compression fix):** rules 15–17
told the first GEO pass what to build; this pass is what its own review caught it breaking while
building it. Both sets stand together.

18. **Never strip a hedge to make an answer punchier.** "An estimated," "usually," "most," and "from cargo" are load-bearing qualifiers, not filler a tighter sentence can drop: removing one changes what the sentence claims, not just how it sounds. The first GEO pass did exactly that: it dropped "An estimated" and "usually" from an APOP obesity figure, turning a survey estimate into a stated fact and a tendency into "the leading driver"; it dropped "from cargo" from an airline pet-travel restriction, inverting a cargo-hold-specific rule into a blanket flying ban that contradicted the same page's own copy elsewhere; and it invented the unsourced superlative "the most dangerous food for dogs" on the toxin page. On YMYL-adjacent pet-health copy, a compressed hedge is a false claim, not a style choice, and because rule 16 mirrors every FAQ answer into machine-readable JSON-LD, a compressed hedge there is a false claim a machine reads as fact.
19. **An answer-first paragraph must stand alone when quoted with its links stripped.** A paragraph whose content amounts to "the answer is on this other page" is a link menu, not an answer, and a link menu is exactly what an AI engine will lift and quote, minus the link, pointing at nothing.
20. **Don't add an H2-derived question to a page's FAQPage if a semantically equivalent question is already in it.** Check the page's existing FAQ list before promoting a heading into a new entry. Two entries answering the same underlying question don't reinforce each other: they dilute the schema and read as padding to anything that parses it, human or machine.
21. **Don't bolt an answer-first paragraph onto a section that already opens with its answer.** Check before adding one: a section that already leads with its verdict doesn't need a second opening line stacked on top of the first. If you find you've already done this, delete the redundant paragraph rather than try to edit it into something new.
22. **Never assert in the body or the schema anything the page's own Notes section flags as unverified.** A fact-check flag in Notes exists precisely so a rewrite pass doesn't accidentally promote a hedge into a claim (rule 18): treat every flag there as binding on the FAQPage twin as much as on the visible copy it was written against, not just on the original draft.
23. **HOUSE STYLE: no em dashes (U+2014) anywhere in published copy or user-facing UI strings.** Use a colon, a comma, parentheses, or a full stop, whichever the sentence actually calls for. En dashes (U+2013) are kept and required in numeric and date ranges ($1,000–$2,000, 10–16 years, 4–6 weeks); removing one corrupts a price, a dose, or a timeline. This is the published house style: the live site contains zero em dashes, every one was replaced by hand at publication, and the repo had drifted to roughly 2,779 of them across 130 files before the 2026-09-15 sweep, so every future wave would have re-introduced them. The rule governs published page copy and user-facing strings: of a page's frontmatter, that means only `seo-title` and `meta-description` (they publish verbatim as the page's RankMath title/description). Every other frontmatter field (`page`, `status`, and any inline comment) never reaches a reader and may carry one freely, and several already do, as internal shorthand like "(YMYL: AAHA-sourced)". PHP/JS code comments and internal planning docs are exempt too, since nobody reads them on the site. Before shipping a wave, grep `content/` for the character and expect zero hits everywhere except inside a `(not page copy)` section (`## Notes`, `## Expansion suggestions`, `## Implementation notes`: internal fact-check flags and citation lists that never publish) or a non-publishing frontmatter field; a hit in either is expected and correct. All H1s are now clean too, so a hit on an H1 line is a defect like any other, not an exemption. A check that respects all of this, including the frontmatter boundary: `grep -rlP '\x{2014}' content/ | while read -r f; do awk '/^## .*\(not page copy\)/{skip=1;next} /^## /{skip=0} /^---$/{fm=!fm;next} fm && !/^(seo-title|meta-description):/{next} !skip' "$f" | grep -qP '\x{2014}' && echo "$f"; done` lists only files with a genuine, unexempted hit.
24. **A question heading is only worth the trade when the question is what people actually search and the section's thesis survives the rewrite.** Turning an H2 into a question form can quietly drop the argument the original heading was making, not just its phrasing: "Catch illness early, because your cat won't tell you" carried the hub's thesis; "How do I know if my cat is sick?" doesn't: it's a narrower, blander question that answers itself without making the point the section exists to make. Watch for the same failure in permission-vs-mandate phrasing, too: rewriting a heading that states what's *allowed* ("Cats May Free-Feed if They Self-Regulate," "It's Fine to Skip a Meal Once") into a question ("Should You Free-Feed Your Cat?", "Do You Need to Feed on a Schedule?") can quietly turn permission into a decision the reader now has to make, or into an instruction they're expected to follow, changing what the page is telling the reader to do, not just how it's asking it.

**Style:** American English, expert-but-warm with dry humor, complete sentences, no keyword
stuffing, honest about drawbacks (the French Bulldog and Bengal profiles are the house style
for "loving a breed honestly"). **No em dashes (U+2014) in body copy** (added 2026-09; see rule
23 above): a house-style pass already applied this live, on the 18 wave-1 pages, before this
document was updated to say so. New copy should be written this way from the start rather than
fixed after the fact. Use a colon for "clause: elaboration," a semicolon for two related
independent clauses, a comma before a coordinating conjunction ("but"/"and"/"or"), or parentheses
for a true aside, whichever the sentence actually calls for, not a mechanical find-and-replace.
An en dash for a number range ("10–15 years") is unaffected; that's a different character doing a
different job, including as a "not applicable" placeholder in a table cell.

## What remains (in order)

> This list is current as of 2026-09-14. Earlier versions of this section described pre-launch
> work (committing `content/` to a real repo, filling trust-page placeholders, running the WXR
> import) as still outstanding. **All of that is done:** the repo has been git-managed since,
> the six trust pages carry real details and are live (rule 8 above; CLAUDE.md §13), and wave 1
> (18 URLs) imported and went live 2026-09-10 (see `docs/LAUNCH-RUNBOOK.md` and CLAUDE.md §11).
> What follows is what genuinely remains, not that original pre-launch list.

1. **Fact-check pass (still blocking).** Nothing changed here: each page's "Notes (not page
   copy)" section lists its own flags, and publishing an unverified veterinary or nutrition
   claim is the one mistake in this project that's genuinely expensive to undo (CLAUDE.md's
   "two things that block launch"). This gates every page, wave 1's live six health/trust pages
   included, not just unpublished ones.
2. **FAQ visible-text / JSON-LD sync (blocking wave 2).** Re-measured 2026-09-15 by parsing
   every FAQPage block in `content/` (all directories), stripping markdown from each visible
   answer paragraph, and comparing it word for word against its `acceptedAnswer.text`: the real
   count is **118 of the 121 pages carrying FAQ schema** (roughly 495 of 574 Question entities),
   far above the 25 pages previously quoted secondhand. 23 of those entities have no visible
   heading matching the JSON `name` at all, meaning the question itself was reworded on one
   side only (for example `content/cats/breeds/siamese.md`, where the visible heading asks
   "Why do Siamese cats have dark faces, ears, paws and tails?" but the JSON-LD `name` asks
   "Why do Siamese cats have dark points?"). Only 3 of the 121 pages have zero drift by a
   strict word-for-word comparator, though a second independent run of the same method
   disagreed with the first on exactly which 3 (the two agreed on the totals to within a
   handful of entities, so the discrepancy is at the margin, not in the headline number). The
   likely cause: several already-reviewed pages deliberately truncate `acceptedAnswer.text` by
   dropping a trailing, link-only sentence from the visible paragraph (see
   `content/cats/food/senior-cat-food.md`'s "At what age is a cat considered senior?" pair for
   a correct example of this pattern), which a strict comparator flags as drift even though
   it's an accepted, deliberate convention. A precise clean-page list needs that convention
   decided and encoded into the comparator first (tolerate one trailing sentence being
   dropped, or don't) before it can be trusted at single-page precision. Fix every drifted
   entity before generating wave 2 regardless of the exact clean-page list: a mismatch here
   isn't cosmetic, it breaks the schema Google reads.
3. **Legal review of `content/terms.md`.** It currently governs the site under Danish law on a
   site aimed at US readers, worth a review before launch relies on it, though it's an
   editorial follow-up now, not an unfilled-placeholder publish-blocker the way it used to be.
4. **GEO rewrite rollout.** 11 pages have the answer-first / FAQ-schema rewrite (rules 15–17)
   applied in `content/` only: `SITEMAP.md` marks them "GEO drafted (repo only)." It hasn't
   reached the live pages yet; that requires hand-pasting each page's body into WP admin (Block
   editor → Code editor), page by page, diffing against live first per `SITEMAP.md`'s
   "Live-vs-repo drift" note so an em-dash-to-colon house-style pass already applied live isn't
   silently reverted. The remaining ~100 unpublished pages haven't had the GEO pass at all yet.
   Apply it before treating any of them as wave-ready.
5. **Wave 2 and onward.** 109 of 127 drafted pages are written but not yet published. Publish
   them in further waves per `SEO-STRATEGY.md`'s wave plan, never in bulk, and only once each
   prior wave has had time to get indexed (checked in Search Console, not assumed, see the
   indexing note at the top of `SITEMAP.md`).
6. **The remaining Tools pages.** 6 of 8 tools pages are still on hold behind their calculators.
   Write each one once its calculator ships, following the pairing order already noted
   (feeding guides ↔ food calculators, cost guide ↔ cost calculator).
7. **Ongoing sync discipline.** Whenever a live page is edited directly in WordPress (a style
   pass, a correction, anything done in WP admin rather than through `tools/wxr/`), bring the
   same edit back into the matching `content/*.md` file before the session ends. `content/` is
   not guaranteed newer than live for any already-published page (see `SITEMAP.md`'s
   "Live-vs-repo drift" note and CLAUDE.md §13's matching standing rule); skipping this step is
   how a future hand-paste silently overwrites a live fix with stale repo copy.
8. **Em dash sweep (rule 23 above).** Executed 2026-09-15 across the internal docs (this file,
   `SITEMAP.md`, `SEO-STRATEGY.md`) and `CLAUDE.md`, plus the homepage template strings. The
   same standing rule now governs every content page too: run its grep check before calling any
   wave launch-ready, since a page swept once can still pick up a fresh em dash on a later edit.

## If you're a Claude continuing this

Match the existing pages before writing new ones: read one hub + one article from the relevant
cluster first, reuse the frontmatter format and section pattern, run the char-count check on
titles/metas, mirror FAQ text into the JSON-LD exactly, put implementation notes under "Notes
(not page copy)", update SITEMAP.md's status column after writing, and respect the constitution
above, especially rules 6–8 and 12–13. When a claim needs a number you can't verify, hedge it
in copy and flag it in Notes rather than inventing precision. The expansion table in SITEMAP.md
is the backlog; SEO-STRATEGY §8 is the cadence.
