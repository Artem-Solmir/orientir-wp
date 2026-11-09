import "server-only";
import { db } from "./db";
import { listPosts } from "./content";
import { pageCount } from "./pagination";
import { csv, isLatestSection, sectionConfig } from "./section-config";

/** Slugs that exist in the database but are replaced by app routes or redirects. */
const SYSTEM_PAGES = new Set(["home", "blog"]);

/** Values of the optional catch-all [[...page]] for pages 1..N: [] then ["page","2"], ... */
export function pageSegments(pages: number): string[][] {
  return Array.from({ length: Math.max(1, pages) }, (_, i) => (i === 0 ? [] : ["page", String(i + 1)]));
}

export async function archiveParams() {
  const { total } = await listPosts({}, 1, 1);
  return pageSegments(pageCount(total)).map((page) => ({ page }));
}

export async function categoryParams() {
  const categories = await db.category.findMany({ where: { posts: { some: {} } }, select: { slug: true } });
  const out: { slug: string; page: string[] }[] = [];
  for (const { slug } of categories) {
    const { total } = await listPosts({ categorySlugs: [slug] }, 1, 1);
    out.push(...pageSegments(pageCount(total)).map((page) => ({ slug, page })));
  }
  return out;
}

export async function tagParams() {
  const tags = await db.tag.findMany({ where: { posts: { some: {} } }, select: { slug: true } });
  const out: { slug: string; page: string[] }[] = [];
  for (const { slug } of tags) {
    const { total } = await listPosts({ tagSlug: slug }, 1, 1);
    out.push(...pageSegments(pageCount(total)).map((page) => ({ slug, page })));
  }
  return out;
}

/** Highest page number a composer page needs: the largest paginated "latest" section decides. */
async function composerPages(sections: { type: string; config: unknown }[]): Promise<number> {
  let pages = 1;
  for (const s of sections) {
    const parsed = sectionConfig.safeParse(s.config);
    if (!parsed.success || !isLatestSection(s.type) || parsed.data.show_pagination !== 1) continue;
    const c = parsed.data;
    const { total } = await listPosts({ categorySlugs: csv(c.categorySlug), excludeCategorySlugs: csv(c.excludeCategorySlugs) }, 1, 1);
    pages = Math.max(pages, pageCount(total, c.number));
  }
  return pages;
}

/** Every post and page URL, plus the extra pages of paginated composer pages. */
export async function slugParams() {
  const [posts, pages] = await Promise.all([
    db.post.findMany({ where: { status: "published", publishedAt: { lte: new Date() } }, select: { slug: true } }),
    db.page.findMany({ where: { status: "published" }, select: { slug: true, sections: { select: { type: true, config: true } } } }),
  ]);
  const out: { slug: string; page: string[] }[] = posts.map((p) => ({ slug: p.slug, page: [] }));
  for (const page of pages) {
    if (SYSTEM_PAGES.has(page.slug)) continue;
    out.push(...pageSegments(await composerPages(page.sections)).map((segments) => ({ slug: page.slug, page: segments })));
  }
  return out;
}
