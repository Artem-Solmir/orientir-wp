/**
 * Golden URL check: every imported post/page slug must answer 200 on Next and carry the same <h1>
 * as the WordPress original. Usage: npm run golden [-- --next=http://localhost:3000 --wp=http://orientir.local]
 */
import { PrismaClient } from "@prisma/client";

const arg = (name: string, fallback: string) =>
  process.argv.find((a) => a.startsWith(`--${name}=`))?.split("=").slice(1).join("=") ?? fallback;
const NEXT = arg("next", "http://localhost:3000");
const WP = arg("wp", "http://orientir.local");

const decodeEntities = (s: string) =>
  s.replace(/&#(\d+);/g, (_, n) => String.fromCodePoint(Number(n)))
    .replace(/&#x([0-9a-f]+);/gi, (_, n) => String.fromCodePoint(parseInt(n, 16)))
    .replace(/&amp;/g, "&").replace(/&quot;/g, '"').replace(/&lt;/g, "<").replace(/&gt;/g, ">").replace(/&nbsp;/g, " ");

/** WordPress wptexturize turns quotes/dashes typographic; that is cosmetic, not a content difference. */
const norm = (s: string) =>
  s.replace(/…/g, "...").replace(/[«»“”„"'’‘]/g, "").replace(/[–—-]/g, "-").replace(/\s+/g, " ").trim();

function h1Of(html: string): string {
  const m = /<h1[^>]*>([\s\S]*?)<\/h1>/i.exec(html);
  return m ? decodeEntities(m[1]!.replace(/<[^>]+>/g, "")).replace(/\s+/g, " ").trim() : "";
}

async function get(base: string, slug: string) {
  try {
    const res = await fetch(`${base}/${encodeURIComponent(slug)}${base === WP ? "/" : ""}`, { redirect: "follow" });
    return { status: res.status, h1: h1Of(await res.text()) };
  } catch (e) {
    return { status: 0, h1: String(e) };
  }
}

async function main() {
  const db = new PrismaClient();
  const slugs = [
    ...(await db.post.findMany({ where: { status: "published" }, select: { slug: true } })),
    ...(await db.page.findMany({ select: { slug: true } })),
  ].map((r) => r.slug).filter((s) => !["home", "blog"].includes(s)); // WP system pages: home and /archive replace them
  await db.$disconnect();

  const problems: string[] = [];
  let next = 0;
  const worker = async () => {
    for (let i = next++; i < slugs.length; i = next++) {
      const slug = slugs[i]!;
      const [n, w] = await Promise.all([get(NEXT, slug), get(WP, slug)]);
      if (n.status !== 200) problems.push(`${slug}: next status ${n.status} (wp ${w.status})`);
      else if (w.status === 200 && norm(n.h1) !== norm(w.h1)) problems.push(`${slug}: h1 differs\n  next: ${n.h1}\n  wp:   ${w.h1}`);
    }
  };
  await Promise.all(Array.from({ length: 6 }, worker));

  console.log(`checked ${slugs.length} slugs, ${problems.length} problems`);
  for (const p of problems) console.log(p);
  process.exitCode = problems.length ? 1 : 0;
}

void main();

