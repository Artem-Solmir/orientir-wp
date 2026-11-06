import type { Metadata } from "next";
import { notFound } from "next/navigation";
import { Listing } from "@/components/listing";
import { getTag, listPosts } from "@/lib/content";
import { parsePageSegments } from "@/lib/pagination";
import { absoluteUrl } from "@/lib/site";

export const revalidate = 3600;

export function generateStaticParams() {
  return [];
}

type Props = { params: Promise<{ slug: string; page?: string[] }> };

const decode = (value: string) => {
  try {
    return decodeURIComponent(value);
  } catch {
    return null;
  }
};

export async function generateMetadata({ params }: Props): Promise<Metadata> {
  const { slug: raw, page } = await params;
  const slug = decode(raw);
  const n = parsePageSegments(page);
  const tag = slug ? await getTag(slug) : null;
  if (!tag || n === null) return {};
  const { total } = await listPosts({ tagSlug: tag.slug }, 1, 1);
  return {
    title: `Метка: ${tag.name}`,
    alternates: { canonical: absoluteUrl(n > 1 ? `/tag/${tag.slug}/page/${n}` : `/tag/${tag.slug}`) },
    // A tag with a single post is thin content; keep it out of search results.
    robots: total < 2 ? { index: false, follow: true } : undefined,
  };
}

export default async function TagPage({ params }: Props) {
  const { slug: raw, page } = await params;
  const slug = decode(raw);
  const n = parsePageSegments(page);
  const tag = slug ? await getTag(slug) : null;
  if (!tag || n === null) notFound();
  return <Listing title={`Метка: ${tag.name}`} filter={{ tagSlug: tag.slug }} page={n} base={`/tag/${tag.slug}`} />;
}
