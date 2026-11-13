import "server-only";
import { postCreateSchema, postPatchSchema } from "../../../scripts/agent/schemas";
import { loadStore } from "@/lib/store/reader";
import { StoreError, createPost, getPostRecord, listPostRecords, updatePost } from "@/lib/store/write";
import { requireAdmin } from "./auth";

export async function adminListPosts(limit = 30) {
  await requireAdmin();
  const { items } = listPostRecords({ limit, offset: 0 });
  return items.slice(0, limit).map((p) => ({
    slug: p.slug,
    title: p.title,
    status: p.status,
    publishedAt: p.publishedAt ? new Date(p.publishedAt) : null,
  }));
}

export async function adminGetPost(slug: string) {
  await requireAdmin();
  const row = getPostRecord(slug);
  if (!row) return null;
  return {
    slug: row.slug,
    title: row.title,
    excerpt: row.excerpt,
    body: row.body,
    authorName: row.authorName,
    categories: row.categories,
  };
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

export async function adminCreatePost(form: FormData) {
  await requireAdmin();
  const raw = {
    title: String(form.get("title") ?? ""),
    body: String(form.get("body") ?? ""),
    excerpt: emptyToNull(form.get("excerpt")),
    slug: emptyToUndefined(form.get("slug")),
    authorName: emptyToNull(form.get("authorName")),
    categories: parseCategories(form.get("categories")),
    tags: [] as string[],
  };
  const parsed = postCreateSchema.safeParse(raw);
  if (!parsed.success) return { ok: false as const, message: parsed.error.issues[0]?.message ?? "Ошибка валидации" };
  try {
    const post = createPost(parsed.data);
    return { ok: true as const, slug: post.slug };
  } catch (e) {
    const message = e instanceof StoreError ? e.message : e instanceof Error ? e.message : "Не удалось создать запись";
    return { ok: false as const, message };
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
    updatePost(slug, parsed.data);
    return { ok: true as const, slug };
  } catch (e) {
    const message = e instanceof StoreError ? e.message : e instanceof Error ? e.message : "Не удалось сохранить";
    return { ok: false as const, message };
  }
}

export async function adminCategories() {
  await requireAdmin();
  return loadStore().categories.map((c) => ({ slug: c.slug, name: c.name }));
}
