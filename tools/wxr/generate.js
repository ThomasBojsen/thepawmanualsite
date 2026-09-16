#!/usr/bin/env node
'use strict';

/**
 * ThePawManual WXR generator.
 *
 * Converts the markdown files in content/ into a WordPress eXtended RSS (WXR)
 * file that Tools -> Import -> WordPress can load. Everything imports as a
 * draft Page with correct parent/child hierarchy (derived from each page's
 * frontmatter `url`) and RankMath postmeta. See tools/wxr/README.md for the
 * full how-to; this file is the implementation.
 *
 * Usage:
 *   node tools/wxr/generate.js --wave 1
 *   node tools/wxr/generate.js --wave 1,2,3
 *   node tools/wxr/generate.js --all
 *   node tools/wxr/generate.js --wave 1 --out tools/wxr/output/custom-name.xml
 */

const fs = require('fs');
const path = require('path');
const crypto = require('crypto');

const { parsePage } = require('./lib/parse-page');
const { buildWxr } = require('./lib/wxr-xml');

const REPO_ROOT = path.resolve(__dirname, '..', '..');
const CONTENT_DIR = path.join(REPO_ROOT, 'content');
const OUTPUT_DIR = path.join(__dirname, 'output');
const WAVES_PATH = path.join(__dirname, 'waves.json');
const PUBLISH_REGISTRY_PATH = path.join(__dirname, 'publish-registry.json');

// Non-page control docs living alongside the content, and the homepage
// (content/README.md: "The homepage is the exception ... every other page is
// a normal WP Page" — home.md's copy is rendered by front-page.php, not
// imported as a Page, so it's excluded from the WXR by default).
const EXCLUDED_FILENAMES = new Set(['README.md', 'SEO-STRATEGY.md', 'SITEMAP.md']);
const HOME_URL = '/';

function findMarkdownFiles(dir) {
  const out = [];
  for (const entry of fs.readdirSync(dir, { withFileTypes: true })) {
    const full = path.join(dir, entry.name);
    if (entry.isDirectory()) {
      out.push(...findMarkdownFiles(full));
    } else if (entry.isFile() && entry.name.endsWith('.md') && !EXCLUDED_FILENAMES.has(entry.name)) {
      out.push(full);
    }
  }
  return out;
}

function loadAllPages() {
  const files = findMarkdownFiles(CONTENT_DIR);
  const pages = new Map(); // url -> page record
  for (const file of files) {
    const raw = fs.readFileSync(file, 'utf8');
    const page = parsePage(raw, path.relative(REPO_ROOT, file));
    if (page.url === HOME_URL) continue; // homepage is a theme template, not a WP Page
    if (pages.has(page.url)) {
      throw new Error(`Duplicate url "${page.url}" — also defined in ${pages.get(page.url).filePath}`);
    }
    pages.set(page.url, page);
  }
  return pages;
}

// If a URL's hash ever collides with another URL's (see assignStableIds
// below), add the losing URL here with any string — it perturbs that one
// URL's hash so it lands on a different id, without touching anyone else's.
// Empty because it has never been needed; keep it that way unless the
// generator's collision check actually throws.
const ID_SALT_OVERRIDES = {};

// Floor and width of the id space a URL's hash is folded into. 100 keeps
// generated ids out of WordPress's own low, human/plugin-assigned range;
// the width (90 million) is generously large relative to the ~150 pages
// content/ has today, so that even as the site grows toward the thousands
// of pages CLAUDE.md's programmatic-SEO roadmap anticipates, the odds of
// two URLs' hashes landing on the same id (a birthday-paradox problem,
// not a "will id 151 ever get used twice" problem) stay small. It is not
// zero, which is exactly why assignStableIds() below checks for real
// rather than assuming it away.
const ID_FLOOR = 100;
const ID_SPACE = 90_000_000;

/**
 * A page's post_id is a hash of its URL text — nothing else. That is the
 * fix for the bug this file used to have: a per-run sequential counter
 * gives a URL a different id depending on what else is being generated
 * alongside it (which wave, in what order, how many). A hash depends only
 * on the URL's own characters, so /dogs/ gets the same id whether it's
 * emitted alone, pulled in as wave 2's ancestor, or included in --all —
 * today, tomorrow, or after ten more waves ship.
 *
 * This also answers "what happens to existing ids when someone adds a new
 * page", which the id scheme has to survive without a persisted registry
 * file recording what's already been handed out:
 *   - Adding a new content file adds one new URL to the corpus. That URL's
 *     id is a hash of ITS OWN text, computed independently of every other
 *     URL that does or doesn't exist. No existing page's id is a function
 *     of the new page, so no existing page's id can change.
 *   - Contrast with the tempting alternative — "id = this URL's position
 *     in the full sorted list of every URL in content/". That also gives
 *     per-URL stability *within one full run*, but it silently renumbers
 *     every page whose URL sorts after the new one the moment somebody
 *     adds a page — the same instability this file is being fixed to
 *     remove, just one level removed and easier to miss in review. That
 *     scheme was rejected for exactly this reason.
 *   - The one way a hash scheme *can* renumber something is a genuine
 *     collision: two different URLs whose hashes land on the same id. When
 *     that happens the newly-added URL is the one that needs to move (via
 *     ID_SALT_OVERRIDES above); the already-shipped URL never has to.
 */
