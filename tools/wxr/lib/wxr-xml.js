'use strict';

const SITE_URL = 'https://thepawmanual.com';
const AUTHOR_LOGIN = 'admin';
const AUTHOR_EMAIL = 'info@thepawmanual.com';
const AUTHOR_DISPLAY_NAME = 'ThePawManual';

// This is ONLY the RSS <channel>'s own <pubDate> — the "when was this feed
// generated" field the WXR format inherits from RSS, describing the export
// itself, not any one page. It is never read per-post: WordPress's importer
// takes each page's date from that item's own <wp:post_date> (see
// buildWxr's `page.date` below), and nothing in WordPress persists the
// channel-level value against any post_id. So, unlike a page's own date, it
// doesn't need to be "plausible" — it just needs to be fixed, so that
// generating the same content twice keeps producing byte-identical output
// (see tools/wxr/README.md's "Reproducible output"). Each page's *own*
// date is NOT allowed to default to this or to "now" — see
// generate.js's resolvePublishDates() for why a page's date has its own,
// separate, per-URL source of truth (a committed registry, not a constant).
const CHANNEL_DATE = new Date('1970-01-01T00:00:00Z');

/** Wrap text in a CDATA section, escaping any literal "]]>" it might contain. */
function cdata(text) {
  const safe = String(text == null ? '' : text).replace(/]]>/g, ']]]]><![CDATA[>');
  return `<![CDATA[${safe}]]>`;
}

/** Escape text for use in a plain (non-CDATA) XML text node or attribute. */
function escapeXml(text) {
  return String(text == null ? '' : text)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&apos;');
}

function slugFromUrl(url) {
  const trimmed = url.replace(/^\/+|\/+$/g, '');
  const parts = trimmed.split('/');
  return parts[parts.length - 1] || 'home';
}

function formatWpDate(date) {
  // WXR wants "YYYY-MM-DD HH:MM:SS" in both post_date and post_date_gmt.
  const pad = (n) => String(n).padStart(2, '0');
  return (
    `${date.getUTCFullYear()}-${pad(date.getUTCMonth() + 1)}-${pad(date.getUTCDate())} ` +
    `${pad(date.getUTCHours())}:${pad(date.getUTCMinutes())}:${pad(date.getUTCSeconds())}`
  );
}

/**
 * @param {Array<{
 *   id: number, parentId: number, url: string, title: string, contentHtml: string,
 *   seoTitle: string, metaDescription: string, date: Date,
 * }>} pages  Already ordered parents-before-children. `date` is per-page —
 *   see generate.js's resolvePublishDates() for where it comes from and why
 *   it can't be a single shared value the way `id` and `parentId` aren't
 *   either.
 */
function buildWxr(pages, { channelDate = CHANNEL_DATE } = {}) {
  const channelPubDate = channelDate.toUTCString();

  const items = pages
    .map((page) => {
      const link = `${SITE_URL}${page.url}`;
      const postName = slugFromUrl(page.url);
      const itemPubDate = page.date.toUTCString();
      const wpDate = formatWpDate(page.date);
      const postmeta = [
        ['rank_math_title', page.seoTitle],
        ['rank_math_description', page.metaDescription],
        ['_wp_page_template', 'default'],
      ]
        .map(
          ([key, value]) =>
            `\t\t<wp:postmeta>\n\t\t\t<wp:meta_key>${cdata(key)}</wp:meta_key>\n\t\t\t<wp:meta_value>${cdata(
              value
            )}</wp:meta_value>\n\t\t</wp:postmeta>`
        )
        .join('\n');

      return [
        '\t<item>',
        `\t\t<title>${cdata(page.title)}</title>`,
        `\t\t<link>${escapeXml(link)}</link>`,
        `\t\t<pubDate>${itemPubDate}</pubDate>`,
        `\t\t<dc:creator>${cdata(AUTHOR_LOGIN)}</dc:creator>`,
        `\t\t<guid isPermaLink="false">${escapeXml(`${SITE_URL}/?page_id=${page.id}`)}</guid>`,
        '\t\t<description></description>',
        `\t\t<content:encoded>${cdata(page.contentHtml)}</content:encoded>`,
        `\t\t<excerpt:encoded>${cdata('')}</excerpt:encoded>`,
        `\t\t<wp:post_id>${page.id}</wp:post_id>`,
        `\t\t<wp:post_date>${wpDate}</wp:post_date>`,
        `\t\t<wp:post_date_gmt>${wpDate}</wp:post_date_gmt>`,
        `\t\t<wp:post_modified>${wpDate}</wp:post_modified>`,
        `\t\t<wp:post_modified_gmt>${wpDate}</wp:post_modified_gmt>`,
        '\t\t<wp:comment_status>closed</wp:comment_status>',
        '\t\t<wp:ping_status>closed</wp:ping_status>',
        `\t\t<wp:post_name>${cdata(postName)}</wp:post_name>`,
        '\t\t<wp:status>draft</wp:status>',
        `\t\t<wp:post_parent>${page.parentId}</wp:post_parent>`,
        '\t\t<wp:menu_order>0</wp:menu_order>',
        '\t\t<wp:post_type>page</wp:post_type>',
        '\t\t<wp:post_password></wp:post_password>',
        '\t\t<wp:is_sticky>0</wp:is_sticky>',
        postmeta,
        '\t</item>',
      ].join('\n');
    })
    .join('\n');

  return `<?xml version="1.0" encoding="UTF-8" ?>
<rss version="2.0"
	xmlns:excerpt="http://wordpress.org/export/1.2/excerpt/"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:wfw="http://wellformedweb.org/CommentAPI/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:wp="http://wordpress.org/export/1.2/"
>
<channel>
	<title>${escapeXml('ThePawManual')}</title>
	<link>${escapeXml(SITE_URL)}</link>
	<description>${escapeXml('ThePawManual content import')}</description>
	<pubDate>${channelPubDate}</pubDate>
	<language>en-US</language>
	<wp:wxr_version>1.2</wp:wxr_version>
	<wp:base_site_url>${escapeXml(SITE_URL)}</wp:base_site_url>
	<wp:base_blog_url>${escapeXml(SITE_URL)}</wp:base_blog_url>
	<wp:author>
		<wp:author_id>1</wp:author_id>
		<wp:author_login>${cdata(AUTHOR_LOGIN)}</wp:author_login>
		<wp:author_email>${escapeXml(AUTHOR_EMAIL)}</wp:author_email>
		<wp:author_display_name>${cdata(AUTHOR_DISPLAY_NAME)}</wp:author_display_name>
		<wp:author_first_name>${cdata('')}</wp:author_first_name>
		<wp:author_last_name>${cdata('')}</wp:author_last_name>
	</wp:author>
${items}
</channel>
</rss>
`;
}

module.exports = { buildWxr, slugFromUrl };
