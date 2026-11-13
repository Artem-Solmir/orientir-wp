import Link from "next/link";
import { redirect } from "next/navigation";
import { logoutAdminAction } from "@/lib/admin/actions";
import { adminEmail } from "@/lib/admin/auth";
import { adminEnabled } from "@/lib/admin/config";
import { adminListPosts } from "@/lib/admin/posts";

export default async function AdminHome() {
  if (!adminEnabled()) return null;
  const email = await adminEmail();
  if (!email) redirect("/admin/login");
  const posts = await adminListPosts(40);

  return (
    <>
      <header className="admin-head">
        <div>
          <h1 className="page-title">Записи</h1>
          <p className="label">Сессия: {email}</p>
        </div>
        <div className="actions">
          <Link href="/admin/posts/new" className="btn btn--primary">
            Новая запись
          </Link>
          <form action={logoutAdminAction}>
            <button type="submit" className="btn btn--secondary">
              Выйти
            </button>
          </form>
        </div>
      </header>
      <ul className="admin-post-list">
        {posts.map((p) => (
          <li key={p.slug}>
            <time className="label" dateTime={p.publishedAt?.toISOString()}>
              {p.publishedAt?.toLocaleDateString("ru-RU") ?? "—"}
            </time>
            <Link href={`/admin/posts/${p.slug}`}>{p.title}</Link>
            <span className="label">{p.status}</span>
          </li>
        ))}
      </ul>
    </>
  );
}
