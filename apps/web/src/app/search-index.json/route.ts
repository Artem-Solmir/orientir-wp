import { allLivePostsForSearch } from "@/lib/content";
import { parseIso } from "@/lib/store/reader";
import { htmlToText, summarize } from "@/lib/text";

export const dynamic = "force-static";

const TEXT_LIMIT = 2000;

/** Compact index for client-side search on the static site: title, date and the start of the text. */
export async function GET() {
  const posts = await allLivePostsForSearch();
  const index = posts.map((p) => ({
    s: p.slug,
    t: p.title,
    d: parseIso(p.publishedAt)?.toISOString().slice(0, 10) ?? "",
    x: summarize(htmlToText(p.body), TEXT_LIMIT),
    e: summarize(htmlToText(p.excerpt || p.body), 140),
  }));
  return Response.json(index);
}
