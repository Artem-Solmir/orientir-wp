import type { Prisma, PrismaClient } from "@prisma/client";
import { sanitize } from "../../src/lib/sanitize";
import { isReservedSlug, slugify } from "../../src/lib/slug";
import type { PostCreate, PostPatch } from "./schemas";

export class AgentError extends Error {}

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
    slug: p.slug, url: `/${p.slug}/`, title: p.title, excerpt: p.excerpt, status: p.status,
    publishedAt: p.publishedAt, expiresAt: p.expiresAt, authorName: p.authorName,
    seoTitle: p.seoTitle, seoDescription: p.seoDescription, coverPath: p.cover?.path ?? null,
    categories: p.categories.map((c) => c.category.slug), tags: p.tags.map((t) => t.tag.name),
  };
}

export class Agent {
  constructor(private readonly db: PrismaClient) {}

  private async audit(action: string, entity: string, entityId: string, meta?: Prisma.InputJsonObject) {
    await this.db.auditLog.create({ data: { actorType: "agent", action, entity, entityId, meta } });
  }

  private async assertSlugFree(slug: string, exceptPostId?: string): Promise<void> {
    if (isReservedSlug(slug)) throw new AgentError(`slug «${slug}» зарезервирован`);
    const [post, page] = await Promise.all([
      this.db.post.findUnique({ where: { slug }, select: { id: true } }),
      this.db.page.findUnique({ where: { slug }, select: { id: true } }),
    ]);
    if ((post && post.id !== exceptPostId) || page) throw new AgentError(`slug «${slug}» уже занят`);
  }

  private async freeSlugFromTitle(title: string): Promise<string> {
    const base = slugify(title) || "post";
    for (let n = 1; n < 50; n++) {
      const candidate = n === 1 ? base : `${base}-${n}`;
      const taken = await Promise.all([
        this.db.post.findUnique({ where: { slug: candidate }, select: { id: true } }),
        this.db.page.findUnique({ where: { slug: candidate }, select: { id: true } }),
      ]);
      if (!taken[0] && !taken[1] && !isReservedSlug(candidate)) return candidate;
    }
    throw new AgentError("Не удалось подобрать свободный slug, укажите его явно");
  }

  private async relations(tx: Prisma.TransactionClient, categories: string[], tags: string[]) {
    const found = await tx.category.findMany({ where: { slug: { in: categories } }, select: { id: true, slug: true } });
    const missing = categories.filter((s) => !found.some((c) => c.slug === s));
    if (missing.length > 0) throw new AgentError(`Неизвестные рубрики: ${missing.join(", ")}`);

    const tagIds = new Set<string>();
    for (const name of new Set(tags)) {
      const slug = slugify(name);
      if (!slug) throw new AgentError(`Недопустимая метка: ${name}`);
      const tag = await tx.tag.upsert({ where: { slug }, create: { slug, name }, update: {}, select: { id: true } });
      tagIds.add(tag.id);
    }
    return { categoryIds: found.map((c) => c.id), tagIds: [...tagIds] };
  }

  private async coverId(path: string | null | undefined): Promise<string | null | undefined> {
    if (path === undefined || path === null) return path;
    const media = await this.db.mediaAsset.findUnique({ where: { path }, select: { id: true } });
    if (!media) throw new AgentError(`Медиафайл не найден: ${path}`);
    return media.id;
  }

  async createPost(input: PostCreate) {
    const slug = input.slug ?? (await this.freeSlugFromTitle(input.title));
    if (input.slug) await this.assertSlugFree(slug);
    const cover = await this.coverId(input.coverPath);

    const post = await this.db.$transaction(async (tx) => {
      const { categoryIds, tagIds } = await this.relations(tx, input.categories, input.tags);
      return tx.post.create({
        data: {
          slug, title: input.title, body: sanitize(input.body), excerpt: input.excerpt, coverId: cover,
          expiresAt: input.expiresAt, authorName: input.authorName, seoTitle: input.seoTitle,
          seoDescription: input.seoDescription, status: "published", publishedAt: input.publishedAt ?? new Date(),
          categories: { create: categoryIds.map((categoryId) => ({ categoryId })) },
          tags: { create: tagIds.map((tagId) => ({ tagId })) },
        },
        select: POST_SELECT,
      });
    });
    await this.audit("post.create", "post", slug, { title: post.title });
    return presentPost(post);
  }

  async updatePost(slug: string, patch: PostPatch) {
    const existing = await this.db.post.findUnique({ where: { slug }, select: { id: true } });
    if (!existing) throw new AgentError("Запись не найдена");
    const cover = await this.coverId(patch.coverPath);
    const { categories, tags, body, coverPath: _cover, ...scalars } = patch;
    void _cover;

    const post = await this.db.$transaction(async (tx) => {
      if (categories !== undefined || tags !== undefined) await this.replaceRelations(tx, existing.id, { categories, tags });
      return tx.post.update({
        where: { id: existing.id },
        data: { ...scalars, body: body === undefined ? undefined : sanitize(body), coverId: cover },
        select: POST_SELECT,
      });
    });
    await this.audit("post.update", "post", slug, { fields: Object.keys(patch) });
    return presentPost(post);
  }

  private async replaceRelations(tx: Prisma.TransactionClient, postId: string, patch: Pick<PostPatch, "categories" | "tags">) {
    const { categoryIds, tagIds } = await this.relations(tx, patch.categories ?? [], patch.tags ?? []);
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
  async archivePost(slug: string) {
    const result = await this.db.post.updateMany({ where: { slug }, data: { status: "archived" } });
    if (result.count === 0) throw new AgentError("Запись не найдена");
    await this.audit("post.archive", "post", slug);
  }

  async getPost(slug: string) {
    const post = await this.db.post.findUnique({ where: { slug }, select: { ...POST_SELECT, body: true } });
    if (!post) throw new AgentError("Запись не найдена");
    return { ...presentPost(post), body: post.body };
  }

  async listPosts(opts: { limit: number; offset: number; q?: string; status?: string }) {
    const where: Prisma.PostWhereInput = {
      ...(opts.status === "published" || opts.status === "archived" ? { status: opts.status } : {}),
      ...(opts.q ? { title: { contains: opts.q, mode: "insensitive" } } : {}),
    };
    const [total, rows] = await Promise.all([
      this.db.post.count({ where }),
      this.db.post.findMany({ where, select: POST_SELECT, orderBy: { publishedAt: "desc" }, take: opts.limit, skip: opts.offset }),
    ]);
    return { total, items: rows.map(presentPost) };
  }

  async updatePage(slug: string, patch: { title?: string; body?: string }) {
    const result = await this.db.page.updateMany({
      where: { slug },
      data: { title: patch.title, body: patch.body === undefined ? undefined : sanitize(patch.body) },
    });
    if (result.count === 0) throw new AgentError("Страница не найдена");
    await this.audit("page.update", "page", slug, { fields: Object.keys(patch) });
  }
}
