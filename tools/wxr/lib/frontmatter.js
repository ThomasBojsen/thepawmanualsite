'use strict';

/**
 * Hand-rolled frontmatter parser.
 *
 * The frontmatter in content/*.md is simple `key: value` YAML-ish text, not real
 * YAML — some values are double-quoted (seo-title, meta-description), some are
 * bare (url, page, status), and the quoted ones carry a trailing annotation
 * comment like `   # 57 chars` that must be stripped and never shown to WordPress.
 *
 * We do NOT use a YAML library here per the brief: frontmatter parsing is simple
 * enough that hand-rolling it is more transparent than pulling in a dependency,
 * whereas the markdown body is not (that's what `marked` is for).
 */

/**
 * Split a raw markdown file into { frontmatter: Map, body: string }.
 * Throws if the file doesn't open with a `---` frontmatter fence.
 */
function splitFrontmatter(raw, filePath) {
  const lines = raw.split('\n');
  if (lines[0].trim() !== '---') {
    throw new Error(`${filePath}: expected file to start with "---" frontmatter fence`);
  }
  let closeIdx = -1;
  for (let i = 1; i < lines.length; i++) {
    if (lines[i].trim() === '---') {
      closeIdx = i;
      break;
    }
  }
  if (closeIdx === -1) {
    throw new Error(`${filePath}: frontmatter fence never closes ("---" not found)`);
  }

  const fmLines = lines.slice(1, closeIdx);
  const bodyLines = lines.slice(closeIdx + 1);
  // Drop a single leading blank line so the body starts at the H1.
  if (bodyLines[0] !== undefined && bodyLines[0].trim() === '') {
    bodyLines.shift();
  }

  const frontmatter = parseFrontmatterLines(fmLines, filePath);
  return { frontmatter, body: bodyLines.join('\n') };
}

/**
 * Parse `key: value` lines. Handles:
 *  - quoted values: seo-title: "Some Title — With Punctuation"   # 57 chars
 *  - bare values:   url: /dogs/food/puppy-food/
 *  - bare values that run to end of line with no trailing comment (status, page)
 */
function parseFrontmatterLines(lines, filePath) {
  const map = new Map();
  for (const rawLine of lines) {
    if (!rawLine.trim()) continue;
    const m = rawLine.match(/^([a-zA-Z][a-zA-Z0-9-]*):\s?(.*)$/);
    if (!m) {
      throw new Error(`${filePath}: unrecognized frontmatter line: "${rawLine}"`);
    }
    const key = m[1];
    const value = parseValue(m[2]);
    map.set(key, value);
  }
  return map;
}

function parseValue(rest) {
  const trimmed = rest.trim();
  if (trimmed.startsWith('"')) {
    // Quoted value. Content pages never escape internal quotes, so the value
    // runs from the opening quote to the next quote character.
    const m = trimmed.match(/^"([^"]*)"/);
    if (m) return m[1];
    // Defensive fallback for a malformed/unterminated quote.
    return trimmed.replace(/^"/, '').replace(/"\s*(#.*)?$/, '');
  }
  // Bare value: strip a trailing " # ..." annotation comment if present, then trim.
  const hashIdx = trimmed.indexOf(' #');
  if (hashIdx !== -1) {
    return trimmed.slice(0, hashIdx).trim();
  }
  return trimmed;
}

module.exports = { splitFrontmatter };
