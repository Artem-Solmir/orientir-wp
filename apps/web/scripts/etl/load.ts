import { Prisma, type PrismaClient } from "@prisma/client";
import { parseComposer, resolveCategories } from "./composer";
import { transformBody } from "./content";
import { existsSync } from "node:fs";
import { dirname, resolve } from "node:path";
import { imageSize, imageVariants, serializedIds } from "./php";
import {
  sql,
  type AttachmentRow,
  type GalleryRow,
  type MetaRow,
  type PostRow,
  type RelRow,
  type Source,
  type TermRow,
  type UserRow,
  type ViewRow,
} from "./source";

export type Stats = Record<string, number>;
type Ids = Map<number, string>; // legacy WP id -> new id

const inc = (stats: Stats, key: string, by = 1) => void (stats[key] = (stats[key] ?? 0) + by);

export async function loadTaxonomies(src: Source, db: PrismaClient, stats: Stats) {
  const categoryIds: Ids = new Map();
  const tagIds: Ids = new Map();
  const slugById = new Map<number, string>();
  for (const t of await src.query<TermRow>(sql.terms)) {
    const data = { slug: t.slug, name: t.name };
    if (t.taxonomy === "category") {
      const row = await db.category.upsert({ where: { legacyWpId: t.term_id }, create: { ...data, legacyWpId: t.term_id }, update: data });
      categoryIds.set(t.term_id, row.id);
      slugById.set(t.term_id, t.slug);
      inc(stats, "categories");
    } else {
      const row = await db.tag.upsert({ where: { legacyWpId: t.term_id }, create: { ...data, legacyWpId: t.term_id }, update: data });
      tagIds.set(t.term_id, row.id);
      inc(stats, "tags");
    }
  }
  return { categoryIds, tagIds, slugById };
}

const uploadsRoot = () => resolve(process.env.MEDIA_ROOT ?? resolve(process.cwd(), "../../wp-content/uploads"));

/** Only variants that really exist on disk are kept, so the site never links to a missing thumbnail. */
function presentVariants(path: string, meta: string | null) {
  const root = uploadsRoot();
  if (!existsSync(root)) return [];
  return imageVariants(meta).filter((v) => existsSync(resolve(root, dirname(path), v.file)));
}

export async function loadMedia(src: Source, db: PrismaClient, stats: Stats): Promise<Ids> {
  const ids: Ids = new Map();
  for (const a of await src.query<AttachmentRow>(sql.attachments)) {
    if (!a.file) {
      inc(stats, "media_skipped_no_file");
      continue;
    }
    const size = imageSize(a.meta);
    const data = { path: a.file, mimeType: a.mime || "application/octet-stream", width: size?.width ?? null, height: size?.height ?? null, alt: a.alt || null, variants: presentVariants(a.file, a.meta) };
    const row = await db.mediaAsset.upsert({ where: { legacyWpId: a.ID }, create: { ...data, legacyWpId: a.ID }, update: data });
    ids.set(a.ID, row.id);
    inc(stats, "media");
  }
  return ids;
}

export type PostContext = {
  categoryIds: Ids;
  tagIds: Ids;
  slugById: Map<number, string>;
  mediaIds: Ids;
};

type Lookups = {
  meta: Map<number, Record<string, string>>;
  views: Map<number, number>;
  authors: Map<number, string>;
  rels: Map<number, RelRow[]>;
};

async function loadLookups(src: Source): Promise<Lookups> {
  const meta = new Map<number, Record<string, string>>();
  for (const m of await src.query<MetaRow>(sql.postMeta)) {
    const bag = meta.get(m.post_id) ?? {};
    bag[m.meta_key] = m.meta_value ?? "";
    meta.set(m.post_id, bag);
  }
  const rels = new Map<number, RelRow[]>();
  for (const r of await src.query<RelRow>(sql.relationships)) {
    rels.set(r.object_id, [...(rels.get(r.object_id) ?? []), r]);
  }
  const views = new Map((await src.query<ViewRow>(sql.views)).map((v) => [v.id, Number(v.views)]));
  const authors = new Map((await src.query<UserRow>(sql.users)).map((u) => [u.ID, u.display_name]));
  return { meta, views, authors, rels };
}

function expiresAt(meta: Record<string, string>): Date | null {
  const ts = Number(meta["_expiration-date"]);
  return Number.isInteger(ts) && ts > 0 ? new Date(ts * 1000) : null;
}

