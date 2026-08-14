# ThePawManual content system — handover & source of truth

> **Read this first.** This document explains how the `content/` workstream is built, the
> editorial rules every page follows, and what remains. It's written for whoever continues the
> work — human or Claude — without access to the conversation that produced it.
> The repo's `CLAUDE.md` (repo root) is the source of truth for the **WordPress theme/plugin
> build**; this file is the source of truth for the **content**. They deliberately don't overlap.

## What this is

Complete SEO content for **thepawmanual.com** — a US pet site (dogs + cats primary), WordPress
on Simply.com, monetized via display ads + affiliate, with free calculators as the product
(see CLAUDE.md for that architecture). Content was drafted 2026-07 by Claude for Seekings,
in American English, against a 113-page sitemap expanded to 119 with a trust set.

**Status: 110 of 119 pages drafted.** Remaining: 9 Tools pages, deliberately on hold — only 2
of 8 calculators are built, and tool pages without working tools are thin content. See
[`SEO-STRATEGY.md`](SEO-STRATEGY.md) §0 for the recommendation.

## The three control documents

| File | Role |
|---|---|
| [`SITEMAP.md`](SITEMAP.md) | Page-by-page tracker: every URL, status, file path, plus the **expansion table** of proposed-but-unwritten pages with priorities |
| [`SEO-STRATEGY.md`](SEO-STRATEGY.md) | Launch checklist, GSC setup, the **wave publishing plan** (never bulk-publish — scaled-content risk), link building, ops calendar |
| This `README.md` | How the system works + the editorial constitution |

## How the files work

- **Structure mirrors URLs:** `content/dogs/food/puppy-food.md` → `/dogs/food/puppy-food/`. Hub pages are `index.md` in their folder. Trust pages sit at `content/` root.
- **Frontmatter = RankMath fields:** `url`, `seo-title` (≤60 chars, final string — some include "| ThePawManual", so RankMath's template must be `%title%` only, no auto-suffix), `meta-description` (≤155), `status`. Char counts are annotated in comments.
- **Body = the page copy**, markdown. H1 included (first line after frontmatter).
- **FAQ section + JSON-LD:** every content page ends with 3–5 FAQs whose visible text is mirrored in a ```html code block of FAQPage schema. **These must stay synchronized through every edit** — that's a hard rule, not a preference.
- **"Notes (not page copy)" / "Expansion suggestions (not page copy)"** at the bottom of most files: implementation notes, citation lists, and **fact-check flags** for the SEO specialist. **Never publish these sections.**
- **The homepage is the exception:** `home.md`'s copy maps onto theme template parts (`front-page.php` is hardcoded — see the implementation notes inside `home.md`). Every other page is a normal WP Page.
- **Import path:** generate a WXR file from these markdown files (pages as drafts, parent/child hierarchy from the URL paths, RankMath meta as postmeta) rather than hand-pasting 110 pages. Claude can generate this on request.

## The editorial constitution (the rules every page follows — keep them)

**Format & GEO:**
1. **BLUF:** the direct answer lives in the first 2–3 sentences, often bolded — built for featured snippets and AI engines. Don't bury it in edits.
2. Scannable structure: H2/H3, tables for comparisons, bullets where they help.
3. Dense internal linking with descriptive anchors; hubs link every child; articles cross-link related clusters. The two vet-signs pages are deliberately the most-linked pages on the site.

**E-E-A-T & sourcing:**
4. Source hierarchy: AVMA, AAHA, AAFP, AAFCO, WSAVA, AVSAB, VOHC, AVDC, Cornell Feline Health Center, Merck Veterinary Manual, ASPCA APCC, peer-reviewed literature. External authority links get `rel="nofollow"`. Marketing claims are never sources.
5. Every health/nutrition page carries a **veterinary disclaimer** and escalation guidance ("when to call the vet" is a feature, not fine print).
6. **No medication brand names, no dosages** — class-level mentions only ("modern prescription anti-itch medication"). This is deliberate scope-of-practice discipline.
7. **No fabricated experience:** product pages are honest *buying guides* (spec criteria, design classes like "front-clip harness") with empty "tested picks" slots reserved for when real first-hand testing exists. Adding fake "we tested" claims is the highest-risk edit anyone could make (Google Reviews-update logic).
8. **No fabricated humans:** the trust pages (`about.md`, `editorial-process.md`, `contact.md`) contain `[PLACEHOLDERS]` requiring real names/emails — publish-blockers. Never imply veterinary review that doesn't happen.

**Editorial stances (consistent site-wide — don't contradict them):**
9. Training content is **positive-reinforcement only** per AVSAB: no shock/prong/choke collars, no alpha/dominance framing (explicitly debunked on the puppy-training page), debarking and declawing condemned (AVMA/AAFP), Scottish Fold deliberately not recommended (cartilage disorder).
10. **Punishment doesn't work on cats** — environment design does. Recurring line, keep it.
11. **"Behavior change = pain until proven otherwise"** — the bridge rule between behavior and health clusters, both species.
12. **Species-true contrasts** are load-bearing: cats can never fast (hepatic lipidosis, 24-hour rule), open-mouth breathing in cats = emergency, male-cat urinary straining = THE cat emergency, never pull visible string; dogs get the bland-diet/food-rest protocol cats don't. Don't cross-contaminate the species clusters.
13. **Myth-corrections are the GEO strategy:** hypoallergenic pets don't exist (Fel d 1 / Can f 1), kibble doesn't clean teeth, "cats just vomit" is false, hairball-hacking-without-hairballs is often asthma, anesthesia-free dentals are cosmetic, grain-free isn't better, the 7-dog-years rule is wrong. These direct corrections are what AI engines quote — protect them in edits.
14. Affiliate transparency: product pages open with a disclosure line linking `/affiliate-disclosure/` (FTC: disclosure near the links, not just a footer page). The **insurance page takes no affiliate money, ever** — its Notes explain why.

**Style:** American English, expert-but-warm with dry humor, complete sentences, no keyword
stuffing, honest about drawbacks (the French Bulldog and Bengal profiles are the house style
for "loving a breed honestly").

## What remains (in order)

1. **Owner tasks:** commit `content/` to the real repo (this copy came from a ZIP, no `.git`) · fill trust placeholders · legal review of privacy/terms · fact-check pass (each page's Notes lists its flags; SEO-STRATEGY §1 aggregates the big ones).
2. **Dev tasks:** homepage `seo-content.php` template part (spec in `home.md`) · WXR import · menus/footer · last-updated dates · RankMath per SEO-STRATEGY §3.
3. **Tools decision:** write tools hub + 2 live calculator pages (dog/cat age); hold the other 6 until each calculator ships. Content-tool pairs already exist in the copy (feeding guides ↔ food calculators, cost guide ↔ cost calculator) — build calculators in that order.
4. **Launch:** follow SEO-STRATEGY §1–§4 (the wave plan). Then the ops calendar (§8) and the expansion table in SITEMAP.md take over.

## If you're a Claude continuing this

Match the existing pages before writing new ones: read one hub + one article from the relevant
cluster first, reuse the frontmatter format and section pattern, run the char-count check on
titles/metas, mirror FAQ text into the JSON-LD exactly, put implementation notes under "Notes
(not page copy)", update SITEMAP.md's status column after writing, and respect the constitution
above — especially rules 6–8 and 12–13. When a claim needs a number you can't verify, hedge it
in copy and flag it in Notes rather than inventing precision. The expansion table in SITEMAP.md
is the backlog; SEO-STRATEGY §8 is the cadence.
