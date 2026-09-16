# ThePawManual: SEO Strategy & Launch Checklist

> The final deliverable of the content build: the step-by-step plan from "110 markdown
> drafts in a repo" to "ranking website." Written 2026-07-23. Companion documents:
> [`README.md`](README.md) (how this content system works, read first if you're new)
> and [`SITEMAP.md`](SITEMAP.md) (page-by-page status tracker).

---

## 0. Where things stand

**Written (110 pages):** homepage · 6 trust pages · Dogs pillar (51) · Cats pillar (46) · Pet Guides (6).
**Not written (9):** Tools pages, deliberately on hold: only 2 of 8 calculators are built, and a tool
page without a working tool is thin content a fresh domain can't afford. Decision when ready:
write tools hub + the 2 live calculator pages now; hold the other 6 until each tool ships.
**The site itself:** WordPress POC is live locally and password-protected on production with
search engines discouraged (CLAUDE.md §12), correct for now, and item #1 below to reverse at launch.

---

## 1. Phase 0: Pre-launch blockers (nothing publishes before these)

| # | Task | Owner | Notes |
|---|---|---|---|
| 0.1 | Commit `content/` to the real repo | Dev | The Downloads copy is a ZIP without `.git` |
| 0.2 | Fill trust-page placeholders | Owner | Real names on `/about/` (no fabricated credentials, no implied vet review that doesn't exist), domain email on `/contact/`, legal review of privacy/terms, affiliate program list (Amazon's sentence verbatim) |
| 0.3 | Fact-check pass on flagged items | SEO specialist | Every page's "Notes" section flags its items; the big ones: AAHA/AAFP vaccine guidelines, calorie tables, vital-sign ranges, cost/premium ranges, Bengal legality, CDC import rules, grass-eating study figures, tooth-resorption prevalence, cat-bite infection rate, Ragdoll CFA ranking, catnip non-responder share |
| 0.4 | Homepage template part | Dev | `front-page.php` has almost no indexable text: build `template-parts/home/seo-content.php` from `content/home.md` (implementation notes in that file) |
| 0.5 | RankMath install + config | SEO specialist | §3 below |
| 0.6 | GA4 + Consent | Dev/SEO | GA4 property, consent solution appropriate for US traffic (ad networks will later require a CMP) |
| 0.7 | WordPress import | Dev + me | I generate a WXR file from the markdown (all pages as **drafts**, correct parent/child hierarchy, RankMath title/description as postmeta), nobody hand-pastes 110 pages. FAQ JSON-LD blocks go in via HTML block or RankMath schema per page |
| 0.8 | Menus & footer | Dev | Main nav: Dogs · Cats · Pet Guides · Tools. Footer: About, Editorial Process, Contact, Privacy, Terms, Affiliate Disclosure + a "Pet Emergency?" link to both vet-signs pages |
| 0.9 | Visible last-updated dates | Dev | Health pages especially: theme or plugin support |

**Launch-moment flips (the day wave 1 goes live):** remove the Password Protected plugin,
untick "Discourage search engines," confirm SSL, then §2 and §3.

## 2. Google Search Console: setup and submission

1. **Verify a Domain property** for `thepawmanual.com` (DNS TXT record at the registrar): covers www/non-www and http/https in one property. Add Bing Webmaster Tools the same day (free, imports GSC settings, feeds Copilot).
2. **Submit the sitemap:** RankMath generates `https://thepawmanual.com/sitemap_index.xml`: submit that one URL. Confirm only real content is in it (§3 exclusions).
3. **URL-inspect and request indexing** for the homepage, both pillar hubs, and the trust pages on launch day: the rest follows via the sitemap.
4. **Weekly during rollout:** Pages report (indexed vs. excluded: "Discovered, not indexed" is normal early; "Crawled, not indexed" at scale means quality/thin-content review), Performance report per cluster, and Core Web Vitals once field data accumulates.

## 3. RankMath configuration (the fields the content was written for)

- **Titles/descriptions:** every page's frontmatter carries the final `seo-title` and `meta-description`: paste as-is and set the title template to `%title%` only (several titles already include "| ThePawManual"; a global suffix would double-brand them).
- **Sitemap:** include Pages only; **exclude/noindex**: search results, author archives, tag/category archives (the site uses Pages, so archives are noise), attachment pages (redirect to file).
- **Schema:** Organization + WebSite on the homepage (point Organization at `/about/`); Article or WebPage on content pages; the FAQ schema ships in each page's HTML block (answers must stay identical to visible FAQ text when editing). The calculator plugin may add WebApplication schema per CLAUDE.md: that's the plugin's job, not RankMath's.
- **Breadcrumbs:** enable: the URL hierarchy is the information architecture; show it.
- **External links:** the content's convention is `rel="nofollow"` on authority citations (AVMA, AAHA, etc.): set manually per link during import-cleanup, not via a blanket auto-nofollow (internal links must stay followed).

## 4. The publishing plan: waves, not a dump

**The rule: never publish all ~110 pages at once on a fresh domain.** Google's scaled-content
policies flag single-shot templated dumps; waves of 10–20 pages every 2–3 weeks read as a
publishing operation. Watch GSC indexing between waves; if a wave stalls at "crawled, not
indexed," pause and investigate before the next.

| Wave | When | Pages (~count) | Why this order |
|---|---|---|---|
| 1: Launch | Day 0 | Trust set (6) + homepage + `/dogs/` + `/cats/` + Pet Guides cluster (6) + tools hub & 2 live calculator pages when written (~17) | Identity, architecture, and the most-linked utility pages first |
| 2 | +2–3 wks | All 13 sub-hubs (7 dog + 6 cat) | Completes the skeleton every article hangs on |
| 3 | +2–3 wks | Dog health (7) + puppy care (5) | Highest-intent informational content; E-E-A-T flagship |
| 4 | +2–3 wks | Dog food (7) + training (6) | |
| 5 | +2–3 wks | Dog breeds (9) + behavior (4) | |
| 6 | +2–3 wks | Cat health (7) + kitten care (5) | |
| 7 | +2–3 wks | Cat food (8) + behavior (5) | |
| 8 | +2–3 wks | Cat breeds (9) | |
| 9 | Last | All products (10) | Product content **after** informational authority exists (post-Reviews-update logic), and only once `/affiliate-disclosure/` is live |

Sæt punkt på hver wave: intern-link-tjek (links to not-yet-published pages will 404: either
publish targets with their sources, or accept brief 404s within a wave's own cluster and fix at
the next wave; the wave design above keeps hub→article links mostly intra-wave).

## 5. Technical SEO checklist (one-time + recurring)

**One-time at launch:**
- [ ] SSL forced, single canonical host (https + non-www or www, pick one, redirect the other)
- [ ] Permalinks "Post name" (done per CLAUDE.md), trailing-slash consistency: all internal links in the content assume `/path/` form
- [ ] Page hierarchy correct in WP (`/dogs/breeds/golden-retriever/` requires parent pages `dogs` → `breeds`: the WXR import handles this; verify slugs post-import)
- [ ] 404-crawl the site (Screaming Frog free tier covers 500 URLs): catch typo'd internal links from import
- [ ] Rich Results test on 3–4 FAQ pages; fix schema JSON if the HTML-block route mangles quotes
- [ ] Images: WebP, lazy-load (native WP), descriptive alt text as images get added: the content shipped text-first; images are an enhancement pass
- [ ] Fonts self-hosted (CLAUDE.md §10 already plans this), no external CDNs: Core Web Vitals protection
- [ ] robots.txt: allow all by default; **decide AI crawler policy deliberately** (GPTBot, PerplexityBot, ClaudeBot etc.): the content is GEO-optimized on purpose; blocking AI crawlers would waste that investment. Recommendation: allow.
- [ ] Favicon + default OG image

**Recurring:** §8's calendar.

## 6. Link building: the realistic version

The brief asked for HARO among others; note: **HARO no longer exists** (rebranded to
Connectively in 2024, shut down December 2024). The living successors serve the same play:

1. **Journalist-request platforms:** **Source of Sources (SOS)**, **Qwoted**, **Featured**: respond as "ThePawManual, a veterinary-sourced pet care resource" to pet/lifestyle queries. Requires the [named humans](/about/) from the trust set: anonymous sites don't get quoted. 2–3 responses/week; expect single-digit hit rates and compounding value.
2. **Linkable assets already planted in the content:** printable PDFs of the two vet-signs pages, the toxin fridge-list, and the puppy/kitten checklists (flagged in their Notes). Produce them, then outreach: shelters, rescues, vet clinic resource pages, puppy-class instructors; "free printable for your adopters" is the cleanest .org-link pitch in the niche.
3. **The calculators are the real magnets:** tools earn passive links content rarely does. Prioritize building the remaining calculators over almost any other marketing task: each new tool is both a page and a link asset.
4. **Guest posts:** pet-niche blogs and local vet/groomer/trainer blogs: pitch the honest-expertise angles (the anesthesia-free dentistry takedown, the hypoallergenic myth, the apartment-breeds-to-avoid list travel well). Quality bar: real sites with real traffic; skip anything selling "guest post packages."
5. **Broken-link building:** crawl pet resource pages (shelter link lists especially, they rot) for dead links whose content ours replaces; Wayback Machine confirms what died, the outreach writes itself.
6. **Never:** paid links, PBNs, mass directories, reciprocal schemes. One AVMA-adjacent .org link outweighs fifty directory entries.

## 7. GEO: the AI-search layer (already built, needs protecting)

The content was engineered for generative engines: BLUF answers in the first 100 words,
FAQ schema mirroring visible text, myth-corrections AI loves to quote, consistent entity data.
Protect the investment: allow AI crawlers (§5), keep FAQ text and schema identical when editing,
keep the entity consistent (same site name/description on About, Organization schema, and social
profiles when created), and watch GSC + server logs for AI-referral patterns: attribution is
immature, but Perplexity/ChatGPT referrals do show in GA4 as referral traffic.

## 8. Content operations calendar

| Cadence | Task |
|---|---|
| Weekly (during rollout) | GSC Pages + Performance review; publish per wave plan |
| Monthly (post-rollout) | 2–6 new pages from the expansion table in SITEMAP.md; priority order: can-dogs-eat grapes & chocolate, cat urinary/FLUTD page, litter-box-problems, can-cats-drink-milk, grooming hubs; plus tools pages as calculators ship |
| Quarterly | Refresh pass on top-10 traffic pages (GSC data decides); site 404/broken-external-link crawl; review "PLAUSIBLE"-flagged facts against sources |
| Annually | The dated-content pass: `/pet-guides/pet-costs/` (inflation), both vaccine pages (guideline updates), insurance ranges, CDC travel rules: every page flagged "verify at publish" gets a standing annual slot. Update visible last-updated dates only when content genuinely changes |
| Continuous | Corrections inbox via `/contact/` per the editorial-process promise |

## 9. Measurement: honest expectations

- **Months 1–3:** indexing, impressions, near-zero clicks. Normal. A fresh domain earns trust slowly; long-tail question pages ("why does my dog shake") move first.
- **Months 3–6:** long-tail rankings and first meaningful clicks; hubs begin ranking for mid-tail. Watch cluster-level GSC data: a cluster that indexes but never gets impressions is a content-quality signal.
- **Months 6–12:** if the trajectory is healthy, this is where "best dog food"-class terms become realistic page-2 targets and tools accumulate links.
- **KPIs that matter:** indexed-page share, clicks per cluster, position on 10 tracked pillar terms, links earned per quarter. **Vanity to ignore:** total impressions alone, DA-style scores.
- **Monetization gates:** AdSense any time after traffic exists (low RPM), Mediavine Journey ~10k sessions/mo, full Mediavine ~50k, Raptive ~100k pageviews: the display roadmap. Affiliate activates with wave 9. The insurance page stays unmonetized (its Notes explain why).

## 10. The standing risks (so nobody relearns them)

1. **Scaled-content flag**: respect the wave plan; never bulk-publish.
2. **YMYL without humans**: the trust set's placeholders are load-bearing; an anonymous pet-health site caps its own rankings. A real veterinary reviewer, when affordable, is the single biggest E-E-A-T upgrade available.
3. **Product content without first-hand evidence**: wave 9's pages are honest buying guides; adding fake "we tested" claims later would convert the safest content into the riskiest. Real testing → fill the tested-picks slots → then it's the good kind of review content.
4. **Dated facts**: the annual calendar exists because vaccine guidelines, costs, and travel rules rot. The fact-check flags in each page's Notes are the checklist.
5. **FAQ drift**: visible FAQ text and JSON-LD must stay synchronized through every edit.
