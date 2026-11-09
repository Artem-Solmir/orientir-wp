import type { MetadataRoute } from "next";
import { allSlugsForSitemap } from "@/lib/content";
import { absoluteUrl } from "@/lib/site";

export const dynamic = "force-static";

export default async function sitemap(): Promise<MetadataRoute.Sitemap> {
  const { posts, pages, categories, tags } = await allSlugsForSitemap();
  return [
    { url: absoluteUrl("/"), changeFrequency: "daily", priority: 1 },
    { url: absoluteUrl("/archive"), changeFrequency: "daily", priority: 0.6 },
    ...categories.map((c) => ({ url: absoluteUrl(`/category/${c.slug}`), changeFrequency: "weekly" as const, priority: 0.5 })),
    // Tags with one post are noindex (thin), so they stay out of the sitemap too.
    ...tags.filter((t) => t._count.posts >= 2).map((t) => ({ url: absoluteUrl(`/tag/${t.slug}`), changeFrequency: "monthly" as const, priority: 0.3 })),
    ...pages.filter((p) => p.slug !== "home").map((p) => ({ url: absoluteUrl(`/${p.slug}`), lastModified: p.updatedAt, priority: 0.5 })),
    ...posts.map((p) => ({ url: absoluteUrl(`/${p.slug}`), lastModified: p.updatedAt, priority: 0.8 })),
  ];
}
