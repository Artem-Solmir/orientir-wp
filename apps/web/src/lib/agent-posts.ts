import "server-only";
import { Prisma } from "@prisma/client";
import { revalidatePath, revalidateTag } from "next/cache";
import { AgentError } from "./agent-http";
import type { PostCreate, PostPatch } from "./agent-schemas";
import { db } from "./db";
import { sanitize } from "./sanitize";
import { isReservedSlug, slugify } from "./slug";

const POST_SELECT = {
  slug: true, title: true, excerpt: true, status: true, publishedAt: true, expiresAt: true,
  authorName: true, seoTitle: true, seoDescription: true,
  cover: { select: { path: true } },
  categories: { select: { category: { select: { slug: true } } } },
  tags: { select: { tag: { select: { name: true } } } },
} satisfies Prisma.PostSelect;

type PostRow = Prisma.PostGetPayload<{ select: typeof POST_SELECT }>;

export function presentPost(p: PostRow) {
  return {
    slug: p.slug, url: `/${p.slug}`, title: p.title, excerpt: p.excerpt, status: p.status,
    publishedAt: p.publishedAt, expiresAt: p.expiresAt, authorName: p.authorName,
    seoTitle: p.seoTitle, seoDescription: p.seoDescription, coverPath: p.cover?.path ?? null,
    categories: p.categories.map((c) => c.category.slug), tags: p.tags.map((t) => t.tag.name),
  };
}

/** Public pages are ISR-cached; every write drops the cache so the site updates without a redeploy. */
export function revalidateSite(): void {
  revalidatePath("/", "layout");
  revalidateTag("nav", "max");
}

export async function audit(action: string, entity: string, entityId: string | null, meta?: Prisma.InputJsonObject) {
  await db.auditLog.create({ data: { actorType: "agent", action, entity, entityId, meta } });
}

async function assertSlugFree(slug: string, exceptPostId?: string): Promise<void> {
  if (isReservedSlug(slug)) throw new AgentError(422, `slug «${slug}» зарезервирован`);
  const [post, page] = await Promise.all([
    db.post.findUnique({ where: { slug }, select: { id: true } }),
    db.page.findUnique({ where: { slug }, select: { id: true } }),
  ]);
  if ((post && post.id !== exceptPostId) || page) throw new AgentError(409, `slug «${slug}» уже занят`);
}

async function freeSlugFromTitle(title: string): Promise<string> {
  const base = slugify(title) || "post";
  for (let n = 1; n < 50; n++) {
    const candidate = n === 1 ? base : `${base}-${n}`;
    try {
      await assertSlugFree(candidate);
      return candidate;
    } catch (error) {
      if (!(error instanceof AgentError) || error.status !== 409) throw error;
    }
  }
  throw new AgentError(409, "Не удалось подобрать свободный slug, укажите его явно");
}

async function resolveRelations(tx: Prisma.TransactionClient, categories: string[], tags: string[]) {
  const found = await tx.category.findMany({ where: { slug: { in: categories } }, select: { id: true, slug: true } });
  const missing = categories.filter((s) => !found.some((c) => c.slug === s));
  if (missing.length > 0) throw new AgentError(422, `Неизвестные рубрики: ${missing.join(", ")}`);

  const tagIds: string[] = [];
  for (const name of new Set(tags)) {
    const slug = slugify(name);
    if (!slug) throw new AgentError(422, `Недопустимая метка: ${name}`);
    const tag = await tx.tag.upsert({ where: { slug }, create: { slug, name }, update: {}, select: { id: true } });
    tagIds.push(tag.id);
  }
  return { categoryIds: found.map((c) => c.id), tagIds: [...new Set(tagIds)] };
}

async function coverId(path: string | null | undefined): Promise<string | null | undefined> {
  if (path === undefined || path === null) return path;
  const media = await db.mediaAsset.findUnique({ where: { path }, select: { id: true } });
  if (!media) throw new AgentError(422, `Медиафайл не найден: ${path}`);
  return media.id;
}

