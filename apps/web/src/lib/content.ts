import "server-only";
import { unstable_cache } from "next/cache";
import { cache } from "react";
import { htmlToText, summarize } from "./text";
import { isLivePost, loadStore, parseIso } from "./store/reader";
import type { PostRecord } from "./store/types";
import { PER_PAGE } from "./pagination";

export const REVALIDATE = 3600;

export type Cover = { path: string; width: number | null; height: number | null; alt: string | null; variants: unknown };
export type CardPost = {
  slug: string;
  title: string;
  summary: string;
  publishedAt: Date | null;
  expiresAt: Date | null;
  authorName: string | null;
  cover: Cover | null;
  primaryCategory: { slug: string; name: string } | null;
};

export type PostFilter = { categorySlugs?: string[]; excludeCategorySlugs?: string[]; tagSlug?: string; search?: string; withCover?: boolean };

function primaryCategory(post: PostRecord) {
  const store = loadStore();
  const slug = post.categories[0];
  if (!slug) return null;
  const c = store.categoriesBySlug.get(slug);
  return c ? { slug: c.slug, name: c.name } : { slug, name: slug };
}

function toCard(post: PostRecord): CardPost {
  return {
    slug: post.slug,
    title: post.title,
    publishedAt: parseIso(post.publishedAt),
    expiresAt: parseIso(post.expiresAt),
    authorName: post.authorName,
    cover: post.cover,
    primaryCategory: primaryCategory(post),
    summary: summarize(post.excerpt ? htmlToText(post.excerpt) : htmlToText(post.body), 140),
  };
}

function matchesCategory(post: PostRecord, filter: PostFilter): boolean {
  if (filter.categorySlugs?.length && !filter.categorySlugs.some((s) => post.categories.includes(s))) return false;
  if (filter.excludeCategorySlugs?.length && filter.excludeCategorySlugs.some((s) => post.categories.includes(s))) return false;
  return true;
}

function matchesSearch(post: PostRecord, q: string | undefined): boolean {
  if (!q) return true;
  const needle = q.toLowerCase();
  return post.title.toLowerCase().includes(needle) || post.body.toLowerCase().includes(needle);
}

function matchesFilter(post: PostRecord, filter: PostFilter, now: Date): boolean {
  if (!isLivePost(post, now)) return false;
  if (!matchesCategory(post, filter)) return false;
  if (filter.tagSlug && !post.tags.includes(filter.tagSlug)) return false;
  if (filter.withCover && !post.cover) return false;
  return matchesSearch(post, filter.search);
}

function filteredPosts(filter: PostFilter, now = new Date()): PostRecord[] {
  return loadStore().posts.filter((p) => matchesFilter(p, filter, now));
}

export async function listPosts(filter: PostFilter, page = 1, perPage = PER_PAGE) {
  const items = filteredPosts(filter);
  const total = items.length;
  const slice = items.slice((page - 1) * perPage, page * perPage);
  return { items: slice.map(toCard), total };
}

export type PostView = NonNullable<Awaited<ReturnType<typeof getPost>>>;

export const getPost = cache(async (slug: string) => {
  const post = loadStore().postsBySlug.get(slug);
  if (!post || !isLivePost(post)) return null;
  const store = loadStore();
  return {
    slug: post.slug,
    title: post.title,
    excerpt: post.excerpt,
    body: post.body,
    publishedAt: parseIso(post.publishedAt),
    updatedAt: parseIso(post.updatedAt),
    authorName: post.authorName,
    cover: post.cover,
    categories: post.categories
      .map((slug) => store.categoriesBySlug.get(slug))
      .filter(Boolean)
      .map((c) => ({ category: { slug: c!.slug, name: c!.name } })),
    tags: post.tags
      .map((slug) => store.tagsBySlug.get(slug))
      .filter(Boolean)
      .map((t) => ({ tag: { slug: t!.slug, name: t!.name } })),
  };
});

