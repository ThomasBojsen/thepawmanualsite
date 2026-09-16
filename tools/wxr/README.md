# WXR import tool — how to publish the content

This turns the markdown files in `content/` into a WordPress import file (a `.xml`
file in the "WXR" format — WordPress's own export/import format, the same one
**Tools → Export** produces). You do not need to know Node.js or read the code to
use this. Everything below is WordPress-admin steps.

## What it does, in plain terms

- Reads every page in `content/` (about 126 of them, everything except the
  homepage — see "Why isn't the homepage in here?" below).
- Turns each one into a WordPress **Page**, set to **Draft** — nothing goes
  live automatically. You publish pages yourself, in WP Admin, on the schedule
  in `content/SEO-STRATEGY.md` §4 (the "wave plan").
- Sets up parent/child structure automatically, e.g. `/dogs/food/puppy-food/`
  is created as a child of `/dogs/food/`, which is a child of `/dogs/`.
- Fills in the RankMath SEO title and meta description for every page from
  that page's frontmatter, so you don't have to paste them in one at a time.
- Strips out everything marked "not page copy" in the markdown (fact-check
  flags, editor's notes) — none of that reaches WordPress.
- Attaches each page's FAQ schema (the JSON-LD block search engines read) so
  it's live on the page as soon as it's imported — no separate schema step.

## Running it

You need Node.js installed (it already is, if you've run `npm install` for the
theme's Tailwind build). From the repo root:

```bash
# One wave at a time (recommended — matches the publishing plan):
node tools/wxr/generate.js --wave 1

# A few waves in one file:
node tools/wxr/generate.js --wave 1,2

# Every page in content/ in one file (useful for a full staging import, not for
# a real launch — see the wave-plan warning below):
node tools/wxr/generate.js --all
```

Or via npm, if you prefer:

```bash
npm run wxr:wave1     # same as: node tools/wxr/generate.js --wave 1
npm run wxr:all       # same as: node tools/wxr/generate.js --all
npm run wxr -- --wave 3    # any other wave
```

Each run writes an `.xml` file into `tools/wxr/output/` (e.g.
`thepawmanual-wave-1.xml`). That file is what you upload to WordPress.

### Reproducible output

Running the same command twice produces **byte-for-byte the same file** —
every page always gets the same title, the same content, the same hidden
reference number (see below), and the same date.

The date (and the title) need their own explanation, because "always the
same" can't just mean "the tool's own fixed placeholder," the way the
hidden reference number does — see "Where a page's date AND title come
from" below.

### Where a page's date AND title come from

WordPress recognizes an already-imported page by matching its **title and
date together** (see "How WordPress decides..." below) — not just the date.
So both have to satisfy the same two things at once, which pull in opposite
directions:

- **They have to stay exactly the same, forever**, every single time that
  page gets generated again — in whichever wave, alongside whichever other
  pages, months or years apart. This is what lets WordPress recognize an
  already-imported page as the same page instead of duplicating it — the
  same reasoning as the hidden reference number, just applied here too.
- **The date has to be real and believable on the published page** — not a
  placeholder like "1 January 1970" (an earlier version of this tool did
  exactly that, and a fixed placeholder does not work: it shows up as the
  page's date forever, since nothing about publishing a page changes it).
  **The title has to be whatever the page's H1 actually, currently says** —
  a title frozen at registration time and never allowed to update would
  eventually go stale against the real page content, which is just as bad
  as a fake date.

  One more wrinkle, corrected 2026-09-14 after it bit wave 1 (see
  "Re-harvesting a drifted date" below): this tool always imports every page
  as a **Draft** (see "Importing into WordPress" below), and a WordPress
  draft's post_date isn't the real "went live" date yet — the moment a human
  clicks **Publish** on it, WordPress stamps that literal moment as the
  page's real, permanent date, overwriting whatever this tool imported it
  with. So "real and believable" only holds automatically for a URL nobody
  has published yet. Once a page IS published, this registry has to be
  updated by hand, once, to match — it doesn't happen on its own.

Nothing the tool can *compute* satisfies both at once for either field — a
fixed placeholder date is stable but not believable, "whatever day you
happen to run the tool" is believable but not stable, and simply
re-reading the current H1 every time (title is the one field that
*could*, in principle, just be re-derived from the markdown on every run —
and an earlier version of this tool did exactly that) is accurate but not
stable, because nothing stops someone from editing an H1 between runs.
So instead, this tool **remembers** each page's date and title the first
time it's ever generated, in a small committed file:
**`tools/wxr/publish-registry.json`** — one entry per URL, in plain
`"/some-page/": { "date": "2026-08-14", "title": "..." }` format, readable
and editable by hand, no code required. `"date"` is either a bare
`"YYYY-MM-DD"` (a reservation, for a URL nobody has published yet — the
common case) or a full ISO-8601 datetime with an offset, e.g.
`"2026-09-10T20:03:42+00:00"` (a value actually harvested from a live,
already-published page — see "Re-harvesting a drifted date" below for when
and how that happens). (The registry also records which content file
produced the entry — that's only used to catch a URL rename; see "A page's
URL changing" below.)

What this means when you run the tool:

- **A URL that's already in `publish-registry.json`** — most of them, most
  of the time — just reuses the date and title that are already there,
  *provided the title still matches*. You don't need to do anything.
- **A URL whose current H1 no longer matches its registered title** — most
  often because someone edited the markdown, an entirely normal editorial
  act — makes the tool **stop and refuse to guess**:
  ```
  Error: /dogs/'s title has changed since it was first generated:
    registered (tools/wxr/publish-registry.json): "Dog Care Guides..."
    current (this page's H1 right now):           "Dogs: The Complete Guide"
  ...
    1. Mistake? Put the H1 in the markdown file back to the registered title.
    2. Deliberate, and this page was NEVER imported into WordPress yet? Edit its
       "title" in tools/wxr/publish-registry.json by hand to match the new H1.
    3. Deliberate, and this page IS already live? Rename it to match in WordPress
       FIRST (Pages -> edit -> Title -> Update), THEN edit the registry to match.
  ```
  This is deliberately not a "which one wins" default. A title change is
  completely ordinary editorial work — but if this page is already live and
  the tool silently accepted the new title, the *next* wave that happens to
  re-include this URL as an ancestor hub (weeks later, maybe by someone
  else) would emit a title that no longer matches the live page, and
  WordPress would create a duplicate — the exact bug this whole mechanism
  exists to prevent, arriving from the title side instead of the date side,
  and invisible until it happens. Read the three options in the error and
  pick the one that matches what actually happened.
- **A URL that has never been generated before** — a page you just wrote, or
  an ancestor hub pulled in for the first time — has no entry yet, and the
  tool **stops and tells you**, rather than guessing:
  ```
  Error: these URLs have no date yet in tools/wxr/publish-registry.json:
    - /dogs/some-new-page/
  Run again with --date YYYY-MM-DD (e.g. today's date) to register one for them.
  ```
  Run the same command again with `--date` and today's date (or whatever
  date you want that page to carry), e.g.:
  ```bash
  node tools/wxr/generate.js --wave 4 --date 2026-09-01
  ```
  The tool adds one entry per new URL to `publish-registry.json` (capturing
  today's date AND the page's current title) and tells you it did so
  ("Registered 3 new date(s)..."). **Commit that file** the same way you'd
  commit any other change — it's what makes the date and title permanent for
  that URL from then on. Every later run, by anyone, on any machine, reuses
  them automatically; `--date` is not needed again for that URL and has no
  effect if you pass it anyway (an existing entry is never overwritten, on
  purpose — see "How WordPress decides..." below for why overwriting it
  would be dangerous once a page is actually live).
- **Registering more than 25 brand-new URLs in a single run** (bigger than
  any one wave in `waves.json`) needs an extra `--confirm-bulk-register`
  flag alongside `--date`. This is a deliberate speed bump — see "Registry
  loss or corruption" below for exactly what it's guarding against.
- **Made a typo, or want to correct an entry before anything is actually
  imported into WordPress yet?** Open `publish-registry.json` and edit the
  line by hand — it's plain JSON, safe to edit directly, no tool required.
- **A page's registered date needs correcting because it's already live** —
  this is different from the point above, and it's an expected, supported
  repair, not a mistake to avoid: see "Re-harvesting a drifted date" below.
  **Title is the one field that's more dangerous to hand-edit once a page is
  live** — see the three-option error flow above, and don't skip straight to
  editing the registry for a title without first confirming which of the
  three situations you're actually in.

**If a wave's pages link up to a page from an earlier wave that hasn't been
generated yet** (e.g. you generate wave 3 — dog health articles — without ever
generating wave 1 or 2), the tool automatically pulls in whatever ancestor hub
pages those articles need (here, `/dogs/` and `/dogs/health/`) so nothing
imports as an orphan with a broken parent. You'll never end up with a page
whose parent doesn't exist in the same file.

## Which wave to import, and when

Follow `content/SEO-STRATEGY.md` §4 — never import-and-publish everything at
once; Google flags large single-shot dumps of templated content on a fresh
domain. The wave order:

| Wave | Content | Command |
|---|---|---|
| 1 — Launch | Trust pages, `/dogs/`, `/cats/`, Pet Guides, Tools hub + 2 calculators | `node tools/wxr/generate.js --wave 1` |
| 2 | All 14 cluster hub pages (8 dog + 6 cat) | `--wave 2` |
| 3 | Dog health + puppy care | `--wave 3` |
| 4 | Dog food + training | `--wave 4` |
| 5 | Dog breeds + behavior | `--wave 5` |
| 6 | Cat health + kitten care | `--wave 6` |
| 7 | Cat food + behavior | `--wave 7` |
| 8 | Cat breeds | `--wave 8` |
| 9 | All products (buying guides) | `--wave 9` |

**Two things that differ from the wave counts written in `SEO-STRATEGY.md` §4** —
both are explained in full in `tools/wxr/waves.json`'s `_assumptions` key, flagging
them for you rather than silently guessing:

1. Wave 2 imports **14** sub-hubs, not the 13 written in the strategy doc. The
   dog grooming hub (`/dogs/grooming/`) was written a day after that doc, and
   was never subtracted from the wave-2 count — it's a real, finished page, so
   it's included alongside the other 7 dog hubs.
2. Waves 3, 4, and 5 include about 14 dog pages (grapes/chocolate toxicity,
   fleas/ticks/worms, ear infections, recall training, puppy biting,
   socialization, zoomies, staring, digging, three breed-list pages) that were
   also written after the strategy doc's wave table and aren't named in it.
   Each was placed in the wave matching its own content cluster (e.g. the new
   dog-health pages went into wave 3 with the rest of dog health) — the same
   logic the rest of the wave table already uses. If you want a different
   split, edit `tools/wxr/waves.json` directly; it's a plain JSON file, one
   array of URLs per wave.

Every page in `content/` (except the homepage) is accounted for in exactly one
of the 9 waves — nothing is missing and nothing is duplicated across waves
(verified when this tool was built).

## Importing into WordPress

1. WP Admin → **Tools → Import**.
2. If "WordPress" isn't listed, click **Install Now** under it, then
   **Run Importer**.
3. Choose the `.xml` file from `tools/wxr/output/` (e.g.
   `thepawmanual-wave-1.xml`) and upload it.
4. On the "Assign Authors" screen, map the import's author (`admin`) to
   whichever real WordPress user should own these pages, or leave it — it
   creates an `admin` user if none exists.
5. Leave "Download and import file attachments" — there are none (this
   content has no images yet).
6. Click **Submit**. WordPress imports every page as a **Draft**.
7. Go to **Pages** and confirm the count matches what the tool printed (e.g.
   "Wrote 17 page(s)" → 17 new draft Pages) and that the **parent** column
   shows the right hierarchy (e.g. "Pet Insurance" nested under "Pet Guides").
8. Spot-check RankMath: open a page, confirm the SEO title and meta
   description are already filled in under the RankMath box.
9. **Nothing is published yet.** Publish pages by hand (or in bulk via
   **Pages → Bulk actions → Edit → Status: Published**) only when that wave is
   actually meant to go live, per the schedule above.

### How WordPress decides "this page is already here" — and what that means for re-importing

When you import a file, WordPress looks at each page's **title and date**
together to decide whether it already has that page. (It ignores the hidden
reference number sitting in the file — more on that number below.) If it
finds a page with the same title and the same date, it leaves that existing
page alone and skips creating a new one. If it doesn't find a match, it
creates a brand-new page.

That's the whole mechanism, and it only has two outcomes: **skip** (nothing
changes) or **create a new page**. WordPress's importer never updates a page
that's already there — there is no "refresh the content" outcome.

This tool now gives a page the exact same title and the exact same date every
single time you generate it — both come from the committed
`publish-registry.json` registry described in "Where a page's date AND title
come from" above, and **the tool itself never changes either one once
they're in there** (a changed H1 stops the tool cold instead of silently
drifting — see that section). WordPress can still move a page's date out
from under this file, though — see "Re-harvesting a drifted date" below —
which is why "the tool never changes it" is not quite the same promise as
"it can never go stale." That fixes the specific bug this section used to
describe wrongly, and it changes what re-importing does:

- **Importing the exact same wave file a second time** — same title, same
  date as before — WordPress recognizes every page as already there and
  skips all of them. You get **no duplicates**. You also get no update:
  if you meant to fix something, this does nothing for you.
- **Regenerating a wave after editing its markdown, then re-importing** — the
  title and date are usually unchanged (only the body content changed), so
  WordPress will still recognize the page as already there and **skip it,
  silently leaving the old content in place.** Re-importing is *not* how you
  push a content fix once a page has already been imported. To fix a page
  that's already in WordPress, either edit it directly in WordPress, or
  delete the old draft Page first and then re-import the regenerated file.
- **A later wave's file pulling in an already-imported hub page** — e.g.
  wave 2's file includes `/dogs/` again, because wave 3's dog-health articles
  need `/dogs/` → `/dogs/health/` as their parent chain — now carries the
  exact same title and date `/dogs/` had in wave 1's file (`/dogs/`'s entry in
  `publish-registry.json` was written once, back when wave 1 was first
  generated, and every later run just reads it back). WordPress recognizes
  it as the same page, skips creating a duplicate, and correctly nests
  wave 2's new sub-hub pages under the real, already-published `/dogs/`.

  **This is the bug this section used to explain wrongly.** It used to say
  re-importing duplicates pages because "each generator run assigns fresh
  sequential IDs" — but two runs with the same arguments never produced
  different numbers, so that was never the mechanism. What actually broke:
  every run stamped every page with whatever moment "now" happened to be
  when you ran the tool, so `/dogs/` got a different **date** in wave 1's
  file than it got in wave 2's file. Different date → WordPress didn't
  recognize it as the same page it already had → it created a second
  `/dogs/` (auto-suffixed to `dogs-2` since the slug was taken) → every
  wave-2 sub-hub, which points at whichever `/dogs/` sits in that same file,
  ended up nested under the new duplicate instead of the real one, and the
  real `/dogs/` was left with no children. Recording each page's date once,
  in `publish-registry.json`, and reusing it forever (instead of restamping
  "now" on every run) is what stops that from happening again. The hidden
  reference numbers described below were *also* drifting for the same
  reason (a fresh counter every run) and are now fixed too (by a different
  mechanism — computed from the URL itself rather than remembered in a
  file, since a number doesn't need to look "believable" the way a date
  does), which matters for its own reasons — see below — even though the
  numbers themselves were never what WordPress used to detect duplicates.
  (An earlier fix stamped every page with a fixed placeholder date,
  1 January 1970, instead of "now" — that solved the duplicate-hub bug the
  same way the registry does, but broke something else: "1 January 1970" is
  a nonsense date to show a reader on a real article. The registry is the
  fix that keeps the bug fixed *and* the visible date real — for a page
  nobody's published yet. For a page that HAS been published, "real" needs
  one more step; see "Re-harvesting a drifted date" immediately below.)
