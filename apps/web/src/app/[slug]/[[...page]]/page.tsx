import type { Metadata } from "next";
import Image from "next/image";
import Link from "next/link";
import { notFound } from "next/navigation";
import { Sections } from "@/components/sections";
import { getNeighbors, getPage, getPost } from "@/lib/content";
import { parsePageSegments } from "@/lib/pagination";
import { SITE_DESCRIPTION, absoluteUrl, mediaUrl } from "@/lib/site";
import { formatDate, htmlToText, summarize } from "@/lib/text";

export const revalidate = 3600;
export const dynamicParams = true;

// Nothing is prerendered at build time; pages are generated on first request and revalidated
// hourly or on demand (revalidatePath/Tag from the agent API in Phase 5).
export function generateStaticParams() {
  return [];
}

type Props = { params: Promise<{ slug: string; page?: string[] }> };

const decode = (slug: string) => {
  try {
    return decodeURIComponent(slug);
  } catch {
    return null;
  }
};

export async function generateMetadata({ params }: Props): Promise<Metadata> {
  const { slug: raw, page } = await params;
  const slug = decode(raw);
  if (!slug || parsePageSegments(page) === null) return {};

  const post = await getPost(slug);
  if (post) {
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
        modifiedTime: post.updatedAt.toISOString(),
        images: image ? [{ url: image }] : undefined,
      },
    };
  }
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
    return <PostView post={post} />;
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

async function PostView({ post }: { post: NonNullable<Awaited<ReturnType<typeof getPost>>> }) {
  const { prev, next } = await getNeighbors(post.publishedAt);
  return (
    <article className="container post">
      <header className="post-head">
        <h1>{post.title}</h1>
        <div className="meta">
          {post.publishedAt && <time dateTime={post.publishedAt.toISOString()}>{formatDate(post.publishedAt)}</time>}
          {post.authorName && <span>{post.authorName}</span>}
          {post.categories.map(({ category }) => (
            <Link key={category.slug} href={`/category/${category.slug}`}>
              {category.name}
            </Link>
          ))}
        </div>
      </header>
      {post.cover && (
        <figure className="post-cover">
          <Image
            src={mediaUrl(post.cover.path)}
            alt={post.cover.alt ?? ""}
            width={post.cover.width ?? 1200}
            height={post.cover.height ?? 800}
            sizes="(max-width: 72rem) 100vw, 68ch"
            priority
          />
        </figure>
      )}
      {/* Body HTML is sanitized by the ETL and by the agent API on write (allow-list, no scripts). */}
      <div className="prose" dangerouslySetInnerHTML={{ __html: post.body }} />
      {post.tags.length > 0 && (
        <ul className="tags" aria-label="Метки">
          {post.tags.map(({ tag }) => (
            <li key={tag.slug}>
              <Link href={`/tag/${tag.slug}`}>{tag.name}</Link>
            </li>
          ))}
        </ul>
      )}
      <nav className="neighbors" aria-label="Соседние записи">
        <div>{prev && <Link href={`/${prev.slug}`}>{prev.title}</Link>}</div>
        <div>{next && <Link href={`/${next.slug}`}>{next.title}</Link>}</div>
      </nav>
    </article>
  );
}
