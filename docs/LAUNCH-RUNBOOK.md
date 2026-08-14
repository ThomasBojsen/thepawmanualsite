# ThePawManual — Launch Runbook

> **What this is:** an executable, top-to-bottom checklist for taking ThePawManual from
> "password-protected preview" to "publicly live, wave 1 indexed, Google Search Console
> connected." It operationalizes [`content/SEO-STRATEGY.md`](../content/SEO-STRATEGY.md) —
> read that document for the *why*; read this one on launch day for the *what, in what order*.
> Companion docs: [`content/README.md`](../content/README.md) (content system),
> [`content/SITEMAP.md`](../content/SITEMAP.md) (every URL, status), and
> [`CLAUDE.md`](../CLAUDE.md) §8 (LocalWP) and §12 (the simply.com deploy procedure this
> runbook picks up from).
>
> Nothing in this file invents facts. Every checklist item cites its source in
> SEO-STRATEGY.md; where this repo hasn't caught up to the strategy yet, that's called out
> explicitly rather than assumed.

---

## 1. Current state → target state

**Current state (as of this writing):**
- The WordPress POC (theme + `pawmanual-calculators` plugin) runs locally in LocalWP and is
  deployed to `thepawmanual.com` on simply.com (CLAUDE.md §12).
- The production site is **password-protected** (the "Password Protected" plugin) and has
  **"Discourage search engines" ticked** in Settings → Reading.
- 110 of 119 planned content pages are drafted in `content/` as markdown; the 9 Tools pages
  are on hold except the tools hub + the 2 pages for calculators that already exist
  (dog-age, cat-age) — see SEO-STRATEGY.md §0.

**Target state (end of this runbook):**
- Wave 1 — the 17 WXR-imported Pages listed in §2 below, plus the homepage (already live via
  `front-page.php`, never a WXR Page — see the note in §2) — is published and publicly
  reachable, with the password gate and the noindex flag both removed.
- Google Search Console has a verified **Domain property** for `thepawmanual.com`, the
  RankMath sitemap is submitted, and the wave-1 pages have been individually requested for
  indexing.
- Bing Webmaster Tools is connected the same day.

**The one rule that matters most here: the password gate and "Discourage search engines"
come off together, at one single moment — not gradually, not one before the other.**
Removing the noindex flag while the password gate is still up is pointless (nothing is
reachable to index). Removing the password gate while noindex is still ticked invites Google
to crawl a site that is explicitly telling it not to be indexed, which can delay indexing
after you *do* flip the flag. Both flips happen in the same launch-day window, after
everything in §3 is checked off — see SEO-STRATEGY.md §1, "Launch-moment flips."

---

## 2. Wave 1 — the exact page list

Derived from SEO-STRATEGY.md §4 (wave 1 definition) and cross-checked against
`content/SITEMAP.md` and `tools/wxr/waves.json` (the generator's own wave-1 URL list —
running `node tools/wxr/generate.js --wave 1` prints "Wrote 17 page(s)", matching this list
exactly). **17 pages import via WXR**, enumerated:

**Trust set (6)** — SITEMAP.md "Trust & E-E-A-T pages":
1. `/about/`
2. `/editorial-process/`
3. `/contact/`
4. `/privacy-policy/`
5. `/terms/`
6. `/affiliate-disclosure/`

**Pillar hubs (2):**
7. `/dogs/`
8. `/cats/`

**Pet Guides cluster (6)** — SITEMAP.md "Pet Guides":
9. `/pet-guides/`
10. `/pet-guides/pet-insurance/`
11. `/pet-guides/pet-costs/`
12. `/pet-guides/pet-safety/`
13. `/pet-guides/pet-travel/`
14. `/pet-guides/new-pet-checklist/`

**Tools (3)** — the hub plus the two calculators that are actually built (SITEMAP.md "Tools"
row notes "calculator built ✓ — page needs SEO copy" for both):
15. `/tools/`
16. `/tools/dog-age-calculator/`
17. `/tools/cat-age-calculator/`

**Plus the homepage (`/`) — not one of the 17.** It's a theme template (`front-page.php`),
never a WordPress Page and never part of the WXR import — see `content/README.md` and
`tools/wxr/README.md`'s "Why isn't the homepage in here?" section. It's already live the
moment the theme is active, so there's no "publish" action for it in §3 or §7; it just needs
the password gate removed like everything else. **Don't go looking for an 18th draft Page in
WP Admin after importing wave 1** — 17 is the correct count, matching both
`content/SEO-STRATEGY.md` §4's "~17" and the generator's own printed output.

