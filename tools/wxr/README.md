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

**Re-importing a wave** (e.g. after a content fix) creates new duplicate Pages
rather than updating the old ones — the WordPress importer matches by
`post_id`, and each generator run assigns fresh sequential IDs. Delete the old
draft Pages for that wave before re-importing, or import into a scratch/staging
site first.

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
  prints usage.
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
- `tools/wxr/lib/wxr-xml.js` — builds the actual WXR/RSS XML, sequential
  post IDs, parent-ID wiring, RankMath postmeta, CDATA escaping.
- `tools/wxr/waves.json` — the wave → URL-list mapping. Pure data; edit
  directly if the publishing plan changes. Keys starting with `_` are
  documentation, ignored by the loader.
- Validate output any time with `xmllint --noout tools/wxr/output/<file>.xml`
  (built into macOS) — it should always report no errors, since well-formed
  XML is a hard requirement, not a nice-to-have.
