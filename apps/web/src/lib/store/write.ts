import { appendFileSync, mkdirSync, readFileSync, renameSync, writeFileSync } from "node:fs";
import { dirname, join } from "node:path";
import { sanitize } from "@/lib/sanitize";
import { isReservedSlug, slugify } from "@/lib/slug";
import { clearStoreCache, loadStore } from "./reader";
import { contentRoot, pagesDir, postsDir } from "./paths";
import type { MediaMeta, PageRecord, PostRecord } from "./types";

export class StoreError extends Error {}

function postPath(slug: string) {
  return join(postsDir(), `${slug}.json`);
}

function atomicWrite(filePath: string, data: string) {
  mkdirSync(dirname(filePath), { recursive: true });
  const tmp = `${filePath}.${process.pid}.tmp`;
  writeFileSync(tmp, data, "utf8");
  renameSync(tmp, filePath);
}

function audit(action: string, entity: string, entityId: string, meta?: Record<string, unknown>) {
  const line = JSON.stringify({ at: new Date().toISOString(), action, entity, entityId, meta });
  appendFileSync(join(process.cwd(), "content", "audit.log"), `${line}\n`, "utf8");
}

function readPostFile(slug: string): PostRecord | null {
  const path = postPath(slug);
  try {
    return JSON.parse(readFileSync(path, "utf8")) as PostRecord;
  } catch {
    return null;
  }
}

function assertSlugFree(slug: string, exceptSlug?: string) {
  if (isReservedSlug(slug)) throw new StoreError(`slug «${slug}» зарезервирован`);
  const store = loadStore();
  if (store.pagesBySlug.has(slug)) throw new StoreError(`slug «${slug}» уже занят`);
  const post = store.postsBySlug.get(slug);
  if (post && post.slug !== exceptSlug) throw new StoreError(`slug «${slug}» уже занят`);
}

function freeSlugFromTitle(title: string): string {
  const base = slugify(title) || "post";
  const store = loadStore();
  for (let n = 1; n < 50; n++) {
    const candidate = n === 1 ? base : `${base}-${n}`;
    if (!store.postsBySlug.has(candidate) && !store.pagesBySlug.has(candidate) && !isReservedSlug(candidate)) return candidate;
  }
  throw new StoreError("Не удалось подобрать свободный slug, укажите его явно");
}

function assertCategories(slugs: string[]) {
  const store = loadStore();
  const missing = slugs.filter((s) => !store.categoriesBySlug.has(s));
  if (missing.length) throw new StoreError(`Неизвестные рубрики: ${missing.join(", ")}`);
}

function assertCover(path: string | null | undefined) {
  if (path == null) return null;
  const store = loadStore();
  if (!store.media[path]) throw new StoreError(`Медиафайл не найден: ${path}`);
  const meta = store.media[path];
  return {
    path,
    width: meta.width,
    height: meta.height,
    alt: meta.alt,
    variants: meta.variants,
  };
}

function upsertTags(names: string[]): string[] {
  const store = loadStore();
  const root = join(process.cwd(), "content");
  const slugs: string[] = [];
  const tags = [...store.tags];
  let changed = false;
  for (const name of new Set(names)) {
    const slug = slugify(name);
    if (!slug) throw new StoreError(`Недопустимая метка: ${name}`);
    slugs.push(slug);
    if (!store.tagsBySlug.has(slug)) {
      tags.push({ slug, name });
      changed = true;
    }
  }
  if (changed) {
    atomicWrite(join(root, "tags.json"), `${JSON.stringify(tags, null, 2)}\n`);
    clearStoreCache();
  }
  return slugs;
}

function writePost(slug: string, post: PostRecord) {
  atomicWrite(postPath(slug), `${JSON.stringify(post, null, 2)}\n`);
  clearStoreCache();
}

export function createPost(input: {
  title: string;
  body: string;
  excerpt?: string | null;
  slug?: string;
  authorName?: string | null;
  categories: string[];
  tags: string[];
  coverPath?: string | null;
  publishedAt?: Date;
  expiresAt?: Date | null;
  seoTitle?: string | null;
  seoDescription?: string | null;
}) {
  const slug = input.slug ?? freeSlugFromTitle(input.title);
  if (input.slug) assertSlugFree(slug);
  assertCategories(input.categories);
  const cover = assertCover(input.coverPath);
  const tagSlugs = upsertTags(input.tags);
  const now = new Date().toISOString();
  const post: PostRecord = {
    slug,
    title: input.title,
    body: sanitize(input.body),
    excerpt: input.excerpt ?? null,
    status: "published",
    publishedAt: (input.publishedAt ?? new Date()).toISOString(),
    expiresAt: input.expiresAt?.toISOString() ?? null,
    updatedAt: now,
    authorName: input.authorName ?? null,
    seoTitle: input.seoTitle ?? null,
    seoDescription: input.seoDescription ?? null,
    legacyWpId: null,
    cover,
    categories: input.categories,
    tags: tagSlugs,
  };
  writePost(slug, post);
  audit("post.create", "post", slug, { title: post.title });
  return post;
}