**Not in wave 1:** the other 6 tools pages (calculators not built yet), all 13 sub-hubs, and
every article page — those arrive in waves 2–9 (§7).

---

## 3. Pre-launch blockers

Nothing in §2 goes live before every box below is checked. Source: SEO-STRATEGY.md §1.

- [ ] **0.1 — Commit `content/` to the real repo.** *Owner: Dev.* Done when: `content/` is
  tracked in `git log` on the actual project repo, not living only in a ZIP export.
- [ ] **0.2 — Fill trust-page placeholders.** *Owner: Owner.* Done when: `content/about.md`
  has a real founder/team section (real name, real background — no fabricated credentials,
  no implied veterinary review that isn't happening); `content/contact.md` has a real
  domain email (not a placeholder like `hello@thepawmanual.com` left unconfirmed); privacy
  and terms have had a legal review pass; the affiliate program list is confirmed and
  Amazon's required disclosure sentence is reproduced verbatim. **See the callout below —
  this can block launch on its own.**
- [ ] **0.3 — Fact-check pass on flagged items.** *Owner: SEO specialist.* Done when: every
  item flagged in each page's "Notes (not page copy)" section has been checked against a
  primary source. The big ones named in the strategy: AAHA/AAFP vaccine guidelines, calorie
  tables, vital-sign ranges, cost/insurance premium ranges, Bengal legality by state, CDC
  import rules, the grass-eating study figures, tooth-resorption prevalence, cat-bite
  infection rate, the Ragdoll CFA popularity ranking, and the catnip non-responder share.
  **See the callout below — this can block launch on its own.**
- [ ] **0.4 — Homepage template part.** *Owner: Dev.* Done when:
  `template-parts/home/seo-content.php` exists and renders the copy from `content/home.md`
  (implementation notes are inside that file) — `front-page.php` currently has almost no
  indexable text.
- [ ] **0.5 — RankMath install + configuration.** *Owner: SEO specialist.* Done when: every
  field in §5 below is set. RankMath must be installed before pages are imported so
  postmeta (title/description) attaches correctly.
- [ ] **0.6 — GA4 + consent.** *Owner: Dev/SEO.* Done when: a GA4 property is receiving
  data and a consent solution appropriate for US traffic is live (a CMP will be required
  once ad networks are added, per SEO-STRATEGY.md §9's monetization roadmap).
- [ ] **0.7 — WordPress import.** *Owner: Dev (+ the WXR generator — see §8).* Done when:
  a WXR file has been generated from the markdown, imported with every page as a **draft**,
  correct parent/child hierarchy, and RankMath title/description set from frontmatter as
  postmeta. FAQ JSON-LD blocks are placed via HTML block or RankMath schema per page.
  Nobody hand-pastes 110 pages.
- [ ] **0.8 — Menus & footer.** *Owner: Dev.* Done when: the main nav reads Dogs · Cats ·
  Pet Guides · Tools, and the footer has About, Editorial Process, Contact, Privacy, Terms,
  Affiliate Disclosure, plus a "Pet Emergency?" link to both vet-signs pages
  (`/dogs/health/signs-your-dog-needs-a-vet/` and `/cats/health/signs-your-cat-needs-a-vet/`).
- [ ] **0.9 — Visible last-updated dates.** *Owner: Dev.* Done when: health pages especially
  display a visible last-updated date, via theme or plugin support.

### The two that can genuinely stop a launch

Everything above is dev-fixable on a normal engineering timeline. **These two are not —
they need a human decision, and no amount of developer effort substitutes for them:**

- **0.2, the trust-page placeholders.** An anonymous site publishing pet-health content caps
  its own rankings — Google treats pet health/nutrition as YMYL-adjacent, and E-E-A-T
  guidelines explicitly weight identifiable authorship. Shipping `/about/` and `/contact/`
  with bracketed placeholders instead of a real name and a real domain email isn't a cosmetic
  gap; it's the single biggest lever the strategy identifies (SEO-STRATEGY.md §10, risk #2:
  "a real veterinary reviewer, when affordable, is the single biggest E-E-A-T upgrade
  available"). Wait for the owner, not the dev team, to close this one.
- **0.3, the fact-check pass.** Publishing unverified veterinary claims — vaccine schedules,
  calorie tables, cost ranges, medical prevalence figures — is the one mistake on this whole
  launch that is expensive to undo: it's the difference between "conservative estimate,
  correct" and "a pet site told someone wrong information about their animal's health," and
  once a page has been indexed and shared, a quiet correction doesn't undo the exposure. This
  is a domain-expert review, not a proofread — don't let it be scheduled like one.

**Launch-moment flips (same day, same window, after every box above is checked):** remove
the Password Protected plugin, untick "Discourage search engines," confirm SSL, then proceed
to §4 and §5.

---

## 4. Google Search Console — literal steps

Source: SEO-STRATEGY.md §2.

1. **Verify a Domain property for `thepawmanual.com`.**
   - In GSC, choose **Domain property** (not URL-prefix) and enter `thepawmanual.com`.
   - GSC gives you a DNS TXT record. Add it at the domain **registrar** (not simply.com's
     hosting panel, unless the registrar and host are the same account).
   - Wait for DNS propagation, then click Verify in GSC.
   - **Why Domain, not URL-prefix:** a Domain property automatically covers `www` and
     non-`www`, and `http` and `https`, in a single property. A URL-prefix property only
     covers the exact URL form you enter — you'd need up to four separate properties to get
     the same coverage. One DNS record beats four verification methods.
2. **Add Bing Webmaster Tools the same day.** It's free, it can import settings directly from
   a verified GSC property, and it feeds Microsoft Copilot's answer engine — relevant given
   the GEO strategy in §6 below.
3. **Submit the sitemap.**
   - RankMath auto-generates `https://thepawmanual.com/sitemap_index.xml` once configured
     (§5). Submit that single URL in GSC → Sitemaps.
   - **Before submitting, open the sitemap URL and confirm it lists only real, published
     wave-1 content** — no placeholder pages, no draft leakage, no excluded content types
     (search results, author archives, tag/category archives, attachment pages — see §5's
     sitemap exclusions). Submitting a sitemap full of thin or placeholder pages on a fresh
     domain works against you.
4. **URL-inspect and Request Indexing, by hand, on launch day, for:**
   - the homepage (`/`)
   - `/dogs/`
   - `/cats/`
   - all 6 trust pages (`/about/`, `/editorial-process/`, `/contact/`, `/privacy-policy/`,
     `/terms/`, `/affiliate-disclosure/`)
   - Everything else in wave 1 follows via the submitted sitemap — you don't need to
     hand-request all 17 WXR pages plus the homepage, just these 9 highest-priority ones.
5. **Weekly during rollout, check the Pages report** (Search → Indexing → Pages):
   - **"Discovered, not indexed"** is normal in the first few weeks on a fresh domain. Don't
     react to it.
   - **"Crawled, not indexed" at scale** is a quality signal, not a crawl-budget problem —
     it means Google looked at the pages and decided not to index them. If a meaningful
     share of a wave lands here, pause the next wave and investigate content quality before
     publishing more (this is the wave-calendar go/no-go gate in §7).
   - Also check the Performance report per content cluster, and Core Web Vitals once enough
     field data accumulates.

### Honest expectation-setting (SEO-STRATEGY.md §9)

Set this expectation with the owner *before* launch, not three months in:

- **Months 1–3:** indexing and impressions, near-zero clicks. This is normal — a fresh
  domain earns trust slowly, and long-tail question pages ("why does my dog shake") tend to
  move first.
- **Months 3–6:** long-tail rankings and the first meaningful clicks; hubs start ranking for
  mid-tail terms. A cluster that indexes but never gets impressions is a content-quality
  signal worth investigating, not something to wait out.
- **Months 6–12:** if the trajectory is healthy, "best dog food"-class head terms become
  realistic page-2 targets, and the calculators start accumulating links.
- **KPIs that matter:** indexed-page share, clicks per cluster, position on the 10 tracked
  pillar terms, links earned per quarter.
- **Vanity metrics to ignore:** raw total impressions on their own, DA-style third-party
  "authority" scores.

If month 2 looks like "nothing is happening," that's the plan working, not failing.

---

## 5. RankMath configuration

Source: SEO-STRATEGY.md §3. Set these before the WXR import (0.7) so postmeta attaches
correctly, and don't deviate from any of these without checking why the content was written
this way first.

- **Title template: `%title%` only — no site-name suffix.** Every page's frontmatter already
  carries its final `seo-title`, and several of those titles already end in
  "| ThePawManual." If RankMath's global title template *also* appends a site-name suffix,
  those titles double-brand ("Dog Age Calculator | ThePawManual | ThePawManual"). Paste each
  page's `seo-title` and `meta-description` from frontmatter as-is; don't let a template
  override them.
- **Sitemap: Pages only.** Explicitly exclude/noindex: search results, author archives,
  tag/category archives (the site is built entirely from WP Pages, so these archive types
  are pure noise here), and attachment pages (they should redirect to the file, not appear
  in the sitemap).
- **Schema:**
  - **Organization + WebSite schema on the homepage**, with the Organization entity pointing
    at `/about/`.
  - **Article or WebPage schema** on content pages.
  - **FAQ schema** ships per-page in an HTML block already in the content — when editing a
    page's FAQ section, the visible FAQ text and the JSON-LD must be edited together; they
    are a hard-sync requirement, not a nice-to-have (content/README.md rule 3 in the
    editorial constitution).
  - The calculator plugin may add its own **WebApplication schema** per CLAUDE.md — that's
    the plugin's responsibility, not RankMath's; don't duplicate it here.
- **Breadcrumbs: enabled.** The URL hierarchy *is* the site's information architecture —
  show it.
- **External links: `rel="nofollow"` applied per-link on authority citations** (AVMA, AAHA,
  AAFP, AAFCO, WSAVA, etc.) — set manually per link during import-cleanup.
  **Do not turn on a blanket auto-nofollow setting.** A global auto-nofollow would also
  nofollow the site's own internal links, which need to stay followed for the internal-link
  architecture (dense hub→article linking) to actually pass authority around the site.

---

## 6. Technical checklist

Source: SEO-STRATEGY.md §5.

**One-time, at launch:**
- [ ] SSL forced; single canonical host chosen (https + one of www/non-www) with the other
      redirected
- [ ] Permalinks set to "Post name" (already done per CLAUDE.md §8 Step 7); trailing-slash
      consistency confirmed — every internal link in the content assumes the `/path/` form
- [ ] Page hierarchy correct in WordPress (e.g. `/dogs/breeds/golden-retriever/` requires
      parent pages `dogs` → `breeds` to exist first) — the WXR import (0.7) sets this up;
      spot-check slugs post-import
- [ ] 404-crawl the site (Screaming Frog's free tier covers 500 URLs) to catch typo'd
      internal links introduced during import
- [ ] Rich Results Test run on 3–4 FAQ pages; fix the schema JSON if the HTML-block route
      mangles quotes
- [ ] Images: WebP format, lazy-loaded (native WP), descriptive alt text as images get added
      — the content shipped text-first, images are an enhancement pass, not a blocker
- [ ] Fonts self-hosted (already planned per CLAUDE.md §10), no external font CDNs — this is
      a Core Web Vitals protection, not a nice-to-have
- [ ] Favicon + a default Open Graph image set

**Decide deliberately — the robots.txt / AI crawler policy:**

> **Recommendation (yours to confirm, not a default we've already applied): allow the named
> AI crawlers.** GPTBot, PerplexityBot, ClaudeBot, Google-Extended, and CCBot are allowed in
> the reference file at [`docs/robots.txt`](robots.txt). The content was deliberately
> GEO-optimized — BLUF answers, FAQ schema mirroring visible text, explicit myth-corrections
> — specifically so AI answer engines can quote it. Blocking these crawlers throws that
> investment away for no compensating benefit. This is presented as a decision for the owner
> to sign off on, not something already locked in — see SEO-STRATEGY.md §5 and §7 for the
> full reasoning, and deploy `docs/robots.txt` (or paste its contents into RankMath's
> robots.txt editor) once confirmed.

**Recurring:** the ops calendar in SEO-STRATEGY.md §8 (weekly/monthly/quarterly/annual tasks)
takes over once launch is stable — see §9 of this runbook for the compressed risk list that
calendar exists to manage.

---

## 7. The wave calendar

**The rule, worth repeating because it's the whole publishing strategy in one sentence:
never publish all ~119 pages at once on a fresh domain.** Google's scaled-content policies
flag single-shot templated dumps; waves of 10–20 pages every 2–3 weeks read as an ordinary
publishing operation instead. Source: SEO-STRATEGY.md §4.

| Wave | Target date | Pages (~count) | Contents |
|---|---|---|---|
| 1 — Launch | Day 0 | 17 WXR pages + homepage (see §2) | Trust set + homepage + `/dogs/` + `/cats/` + Pet Guides cluster + tools hub & the 2 live calculator pages |
| 2 | +2–3 wks after wave 1 | 13 | All sub-hubs (7 dog + 6 cat) — completes the skeleton every article hangs on |
| 3 | +2–3 wks after wave 2 | 12 | Dog health (7) + puppy care (5) — highest-intent informational content, the E-E-A-T flagship cluster |
| 4 | +2–3 wks after wave 3 | 13 | Dog food (7) + training (6) |
| 5 | +2–3 wks after wave 4 | 13 | Dog breeds (9) + behavior (4) |
| 6 | +2–3 wks after wave 5 | 12 | Cat health (7) + kitten care (5) |
| 7 | +2–3 wks after wave 6 | 13 | Cat food (8) + behavior (5) |
| 8 | +2–3 wks after wave 7 | 9 | Cat breeds (9) |
| 9 | Last (no fixed offset) | 10 | All products — deliberately after informational authority exists (post-"Reviews update" logic), and only once `/affiliate-disclosure/` is already live |

**Go/no-go gate before every wave:** check the GSC Pages report for the previous wave (§4).
If that wave is indexing normally, proceed. **If it has stalled at "crawled, not indexed,"
pause and investigate content quality before publishing the next wave** — publishing more
pages on top of an unresolved quality signal compounds the problem instead of diluting it.

**Internal-link check before each wave:** links to pages that haven't published yet will
404. The wave order above was deliberately chosen to keep most hub→article links inside the
same wave, but where a link does cross a wave boundary, either publish the linked target
alongside its source, or accept the brief in-wave 404 and fix it once the target's wave
ships.

---

## 8. What this repo gives you

The dev workstream (a separate effort from this runbook) is building three things that
directly satisfy specific pre-launch blockers from §3. This runbook doesn't own their
implementation — check with that workstream for current status — but here's what they map
to:

- **The WXR importer** (`tools/wxr/` — generates a WordPress import file from the `content/`
  markdown, filterable by wave, imports every page as a **draft**) → satisfies **0.7**
  (WordPress import), and is what makes the wave calendar in §7 operationally realistic —
  without it, "10–20 pages per wave" means hand-pasting content into WordPress by hand.
- **The homepage SEO block** (a `template-parts/home/seo-content.php`-style template part
  rendering `content/home.md`'s copy) → satisfies **0.4** (the homepage currently has almost
  no indexable text).
- **The internal-linking templates** (reusable partials for navigation and cross-linking) →
  satisfy **0.8** (menus & footer) and support **0.9** (visible last-updated dates), by
  giving those recurring UI elements one shared implementation instead of one per page.

---

## 9. Standing risks

Compressed from SEO-STRATEGY.md §10 — so nobody has to relearn these the hard way:

1. **Scaled-content flag.** Respect the wave plan (§7). Never bulk-publish.
2. **YMYL without humans.** The trust-set placeholders (§3, 0.2) are load-bearing, not
   cosmetic. An anonymous pet-health site caps its own rankings. A real veterinary reviewer,
   when affordable, is the single biggest E-E-A-T upgrade available.
3. **Product content without first-hand evidence.** Wave 9's product pages are honest buying
   guides with empty "tested picks" slots. Adding fake "we tested" claims later would turn
   the safest content on the site into the riskiest (Google's Reviews-update logic treats
   fabricated testing harshly). Real testing → fill the slots → then it becomes the good kind
   of review content.
4. **Dated facts.** The annual ops-calendar pass exists because vaccine guidelines, cost
   figures, and travel rules go stale. Each page's fact-check flags (§3, 0.3) are the
   checklist for that pass.
5. **FAQ drift.** Visible FAQ text and its JSON-LD schema must stay synchronized through
   every edit, forever — not just at launch.
