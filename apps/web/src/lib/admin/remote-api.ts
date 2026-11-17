export type AdminCategory = { slug: string; name: string };
export type AdminListItem = { slug: string; title: string; status: string; publishedAt: string | null };
export type AdminPost = {
  slug: string;
  title: string;
  excerpt: string | null;
  body: string;
  authorName: string | null;
  categories: string[];
};

function apiUrl(): string {
  return process.env.NEXT_PUBLIC_ADMIN_API_URL?.trim() || "/admin-api.php";
}

export function adminUsesRemoteApi(): boolean {
  return Boolean(process.env.NEXT_PUBLIC_ADMIN_API_URL?.trim());
}

async function apiPost<T>(action: string, payload: Record<string, string | undefined> = {}): Promise<T | { error: string }> {
  const body = new URLSearchParams({ action, ...payload });
  const res = await fetch(apiUrl(), {
    method: "POST",
    credentials: "include",
    headers: { "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8" },
    body,
  });
  const data = (await res.json().catch(() => ({}))) as T & { error?: string };
  if (!res.ok) return { error: data.error ?? "Ошибка запроса" };
  return data;
}

async function apiJson<T>(action: string, json: Record<string, unknown>): Promise<T | { error: string }> {
  const res = await fetch(apiUrl(), {
    method: "POST",
    credentials: "include",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ action, ...json }),
  });
  const data = (await res.json().catch(() => ({}))) as T & { error?: string };
  if (!res.ok) return { error: data.error ?? "Ошибка запроса" };
  return data;
}

export async function remoteAdminSession(): Promise<{ ok: boolean; email?: string }> {
  const res = await fetch(`${apiUrl()}?action=session`, { credentials: "include", cache: "no-store" });
  const data = (await res.json().catch(() => ({}))) as { ok?: boolean; email?: string };
  return { ok: Boolean(data.ok), email: data.email };
}

export async function remoteLogout(): Promise<void> {
  await apiPost("logout");
}

export async function remoteListPosts(): Promise<AdminListItem[] | { error: string }> {
  const data = await apiPost<{ ok: boolean; items: AdminListItem[] }>("list");
  if ("error" in data) return data;
  return data.items ?? [];
}

export async function remoteCategories(): Promise<AdminCategory[] | { error: string }> {
  const data = await apiPost<{ ok: boolean; items: AdminCategory[] }>("categories");
  if ("error" in data) return data;
  return data.items ?? [];
}

export async function remoteGetPost(slug: string): Promise<AdminPost | null | { error: string }> {
  const data = await apiPost<{ ok: boolean; post?: AdminPost }>("get", { slug });
  if ("error" in data) return data;
  return data.post ?? null;
}

export async function remoteCreatePost(payload: Record<string, unknown>): Promise<{ ok: true; slug: string } | { ok: false; message: string }> {
  const data = await apiJson<{ ok: boolean; slug?: string; error?: string }>("create", payload);
  if ("error" in data) return { ok: false, message: data.error ?? "Ошибка" };
  if (!data.ok || !data.slug) return { ok: false, message: "Не удалось создать" };
  return { ok: true, slug: data.slug };
}

export async function remoteUpdatePost(
  slug: string,
  payload: Record<string, unknown>,
): Promise<{ ok: true } | { ok: false; message: string }> {
  const data = await apiJson<{ ok: boolean; error?: string }>("update", { slug, ...payload });
  if ("error" in data) return { ok: false, message: data.error ?? "Ошибка" };
  if (!data.ok) return { ok: false, message: "Не удалось сохранить" };
  return { ok: true };
}
