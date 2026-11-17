"use client";

import Link from "next/link";
import { useRouter } from "next/navigation";
import { useEffect, useState } from "react";
import { adminUsesRemoteApi, remoteAdminSession, remoteListPosts, remoteLogout, type AdminListItem } from "@/lib/admin/remote-api";

export function AdminHome() {
  const router = useRouter();
  const [email, setEmail] = useState<string | null>(null);
  const [posts, setPosts] = useState<AdminListItem[]>([]);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    let cancelled = false;
    (async () => {
      if (!adminUsesRemoteApi()) {
        router.replace("/admin/login");
        return;
      }
      const session = await remoteAdminSession();
      if (!session.ok) {
        router.replace("/admin/login/");
        return;
      }
      if (cancelled) return;
      setEmail(session.email ?? "админ");
      const list = await remoteListPosts();
      if (cancelled) return;
      if ("error" in list) {
        setError(list.error);
        return;
      }
      setPosts(list);
    })();
    return () => {
      cancelled = true;
    };
  }, [router]);

  async function onLogout() {
    await remoteLogout();
    router.replace("/admin/login/");
  }

  if (!email && !error) return <p className="label">Загрузка…</p>;

  return (
    <>
      <header className="admin-head">
        <div>
          <h1 className="page-title">Записи</h1>
          <p className="label">Вход: {email}</p>
          <p className="notice">
            Правки сохраняются на сервере. Чтобы они появились на публичных страницах, нужна публикация сайта.
          </p>
        </div>
        <div className="actions">
          <Link href="/admin/posts/new/" className="btn btn--primary">
            Новая запись
          </Link>
          <button type="button" className="btn btn--secondary" onClick={onLogout}>
            Выйти
          </button>
        </div>
      </header>
      {error && <p className="error">{error}</p>}
      <ul className="admin-post-list">
        {posts.map((p) => (
          <li key={p.slug}>
            <time className="label" dateTime={p.publishedAt ?? undefined}>
              {p.publishedAt ? new Date(p.publishedAt).toLocaleDateString("ru-RU") : "—"}
            </time>
            <Link href={`/admin/posts/${p.slug}/`}>{p.title}</Link>
            <span className="label">{p.status}</span>
          </li>
        ))}
      </ul>
    </>
  );
}