function idForUrl(url) {
  const salt = ID_SALT_OVERRIDES[url] || '';
  const digest = crypto.createHash('sha1').update(url + salt).digest();
  return ID_FLOOR + (digest.readUInt32BE(0) % ID_SPACE);
}

/**
 * Assigns an id to every known page — the WHOLE corpus, not just whatever
 * this run is emitting — and fails loudly on any collision. Checking the
 * full corpus every run (cheap: content/ is ~150 files) means a collision
 * is caught the moment it exists, even if the two colliding URLs happen to
 * fall in different waves and would otherwise never appear in the same
 * output file to reveal the clash.
 */
function assignStableIds(allPages) {
  const idByUrl = new Map();
  const urlById = new Map();
  for (const url of allPages.keys()) {
    const id = idForUrl(url);
    const existing = urlById.get(id);
    if (existing) {
      throw new Error(
        `post_id collision: "${url}" and "${existing}" both hash to ${id}. ` +
          `Add one of them to ID_SALT_OVERRIDES in generate.js (any string value works) ` +
          `to move it to a different id, then re-run.`
      );
    }
    idByUrl.set(url, id);
    urlById.set(id, url);
  }
  return idByUrl;
}

function parentUrlOf(url) {
  const segments = url.replace(/^\/|\/$/g, '').split('/').filter(Boolean);
  if (segments.length <= 1) return null; // top-level page: parent is the site root, not a WP page
  return '/' + segments.slice(0, -1).join('/') + '/';
}

function depthOf(url) {
  return url.replace(/^\/|\/$/g, '').split('/').filter(Boolean).length;
}

/**
 * Given a set of target URLs, pull in every ancestor hub page required for
 * WordPress parent/child hierarchy — even if that ancestor's own wave hasn't
 * come up yet. Per the brief: never emit an orphan whose parent is absent.
 */
function expandWithAncestors(targetUrls, allPages, warn) {
  const result = new Set();
  const queue = [...targetUrls];
  while (queue.length) {
    const url = queue.pop();
    if (result.has(url)) continue;
    if (!allPages.has(url)) {
      warn(`skipping "${url}" — no content file defines this URL yet`);
      continue;
    }
    result.add(url);
    const parent = parentUrlOf(url);
    if (parent && !result.has(parent)) {
      queue.push(parent);
    }
  }
  return result;
}

function loadWaves() {
  const raw = JSON.parse(fs.readFileSync(WAVES_PATH, 'utf8'));
  const waves = {};
  for (const [key, value] of Object.entries(raw)) {
    if (key.startsWith('_')) continue; // documentation keys, not wave data
    waves[key] = value;
  }
  return waves;
}

/**
 * Loads the committed per-URL registry: for every URL ever emitted, the
 * date and title it was FIRST emitted with, plus the content file that
 * produced it at the time. Missing file = a fresh checkout that has never
 * registered anything yet, not an error — so day one doesn't require
 * hand-creating an empty JSON file first.
 */
function loadPublishRegistry() {
  if (!fs.existsSync(PUBLISH_REGISTRY_PATH)) return new Map();
  const raw = JSON.parse(fs.readFileSync(PUBLISH_REGISTRY_PATH, 'utf8'));
  const registry = new Map();
  for (const [url, value] of Object.entries(raw)) {
    if (url.startsWith('_')) continue; // documentation keys, e.g. _readme
    registry.set(url, value); // { date, title, filePath }
  }
  return registry;
}

/**
 * The registry file's own `_readme` key — the single source of truth for
 * what this file is and how to read a "date" value, kept here (not
 * retyped at the top of the JSON by hand) so the file this tool writes and
 * the docs a human reads by opening it can never drift apart the way the
 * false "WordPress never resets post_date" claim once did (see the
 * now-corrected comment on resolvePublishRegistry, and the 2026-09-14
 * wave-1 date correction that this text documents).
 */
