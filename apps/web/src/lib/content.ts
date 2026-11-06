import "server-only";
import { unstable_cache } from "next/cache";
import { cache } from "react";
import type { Prisma } from "@prisma/client";
import { db } from "./db";
import { PER_PAGE } from "./pagination";
import { htmlToText, summarize } from "./text";

export const REVALIDATE = 3600;

export type Cover = { path: string; width: number | null; height: number | null; alt: string | null };
export type CardPost = {
  slug: string;
  title: string;
  summary: string;
  publishedAt: Date | null;
  authorName: string | null;
  cover: Cover | null;
};

const live = (): Prisma.PostWhereInput => ({ status: "published", publishedAt: { lte: new Date() } });

const cardSelect = {
  slug: true,
  title: true,
  excerpt: true,
  body: true,
  publishedAt: true,
  authorName: true,
  cover: { select: { path: true, width: true, height: true, alt: true } },
} satisfies Prisma.PostSelect;

type CardRow = Prisma.PostGetPayload<{ select: typeof cardSelect }>;

function toCard(row: CardRow): CardPost {
  const { body, excerpt, ...rest } = row;
  return { ...rest, summary: summarize(excerpt ? htmlToText(excerpt) : htmlToText(body), 140) };
}

export type PostFilter = { categorySlugs?: string[]; excludeCategorySlugs?: string[]; tagSlug?: string; search?: string; withCover?: boolean };

function where(filter: PostFilter): Prisma.PostWhereInput {
  const and: Prisma.PostWhereInput[] = [live()];
  if (filter.categorySlugs?.length) and.push({ categories: { some: { category: { slug: { in: filter.categorySlugs } } } } });
  if (filter.excludeCategorySlugs?.length) and.push({ categories: { none: { category: { slug: { in: filter.excludeCategorySlugs } } } } });
  if (filter.tagSlug) and.push({ tags: { some: { tag: { slug: filter.tagSlug } } } });
  if (filter.withCover) and.push({ coverId: { not: null } });
  if (filter.search) {
    and.push({ OR: [{ title: { contains: filter.search, mode: "insensitive" } }, { body: { contains: filter.search, mode: "insensitive" } }] });
  }
  return { AND: and };
}

export async function listPosts(filter: PostFilter, page = 1, perPage = PER_PAGE) {
  const w = where(filter);
  const [rows, total] = await Promise.all([
    db.post.findMany({ where: w, select: cardSelect, orderBy: { publishedAt: "desc" }, skip: (page - 1) * perPage, take: perPage }),
    db.post.count({ where: w }),
  ]);
  return { items: rows.map(toCard), total };
}

export type PostView = NonNullable<Awaited<ReturnType<typeof getPost>>>;

export const getPost = cache(async (slug: string) => {
  return db.post.findFirst({
    where: { slug, ...live() },
    select: {
      slug: true, title: true, excerpt: true, body: true, publishedAt: true, updatedAt: true, authorName: true,
      cover: { select: { path: true, width: true, height: true, alt: true } },
      categories: { select: { category: { select: { slug: true, name: true } } } },
      tags: { select: { tag: { select: { slug: true, name: true } } } },
    },
  });
});

export async function getNeighbors(publishedAt: Date | null) {
  if (!publishedAt) return { prev: null, next: null };
  const pick = { slug: true, title: true } as const;
  const [prev, next] = await Promise.all([
    db.post.findFirst({ where: { ...live(), publishedAt: { lt: publishedAt } }, orderBy: { publishedAt: "desc" }, select: pick }),
    db.post.findFirst({ where: { ...live(), publishedAt: { gt: publishedAt, lte: new Date() } }, orderBy: { publishedAt: "asc" }, select: pick }),
  ]);
  return { prev, next };
}

export const getPage = cache(async (slug: string) => {
  return db.page.findFirst({
    where: { slug, status: "published" },
    include: { sections: { orderBy: { order: "asc" } } },
  });
});

export const getCategory = (slug: string) => db.category.findUnique({ where: { slug } });
export const getTag = (slug: string) => db.tag.findUnique({ where: { slug } });

/** Navigation: categories with a meaningful number of posts. Cached; revalidated hourly or by tag. */
export const getNavCategories = unstable_cache(
  async () => {
    const rows = await db.category.findMany({
      select: { slug: true, name: true, _count: { select: { posts: true } } },
    });
    return rows.filter((c) => c._count.posts >= 5 && c.slug !== "bez-rubriki").sort((a, b) => b._count.posts - a._count.posts);
  },
  ["nav-categories"],
  { revalidate: REVALIDATE, tags: ["nav"] },
);

export async function allSlugsForSitemap() {
  const [posts, pages, categories, tags] = await Promise.all([
    db.post.findMany({ where: live(), select: { slug: true, updatedAt: true } }),
    db.page.findMany({ where: { status: "published" }, select: { slug: true, updatedAt: true } }),
    db.category.findMany({ where: { posts: { some: {} } }, select: { slug: true } }),
    db.tag.findMany({ where: { posts: { some: {} } }, select: { slug: true, _count: { select: { posts: true } } } }),
  ]);
  return { posts, pages, categories, tags };
}
