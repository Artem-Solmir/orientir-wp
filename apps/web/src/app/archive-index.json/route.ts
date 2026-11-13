import { allLivePostsForSearch } from "@/lib/content";
import { loadStore, parseIso } from "@/lib/store/reader";
import { htmlToText, summarize } from "@/lib/text";

export const dynamic = "force-static";

/** Lightweight listing for client-side archive filters on the static site. */
export async function GET() {
  const store = loadStore();
  const posts = await allLivePostsForSearch();
  const index = posts.map((p) => ({
    s: p.slug,
    t: p.title,
    d: parseIso(p.publishedAt)?.toISOString().slice(0, 10) ?? "",
    y: parseIso(p.publishedAt)?.getFullYear() ?? 0,
    c: p.categories.map((slug) => store.categoriesBySlug.get(slug)?.slug ?? slug),
    e: summarize(htmlToText(p.excerpt || p.body), 140),
  }));
  return Response.json(index);
}
