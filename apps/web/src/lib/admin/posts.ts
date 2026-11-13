import "server-only";
import { Agent } from "../../../scripts/agent/posts";
import { postCreateSchema, postPatchSchema } from "../../../scripts/agent/schemas";
import { db } from "@/lib/db";
import { requireAdmin } from "./auth";

const agent = new Agent(db);

export async function adminListPosts(limit = 30) {
  await requireAdmin();
  const rows = await db.post.findMany({
    orderBy: { publishedAt: "desc" },
    take: limit,
    select: { slug: true, title: true, status: true, publishedAt: true },
  });
  return rows;
}

export async function adminGetPost(slug: string) {
  await requireAdmin();
  const row = await db.post.findUnique({
    where: { slug },
    select: {
      slug: true,
      title: true,
      excerpt: true,
      body: true,
      authorName: true,
      categories: { select: { category: { select: { slug: true } } } },
    },
  });
  if (!row) return null;
  return {
    ...row,
    categories: row.categories.map((c) => c.category.slug),
  };
}

export async function adminCreatePost(form: FormData) {
  await requireAdmin();
  const raw = {
    title: String(form.get("title") ?? ""),
    body: String(form.get("body") ?? ""),
    excerpt: emptyToNull(form.get("excerpt")),
    slug: emptyToUndefined(form.get("slug")),
    authorName: emptyToNull(form.get("authorName")),
    categories: parseCategories(form.get("categories")),
    tags: [],
  };
  const parsed = postCreateSchema.safeParse(raw);
  if (!parsed.success) return { ok: false as const, message: parsed.error.issues[0]?.message ?? "Ошибка валидации" };
  try {
    const post = await agent.createPost(parsed.data);
    return { ok: true as const, slug: post.slug };
  } catch (e) {
    return { ok: false as const, message: e instanceof Error ? e.message : "Не удалось создать запись" };
  }
}

function patchFromForm(form: FormData) {
  return {
    title: form.get("title") !== null ? String(form.get("title")) : undefined,
    body: form.get("body") !== null ? String(form.get("body")) : undefined,
    excerpt: form.has("excerpt") ? emptyToNull(form.get("excerpt")) : undefined,
    authorName: form.has("authorName") ? emptyToNull(form.get("authorName")) : undefined,
    categories: form.has("categories") ? parseCategories(form.get("categories")) : undefined,
  };
}

export async function adminUpdatePost(slug: string, form: FormData) {
  await requireAdmin();
  const raw = patchFromForm(form);
  const parsed = postPatchSchema.safeParse(raw);
  if (!parsed.success) return { ok: false as const, message: parsed.error.issues[0]?.message ?? "Ошибка валидации" };
  try {
    await agent.updatePost(slug, parsed.data);
    return { ok: true as const, slug };
  } catch (e) {
    return { ok: false as const, message: e instanceof Error ? e.message : "Не удалось сохранить" };
  }
}

function emptyToNull(v: FormDataEntryValue | null): string | null {
  if (v === null) return null;
  const s = String(v).trim();
  return s === "" ? null : s;
}

function emptyToUndefined(v: FormDataEntryValue | null): string | undefined {
  if (v === null) return undefined;
  const s = String(v).trim();
  return s === "" ? undefined : s;
}

function parseCategories(v: FormDataEntryValue | null): string[] {
  if (v === null) return [];
  return String(v)
    .split(/[,;\s]+/)
    .map((s) => s.trim())
    .filter(Boolean);
}
