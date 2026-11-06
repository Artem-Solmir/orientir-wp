import type { Metadata } from "next";
import { notFound } from "next/navigation";
import { Listing } from "@/components/listing";
import { getCategory } from "@/lib/content";
import { parsePageSegments } from "@/lib/pagination";
import { absoluteUrl } from "@/lib/site";

export const revalidate = 3600;

export function generateStaticParams() {
  return [];
}

type Props = { params: Promise<{ slug: string; page?: string[] }> };

export async function generateMetadata({ params }: Props): Promise<Metadata> {
  const { slug, page } = await params;
  const category = await getCategory(slug);
  const n = parsePageSegments(page);
  if (!category || n === null) return {};
  const suffix = n > 1 ? `, страница ${n}` : "";
  return {
    title: `${category.name}${suffix}`,
    alternates: { canonical: absoluteUrl(n > 1 ? `/category/${slug}/page/${n}` : `/category/${slug}`) },
  };
}

export default async function CategoryPage({ params }: Props) {
  const { slug, page } = await params;
  const n = parsePageSegments(page);
  const category = await getCategory(slug);
  if (!category || n === null) notFound();
  return <Listing title={category.name} filter={{ categorySlugs: [category.slug] }} page={n} base={`/category/${category.slug}`} />;
}