- **Two different places a title can change, and only one is guarded:**
  editing a page's H1 **in the markdown source** — where all real authoring
  on this project happens — is now caught automatically: the tool compares
  it against the registry on every run and refuses to proceed on a mismatch
  (see "Where a page's date AND title come from" above). But editing a
  page's **title, or its date, directly inside WordPress** after it's
  imported is invisible to this tool — it has no connection to the live
  site and can't compare against what's actually there. If you (or an
  editor) rename a page in WP Admin, that page no longer matches what
  `publish-registry.json` says, and the next time a wave re-includes it
  (most often as a pulled-in ancestor hub, per the point above), WordPress
  won't recognize it and will create a duplicate — the registry can't catch
  this side of the mismatch because it isn't wrong about anything; the
  *live page* is the one that moved. Rule of thumb: once you've hand-edited
  a page's title or date in WordPress, don't re-import the wave it came
  from again. (This is also exactly why `publish-registry.json` entries are
  never overwritten by `--date` once they're set. If the tool silently
  updated a URL's date or title on every run, any wave that later
  re-included that URL as an ancestor hub would stop matching the live
  page, and the duplicate-hub bug would be right back — which is the same
  reasoning behind refusing to guess on a source-side title mismatch too.)

  This is about a **human** retitling or redating a page in wp-admin — a
  separate thing from **WordPress itself** silently moving a page's date the
  moment someone clicks Publish on an imported Draft, with nobody touching
  the date field at all. That second kind of drift isn't a mistake to avoid;
  it happens to every wave-1 URL that goes live, every time, by design of
  how WordPress handles drafts — see "Re-harvesting a drifted date" next.

