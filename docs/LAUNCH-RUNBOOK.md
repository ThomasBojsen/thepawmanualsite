# ThePawManual — Redeploy Runbook

> **Rewritten 2026-09-16. The previous version of this file described a
> pre-launch state that is no longer true**: it assumed the site was still
> password-protected with "Discourage search engines" ticked, content
> un-imported, and the trust pages carrying `[PLACEHOLDER]` text. None of
> that is current. The site is **live at `thepawmanual.com`** and **already
> being indexed by Google Search Console** (per the owner, 2026-09-16); the
> 18 wave-1 pages published on 2026-09-10 and the trust-page placeholders
> were filled directly in WordPress before that. Since then, an extensive
> pass reworked the theme's design/SEO rendering, applied a full fact-check
> against primary sources, rebranded to the Terracotta palette, purged every
> em dash, and rewrote all 126 drafted pages for GEO (question-form H2s,
> answer-first paragraphs, FAQ schema). **None of that work is live yet.**
> This document is the step-by-step for getting it there.
>
> **Update 2026-10-06:** that redesign has since gone live. The theme zip built
> on 2026-09-18 (`~/Desktop/thepawmanual-deploy/thepawmanual-theme.zip`) is what
> is running, so read the rest of this file as the record of that operation, not
> as the next one. For a later theme change, build a new zip from the tree that
> matches that one (see CLAUDE.md §11) and do **not** repeat the
> delete-and-reimport. The first such change is the page images:
> `docs/PAGE-IMAGES.md`.

---

## 1. Why delete-and-reimport, not a live edit

**Decision, made by the owner 2026-09-16:** the 18 live pages are deleted
permanently and reimported fresh from `content/`, rather than patched in
place via a REST API script, **because they aren't indexed yet** — there is
no indexing history or backlink equity tied to their current post IDs worth
protecting. Reimporting is also simpler: `tools/wxr/generate.js` already
produces a correct, hierarchy-aware import file from the current
`content/` state; a live-patch script would have to be built and verified
from scratch for a one-time operation.

**This will not lose anything.** The six trust pages
(`/about/`, `/contact/`, `/affiliate-disclosure/`, `/terms/`,
`/privacy-policy/`, `/editorial-process/`) had their placeholder text filled
directly in WordPress after the original import; `content/` was re-synced to
match that live state before this pass started (CLAUDE.md §13, "The two
things that block launch," item 1), so reimporting from `content/` reproduces
the real, filled-in trust pages, not stale placeholders. The other 11 live
pages differ from `content/` on purpose: `content/` now carries the reviewed,
corrected, GEO-rewritten version, and replacing the live pages with it is the
entire point of this operation.

---

## 2. What was built for this (deploy artifacts)

Built and verified in this session, at `~/Desktop/thepawmanual-deploy/`:

| File | What it is | Verified |
|---|---|---|
| `thepawmanual-theme.zip` | The full `thepawmanual` theme, zipped from the working tree (not git — 8 required theme PHP files are new and untracked in git). | 38 PHP files in the zip, matching the working tree's 38 exactly. `theme.css` freshly rebuilt; a second independent build is byte-identical. `php -l` clean on all theme+plugin PHP. |
| `pawmanual-calculators.zip` | The `pawmanual-calculators` plugin, zipped the same way. | 5 PHP files, matching the working tree exactly. |
| `thepawmanual-wave-1.xml` | The final wave-1 WXR export: `node tools/wxr/generate.js --wave 1`. | 17 pages, 11 carrying FAQ JSON-LD. All 126 registry titles verified to match their files' current H1 (not just these 17). All 121 pages' FAQ JSON-LD blocks parse as valid JSON. Zero em dashes anywhere in `content/` outside the documented, non-publishing exemptions. The full `php tools/tests/run.php` suite: 94/94 passing. |

The previous deploy artifacts (from the 2026-09-10 launch and an earlier
2026-08-14 pass) are archived, not deleted, at
`~/Desktop/thepawmanual-deploy/_gammelt-sep10/` and `_gammelt-aug14/` — see
§6, Rollback.

---

## 3. The exact page list

**17 pages reimport via the WXR file**, identical URLs to what's live today:

**Trust set (6):** `/about/`, `/editorial-process/`, `/contact/`,
`/privacy-policy/`, `/terms/`, `/affiliate-disclosure/`

**Pillar hubs (2):** `/dogs/`, `/cats/`

**Pet Guides cluster (6):** `/pet-guides/`, `/pet-guides/pet-insurance/`,
`/pet-guides/pet-costs/`, `/pet-guides/pet-safety/`,
`/pet-guides/pet-travel/`, `/pet-guides/new-pet-checklist/`

**Tools (3):** `/tools/`, `/tools/dog-age-calculator/`,
`/tools/cat-age-calculator/`

**Plus the homepage (`/`) — not a WordPress Page, don't delete it.** It's
rendered by the theme's `front-page.php` from a single, otherwise-empty
"Home" Page that only exists to anchor Settings → Reading's "homepage
displays" setting. It is **not** one of the 17 WXR pages and does not go
through the WXR import. Deleting the 17 content pages and leaving "Home" in
place is correct; do not delete "Home" in step 2 below.

---

## 4. Already done — do not redo

Carried over from the original launch; still true, not part of this
operation:

- Password gate removed, "Discourage search engines" unticked (the site is
  live and being crawled).
- SSL / canonical host.
- RankMath installed and configured (title template, sitemap scope, schema
  settings, breadcrumbs) — CLAUDE.md's render pipeline defers to RankMath for
  breadcrumbs already (§13).
- A Google Search Console property exists and is receiving data (per the
  owner, 2026-09-16).
- `docs/robots.txt` exists as a reference file; whether it's actually
  deployed to the live `robots.txt` wasn't re-verified this session — check
  it while you're in there (step 9 below).

---

## 5. Step-by-step

**[You]** = you, in WP Admin or simply.com's panel. **[Claude]** = already
done, in this repo, described in §2. I do not perform the deletion step
myself — permanently deleting live data is outside what I do regardless of
authorization; that one is yours.

**Step 1 — [You] Back up first.** Before deleting anything, take a full
database backup via simply.com's hosting panel (or WP Admin → Tools →
Export → All content, as a lighter-weight fallback). Cheap insurance against
anything going wrong in step 2.