const REGISTRY_README =
  'URL -> { date, title, filePath } recorded the first time that URL was ever emitted into a ' +
  'WXR file. WordPress recognizes an already-imported page by matching its TITLE AND its DATE ' +
  'together (see "How WordPress decides..." in tools/wxr/README.md). "date" holds ONE OF TWO ' +
  'shapes, and the shape itself is the signal for which one you have: ' +
  '(1) a bare calendar date, "YYYY-MM-DD" — a RESERVATION, not a record. This URL has never been ' +
  'confirmed against a live WordPress post; generate.js will emit it at midnight UTC, which is ' +
  'fine because nobody has published this page yet, so no live timestamp exists yet to disagree ' +
  'with it. (2) a full ISO-8601 datetime with a UTC offset, e.g. "2026-09-10T20:03:42+00:00" — a ' +
  'HARVESTED value, copied verbatim (including the offset) from that exact page\'s live RankMath ' +
  'JSON-LD "datePublished" field. Every wave-1 URL (see tools/wxr/waves.json wave "1") was ' +
  'corrected to this shape on 2026-09-14 after the bare-midnight dates were found to have drifted ' +
  'from what WordPress actually put on the live pages once they were published — the fix that ' +
  'shape (2) exists to make permanent. generate.js reads either shape the same way: a bare date ' +
  'gets "T00:00:00Z" appended, a full ISO string is parsed as-is (see parseRegistryDateString). ' +
  'IMPORTANT — why bare dates drift and full ones don\'t: this tool always imports pages as ' +
  '`<wp:status>draft</wp:status>` (see tools/wxr/lib/wxr-xml.js and the README\'s "What it does"). ' +
  'A WordPress draft does not keep the wp:post_date this tool wrote as its real "this is when it ' +
  'went live" date — internally a draft\'s post_date is treated as unset, and the moment a human ' +
  'clicks Publish on it, WordPress stamps that literal moment as the post\'s real post_date/ ' +
  'post_date_gmt, overwriting whatever this tool imported it with. So a date recorded here is ' +
  'only ever guaranteed to still be accurate for a URL that has NEVER been published in ' +
  'WordPress yet (shape 1, correctly). The instant a human publishes it, this tool\'s stamped ' +
  'date silently goes stale — it is still remembered here, but it no longer matches the live ' +
  'page, exactly the drift that hit all 17 wave-1 URLs (16 of them share 2026-09-10T20:03:42+00:00; ' +
  'one, /pet-guides/pet-travel/, was republished separately at 2026-09-10T20:20:17+00:00 — always ' +
  'harvest per-URL, never assume one page\'s value for another). If this happens again for a URL ' +
  'already in this file: open the live page, read `datePublished` out of its RankMath JSON-LD ' +
  '(view source or a console `document.querySelectorAll(\'script[type="application/ld+json"]\')` ' +
  'search — the same way the 2026-09-14 correction was harvested), and hand-edit just that entry\'s ' +
  '"date" to the exact live string, offset included; generate.js also now warns automatically the ' +
  'moment a run re-emits a URL whose registry date is still bare (see warnAboutUnharvestedDates in ' +
  'generate.js and "Re-harvesting a drifted date" in tools/wxr/README.md) so this cannot rot ' +
  'silently again. generate.js appends one new bare-date entry automatically (via --date) the ' +
  'first time a URL needs one; it never rewrites an entry that already exists on its own — if the ' +
  'current H1 for a URL no longer matches the recorded title, generate.js stops with an error ' +
  'instead of guessing which one is right (silently picking either one reopens the exact ' +
  'duplicate-page bug this file exists to prevent). "filePath" is only used to notice when a URL ' +
  'itself has been renamed in content/ (the same file now defines a different URL) — see "A ' +
  'page\'s URL changing" in tools/wxr/README.md. Commit this file whenever generate.js reports it ' +
  'added new entries, or whenever you hand-correct a drifted date. To correct a mistake before ' +
  'anything has actually been imported into WordPress, it is safe to hand-edit an entry directly ' +
  '— it is plain JSON; correcting a drifted live date is exactly as safe, since this file is a ' +
  'record of fact for WordPress\'s benefit, not an input WordPress reads live.';

/**
 * Rewrites the registry, sorted by URL so a maintainer's `git diff` after
 * committing only ever shows the genuinely new line(s) that were added —
 * never a reshuffle of everything else.
 */
function savePublishRegistry(registry) {
  const out = { _readme: REGISTRY_README };
  for (const url of [...registry.keys()].sort()) out[url] = registry.get(url);
  fs.writeFileSync(PUBLISH_REGISTRY_PATH, JSON.stringify(out, null, 2) + '\n', 'utf8');
}

