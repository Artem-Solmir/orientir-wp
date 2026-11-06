import { listPosts } from "@/lib/content";
import { SITE_DESCRIPTION, SITE_NAME, absoluteUrl } from "@/lib/site";
import { xmlEscape } from "@/lib/text";

export const revalidate = 3600;

export async function GET() {
  const { items } = await listPosts({}, 1, 20);
  const entries = items
    .map((p) => {
      const url = absoluteUrl(`/${p.slug}`);
      return [
        "<item>",
        `<title>${xmlEscape(p.title)}</title>`,
        `<link>${xmlEscape(url)}</link>`,
        `<guid isPermaLink="true">${xmlEscape(url)}</guid>`,
        p.publishedAt ? `<pubDate>${p.publishedAt.toUTCString()}</pubDate>` : "",
        `<description>${xmlEscape(p.summary)}</description>`,
        "</item>",
      ].join("");
    })
    .join("");

  const xml =
    `<?xml version="1.0" encoding="UTF-8"?><rss version="2.0"><channel>` +
    `<title>${xmlEscape(SITE_NAME)}</title><link>${xmlEscape(absoluteUrl("/"))}</link>` +
    `<description>${xmlEscape(SITE_DESCRIPTION)}</description><language>ru</language>${entries}</channel></rss>`;

  return new Response(xml, { headers: { "Content-Type": "application/rss+xml; charset=utf-8" } });
}