**Step 2 — [You] Permanently delete the 18 live pages.** WP Admin → Pages →
select the 17 URLs listed in §3 (not "Home," see §3's note) → Move to Trash
→ Trash → Empty Trash (or delete permanently one at a time). This is the one
step I cannot perform for you.

**Step 3 — [You] Upload and activate the new theme.** WP Admin → Appearance
→ Themes → Add New → Upload Theme → `thepawmanual-theme.zip` → Install. If
WordPress reports the theme folder already exists, use the "Replace installed
with uploaded" option it offers (older WordPress: "Replace current with
uploaded"; or delete the old theme first via Theme
Details → Delete, then upload again) — either way, don't leave two versions
installed. Activate it.

**Step 4 — [You] Upload and activate the new plugin.** Plugins → Add New →
Upload Plugin → `pawmanual-calculators.zip`. Same folder-exists handling as
step 3: deactivate and delete the old version first if WordPress won't
overwrite it directly. Activate.

**Step 5 — [You] Resave permalinks.** Settings → Permalinks → Save (no
change needed — this just flushes WordPress's rewrite rules after the theme
swap, good practice, cheap).

**Step 6 — [You] Import the fresh wave-1 WXR.** Tools → Import → WordPress
(install the importer plugin if prompted) → Upload `thepawmanual-wave-1.xml`
→ assign the author → leave "Download and import file attachments" unchecked
(there's no media in this content) → Submit. This creates all 17 pages as
**drafts**, with parent/child hierarchy already correct from the file.

**Step 7 — [You] Publish all 17 imported drafts.** Pages → filter by Draft →
select all 17 → Bulk actions → Edit → Status: Published → Update. If you'd
rather be careful about hierarchy, publish the four parents first
(`/dogs/`, `/cats/`, `/pet-guides/`, `/tools/`) then the rest, though the
import already resolved `post_parent` correctly regardless of order.

**Step 7a — [You] Set the Featured images again.** The import does not carry
Featured images, and deleting a page in step 2 deleted its image assignment
with it (the images themselves stay in the Media Library). Set each one again
from the table in `docs/PAGE-IMAGES.md`, with its Alternative Text (read the
flag on `Pet-Safety.webp` there first).

**Step 8 — [You] Re-link the primary navigation menu.** The reimport gives
every page a **new** post ID, so the existing menu (Appearance → Menus) now
points at deleted pages and will show broken links. Remove the stale items
and re-add the same set from the "Pages" panel, which now lists the freshly
imported ones. Menu structure per CLAUDE.md §12/§13: header nav **Dogs ·
Cats · Pet Guides · Tools**; footer **About, Editorial Process, Contact,
Privacy Policy, Terms, Affiliate Disclosure**, plus a **"Pet Emergency?"**
link pointing at both `/dogs/health/signs-your-dog-needs-a-vet/` and
`/cats/health/signs-your-cat-needs-a-vet/` (that page pair isn't in wave 1,
so this link will 404 until whichever wave publishes it — see §7).

**Step 9 — [You] Confirm the homepage is still set.** Settings → Reading →
"A static page" → Homepage should still read "Home" (you didn't delete that
page in step 2). If it was somehow deleted, recreate it: Pages → Add New →
title "Home" → Publish, leave content empty → reselect it here.

**Step 10 — [You] Verify, in a private/incognito window** (so nothing is
served from your own logged-in cache):
- Homepage renders with the Terracotta palette: hero, filter bar, tools
  grid, gallery, statement band, promo, footer.
- Header search: click the icon, type a query, confirm results render, at
  every viewport width (this was previously desktop-only — confirm on a
  phone-width window too).
- Both calculator pages (`/tools/dog-age-calculator/`,
  `/tools/cat-age-calculator/`): enter values, Calculate, confirm a correct
  result, try Reset, try invalid input.
- `/tools/` renders `<h1>Free Pet Calculators</h1>` — not "Tools."
- Pick 2–3 article pages (e.g. `/pet-guides/pet-costs/`) and confirm: the
  key-takeaway box under the H1, the FAQ accordion, the table of contents,
  the related-reading block at the bottom, and — where topically relevant —
  an inline tool CTA (most wave-1 articles won't show one; that's correct,
  see CLAUDE.md §13's "no CTA beats an irrelevant CTA" note).
- Every page in the table in `docs/PAGE-IMAGES.md` shows its image between the
  title and the key-takeaway box, on a phone-width window too.
- Pick a hub page (`/dogs/` or `/cats/`) and confirm the child-page grid
  renders and its own related-reading block shows the other top-level hubs.
- The "Pet Emergency?" callout renders in the rose/caution color, visibly
  distinct from the terracotta brand color, not blended into it.
- Run Google's Rich Results Test against 2–3 FAQ-carrying pages to confirm
  the JSON-LD is recognized and matches the visible FAQ text.
- Check `robots.txt` on the live site against `docs/robots.txt` (§4) — copy
  it in via RankMath's robots.txt editor if it isn't already there.
- No PHP notices or warnings on any page (check `WP_DEBUG` output or the
  host's PHP error log if anything looks off).

**Step 11 — [You] Re-request indexing.** Search Console → URL Inspection →
Request Indexing for the homepage, `/dogs/`, `/cats/`, and the 6 trust
pages — the content changed materially from whatever Google saw before.
Everything else in wave 1 follows via the sitemap.

---

## 6. Rollback

If something goes wrong mid-operation, the artifacts from immediately before
this pass are archived, not deleted:
`~/Desktop/thepawmanual-deploy/_gammelt-sep10/` has the theme zip, plugin
zip, and a WXR export **for all 126 drafted pages** (`thepawmanual-all.xml`,
not wave-1-only) as they stood at the 2026-09-10 launch, before the
Terracotta rebrand, the em-dash purge, and the fact-check/GEO passes.
Re-uploading that theme/plugin and reimporting that XML returns the site to
its 2026-09-10 state; because it's the `--all` export, remember to publish
only the same 17 URLs listed in §3 afterward, not all 126, or you'll
publish wave 2–9 content early. `_gammelt-aug14/` is one generation further
back if that's ever needed instead.

---

## 7. What comes after this

Waves 2–9 (the remaining ~109 pages, per `content/SEO-STRATEGY.md` §4's
publishing calendar) are **content-ready ahead of schedule**: every page in
`content/` has already been through the same fact-check and GEO passes as
wave 1, not just the 18 going live now. What's left for waves 2–9 is a
publishing-pace decision, not a content-readiness one — keep following the
"10–20 pages every 2–3 weeks, never a bulk dump" rule in
`content/SEO-STRATEGY.md` §4 and the go/no-go gate there (check the previous
wave's GSC Pages report before publishing the next one). `content/README.md`
item 2 ("What remains") also documents an open, honestly-caveated FAQ
visible/JSON-LD drift figure across the corpus that's independent of this
redeploy and worth a maintenance pass at some point, not a blocker for it.
