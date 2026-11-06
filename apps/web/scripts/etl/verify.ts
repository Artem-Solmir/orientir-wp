/**
 * Golden checks for the ETL result. Exits non-zero on any failed invariant.
 * Usage: npm run etl:verify   (compares PostgreSQL with the legacy MySQL source)
 */
import { existsSync } from "node:fs";
import { resolve } from "node:path";
import { PrismaClient } from "@prisma/client";
import { IMPORT_PAGE_IDS, openSource } from "./source";

const failures: string[] = [];
const check = (name: string, ok: boolean, detail = "") => {
  console.log(`${ok ? "PASS" : "FAIL"}  ${name}${detail ? `  (${detail})` : ""}`);
  if (!ok) failures.push(name);
};

async function count(src: Awaited<ReturnType<typeof openSource>>, sql: string): Promise<number> {
  const [row] = await src.query<{ c: number }>(sql);
  return Number(row?.c);
}

async function verifyCounts(db: PrismaClient, src: Awaited<ReturnType<typeof openSource>>) {
  const expected = {
    posts: await count(src, "SELECT COUNT(*) c FROM wp_posts WHERE post_type='post' AND post_status='publish'"),
    categories: await count(src, "SELECT COUNT(*) c FROM wp_term_taxonomy WHERE taxonomy='category'"),
    tags: await count(src, "SELECT COUNT(*) c FROM wp_term_taxonomy WHERE taxonomy='post_tag'"),
    galleries: await count(src, "SELECT COUNT(*) c FROM wp_posts WHERE post_type='foogallery' AND post_status='publish'"),
    media: await count(src, "SELECT COUNT(*) c FROM wp_postmeta WHERE meta_key='_wp_attached_file'"),
  };
  check("posts count matches source", (await db.post.count()) === expected.posts, `${expected.posts}`);
  check("pages count matches whitelist", (await db.page.count()) === IMPORT_PAGE_IDS.length);
  check("categories match", (await db.category.count()) === expected.categories);
  check("tags match", (await db.tag.count()) === expected.tags);
  check("galleries match", (await db.gallery.count()) === expected.galleries);
  check("media match", (await db.mediaAsset.count()) === expected.media);
}

async function verifyHome(db: PrismaClient) {
  const home = await db.page.findUnique({ where: { legacyWpId: 88 }, include: { sections: { orderBy: { order: "asc" } } } });
  check("home page is a composer page", home?.template === "composer");
  check(
    "home sections: slider, latest, latest_category",
    JSON.stringify(home?.sections.map((s) => s.type)) === JSON.stringify(["featured_post_slider", "latest", "latest_category"]),
  );
  const cat = home?.sections[2]?.config as { categorySlug?: string } | undefined;
  check("home latest_category resolves legacy id 19 to a slug", Boolean(cat?.categorySlug), cat?.categorySlug);
}

async function verifyGoldenPost(db: PrismaClient) {
  const golden = await db.post.findUnique({ where: { slug: "poxod-po-reke-ptich-11-14-maya-2023-goda" }, include: { categories: true } });
  check("golden post exists with body", Boolean(golden?.title) && (golden?.body.length ?? 0) > 100, golden?.title);
  check("golden post has an author name", Boolean(golden?.authorName));
}

async function verifyInvariants(db: PrismaClient) {
  // Visible link text such as "http://orientir.by" is fine; only href/src attributes matter.
  const siteAttr = /\b(?:href|src)=["']https?:\/\/(?:www\.)?orientir\.(?:by|local)/i;
  const bodies = await db.post.findMany({ select: { slug: true, body: true } });
  const dirty = bodies.filter((p) => siteAttr.test(p.body) || /\[caption|<script|onerror=/i.test(p.body));
  check("no raw shortcodes, absolute site urls in attributes or scripts in bodies", dirty.length === 0, dirty.map((d) => d.slug).join(","));
  check("posts have covers", (await db.post.count({ where: { coverId: { not: null } } })) > 150);
  check("every post is published", (await db.post.count({ where: { status: { not: "published" } } })) === 0);

  const slugs = new Set((await db.post.findMany({ select: { slug: true } })).map((p) => p.slug));
  const clash = (await db.page.findMany({ select: { slug: true } })).filter((p) => slugs.has(p.slug));
  check("no post/page slug collisions (both live at the site root)", clash.length === 0, clash.map((c) => c.slug).join(","));
}

async function verifyMediaFiles(db: PrismaClient) {
  const root = resolve(process.cwd(), "../../wp-content/uploads");
  const assets = await db.mediaAsset.findMany({ select: { path: true } });
  const missing = assets.filter((a) => !existsSync(resolve(root, a.path))).length;
  console.log(`INFO  media files missing on disk: ${missing} of ${assets.length}`);
  check("at least 95% of media files exist locally", missing / assets.length < 0.05);
}

async function main() {
  const url = process.env.WP_MYSQL_URL;
  if (!url) throw new Error("WP_MYSQL_URL is not set");
  const src = await openSource(url);
  const db = new PrismaClient();
  try {
    await verifyCounts(db, src);
    await verifyHome(db);
    await verifyGoldenPost(db);
    await verifyInvariants(db);
    await verifyMediaFiles(db);
  } finally {
    await db.$disconnect();
    await src.close();
  }
  if (failures.length) {
    console.error(`\n${failures.length} check(s) failed`);
    process.exit(1);
  }
  console.log("\nall checks passed");
}

main().catch((error: unknown) => {
  console.error(error instanceof Error ? error.message : error);
  process.exit(1);
});