function formatDateOnly(date) {
  const pad = (n) => String(n).padStart(2, '0');
  return `${date.getUTCFullYear()}-${pad(date.getUTCMonth() + 1)}-${pad(date.getUTCDate())}`;
}

// A registry "date" value is a bare calendar date ("2026-08-14") for a URL
// that's never been confirmed against a live WordPress post, or a full
// ISO-8601 datetime with a UTC offset ("2026-09-10T20:03:42+00:00") for one
// that has — see REGISTRY_README for why both shapes exist and are safe to
// mix in the same file.
const BARE_DATE_RE = /^\d{4}-\d{2}-\d{2}$/;

/**
 * Turns a registry "date" string into a Date, honoring whichever of the two
 * shapes above it is: a bare date is midnight UTC (unchanged from before —
 * exactly right for a URL nothing live exists to disagree with yet); a full
 * ISO string (with its own offset) is parsed as-is, so a harvested value
 * like "2026-09-10T20:20:17+00:00" round-trips through formatWpDate
 * (tools/wxr/lib/wxr-xml.js) to the exact live wp:post_date, not midnight.
 */
function parseRegistryDateString(dateStr) {
  const date = BARE_DATE_RE.test(dateStr) ? new Date(`${dateStr}T00:00:00Z`) : new Date(dateStr);
  if (Number.isNaN(date.getTime())) {
    throw new Error(
      `Registry date "${dateStr}" isn't a date Node.js understands. Expected "YYYY-MM-DD" or a ` +
        `full ISO-8601 datetime like "2026-09-10T20:03:42+00:00" (see REGISTRY_README in ` +
        `generate.js).`
    );
  }
  return date;
}

/**
 * Warns (doesn't throw — see the call site) when a run is about to re-emit
 * a URL whose registry date is still a bare, never-harvested placeholder
 * AND that entry already existed on disk before this run started, i.e. a
 * URL a human may already have imported — and possibly published — into
 * WordPress. A URL freshly registered by *this* run can't trigger it: it
 * obviously hasn't been imported by anyone yet, so `registryBeforeThisRun`
 * must be the Map as loaded from disk, read before resolvePublishRegistry
 * mutates it for any newly-registered URL in this same run.
 *
 * This is deliberately a nudge, not a gate (see resolvePublishRegistry's
 * title-drift check for the *blocking* version of a similar problem): there
 * is no way for this script to see live WordPress state itself (no network
 * access, and the production site sits behind a WAF — see the README), so
 * it cannot know FOR CERTAIN whether a given bare-dated URL has actually
 * been published yet or is still sitting untouched in Drafts. Blocking on
 * a maybe would stop ordinary, safe re-generations of pages nobody has
 * touched in WordPress yet. Surfacing the possibility and asking a human to
 * check is what fixed wave 1 on 2026-09-14, and is what's meant to catch
 * the next one before it ships instead of after.
 */
function warnAboutUnharvestedDates(urls, registryBeforeThisRun, warn) {
  for (const url of urls) {
    const entry = registryBeforeThisRun.get(url);
    if (entry && BARE_DATE_RE.test(entry.date)) {
      warn(
        `${url} still has a bare registry date ("${entry.date}", no time-of-day) from an earlier ` +
          `run. If this page has already been imported into WordPress and a human has clicked ` +
          `Publish on it, its live post_date has almost certainly drifted away from this ` +
          `placeholder (WordPress stamps the real publish moment on Publish — see the corrected ` +
          `comment on resolvePublishRegistry in generate.js) and this run's WXR will NOT match the ` +
          `live page. Before trusting a re-import of ${url} as a safe no-op: open the live page, ` +
          `read "datePublished" out of its RankMath JSON-LD, and compare it to "${entry.date}". If ` +
          `they disagree, hand-edit this URL's "date" in publish-registry.json to the live value ` +
          `(full ISO string, offset included) before regenerating. See "Re-harvesting a drifted ` +
          `date" in tools/wxr/README.md. (If this URL is genuinely still an unpublished Draft or ` +
          `has never been imported at all, there's nothing to do — this warning is a reminder to ` +
          `check, not a sign anything is wrong.)`
      );
    }
  }
}

// More new registrations in one run than this is refused unless the human
// says so explicitly (--confirm-bulk-register) — see resolvePublishRegistry
// for what this guards against. Set comfortably above the largest single
// wave in waves.json (19, at the time of writing) so ordinary first-time
// wave generation is never affected by it.
const BULK_REGISTER_THRESHOLD = 25;

