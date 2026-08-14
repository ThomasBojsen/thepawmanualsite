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
 * Rewrites the registry, sorted by URL so a maintainer's `git diff` after
 * committing only ever shows the genuinely new line(s) that were added —
 * never a reshuffle of everything else.
 */
function savePublishRegistry(registry) {
  const out = {
    _readme:
      'URL -> { date, title, filePath } recorded the first time that URL was ever emitted ' +
      'into a WXR file. WordPress recognizes an already-imported page by matching its TITLE ' +
      'AND its DATE together (see "How WordPress decides..." in tools/wxr/README.md), and it ' +
      'keeps both even after a human later publishes the imported Draft by hand — so this file ' +
      "is what makes a page's title+date pair both believable on the live site AND permanently " +
      'stable no matter how many more times it gets regenerated, or how much later. ' +
      'generate.js appends one new entry automatically (via --date) the first time a URL needs ' +
      'one; it never rewrites an entry that already exists — if the current H1 for a URL no ' +
      'longer matches the recorded title, generate.js stops with an error instead of guessing ' +
      'which one is right (silently picking either one reopens the exact duplicate-page bug ' +
      'this file exists to prevent). "filePath" is only used to notice when a URL itself has ' +
      'been renamed in content/ (the same file now defines a different URL) — see ' +
      '"A page\'s URL changing" in tools/wxr/README.md. Commit this file whenever generate.js ' +
      'reports it added new entries. To correct a mistake before anything has actually been ' +
      'imported into WordPress, it is safe to hand-edit an entry directly — it is plain JSON.',
  };
  for (const url of [...registry.keys()].sort()) out[url] = registry.get(url);
  fs.writeFileSync(PUBLISH_REGISTRY_PATH, JSON.stringify(out, null, 2) + '\n', 'utf8');
}

function formatDateOnly(date) {
  const pad = (n) => String(n).padStart(2, '0');
  return `${date.getUTCFullYear()}-${pad(date.getUTCMonth() + 1)}-${pad(date.getUTCDate())}`;
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
 * WordPress does NOT reset a page's post_date when a human later clicks
 * Publish on the imported Draft — whatever date this tool stamps into the
 * WXR file is the date that stays on the live page, forever (confirmed:
 * that is the defect this replaces — a fixed placeholder date, 1970-01-01,
 * was showing up on published pages via
 * template-parts/content/last-updated.php). That rules out both simpler
 * options a hash-style pure function would give:
 *   - A fixed placeholder is reproducible and URL-stable, but ends up on
 *     the live page as a nonsense date — not plausible.
 *   - Leaving the date out and letting WordPress fill one in at publish
 *     time IS plausible, but reopens the original bug: the next wave that
 *     re-includes this URL as an ancestor hub (see README) would need
 *     *some* date to put in wp:post_date, and if that's "whatever moment
 *     this run happens to be," it won't match the date WordPress already
 *     recorded for the live page, the title+date check fails, and the hub
 *     gets duplicated all over again.
 * The exact same argument applies to title, which is why title is stored
 * and checked here too — it can't be recomputed from the URL the way an id
 * can, and re-deriving it fresh from the markdown's current H1 every run
 * (the previous version of this tool did exactly that) silently breaks the
 * same matching the moment someone edits a heading, weeks before anyone
 * notices.
 *
 * A committed per-URL registry is the only option that is both URL-stable
 * forever AND plausible: the date and title are real — whatever the
 * maintainer actually had at the moment they ran the tool for that URL,
 * not an arbitrary placeholder — but captured exactly once, on purpose,
 * and never silently recomputed. Every later run, for any wave, in any
 * order, alongside any other URLs that do or don't exist, reads the same
 * fixed values out of this file instead of ever asking the system clock or
 * re-reading a heading that might have moved — which is also why
 * generate.js never calls `new Date()` as an implicit default, and why a
 * drifted title stops the run instead of being silently re-derived.
 *
 * Adding a new content file only ever adds one new key to this file (via
 * --date, the next time that URL is generated); it never touches an
 * existing key, so no existing page's date or title can shift on its own —
 * only a human, editing the registry or the source H1 by hand, can do
 * that, and editing the H1 is exactly what the title check is watching for.
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
    dateByUrl.set(url, new Date(`${registry.get(url).date}T00:00:00Z`));
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
