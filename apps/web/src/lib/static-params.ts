import "server-only";
import { listPosts } from "./content";
import { loadStore } from "./store/reader";
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
  const store = loadStore();
  const categories = store.categories.filter((c) => store.posts.some((p) => p.categories.includes(c.slug)));
  const out: { slug: string; page: string[] }[] = [];
  for (const { slug } of categories) {
    const { total } = await listPosts({ categorySlugs: [slug] }, 1, 1);
    out.push(...pageSegments(pageCount(total)).map((page) => ({ slug, page })));
  }
  return out;
}

export async function tagParams() {
  const store = loadStore();
  const tags = store.tags.filter((t) => store.posts.some((p) => p.tags.includes(t.slug)));
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
  const store = loadStore();
  const now = new Date();
  const posts = store.posts.filter((p) => p.status === "published" && p.publishedAt && new Date(p.publishedAt) <= now);
  const pages = store.pages.filter((p) => p.status === "published");
  const out: { slug: string; page: string[] }[] = posts.map((p) => ({ slug: p.slug, page: [] }));
  for (const page of pages) {
    if (SYSTEM_PAGES.has(page.slug)) continue;
    out.push(...pageSegments(await composerPages(page.sections)).map((segments) => ({ slug: page.slug, page: segments })));
  }
  return out;
}
