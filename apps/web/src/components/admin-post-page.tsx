"use client";

import { useRouter } from "next/navigation";
import { useEffect, useState } from "react";
import { AdminPostForm } from "@/components/admin-post-form";
import {
  adminUsesRemoteApi,
  remoteAdminSession,
  remoteCategories,
  remoteGetPost,
  type AdminCategory,
} from "@/lib/admin/remote-api";

type Props = { slug?: string; mode: "create" | "edit" };

export function AdminPostPage({ slug, mode }: Props) {
  const router = useRouter();
  const [ready, setReady] = useState(false);
  const [categories, setCategories] = useState<AdminCategory[]>([]);
  const [initial, setInitial] = useState<Parameters<typeof AdminPostForm>[0]["initial"]>();

  useEffect(() => {
    let cancelled = false;
    (async () => {
      if (!adminUsesRemoteApi()) {
        router.replace("/admin/login/");
        return;
      }
      const session = await remoteAdminSession();
      if (!session.ok) {
        router.replace("/admin/login/");
        return;
      }
      const cats = await remoteCategories();
      if (cancelled) return;
      if ("error" in cats) return;
      setCategories(cats);
      if (mode === "edit" && slug) {
        const post = await remoteGetPost(slug);
        if (cancelled) return;
        if (!post || "error" in post) {
          router.replace("/admin/");
          return;
        }
        setInitial(post);
      }
      setReady(true);
    })();
    return () => {
      cancelled = true;
    };
  }, [mode, router, slug]);

  if (!ready) return <p className="label">Загрузка…</p>;
  return <AdminPostForm mode={mode} categories={categories} initial={initial} />;
}