/**
 * Both the id hash (idForUrl) and this registry are keyed on a URL's TEXT —
 * on purpose, so a page's identity survives being generated in different
 * waves, any number of times, in any order. The blind spot that creates:
 * if a content file's `url:` frontmatter is edited so the SAME file now
 * produces a DIFFERENT URL, nothing keyed on URL text notices — the new
 * URL just looks like a brand-new page, while the OLD URL quietly stops
 * being emitted at all. If that old URL was ever actually imported into
 * WordPress, it is now an orphaned page sitting at a dead address, and
 * this tool can regenerate content forever without ever mentioning it.
 *
 * Detecting it: every registry entry also records which content FILE
 * produced it. A rename shows up as "this file's current URL doesn't
 * match the URL its own file path was registered under" — a precise,
 * non-fuzzy signal, unlike trying to match by title or content
 * similarity. This only warns, it doesn't throw: unlike title drift (see
 * resolvePublishRegistry), a rename doesn't make THIS run's own XML wrong
 * — the new URL is legitimately new content and registers normally. The
 * risk is entirely on the *live site*, which this tool can't see, so it
 * can't safely decide the old page's fate on its own — seeing the warning
 * and acting on it is on the human. See "A page's URL changing" in
 * tools/wxr/README.md for the manual redirect/delete steps.
 */
function warnAboutRenamedUrls(allPages, registry) {
  const byFilePath = new Map();
  for (const [url, entry] of registry) {
    if (entry.filePath) byFilePath.set(entry.filePath, { url, ...entry });
  }
  for (const page of allPages.values()) {
    const prior = byFilePath.get(page.filePath);
    if (prior && prior.url !== page.url) {
      console.warn(
        `Warning: ${page.filePath} looks renamed — registered as ${prior.url} (first generated ` +
          `${prior.date}) but now defines ${page.url}. If ${prior.url} was ever imported into ` +
          `WordPress, it is now orphaned at its old address: set up a redirect from ${prior.url} ` +
          `to ${page.url}, then delete or unpublish the old page. See "A page's URL changing" in ` +
          `tools/wxr/README.md.`
      );
    }
  }
}

/**
 * Resolves a Date for every URL about to be emitted, reading the committed
 * registry where an entry already exists and registering a fresh one (via
 * --date) for any URL that has never been emitted before. Also enforces
 * that a URL's title hasn't drifted from what's recorded, since WordPress
 * matches an already-imported page by title AND date TOGETHER — getting
 * the date right while letting the title silently drift reopens the exact
 * bug this file exists to fix, just from the other side.
 *
 * Why a registry and not something computed, the way ids are (idForUrl):
 * a registry is the only way to give a URL a date that survives WordPress
 * actually publishing the page — CORRECTED 2026-09-14, this section used
 * to assert the opposite ("WordPress does NOT reset a page's post_date
 * when a human later clicks Publish... the date stays on the live page,
 * forever"). That claim was false, and it is exactly what let this whole
 * mechanism drift silently: this tool always imports every page as
 * `<wp:status>draft</wp:status>` (tools/wxr/lib/wxr-xml.js) — nothing goes
 * live automatically, by design (README, "What it does"). A freshly
 * imported WordPress draft does NOT keep the wp:post_date this tool wrote
 * as its real "this went live" timestamp; internally a draft's post_date
 * is treated as unset, and the instant a human clicks Publish on it in
 * wp-admin, WordPress stamps that literal moment as the post's real
 * post_date/post_date_gmt — overwriting whatever this tool imported it
 * with. Proof: every one of wave 1's 17 URLs was registered here at
 * "2026-08-14" (midnight, no time-of-day), and by the time the live site
 * was checked on 2026-09-14, their actual RankMath `datePublished` was
 * "2026-09-10T20:03:42+00:00" (16 of them) or "2026-09-10T20:20:17+00:00"
 * (/pet-guides/pet-travel/, republished separately) — not 2026-08-14 at
 * all. See publish-registry.json's own `_readme` (REGISTRY_README, above
 * in this file) for the two date shapes this drift is why the registry
 * now supports, and warnAboutUnharvestedDates (below) for the guard added
 * so a URL sitting on a stale bare date doesn't rot here unnoticed after
 * a human publishes it.
 *
 * What's still true, and still the actual reason a *committed* registry
 * (rather than something computed, like idForUrl) is unavoidable: a
 * plausible, real date for a URL that HASN'T been published yet — the
 * common case, ~108 of ~126 URLs at the time of writing — has no pure,
 * computable source at all. Two simpler options were tried and rejected:
 *   - A fixed placeholder (1970-01-01) is reproducible and URL-stable, but
 *     is a nonsense date if it ever ends up on a live page — this was a
 *     real, shipped defect (template-parts/content/last-updated.php was
 *     showing "January 1970" on published pages) that this registry
 *     replaced.
 *   - Leaving the date out and letting WordPress fill one in at import
 *     time doesn't work either, now for the opposite reason from the
 *     "corrected" paragraph above: a wp:post_date IS required in the WXR
 *     file for the ancestor-hub matching (title+date) to have anything
 *     stable to compare against BEFORE a page is published — without a
 *     registry, every re-generation of an unpublished URL would use
 *     "whatever moment this run happens to be," and the same URL
 *     generated twice, days apart, would carry two different dates.
 * So the registry's job, precisely stated, is: give every unpublished URL
 * one fixed, reusable placeholder date (good enough — nothing live exists
 * yet to contradict it), and give every ALREADY-PUBLISHED URL its real
 * harvested live date (required — something live already exists, and it
 * disagrees with any placeholder). Distinguishing the two is now the
 * bare-date-vs-full-ISO-string shape (REGISTRY_README), not an assumption
 * that dates never change once written.
 *
 * The exact same "can't be computed, must be remembered" argument applies
 * to title, which is why title is stored and checked here too — it can't
 * be recomputed from the URL the way an id can, and re-deriving it fresh
 * from the markdown's current H1 every run (the previous version of this
 * tool did exactly that) silently breaks the same matching the moment
 * someone edits a heading, weeks before anyone notices. Unlike date,
 * WordPress does NOT appear to touch a page's title on publish — this is
 * why only date needed the two-shape fix above.
 *
 * Adding a new content file only ever adds one new key to this file (via
 * --date, the next time that URL is generated); it never touches an
 * existing key on its own, so no existing page's date or title can shift
 * by itself — only a human can do that, either by editing the H1 (which
 * the title check is watching for) or by hand-correcting a date after
 * re-harvesting it from a live, already-published page (which is exactly
 * what the 2026-09-14 fix did for wave 1, and what
 * warnAboutUnharvestedDates now nudges toward doing again if it's ever
 * needed).
 */
