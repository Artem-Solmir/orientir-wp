import { readFileSync, readdirSync } from "node:fs";
import { join } from "node:path";
import type { DataStore, GalleryRecord, PageRecord, PostRecord, RedirectRecord, TaxonRecord } from "./types";
import { pagesDir, postsDir, contentRoot } from "./paths";

function readJson<T>(path: string): T {
  return JSON.parse(readFileSync(path, "utf8")) as T;
}

function loadPosts(): PostRecord[] {
  const dir = postsDir();
  return readdirSync(dir)
    .filter((f) => f.endsWith(".json"))
    .map((f) => readJson<PostRecord>(join(dir, f)))
    .sort((a, b) => (b.publishedAt ?? "").localeCompare(a.publishedAt ?? ""));
}

function loadPages(): PageRecord[] {
  const dir = pagesDir();
  return readdirSync(dir)
    .filter((f) => f.endsWith(".json"))
    .map((f) => readJson<PageRecord>(join(dir, f)));
}

function indexBySlug<T extends { slug: string }>(items: T[]): Map<string, T> {
  return new Map(items.map((i) => [i.slug, i]));
}

function buildStore(): DataStore {
  const root = contentRoot();
  const posts = loadPosts();
  const pages = loadPages();
  const categories = readJson<TaxonRecord[]>(join(root, "categories.json"));
  const tags = readJson<TaxonRecord[]>(join(root, "tags.json"));
  return {
    posts,
    postsBySlug: indexBySlug(posts),
    pages,
    pagesBySlug: indexBySlug(pages),
    categories,
    categoriesBySlug: indexBySlug(categories),
    tags,
    tagsBySlug: indexBySlug(tags),
    media: readJson(join(root, "media.json")),
    galleries: readJson<GalleryRecord[]>(join(root, "galleries.json")),
    redirects: readJson<RedirectRecord[]>(join(root, "redirects.json")),
  };
}

let cached: DataStore | null = null;

export function loadStore(): DataStore {
  if (!cached) cached = buildStore();
  return cached;
}

export function clearStoreCache(): void {
  cached = null;
}

export function parseIso(iso: string | null | undefined): Date | null {
  if (!iso) return null;
  const d = new Date(iso);
  return Number.isNaN(d.getTime()) ? null : d;
}

/** Published and not past WordPress expiration (same rules as the old Prisma filter). */
export function isLivePost(post: PostRecord, now = new Date()): boolean {
  if (post.status !== "published") return false;
  const published = parseIso(post.publishedAt);
  if (!published || published > now) return false;
  const expires = parseIso(post.expiresAt);
  if (expires && expires <= now) return false;
  return true;
}

export function listShortlinkRedirects(store = loadStore()): { wpId: number; slug: string }[] {
  return store.posts
    .filter((p) => p.legacyWpId != null && isLivePost(p))
    .map((p) => ({ wpId: p.legacyWpId!, slug: p.slug }));
}
