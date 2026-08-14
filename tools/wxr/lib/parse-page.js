'use strict';

const { marked } = require('marked');
const { splitFrontmatter } = require('./frontmatter');
const { htmlToGutenberg, faqScriptToGutenbergHtml } = require('./gutenberg');

// Headings that mark the start of a non-publishable section. Everything from
// the earliest match of these to end of file must be removed before the
// content ever reaches WordPress — per content/README.md, these sections carry
// fact-check flags and implementation notes that must never leak publicly.
const NOTES_HEADINGS = [
  '## Notes (not page copy)',
  '## Expansion suggestions (not page copy)',
  '## Implementation notes (not page copy)',
];

/**
 * Remove the notes/expansion/implementation section and any preceding `---`
 * horizontal rule that directly introduces it (with its surrounding blank
 * lines), leaving only publishable copy.
 */
function stripNotesSection(body) {
  let cutIndex = -1;
  for (const heading of NOTES_HEADINGS) {
    const idx = body.indexOf(`\n${heading}`);
    if (idx !== -1 && (cutIndex === -1 || idx < cutIndex)) cutIndex = idx;
    // Also handle the heading appearing as the very first line (shouldn't
    // happen in practice — every page has real copy first — but be safe).
    if (body.startsWith(heading) && (cutIndex === -1 || 0 < cutIndex)) cutIndex = 0;
  }
  if (cutIndex === -1) {
    // No notes section found. Not expected (every content page has one), but
    // don't fail the whole build over it — just pass the body through.
    return { body, hadNotesSection: false };
  }

  let before = body.slice(0, cutIndex);
  // Strip a trailing standalone "---" horizontal rule (with blank lines
  // around it) that directly precedes the notes heading.
  before = before.replace(/\n{1,}-{3,}\s*\n*$/, '\n');
  return { body: before.replace(/\s+$/, '') + '\n', hadNotesSection: true };
}

/**
 * Extract the trailing ```html fenced FAQPage JSON-LD block. Returns the raw
 * inner HTML (the <script> tag, verbatim) and the body with the fence (and
 * the RankMath authoring comment that sometimes precedes it) removed. The
 * visible FAQ text above the fence is left untouched — it flows through the
 * normal markdown conversion.
 */
function extractFaqJsonLd(body) {
  const fenceRe = /```html\n([\s\S]*?)\n```/;
  const m = body.match(fenceRe);
  if (!m) return { body, faqHtml: null };

  const before = body.slice(0, m.index);
  const after = body.slice(m.index + m[0].length);

  // Drop a standalone HTML comment (e.g. the "<!-- FAQ Schema ... -->" authoring
  // note in home.md) that sits directly above the fence.
  const cleanedBefore = before.replace(/<!--[^\n]*-->\s*\n*$/, '');

  const faqHtml = m[1].trim();
  const rejoined = `${cleanedBefore.replace(/\s+$/, '')}\n\n${after.replace(/^\s+/, '')}`;
  return { body: rejoined, faqHtml };
}

/**
 * Extract the first H1 as the post title and remove that line from the body
 * (WordPress renders the title from the template — duplicating it in the
 * content would print it twice).
 */
function extractTitle(body, filePath) {
  const lines = body.split('\n');
  for (let i = 0; i < lines.length; i++) {
    const m = lines[i].match(/^#\s+(.+?)\s*$/);
    if (m) {
      lines.splice(i, 1);
      return { title: m[1], body: lines.join('\n') };
    }
  }
  throw new Error(`${filePath}: no H1 found to use as the post title`);
}

/**
 * Parse one content markdown file into a page record.
 *
 * @param {string} raw   Raw file contents.
 * @param {string} filePath  For error messages.
 * @returns {{
 *   url: string, seoTitle: string, metaDescription: string, status: string,
 *   title: string, contentHtml: string, filePath: string,
 * }}
 */
function parsePage(raw, filePath) {
  const { frontmatter, body: rawBody } = splitFrontmatter(raw, filePath);

  const url = frontmatter.get('url');
  const seoTitle = frontmatter.get('seo-title');
  const metaDescription = frontmatter.get('meta-description');
  const status = frontmatter.get('status') || '';

  if (!url) throw new Error(`${filePath}: missing "url" in frontmatter`);
  if (!seoTitle) throw new Error(`${filePath}: missing "seo-title" in frontmatter`);
  if (!metaDescription) throw new Error(`${filePath}: missing "meta-description" in frontmatter`);

  const { body: bodyNoNotes } = stripNotesSection(rawBody);
  const { body: bodyNoFaqFence, faqHtml } = extractFaqJsonLd(bodyNoNotes);
  const { title, body: bodyNoTitle } = extractTitle(bodyNoFaqFence, filePath);

  const renderedHtml = marked.parse(bodyNoTitle.trim() + '\n', { async: false });
  let contentHtml = htmlToGutenberg(renderedHtml);
  if (faqHtml) {
    contentHtml += '\n\n' + faqScriptToGutenbergHtml(faqHtml);
  }

  return { url, seoTitle, metaDescription, status, title, contentHtml, filePath, hasFaq: Boolean(faqHtml) };
}

module.exports = { parsePage, stripNotesSection, extractFaqJsonLd, extractTitle, NOTES_HEADINGS };
