"use server";

import { redirect } from "next/navigation";
import { adminEnabled } from "./config";
import { clearSession, loginWithPassword } from "./auth";
import { adminCreatePost, adminUpdatePost } from "./posts";

export async function loginAdminAction(formData: FormData) {
  if (!adminEnabled()) return { ok: false as const, message: "Админка отключена" };
  const res = await loginWithPassword(String(formData.get("password") ?? ""));
  if (res.ok) redirect("/admin");
  return res;
}

export async function logoutAdminAction() {
  await clearSession();
  redirect("/admin/login");
}

export async function createPostAction(formData: FormData) {
  if (!adminEnabled()) return { ok: false as const, message: "Админка отключена" };
  const res = await adminCreatePost(formData);
  if (res.ok) redirect(`/admin/posts/${res.slug}`);
  return res;
}

export async function updatePostAction(slug: string, formData: FormData) {
  if (!adminEnabled()) return { ok: false as const, message: "Админка отключена" };
  const res = await adminUpdatePost(slug, formData);
  if (res.ok) redirect(`/admin/posts/${slug}?saved=1`);
  return res;
}
