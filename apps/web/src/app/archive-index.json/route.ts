import { db } from "@/lib/db";
import { htmlToText, summarize } from "@/lib/text";

export const dynamic = "force-static";

/** Lightweight listing for client-side archive filters on the static site. */
export async function GET() {
  const now = new Date();
  const posts = await db.post.findMany({
    where: {
      status: "published",
      publishedAt: { lte: now },
      OR: [{ expiresAt: null }, { expiresAt: { gt: now } }],
    },
    select: {
      slug: true,
      title: true,
      excerpt: true,
      body: true,
      publishedAt: true,
      categories: { select: { category: { select: { slug: true, name: true } } } },
    },
    orderBy: { publishedAt: "desc" },
  });
  const index = posts.map((p) => ({
    s: p.slug,
    t: p.title,
    d: p.publishedAt?.toISOString().slice(0, 10) ?? "",
    y: p.publishedAt?.getFullYear() ?? 0,
    c: p.categories.map((x) => x.category.slug),
    e: summarize(htmlToText(p.excerpt || p.body), 140),
  }));
  return Response.json(index);
}