type PostPatch = {
  title?: string;
  body?: string;
  excerpt?: string | null;
  authorName?: string | null;
  categories?: string[];
  tags?: string[];
  coverPath?: string | null;
  seoTitle?: string | null;
  seoDescription?: string | null;
  expiresAt?: Date | null;
};

function pick<T>(patch: T | undefined, existing: T): T {
  return patch !== undefined ? patch : existing;
}

function mergePost(existing: PostRecord, patch: PostPatch): PostRecord {
  const cover = patch.coverPath !== undefined ? assertCover(patch.coverPath) : existing.cover;
  const tags = patch.tags !== undefined ? upsertTags(patch.tags) : existing.tags;
  const expires =
    patch.expiresAt !== undefined ? patch.expiresAt?.toISOString() ?? null : existing.expiresAt;
  return {
    ...existing,
    title: pick(patch.title, existing.title),
    body: patch.body !== undefined ? sanitize(patch.body) : existing.body,
    excerpt: pick(patch.excerpt, existing.excerpt),
    authorName: pick(patch.authorName, existing.authorName),
    categories: pick(patch.categories, existing.categories),
    tags,
    cover,
    seoTitle: pick(patch.seoTitle, existing.seoTitle),
    seoDescription: pick(patch.seoDescription, existing.seoDescription),
    expiresAt: expires,
    updatedAt: new Date().toISOString(),
  };
}

export function updatePost(slug: string, patch: PostPatch) {
  const existing = readPostFile(slug);
  if (!existing) throw new StoreError("Запись не найдена");
  if (patch.categories) assertCategories(patch.categories);
  const updated = mergePost(existing, patch);
  writePost(slug, updated);
  audit("post.update", "post", slug, { fields: Object.keys(patch) });
  return updated;
}

export function archivePost(slug: string) {
  const existing = readPostFile(slug);
  if (!existing) throw new StoreError("Запись не найдена");
  writePost(slug, { ...existing, status: "archived", updatedAt: new Date().toISOString() });
  audit("post.archive", "post", slug);
}

export function getPostRecord(slug: string) {
  return readPostFile(slug);
}

export function listPostRecords(opts: { limit: number; offset: number; q?: string; status?: string }) {
  const store = loadStore();
  let rows = [...store.posts];
  if (opts.status === "published" || opts.status === "archived") rows = rows.filter((p) => p.status === opts.status);
  if (opts.q) {
    const q = opts.q.toLowerCase();
    rows = rows.filter((p) => p.title.toLowerCase().includes(q));
  }
  rows.sort((a, b) => (b.publishedAt ?? "").localeCompare(a.publishedAt ?? ""));
  const total = rows.length;
  return { total, items: rows.slice(opts.offset, opts.offset + opts.limit) };
}

export function updatePageRecord(slug: string, patch: { title?: string; body?: string }) {
  const store = loadStore();
  const page = store.pagesBySlug.get(slug);
  if (!page) throw new StoreError("Страница не найдена");
  const updated: PageRecord = {
    ...page,
    title: patch.title ?? page.title,
    body: patch.body !== undefined ? sanitize(patch.body) : page.body,
    updatedAt: new Date().toISOString(),
  };
  atomicWrite(join(pagesDir(), `${slug}.json`), `${JSON.stringify(updated, null, 2)}\n`);
  clearStoreCache();
  audit("page.update", "page", slug, { fields: Object.keys(patch) });
}

export function registerMedia(path: string, meta: MediaMeta) {
  const store = loadStore();
  const media = { ...store.media, [path]: meta };
  atomicWrite(join(contentRoot(), "media.json"), `${JSON.stringify(media, null, 2)}\n`);
  clearStoreCache();
  audit("photo.import", "media", path);
}

export function deletePostSlug(slug: string, newSlug: string) {
  const path = postPath(slug);
  const newPath = postPath(newSlug);
  if (slug !== newSlug) {
    renameSync(path, newPath);
    clearStoreCache();
  }
}
