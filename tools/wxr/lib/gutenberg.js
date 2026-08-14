'use strict';

/**
 * Splits a flat run of block-level HTML (as produced by `marked`) into its
 * top-level elements, then wraps each in Gutenberg block comments so the
 * imported page is editable in the block editor instead of one classic blob.
 *
 * This is a pragmatic block-per-top-level-element pass, not an exhaustive
 * HTML->Gutenberg mapping. It assumes well-formed HTML with no stray raw `<`
 * inside attributes or text (true of `marked`'s output for this content set —
 * there are no images and no inline raw HTML in the source markdown).
 */

const VOID_TAGS = new Set([
  'br', 'hr', 'img', 'input', 'meta', 'link', 'area', 'base', 'col', 'embed', 'source', 'track', 'wbr',
]);

const TAG_RE = /<\/?([a-zA-Z0-9]+)(?:\s+[^>]*)?\/?>/g;

/**
 * Walk the HTML tracking generic nesting depth (not per-tag-name — any
 * balanced HTML is correctly split this way: every open tag increments,
 * every close tag decrements, void/self-closing tags don't change depth).
 * Whenever depth returns to 0 we've found one complete top-level element.
 */
function splitTopLevelElements(html) {
  const blocks = [];
  let depth = 0;
  let blockStart = 0;
  let currentTag = null;
  let match;

  TAG_RE.lastIndex = 0;
  while ((match = TAG_RE.exec(html)) !== null) {
    const full = match[0];
    const tag = match[1].toLowerCase();
    const isClosing = full[1] === '/';
    const selfClosing = full.endsWith('/>') || VOID_TAGS.has(tag);

    if (depth === 0) {
      if (isClosing) {
        // Malformed/unexpected — skip defensively rather than crash the build.
        continue;
      }
      blockStart = match.index;
      currentTag = tag;
      if (selfClosing) {
        blocks.push({ tag, html: html.slice(blockStart, match.index + full.length) });
        continue;
      }
      depth = 1;
      continue;
    }

    if (!selfClosing) {
      depth += isClosing ? -1 : 1;
    }
    if (depth === 0) {
      const end = match.index + full.length;
      blocks.push({ tag: currentTag, html: html.slice(blockStart, end) });
    }
  }

  return blocks;
}

function wrapBlock({ tag, html }) {
  if (/^h[1-6]$/.test(tag)) {
    const level = Number(tag[1]);
    const attrs = level === 2 ? '' : ` {"level":${level}}`;
    return `<!-- wp:heading${attrs} -->\n${html}\n<!-- /wp:heading -->`;
  }
  if (tag === 'p') {
    return `<!-- wp:paragraph -->\n${html}\n<!-- /wp:paragraph -->`;
  }
  if (tag === 'ul' || tag === 'ol') {
    const attrs = tag === 'ol' ? ' {"ordered":true}' : '';
    return `<!-- wp:list${attrs} -->\n${html}\n<!-- /wp:list -->`;
  }
  if (tag === 'table') {
    return `<!-- wp:table -->\n<figure class="wp-block-table">${html}</figure>\n<!-- /wp:table -->`;
  }
  if (tag === 'blockquote') {
    const withClass = html.replace('<blockquote>', '<blockquote class="wp-block-quote">');
    return `<!-- wp:quote -->\n${withClass}\n<!-- /wp:quote -->`;
  }
  if (tag === 'hr') {
    return '<!-- wp:separator -->\n<hr class="wp-block-separator has-alpha-channel-opacity"/>\n<!-- /wp:separator -->';
  }
  // Fallback for anything unforeseen (e.g. <pre>, <div>) — keep it live as raw HTML
  // rather than dropping it; correctness of the rendered page beats block purity.
  return `<!-- wp:html -->\n${html}\n<!-- /wp:html -->`;
}

/**
 * Convert a run of block-level HTML into a Gutenberg-block-commented string.
 */
function htmlToGutenberg(html) {
  const blocks = splitTopLevelElements(html);
  return blocks.map(wrapBlock).join('\n\n');
}

/**
 * Wrap raw HTML (e.g. the FAQPage JSON-LD <script> tag) as a trailing wp:html
 * block, unescaped, so the schema is live on the page rather than rendered as
 * visible code.
 */
function faqScriptToGutenbergHtml(rawScriptHtml) {
  return `<!-- wp:html -->\n${rawScriptHtml}\n<!-- /wp:html -->`;
}

module.exports = { splitTopLevelElements, htmlToGutenberg, faqScriptToGutenbergHtml };