export async function getNeighbors(publishedAt: Date | null) {
  if (!publishedAt) return { prev: null, next: null };
  const now = new Date();
  const live = filteredPosts({}, now);
  const prev = live.find((p) => {
    const d = parseIso(p.publishedAt);
    return d && d < publishedAt;
  });
  const next = [...live].reverse().find((p) => {
    const d = parseIso(p.publishedAt);
    return d && d > publishedAt && d <= now;
  });
  const neighbor = (p: PostRecord | undefined) =>
    p
      ? {
          slug: p.slug,
          title: p.title,
          cover: p.cover,
        }
      : null;
  return { prev: neighbor(prev), next: neighbor(next) };
}

export async function listUpcomingPlans(limit = 5) {
  const now = new Date();
  const rows = loadStore().posts.filter(
    (p) =>
      isLivePost(p, now) &&
      p.categories.includes("plany") &&
      p.expiresAt != null &&
      parseIso(p.expiresAt)! > now,
  );
  rows.sort((a, b) => parseIso(a.expiresAt)!.getTime() - parseIso(b.expiresAt)!.getTime());
  return rows.slice(0, limit).map(toCard);
}

export async function archiveYears() {
  const years = new Set<number>();
  for (const p of filteredPosts({})) {
    const d = parseIso(p.publishedAt);
    if (d) years.add(d.getFullYear());
  }
  return [...years].sort((a, b) => b - a);
}

export const getPage = cache(async (slug: string) => {
  const page = loadStore().pagesBySlug.get(slug);
  if (!page || page.status !== "published") return null;
  return {
    slug: page.slug,
    title: page.title,
    body: page.body,
    template: page.template,
    status: page.status,
    seoTitle: page.seoTitle,
    seoDescription: page.seoDescription,
    legacyWpId: page.legacyWpId,
    sections: [...page.sections]
      .sort((a, b) => a.order - b.order)
      .map((s) => ({ id: `${page.slug}-${s.order}`, type: s.type, config: s.config })),
  };
});

export const getCategory = async (slug: string) => loadStore().categoriesBySlug.get(slug) ?? null;
export const getTag = async (slug: string) => loadStore().tagsBySlug.get(slug) ?? null;

export const getNavCategories = unstable_cache(
  async () => {
    const store = loadStore();
    const counts = new Map<string, number>();
    for (const p of filteredPosts({})) {
      for (const slug of p.categories) counts.set(slug, (counts.get(slug) ?? 0) + 1);
    }
    return store.categories
      .map((c) => ({ slug: c.slug, name: c.name, _count: { posts: counts.get(c.slug) ?? 0 } }))
      .filter((c) => c._count.posts >= 5 && c.slug !== "bez-rubriki")
      .sort((a, b) => b._count.posts - a._count.posts);
  },
  ["nav-categories"],
  { revalidate: REVALIDATE, tags: ["nav"] },
);

export async function allSlugsForSitemap() {
  const store = loadStore();
  const now = new Date();
  const posts = store.posts
    .filter((p) => isLivePost(p, now))
    .map((p) => ({ slug: p.slug, updatedAt: parseIso(p.updatedAt) ?? parseIso(p.publishedAt) ?? now }));
  const pages = store.pages
    .filter((p) => p.status === "published")
    .map((p) => ({ slug: p.slug, updatedAt: parseIso(p.updatedAt) ?? now }));
  const postSlugs = new Set(posts.map((p) => p.slug));
  const usedCats = new Set<string>();
  const usedTags = new Set<string>();
  for (const p of store.posts.filter((x) => isLivePost(x, now))) {
    p.categories.forEach((c) => usedCats.add(c));
    p.tags.forEach((t) => usedTags.add(t));
  }
  const categories = store.categories.filter((c) => usedCats.has(c.slug)).map((c) => ({ slug: c.slug }));
  const tags = store.tags
    .filter((t) => usedTags.has(t.slug))
    .map((t) => ({ slug: t.slug, _count: { posts: store.posts.filter((p) => isLivePost(p, now) && p.tags.includes(t.slug)).length } }));
  void postSlugs;
  return { posts, pages, categories, tags };
}

export async function allLivePostsForSearch() {
  const now = new Date();
  return loadStore().posts.filter((p) => isLivePost(p, now));
}