function scalarData(input: PostCreate | PostPatch) {
  const { title, excerpt, expiresAt, authorName, seoTitle, seoDescription } = input;
  const publishedAt = input.publishedAt;
  const body = input.body === undefined ? undefined : sanitize(input.body);
  return { title, excerpt, body, publishedAt, expiresAt, authorName, seoTitle, seoDescription };
}

export async function createPost(input: PostCreate) {
  const slug = input.slug ?? (await freeSlugFromTitle(input.title));
  if (input.slug) await assertSlugFree(slug);
  const cover = await coverId(input.coverPath);

  const post = await db.$transaction(async (tx) => {
    const { categoryIds, tagIds } = await resolveRelations(tx, input.categories, input.tags);
    return tx.post.create({
      data: {
        ...scalarData(input), title: input.title, body: sanitize(input.body), slug, coverId: cover,
        status: "published", publishedAt: input.publishedAt ?? new Date(),
        categories: { create: categoryIds.map((categoryId) => ({ categoryId })) },
        tags: { create: tagIds.map((tagId) => ({ tagId })) },
      },
      select: POST_SELECT,
    });
  });
  await audit("post.create", "post", slug, { title: post.title });
  revalidateSite();
  return presentPost(post);
}

export async function updatePost(slug: string, patch: PostPatch) {
  const existing = await db.post.findUnique({ where: { slug }, select: { id: true } });
  if (!existing) throw new AgentError(404, "Запись не найдена");
  const cover = await coverId(patch.coverPath);

  const post = await db.$transaction(async (tx) => {
    if (patch.categories !== undefined || patch.tags !== undefined) {
      await replaceRelations(tx, existing.id, patch);
    }
    return tx.post.update({
      where: { id: existing.id },
      data: { ...scalarData(patch), status: patch.status, coverId: cover },
      select: POST_SELECT,
    });
  });
  await audit("post.update", "post", slug, { fields: Object.keys(patch) });
  revalidateSite();
  return presentPost(post);
}

async function replaceRelations(tx: Prisma.TransactionClient, postId: string, patch: PostPatch) {
  const { categoryIds, tagIds } = await resolveRelations(tx, patch.categories ?? [], patch.tags ?? []);
  if (patch.categories !== undefined) {
    await tx.postCategory.deleteMany({ where: { postId } });
    await tx.postCategory.createMany({ data: categoryIds.map((categoryId) => ({ postId, categoryId })) });
  }
  if (patch.tags !== undefined) {
    await tx.postTag.deleteMany({ where: { postId } });
    await tx.postTag.createMany({ data: tagIds.map((tagId) => ({ postId, tagId })) });
  }
}

/** Soft delete: the post disappears from the site but stays recoverable (status archived). */
export async function archivePost(slug: string) {
  const result = await db.post.updateMany({ where: { slug }, data: { status: "archived" } });
  if (result.count === 0) throw new AgentError(404, "Запись не найдена");
  await audit("post.archive", "post", slug);
  revalidateSite();
}

export async function getPostForAgent(slug: string) {
  const post = await db.post.findUnique({ where: { slug }, select: { ...POST_SELECT, body: true } });
  if (!post) throw new AgentError(404, "Запись не найдена");
  return { ...presentPost(post), body: post.body };
}

export async function listPostsForAgent(opts: { limit: number; offset: number; q?: string; status?: string }) {
  const where: Prisma.PostWhereInput = {
    ...(opts.status === "published" || opts.status === "archived" ? { status: opts.status } : {}),
    ...(opts.q ? { title: { contains: opts.q, mode: "insensitive" } } : {}),
  };
  const [total, rows] = await Promise.all([
    db.post.count({ where }),
    db.post.findMany({ where, select: POST_SELECT, orderBy: { publishedAt: "desc" }, take: opts.limit, skip: opts.offset }),
  ]);
  return { total, items: rows.map(presentPost) };
}

export async function updatePage(slug: string, patch: { title?: string; body?: string }) {
  const result = await db.page.updateMany({
    where: { slug },
    data: { title: patch.title, body: patch.body === undefined ? undefined : sanitize(patch.body) },
  });
  if (result.count === 0) throw new AgentError(404, "Страница не найдена");
  await audit("page.update", "page", slug, { fields: Object.keys(patch) });
  revalidateSite();
}
