import { sanitize } from "../../src/lib/sanitize";
import {
  StoreError,
  archivePost,
  createPost,
  getPostRecord,
  listPostRecords,
  updatePageRecord,
  updatePost,
} from "../../src/lib/store/write";
import type { PostCreate, PostPatch } from "./schemas";

export class AgentError extends Error {}

export function presentPost(p: NonNullable<ReturnType<typeof getPostRecord>>) {
  return {
    slug: p.slug,
    url: `/${p.slug}/`,
    title: p.title,
    excerpt: p.excerpt,
    status: p.status,
    publishedAt: p.publishedAt ? new Date(p.publishedAt) : null,
    expiresAt: p.expiresAt ? new Date(p.expiresAt) : null,
    authorName: p.authorName,
    seoTitle: p.seoTitle,
    seoDescription: p.seoDescription,
    coverPath: p.cover?.path ?? null,
    categories: p.categories,
    tags: p.tags,
  };
}

export class Agent {
  async createPost(input: PostCreate) {
    try {
      const post = createPost({
        title: input.title,
        body: input.body,
        excerpt: input.excerpt,
        slug: input.slug,
        authorName: input.authorName,
        categories: input.categories,
        tags: input.tags,
        coverPath: input.coverPath,
        expiresAt: input.expiresAt,
        publishedAt: input.publishedAt,
        seoTitle: input.seoTitle,
        seoDescription: input.seoDescription,
      });
      return presentPost(post);
    } catch (e) {
      throw new AgentError(e instanceof StoreError ? e.message : e instanceof Error ? e.message : "ошибка");
    }
  }

  async updatePost(slug: string, patch: PostPatch) {
    try {
      const post = updatePost(slug, {
        title: patch.title,
        body: patch.body,
        excerpt: patch.excerpt,
        authorName: patch.authorName,
        categories: patch.categories,
        tags: patch.tags,
        coverPath: patch.coverPath,
        seoTitle: patch.seoTitle,
        seoDescription: patch.seoDescription,
        expiresAt: patch.expiresAt,
      });
      return presentPost(post);
    } catch (e) {
      throw new AgentError(e instanceof StoreError ? e.message : e instanceof Error ? e.message : "ошибка");
    }
  }

  async archivePost(slug: string) {
    try {
      archivePost(slug);
    } catch (e) {
      throw new AgentError(e instanceof StoreError ? e.message : "Запись не найдена");
    }
  }

  async getPost(slug: string) {
    const post = getPostRecord(slug);
    if (!post) throw new AgentError("Запись не найдена");
    return { ...presentPost(post), body: post.body };
  }

  async listPosts(opts: { limit: number; offset: number; q?: string; status?: string }) {
    const { total, items } = listPostRecords(opts);
    return { total, items: items.map(presentPost) };
  }

  async updatePage(slug: string, patch: { title?: string; body?: string }) {
    try {
      updatePageRecord(slug, { title: patch.title, body: patch.body === undefined ? undefined : sanitize(patch.body) });
    } catch (e) {
      throw new AgentError(e instanceof StoreError ? e.message : "Страница не найдена");
    }
  }
}