### Re-harvesting a drifted date

**Every page this tool imports goes in as a Draft** (see "Importing into
WordPress" above, and `tools/wxr/lib/wxr-xml.js`, which always emits
`<wp:status>draft</wp:status>`) — nothing is ever published automatically.
A freshly imported WordPress draft does not keep the `wp:post_date` this
tool wrote as its real "this page went live" date. Internally, a draft's
post_date is treated as unset, and the instant a human clicks **Publish** on
it in wp-admin, WordPress stamps that literal moment as the page's real,
permanent `post_date`/`post_date_gmt` — overwriting whatever this tool
imported it with. Nobody edits a date field to make this happen; it's just
what Publish does to a draft.

**This means a registry entry with a bare date (`"2026-08-14"`, no
time-of-day) is only ever guaranteed accurate for a URL nobody has published
yet.** The moment a human publishes it, this tool's stamped date quietly
goes stale — still sitting in `publish-registry.json`, but no longer
matching the live page. This is exactly what happened to all 17 of wave 1's
imported URLs: every one was registered here at a bare `"2026-08-14"`, and
by the time the live site was checked on 2026-09-14, their real, live
`datePublished` (read from each page's own RankMath JSON-LD) turned out to
be `2026-09-10T20:03:42+00:00` for 16 of them and, separately,
`2026-09-10T20:20:17+00:00` for `/pet-guides/pet-travel/` — proof that
**every URL has to be checked on its own**, never assumed to share another
page's value just because they were imported together.

**Why this matters, concretely:** if wave 2 (or any later wave) ever
re-includes one of these URLs as an ancestor hub with the wrong, drifted
date still in the registry, WordPress won't recognize it as the same page —
title+date no longer matches the live post — and creates a duplicate (the
exact bug this whole registry exists to prevent, arriving from the date
side after all, just later and by a different route than the original bug).
Fixing the registry *before* that happens is the entire point.

**How to fix it, for any URL, any time you suspect this has happened:**

1. Open the **live** page in a browser (not the WXR file, not WordPress
   admin's editor — the actual published URL).
2. View its page source (or open DevTools) and find the JSON-LD block(s) of
   type `application/ld+json`. In the console, this one-liner lists every
   `datePublished` on the page:
   ```js
   [...document.querySelectorAll('script[type="application/ld+json"]')]
     .flatMap(s => { try { const d = JSON.parse(s.textContent); return d['@graph'] || [d]; } catch { return []; } })
     .filter(n => n.datePublished)
     .map(n => ({ type: n['@type'], datePublished: n.datePublished }))
   ```
   RankMath emits the same `datePublished` on both the page's `WebPage` and
   `Article` nodes — either one is the value you want. Copy it exactly,
   including the `+00:00` (or other) UTC offset.
3. Open `tools/wxr/publish-registry.json` and replace that URL's `"date"`
   with the copied string verbatim — e.g.
   `"date": "2026-09-10T20:03:42+00:00"`. Leave `"title"` and `"filePath"`
   untouched; this is a date-only correction. (If `dateModified` differs
   from `datePublished`, use `datePublished` — the registry models "when
   WordPress first considers this page to exist," which is what the
   duplicate-detection check compares against, not "when it was last
   edited.")
4. Commit the file. `git diff` should show only that one `"date"` line
   changing — if it shows anything else (a title, a filePath, an added or
   removed URL), stop and re-check before committing.
5. Regenerate and confirm: `grep -A2 '"/the-url/"' tools/wxr/publish-registry.json`
   should show the new value, and `node tools/wxr/generate.js --wave N` (for
   whichever wave includes that URL, or `--all`) should complete without a
   title-drift error.

**You don't have to remember to do this on your own.** `generate.js` now
warns automatically, every run, for any URL it's about to emit whose
registry date is still bare AND which already had a registry entry before
this run started (freshly-registered URLs in the same run can't trigger
it — nothing could have published them yet). The warning looks like:
```
Warnings:
  - /some-page/ still has a bare registry date ("2026-08-14", no time-of-day)
    from an earlier run. If this page has already been imported into
    WordPress and a human has clicked Publish on it, its live post_date has
    almost certainly drifted away from this placeholder...
```
It's a nudge, not a gate: the script has no way to check the live site
itself (no network access from `generate.js`, and the production site sits
behind a WAF anyway — curl gets a security block; checking by hand in a
browser, per the steps above, is the only way). So it warns on every
URL that's plausibly at risk rather than guessing which ones actually are,
and generation still completes either way. Treat the warning as "go check,"
not "something is broken."

### The hidden reference number (`wp:post_id`)

Every page in the file also carries a plain number (e.g. `85451044` — these
numbers look large and random on purpose, see below). This is **not** the
number the page will actually have in WordPress — WordPress always assigns
its own new number the moment it creates a page, the same way it always has.
The number in the file exists only so that, *while one import is running*,
WordPress can work out which page is meant to be the parent of which (e.g.
"the page called `/dogs/health/` belongs under whichever page has number
85451044 in this same file"). Once the import finishes, that number is
thrown away.

This tool guarantees that a given page's URL always gets the same number, in
every file, no matter which wave(s) you generate or in what order — so the
files are predictable and comparable, and the parent/child wiring inside any
one file is always correct. But because WordPress discards the number after
each import, it is not what makes re-importing safe — the title-and-date
matching described above is what does that.

### A page's URL changing

Everything above — the hidden number, the date, the title — is remembered
per **URL**. That's deliberate (it's what lets a page keep its identity
across as many waves and as much time as needed), but it has one blind
spot: if a page's URL itself changes — someone edits the `url:` line in its
frontmatter, e.g. moving `/pet-guides/pet-costs/` to
`/pet-guides/cost-of-pet-ownership/` — nothing above notices on its own,
because as far as the id/date/title machinery is concerned, that's just a
brand-new URL that happens to need its own `--date`. Meanwhile the *old*
URL quietly stops being generated at all.

If that old URL was ever actually imported into WordPress (even just as a
Draft), it's now sitting at a dead address, orphaned, with nothing pointing
visitors or search engines to the new one. This tool **will warn you** when
it happens — it recognizes a rename by noticing that the same content file
is now registered under a different URL than before:
```
Warning: content/pet-guides/pet-costs.md looks renamed — registered as
/pet-guides/pet-costs/ (first generated 2026-08-14) but now defines
/pet-guides/cost-of-pet-ownership/. If /pet-guides/pet-costs/ was ever
imported into WordPress, it is now orphaned at its old address...
```
This is a warning, not a hard stop — generation continues, because the new
URL genuinely is fine to register. What the warning can't do is fix the
live site for you (it has no access to WordPress), so when you see it:
1. **If the old page was ever imported/published**, add a 301 redirect from
   the old address to the new one (a redirect plugin, or a rule your host
   supports) so links and search rankings carry over.
2. **Delete or unpublish the old Page in WordPress** once the redirect is
   in place, so it doesn't sit around as duplicate/orphaned content.
3. The registry keeps the old URL's entry (harmless — nothing will ever
   generate it again once no content file defines it), which is exactly
   what lets the warning keep working if this ever happens again.

### Registry loss or corruption

**Never delete `tools/wxr/publish-registry.json`.** It cannot be
reconstructed from anything else in this repo — the dates and titles in it
are the only record of what was already emitted (and, once real waves are
live, of what WordPress already has). If it's lost, every URL looks
brand-new to the tool, and blindly re-registering all of them would hand
out fresh dates and titles for pages that are already published — the next
wave that pulls any of them in as an ancestor would then fail to match and
duplicate the whole site, one hub at a time.

As a safety net, the tool refuses to register more than 25 brand-new URLs
in a single run (bigger than any one wave) unless you explicitly add
`--confirm-bulk-register`. That's not a perfect detector — a truly fresh
checkout running `--all` for the very first time looks exactly the same
as a wiped registry file to the tool, since both mean "a lot of URLs with
no entry yet" — so it can't safely guess which one you're in. It just makes
you say so:
```
Error: this run would register 126 brand-new URLs at once — more than 25...
That's either a deliberate bulk operation... or a sign that
tools/wxr/publish-registry.json was lost, emptied, or reset by accident...
If you're sure this is intentional, re-run with --confirm-bulk-register.
```
If you ever see this and you *aren't* deliberately bootstrapping from
scratch, stop and check `git log -- tools/wxr/publish-registry.json` (or
your backups) before adding the flag — recovering the real file is always
better than re-minting one.

### Two people generating waves on unsynced machines

Narrower, and easy to avoid, but worth knowing: if two people both write a
brand-new page and both run `--date` for it on their own machine before
either has pulled the other's commit, they could register the *same* new
URL with two *different* dates. Because `publish-registry.json` is one
plain-JSON file with one line per URL, this shows up as an ordinary **git
merge conflict** on that one line the moment either of you pulls or merges
— git won't silently pick one, so this fails safe, not silently. Avoid it
in the first place with the obvious rule: `git pull` before you generate a
brand-new page's dates, and commit `publish-registry.json` promptly after
you do.

## Why isn't the homepage in here?

`content/home.md` isn't imported. Per `content/README.md`, the homepage isn't a
normal WordPress Page — its copy is written for the theme's `front-page.php`
template (see `CLAUDE.md` §7.2 Phase 2 and the implementation notes at the
bottom of `content/home.md`). A developer wires that copy into the theme
directly; it never goes through Tools → Import.

## Why aren't all 9 Tools pages here?

Only the Tools hub and the two calculators that actually exist
(`/tools/dog-age-calculator/`, `/tools/cat-age-calculator/`) are ever included
— and only in wave 1. The other six tools pages (food calculator, cost
calculator, breed selectors, name generator) don't have content files yet,
because those calculators aren't built. If you run a wave that references a
tools page that doesn't exist yet, the tool skips it and prints a warning
instead of crashing — it won't happen for wave 1 as things stand today, but it
future-proofs the tool if a wave gets generated before its content is written.

## For whoever maintains this tool (developer-facing)

- Entry point: `tools/wxr/generate.js`. `node tools/wxr/generate.js --help`
  prints usage, including `--date`.
- `tools/wxr/lib/frontmatter.js` — hand-rolled `key: value` frontmatter parser
  (not real YAML; strips the `# nn chars` annotation comments).
- `tools/wxr/lib/parse-page.js` — per-file pipeline: strip the "not page copy"
  section, extract the FAQ JSON-LD fenced block, extract the H1 as the post
  title, then hand the rest to `marked` for markdown → HTML.
- `tools/wxr/lib/gutenberg.js` — wraps `marked`'s HTML output in Gutenberg
  block comments (`wp:paragraph`, `wp:heading`, `wp:list`, `wp:table`,
  `wp:quote`, `wp:html`) so imported pages are block-editable, not one classic
  HTML blob. It's a pragmatic block-per-top-level-element pass, not an
  exhaustive HTML→block mapping — anything it doesn't recognize falls back to
  a `wp:html` block, so the page still renders correctly even if the editor
  experience for that one element is less polished.
- `tools/wxr/lib/wxr-xml.js` — builds the actual WXR/RSS XML: parent-ID
  wiring, RankMath postmeta, CDATA escaping. Each page's own `date` (a `Date`,
  required on every page object passed to `buildWxr()`) comes from
  generate.js's date registry, below; the only date this file defaults on
  its own is `CHANNEL_DATE`, the RSS `<channel>`'s feed-level `<pubDate>` —
  cosmetic metadata about the export itself, not tied to any post_id, so it
  stays a fixed placeholder with no correctness implications.
- `tools/wxr/generate.js`'s `idForUrl()`/`assignStableIds()` — the id scheme.
  A page's `wp:post_id` is a hash of its URL, not a counter, specifically so
  that generating a subset of pages (a wave) never changes any page's id
  relative to any other run. Ids are computed across the *whole* content/
  corpus every run and checked for collisions (extremely unlikely, but
  checked rather than assumed away — see the comment above
  `assignStableIds`), not just the URLs being emitted this run. Unlike
  dates and titles, an id never has to look "believable," so a pure
  function of the URL (no persisted state) is sufficient — see
  `tools/wxr/publish-registry.json` below for why dates and titles need a
  different mechanism.
- `tools/wxr/publish-registry.json` + generate.js's `loadPublishRegistry()` /
  `savePublishRegistry()` / `resolvePublishRegistry()` / `formatDateOnly()`
  — the committed URL → `{ date, title, filePath }` registry described in
  the README's "Where a page's date AND title come from". A page's date and
  title can't be pure functions of its URL the way its id is: both have to
  be stable forever *and* believable/accurate, and no computation satisfies
  both without remembering the answer somewhere. This file is that memory.
  `resolvePublishRegistry()` reads it, throws if any URL's current H1
  doesn't match its recorded title (see below), registers any missing URL
  via `--date` (never overwriting an existing entry, and refusing more than
  `BULK_REGISTER_THRESHOLD` new entries at once without
  `--confirm-bulk-register`), and writes the file back via
  `savePublishRegistry()`, which re-sorts by URL so diffs stay minimal.
- `tools/wxr/generate.js`'s `warnAboutRenamedUrls()` — cross-references the
  registry's `filePath` against every current page's own `filePath`
  (already available from `parsePage()`'s return value) to catch a content
  file whose `url:` frontmatter changed since it was first registered — see
  "A page's URL changing" in the README. Warns via `console.warn()` at
  detection time rather than the deferred `warnings` array `expandWithAncestors`
  uses, so it's never accidentally swallowed if a later step in the same
  run throws.
- `tools/wxr/waves.json` — the wave → URL-list mapping. Pure data; edit
  directly if the publishing plan changes. Keys starting with `_` are
  documentation, ignored by the loader.
- Validate output any time with `xmllint --noout tools/wxr/output/<file>.xml`
  (built into macOS) — it should always report no errors, since well-formed
  XML is a hard requirement, not a nice-to-have.

## Known limitations (read this if something here doesn't cover your situation)

- **No automatic redirect or WordPress cleanup on a URL rename.** The tool
  warns (see "A page's URL changing") but the 301 redirect and the
  old-page deletion in WordPress are manual steps — this tool has no
  connection to a live WordPress site and can't perform them.
- **No connection to WordPress at all.** Everything above about "already
  live" or "already imported" is inferred from what's in
  `publish-registry.json`, not checked against an actual site. If the
  registry and the live site ever disagree (most likely because someone
  edited a title or date by hand in WordPress — see "Two different places a
  title can change" above), this tool cannot detect that on its own.
- **The bulk-register guard is a heuristic, not a certainty.** More than 25
  new URLs in one run could genuinely be intentional (a fresh bootstrap, or
  several waves combined) — the tool can't tell that apart from a lost
  registry file, so it asks a human instead of guessing either way.
- **Unsynced-machine date registration is mitigated, not prevented.** See
  "Two people generating waves on unsynced machines" above — it fails safe
  (a git conflict) rather than silently, but the tool doesn't stop you from
  causing it in the first place.
