import { notFound } from "next/navigation";
import { Suspense } from "react";
import { ArchiveShell } from "@/components/archive-shell";
import { PostGrid } from "@/components/post-card";
import { Pagination } from "@/components/pagination";
import { archiveYears, getNavCategories, listPosts, type PostFilter } from "@/lib/content";
import { pageCount, pageHref } from "@/lib/pagination";

/** Shared body of category, tag and archive pages. 404s for pages past the end. */
export async function Listing({ title, filter, page, base }: { title: string; filter: PostFilter; page: number; base: string }) {
  const { items, total } = await listPosts(filter, page);
  const pages = pageCount(total);
  if (page > pages) notFound();

  const isArchive = base === "/archive" && page === 1 && Object.keys(filter).length === 0;
  const [years, categories] = isArchive ? await Promise.all([archiveYears(), getNavCategories()]) : [[], []];

  return (
    <div className="container">
      <h1 className="page-title">{title}</h1>
      {isArchive ? (
        <Suspense fallback={<p className="empty">Загружаем…</p>}>
          <ArchiveShell total={total} page={page} pages={pages} base={`${base}/`} items={items} years={years} categories={categories} />
        </Suspense>
      ) : (
        <>
          <PostGrid posts={items} />
          <Pagination current={page} total={pages} hrefFor={(p) => pageHref(base, p)} />
        </>
      )}
    </div>
  );
}
