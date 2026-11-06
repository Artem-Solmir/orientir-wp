import { notFound } from "next/navigation";
import { PostGrid } from "@/components/post-card";
import { Pagination } from "@/components/pagination";
import { listPosts, type PostFilter } from "@/lib/content";
import { pageCount, pageHref } from "@/lib/pagination";

/** Shared body of category, tag and archive pages. 404s for pages past the end. */
export async function Listing({ title, filter, page, base }: { title: string; filter: PostFilter; page: number; base: string }) {
  const { items, total } = await listPosts(filter, page);
  const pages = pageCount(total);
  if (page > pages) notFound();

  return (
    <div className="container">
      <h1 className="page-title">{title}</h1>
      <PostGrid posts={items} />
      <Pagination current={page} total={pages} hrefFor={(p) => pageHref(base, p)} />
    </div>
  );
}
