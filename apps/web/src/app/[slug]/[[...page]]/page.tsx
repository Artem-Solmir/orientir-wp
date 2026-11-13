import type { Metadata } from "next";
import { notFound } from "next/navigation";
import { PostArticle } from "@/components/post-view";
import { Sections } from "@/components/sections";
import { getPage, getPost } from "@/lib/content";
import { parsePageSegments } from "@/lib/pagination";
import { slugParams } from "@/lib/static-params";
import { SITE_DESCRIPTION, absoluteUrl, mediaUrl } from "@/lib/site";
import { htmlToText, summarize } from "@/lib/text";

export const dynamicParams = false;

export const generateStaticParams = slugParams;

type Props = { params: Promise<{ slug: string; page?: string[] }> };

const decode = (slug: string) => {
  try {
    return decodeURIComponent(slug);
  } catch {
    return null;
  }
};

function postMetadata(post: NonNullable<Awaited<ReturnType<typeof getPost>>>): Metadata {
  const description = summarize(htmlToText(post.excerpt || post.body), 160) || SITE_DESCRIPTION;
  const image = post.cover ? absoluteUrl(mediaUrl(post.cover.path)) : undefined;
  return {
    title: post.title,
    description,
    alternates: { canonical: absoluteUrl(`/${post.slug}`) },
    openGraph: {
      type: "article",
      title: post.title,
      description,
      url: absoluteUrl(`/${post.slug}`),
      publishedTime: post.publishedAt?.toISOString(),
      modifiedTime: (post.updatedAt ?? post.publishedAt)?.toISOString(),
      images: image ? [{ url: image }] : undefined,
    },
  };
}

export async function generateMetadata({ params }: Props): Promise<Metadata> {
  const { slug: raw, page } = await params;
  const slug = decode(raw);
  if (!slug || parsePageSegments(page) === null) return {};

  const post = await getPost(slug);
  if (post) return postMetadata(post);
  const found = await getPage(slug);
  return found ? { title: found.title, alternates: { canonical: absoluteUrl(`/${found.slug}`) } } : {};
}

export default async function SlugPage({ params }: Props) {
  const { slug: raw, page: segments } = await params;
  const slug = decode(raw);
  const pageNumber = parsePageSegments(segments);
  if (!slug || pageNumber === null) notFound();

  const post = await getPost(slug);
  if (post) {
    if (pageNumber !== 1) notFound(); // posts have no /page/N
    return <PostArticle post={post} />;
  }

  const found = await getPage(slug);
  if (!found) notFound();

  return (
    <div className="container">
      <h1 className="page-title">{found.title}</h1>
      {found.body && <div className="prose" dangerouslySetInnerHTML={{ __html: found.body }} />}
      <Sections sections={found.sections} page={pageNumber} pageBase={`/${found.slug}`} />
    </div>
  );
}