function resolvePublishRegistry(urls, allPages, registry, dateArg, allowBulk) {
  // Title check first: this can't be worked around by --date the way a
  // missing entry can, because there is no safe automatic answer — see the
  // thrown message for what the human has to decide.
  const drifted = [];
  for (const url of urls) {
    const entry = registry.get(url);
    const currentTitle = allPages.get(url).title;
    if (entry && entry.title !== currentTitle) {
      drifted.push({ url, registeredTitle: entry.title, currentTitle });
    }
  }
  if (drifted.length) {
    const registryPath = path.relative(REPO_ROOT, PUBLISH_REGISTRY_PATH);
    throw new Error(
      drifted
        .map(
          ({ url, registeredTitle, currentTitle }) =>
            `${url}'s title has changed since it was first generated:\n` +
            `  registered (${registryPath}): "${registeredTitle}"\n` +
            `  current (this page's H1 right now):  "${currentTitle}"\n` +
            `WordPress recognizes an already-imported page by matching its title AND its date\n` +
            `together (see tools/wxr/README.md, "How WordPress decides..."). Regenerating with the\n` +
            `new title won't update the live page — it creates a DUPLICATE the next time any wave\n` +
            `re-includes ${url} as an ancestor hub, possibly weeks from now. Pick exactly one:\n` +
            `  1. Mistake? Put the H1 in the markdown file back to the registered title.\n` +
            `  2. Deliberate, and this page was NEVER imported into WordPress yet? Edit its\n` +
            `     "title" in ${registryPath} by hand to match the new H1.\n` +
            `  3. Deliberate, and this page IS already live? Rename it to match in WordPress\n` +
            `     FIRST (Pages -> edit -> Title -> Update), THEN edit the registry to match.`
        )
        .join('\n\n')
    );
  }

  // Brand-new URLs need a date and title registered via --date.
  const missing = urls.filter((url) => !registry.has(url));

  if (missing.length && !dateArg) {
    throw new Error(
      `these URLs have no date yet in ${path.relative(REPO_ROOT, PUBLISH_REGISTRY_PATH)}:\n` +
        missing.map((u) => `  - ${u}`).join('\n') +
        `\nRun again with --date YYYY-MM-DD (e.g. today's date) to register one for them. ` +
        `Existing entries are never overwritten, so this is always safe — see tools/wxr/README.md.`
    );
  }

  // Refuse to mass-mint dates without an explicit human confirmation. This
  // is the guard against a lost/emptied registry file: if
  // publish-registry.json were wiped, every URL in the next run would show
  // up as "missing", and a careless --date would silently re-mint fresh
  // dates (and titles) for pages that are already live — the next wave
  // that pulls any of them in as an ancestor would then fail to match and
  // duplicate them all over again, exactly the bug this mechanism exists
  // to prevent. There's no way to reliably tell "the registry was wiped"
  // apart from "this genuinely is the first time this many pages have ever
  // been generated" — both look identical from here: a lot of missing
  // URLs. So instead of guessing, any run that would register more than a
  // normal single wave's worth of brand-new URLs has to say out loud that
  // it means to.
  if (missing.length > BULK_REGISTER_THRESHOLD && !allowBulk) {
    const registryPath = path.relative(REPO_ROOT, PUBLISH_REGISTRY_PATH);
    throw new Error(
      `this run would register ${missing.length} brand-new URLs at once — more than ` +
        `${BULK_REGISTER_THRESHOLD}, comfortably more than any single wave in waves.json. That's ` +
        `either a deliberate bulk operation (a fresh checkout running --all for the first time, ` +
        `or several waves combined) or a sign that ${registryPath} was lost, emptied, or reset by ` +
        `accident — in which case proceeding would silently mint fresh dates and titles for pages ` +
        `that may already be live, and the next wave to reference any of them would duplicate them ` +
        `all over again. If you're sure this is intentional, re-run with --confirm-bulk-register. ` +
        `If you're not sure, STOP and check git history (or a backup) for ${registryPath} first.`
    );
  }

  if (missing.length) {
    for (const url of missing) {
      const page = allPages.get(url);
      registry.set(url, { date: formatDateOnly(dateArg), title: page.title, filePath: page.filePath });
    }
    savePublishRegistry(registry);
    console.log(
      `Registered ${missing.length} new date(s) in ${path.relative(REPO_ROOT, PUBLISH_REGISTRY_PATH)} — commit this file.`
    );
  } else if (dateArg) {
    console.log('--date had nothing new to register — every URL in this run already has a date.');
  }

  const dateByUrl = new Map();
  for (const url of urls) {
    dateByUrl.set(url, parseRegistryDateString(registry.get(url).date));
  }
  return dateByUrl;
}

