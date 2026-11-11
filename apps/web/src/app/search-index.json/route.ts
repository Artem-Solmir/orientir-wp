import { db } from "@/lib/db";
import { htmlToText, summarize } from "@/lib/text";

export const dynamic = "force-static";

const TEXT_LIMIT = 2000;

/** Compact index for client-side search on the static site: title, date and the start of the text. */
export async function GET() {
  const now = new Date();
  const posts = await db.post.findMany({
    where: {
      status: "published",
      publishedAt: { lte: now },
      OR: [{ expiresAt: null }, { expiresAt: { gt: now } }],
    },
    select: { slug: true, title: true, excerpt: true, body: true, publishedAt: true },
    orderBy: { publishedAt: "desc" },
  });
  const index = posts.map((p) => ({
    s: p.slug,
    t: p.title,
    d: p.publishedAt?.toISOString().slice(0, 10) ?? "",
    x: summarize(htmlToText(p.body), TEXT_LIMIT),
    e: summarize(htmlToText(p.excerpt || p.body), 140),
  }));
  return Response.json(index);
}