async function upsertPost(db: PrismaClient, p: PostRow, ctx: PostContext, l: Lookups) {
  const meta = l.meta.get(p.ID) ?? {};
  const rels = l.rels.get(p.ID) ?? [];
  const data = {
    slug: p.post_name,
    title: p.post_title,
    excerpt: p.post_excerpt || null,
    body: transformBody(p.post_content),
    status: "published" as const,
    publishedAt: p.published ? new Date(p.published) : null,
    expiresAt: expiresAt(meta),
    views: l.views.get(p.ID) ?? 0,
    authorName: l.authors.get(p.post_author) ?? null,
    coverId: ctx.mediaIds.get(Number(meta["_thumbnail_id"])) ?? null,
  };
  const categories = rels.filter((r) => r.taxonomy === "category").flatMap((r) => ctx.categoryIds.get(r.term_id) ?? []);
  const tags = rels.filter((r) => r.taxonomy === "post_tag").flatMap((r) => ctx.tagIds.get(r.term_id) ?? []);

  // authorId stays null on purpose: WP accounts are not migrated, attribution is authorName.
  await db.$transaction(async (tx) => {
    const row = await tx.post.upsert({ where: { legacyWpId: p.ID }, create: { ...data, legacyWpId: p.ID }, update: data });
    await tx.postCategory.deleteMany({ where: { postId: row.id } });
    await tx.postTag.deleteMany({ where: { postId: row.id } });
    await tx.postCategory.createMany({ data: categories.map((categoryId) => ({ postId: row.id, categoryId })) });
    await tx.postTag.createMany({ data: tags.map((tagId) => ({ postId: row.id, tagId })) });
  });
}

async function upsertPage(db: PrismaClient, p: PostRow, ctx: PostContext, l: Lookups, stats: Stats) {
  const meta = l.meta.get(p.ID) ?? {};
  const sections = parseComposer(meta).map((s) => ({
    order: s.order,
    type: s.type,
    config: resolveCategories(s.config, ctx.slugById) as Prisma.InputJsonObject,
  }));
  const data = {
    slug: p.post_name,
    title: p.post_title,
    body: transformBody(p.post_content),
    template: meta["_wp_page_template"] === "page_composer.php" ? ("composer" as const) : ("default" as const),
    status: "published" as const,
  };
  await db.$transaction(async (tx) => {
    const row = await tx.page.upsert({ where: { legacyWpId: p.ID }, create: { ...data, legacyWpId: p.ID }, update: data });
    await tx.pageSection.deleteMany({ where: { pageId: row.id } });
    await tx.pageSection.createMany({ data: sections.map((s) => ({ ...s, pageId: row.id })) });
  });
  inc(stats, "page_sections", sections.length);
}

/** Largest share of existing legacy rows a single run may delete; more means a broken source, not real deletions. */
export const MAX_PRUNE_SHARE = 0.2;

export function pruneAllowed(existing: number, toDelete: number): boolean {
  return toDelete === 0 || (existing > 0 && toDelete / existing <= MAX_PRUNE_SHARE);
}

/** Delete-propagation for delta runs: rows that came from WordPress and are gone from the source are removed. */
async function pruneMissing(db: PrismaClient, kind: "post" | "page", keep: number[], stats: Stats) {
  const where = { legacyWpId: { not: null, notIn: keep } };
  const legacy = { legacyWpId: { not: null } };
  const [existing, stale] =
    kind === "post"
      ? await Promise.all([db.post.count({ where: legacy }), db.post.count({ where })])
      : await Promise.all([db.page.count({ where: legacy }), db.page.count({ where })]);
  if (!pruneAllowed(existing, stale)) {
    throw new Error(`Refusing to delete ${stale} of ${existing} legacy ${kind}s: the source looks incomplete`);
  }
  if (stale > 0) await (kind === "post" ? db.post.deleteMany({ where }) : db.page.deleteMany({ where }));
  inc(stats, `${kind}s_removed`, stale);
}

export async function loadPosts(src: Source, db: PrismaClient, ctx: PostContext, stats: Stats) {
  const lookups = await loadLookups(src);
  const seen = { post: [] as number[], page: [] as number[] };
  for (const p of await src.query<PostRow>(sql.posts)) {
    if (p.post_type === "post") await upsertPost(db, p, ctx, lookups);
    else await upsertPage(db, p, ctx, lookups, stats);
    seen[p.post_type === "post" ? "post" : "page"].push(p.ID);
    inc(stats, p.post_type === "post" ? "posts" : "pages");
  }
  await pruneMissing(db, "post", seen.post, stats);
  await pruneMissing(db, "page", seen.page, stats);
}

export async function loadGalleries(src: Source, db: PrismaClient, mediaIds: Ids, stats: Stats) {
  const used = new Set<string>();
  for (const g of await src.query<GalleryRow>(sql.galleries)) {
    let slug = g.post_name || `gallery-${g.ID}`;
    if (used.has(slug)) slug = `${slug}-${g.ID}`;
    used.add(slug);
    const items = serializedIds(g.items).flatMap((id, order) => {
      const mediaId = mediaIds.get(id);
      return mediaId ? [{ mediaId, order }] : [];
    });
    await db.$transaction(async (tx) => {
      const data = { slug, title: g.post_title };
      const row = await tx.gallery.upsert({ where: { legacyWpId: g.ID }, create: { ...data, legacyWpId: g.ID }, update: data });
      await tx.galleryItem.deleteMany({ where: { galleryId: row.id } });
      await tx.galleryItem.createMany({ data: items.map((i) => ({ ...i, galleryId: row.id })) });
    });
    inc(stats, "galleries");
    inc(stats, "gallery_items", items.length);
  }
}