function parseArgs(argv) {
  const args = { wave: null, all: false, out: null, date: null, confirmBulkRegister: false };
  for (let i = 0; i < argv.length; i++) {
    const arg = argv[i];
    if (arg === '--all') {
      args.all = true;
    } else if (arg === '--wave') {
      args.wave = argv[++i];
    } else if (arg.startsWith('--wave=')) {
      args.wave = arg.slice('--wave='.length);
    } else if (arg === '--out') {
      args.out = argv[++i];
    } else if (arg.startsWith('--out=')) {
      args.out = arg.slice('--out='.length);
    } else if (arg === '--date') {
      args.date = argv[++i];
    } else if (arg.startsWith('--date=')) {
      args.date = arg.slice('--date='.length);
    } else if (arg === '--confirm-bulk-register') {
      args.confirmBulkRegister = true;
    } else if (arg === '--help' || arg === '-h') {
      args.help = true;
    } else {
      throw new Error(`Unrecognized argument: ${arg}`);
    }
  }
  return args;
}

/**
 * Parses --date into a Date, or throws a message a non-developer can act on.
 * Accepts anything Date() itself accepts (an ISO date like "2026-08-14" or a
 * full timestamp like "2026-08-14T09:00:00Z") since that is what most people
 * reach for first; resolvePublishRegistry() truncates it to a plain
 * calendar date before it's ever stored.
 */
function parseDateArg(value) {
  const date = new Date(value);
  if (Number.isNaN(date.getTime())) {
    throw new Error(`--date "${value}" isn't a date Node.js understands. Try "2026-08-14" or "2026-08-14T09:00:00Z".`);
  }
  return date;
}

function printHelp() {
  console.log(`ThePawManual WXR generator

Usage:
  node tools/wxr/generate.js --wave 1            Generate wave 1 only
  node tools/wxr/generate.js --wave 1,2,3         Generate a comma-separated set of waves
  node tools/wxr/generate.js --all                Generate every page in content/ (except home.md)
  node tools/wxr/generate.js --wave 1 --out FILE  Write to a specific path instead of the default
  node tools/wxr/generate.js --wave 1 --date 2026-08-14   Register today's date for any URL in
                                                            this run that doesn't have one yet
  node tools/wxr/generate.js --all --date 2026-08-14 \
    --confirm-bulk-register                              Needed only when a run would register
                                                            more than ${BULK_REGISTER_THRESHOLD} brand-new URLs at once

Waves are defined in tools/wxr/waves.json, per content/SEO-STRATEGY.md §4.
Ancestor hub pages required for parent/child hierarchy are always included
automatically, even if their own wave hasn't been requested.

Every page's date AND title come from tools/wxr/publish-registry.json, a
committed registry — a URL keeps the same date and title forever, in every
file, no matter how many more times it's regenerated (see the README's "How
WordPress decides this page is already here" section for why that has to be
true). The first time a URL is generated it isn't in the registry yet, so
you must pass --date to register it; every run after that reuses it
automatically and --date is not needed (and has no effect — an existing
entry is never overwritten). If a page's H1 changes after it's registered,
the tool stops with an error rather than guessing which title is right.
`);
}

