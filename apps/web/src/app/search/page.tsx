import type { Metadata } from "next";
import { PostGrid } from "@/components/post-card";
import { listPosts } from "@/lib/content";

export const metadata: Metadata = { title: "Поиск", robots: { index: false, follow: true } };

type Props = { searchParams: Promise<{ s?: string | string[] }> };

export default async function SearchPage({ searchParams }: Props) {
  const raw = (await searchParams).s;
  const query = (Array.isArray(raw) ? raw[0] : raw)?.trim().slice(0, 100) ?? "";
  const results = query.length >= 2 ? await listPosts({ search: query }, 1, 24) : null;

  return (
    <div className="container">
      <h1 className="page-title">Поиск</h1>
      {results === null ? (
        <p className="empty">Введите не меньше двух символов.</p>
      ) : (
        <>
          <p className="label">
            Найдено записей: {results.total}
            {results.total > results.items.length ? `, показаны первые ${results.items.length}` : ""}
          </p>
          <PostGrid posts={results.items} />
        </>
      )}
    </div>
  );
}
