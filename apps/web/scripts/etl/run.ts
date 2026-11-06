/**
 * Idempotent ETL: WordPress MySQL (read-only) -> PostgreSQL.
 * Usage: npm run etl      (requires WP_MYSQL_URL and DATABASE_URL in .env)
 * Safe to re-run on a fresh dump (delta mode): everything is upserted by legacyWpId.
 * Users are intentionally NOT migrated.
 */
import { PrismaClient } from "@prisma/client";
import { loadGalleries, loadMedia, loadPosts, loadTaxonomies, type Stats } from "./load";
import { openSource } from "./source";

async function main() {
  const url = process.env.WP_MYSQL_URL;
  if (!url) throw new Error("WP_MYSQL_URL is not set");

  const src = await openSource(url);
  const db = new PrismaClient();
  const stats: Stats = {};
  try {
    const { categoryIds, tagIds, slugById } = await loadTaxonomies(src, db, stats);
    const mediaIds = await loadMedia(src, db, stats);
    await loadPosts(src, db, { categoryIds, tagIds, slugById, mediaIds }, stats);
    await loadGalleries(src, db, mediaIds, stats);
    console.log("etl done:", stats);
  } finally {
    await db.$disconnect();
    await src.close();
  }
}

main().catch((error: unknown) => {
  console.error(error instanceof Error ? error.message : error);
  process.exit(1);
});