function main() {
  const args = parseArgs(process.argv.slice(2));
  if (args.help || (!args.wave && !args.all)) {
    printHelp();
    if (!args.help) process.exitCode = 1;
    return;
  }

  const allPages = loadAllPages();
  const warnings = [];
  const warn = (msg) => warnings.push(msg);

  let targetUrls;
  let label;
  if (args.all) {
    targetUrls = new Set(allPages.keys());
    label = 'all';
  } else {
    const waves = loadWaves();
    const requested = args.wave.split(',').map((s) => s.trim()).filter(Boolean);
    const urls = new Set();
    for (const w of requested) {
      const list = waves[w];
      if (!list) {
        throw new Error(`Unknown wave "${w}" — valid waves are: ${Object.keys(waves).join(', ')}`);
      }
      for (const u of list) urls.add(u);
    }
    targetUrls = urls;
    label = `wave${requested.length > 1 ? 's' : ''}-${requested.join('-')}`;
  }

  const finalUrls = expandWithAncestors(targetUrls, allPages, warn);

  // Parents before children: sort by URL depth, then alphabetically for a
  // stable, readable ordering within a depth level.
  const orderedUrls = [...finalUrls].sort((a, b) => {
    const d = depthOf(a) - depthOf(b);
    return d !== 0 ? d : a.localeCompare(b);
  });

  // Computed from allPages (the whole corpus), not orderedUrls (just this
  // run's output) — see assignStableIds' own comment for why that's the
  // part that makes a URL's id independent of which wave(s) are running.
  const idByUrl = assignStableIds(allPages);

  // Checked against the whole corpus, same reasoning as assignStableIds:
  // catch a renamed URL the moment it exists, not only when someone
  // happens to generate a wave that touches it.
  const registry = loadPublishRegistry();
  warnAboutRenamedUrls(allPages, registry);

  // Read BEFORE resolvePublishRegistry mutates `registry` below (which adds
  // an entry for any URL this run registers for the first time) — a URL
  // this run is registering for the first time cannot have been published
  // by anyone yet, so it must never trigger this warning; only a URL that
  // already had a bare-date entry on disk, from some earlier run, can have.
  warnAboutUnharvestedDates(orderedUrls, registry, warn);

  // Every URL in this run needs a date and a title; resolvePublishRegistry
  // registers both (via --date) for any URL that's never been generated
  // before, reuses the committed ones for every URL that already has an
  // entry, and refuses to proceed if the current H1 no longer matches the
  // recorded title — see its own comment for why that's what keeps a
  // re-included ancestor hub (e.g. /dogs/ showing up again in wave 2's
  // file) from getting duplicated.
  const dateArg = args.date ? parseDateArg(args.date) : null;
  const dateByUrl = resolvePublishRegistry(orderedUrls, allPages, registry, dateArg, args.confirmBulkRegister);

  const emitPages = orderedUrls.map((url) => {
    const page = allPages.get(url);
    const parentUrl = parentUrlOf(url);
    const parentId = parentUrl && idByUrl.has(parentUrl) ? idByUrl.get(parentUrl) : 0;
    return {
      id: idByUrl.get(url),
      parentId,
      url: page.url,
      title: page.title,
      contentHtml: page.contentHtml,
      seoTitle: page.seoTitle,
      metaDescription: page.metaDescription,
      date: dateByUrl.get(url),
    };
  });

  const xml = buildWxr(emitPages);

  if (!fs.existsSync(OUTPUT_DIR)) fs.mkdirSync(OUTPUT_DIR, { recursive: true });
  const outPath = args.out
    ? path.resolve(REPO_ROOT, args.out)
    : path.join(OUTPUT_DIR, `thepawmanual-${label}.xml`);
  fs.writeFileSync(outPath, xml, 'utf8');

  console.log(`Wrote ${emitPages.length} page(s) to ${path.relative(REPO_ROOT, outPath)}`);
  const faqCount = orderedUrls.filter((u) => allPages.get(u).hasFaq).length;
  console.log(`  ${faqCount} page(s) carry FAQ JSON-LD.`);
  if (warnings.length) {
    console.log('Warnings:');
    for (const w of warnings) console.log(`  - ${w}`);
  }
}

try {
  main();
} catch (err) {
  console.error(`Error: ${err.message}`);
  process.exitCode = 1;
}
