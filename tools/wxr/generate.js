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

const { parsePage } = require('./lib/parse-page');
const { buildWxr } = require('./lib/wxr-xml');

const REPO_ROOT = path.resolve(__dirname, '..', '..');
const CONTENT_DIR = path.join(REPO_ROOT, 'content');
const OUTPUT_DIR = path.join(__dirname, 'output');
const WAVES_PATH = path.join(__dirname, 'waves.json');

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

function parseArgs(argv) {
  const args = { wave: null, all: false, out: null };
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
    } else if (arg === '--help' || arg === '-h') {
      args.help = true;
    } else {
      throw new Error(`Unrecognized argument: ${arg}`);
    }
  }
  return args;
}

function printHelp() {
  console.log(`ThePawManual WXR generator

Usage:
  node tools/wxr/generate.js --wave 1            Generate wave 1 only
  node tools/wxr/generate.js --wave 1,2,3         Generate a comma-separated set of waves
  node tools/wxr/generate.js --all                Generate every page in content/ (except home.md)
  node tools/wxr/generate.js --wave 1 --out FILE  Write to a specific path instead of the default

Waves are defined in tools/wxr/waves.json, per content/SEO-STRATEGY.md §4.
Ancestor hub pages required for parent/child hierarchy are always included
automatically, even if their own wave hasn't been requested.
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

  const idByUrl = new Map();
  let nextId = 100;
  for (const url of orderedUrls) {
    idByUrl.set(url, nextId++);
  }

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
