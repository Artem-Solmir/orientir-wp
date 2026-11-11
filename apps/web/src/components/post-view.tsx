import Image from "next/image";
import Link from "next/link";
import { PostShare } from "@/components/post-share";
import { getNeighbors, type PostView as Post } from "@/lib/content";
import { pickImage } from "@/lib/media";
import { absoluteUrl } from "@/lib/site";
import { countImagesInHtml, formatDate, htmlToText, readingMinutes } from "@/lib/text";

export async function PostArticle({ post }: { post: Post }) {
  const { prev, next } = await getNeighbors(post.publishedAt);
  const cover = post.cover ? pickImage(post.cover, 800) : null;
  const minutes = readingMinutes(htmlToText(post.body));
  const photoCount = countImagesInHtml(post.body) + (post.cover ? 1 : 0);
  const mainCategory = post.categories[0]?.category;

  return (
    <article className="container post">
      <nav className="breadcrumbs" aria-label="Навигация">
        <Link href="/">Главная</Link>
        {mainCategory && (
          <>
            <span aria-hidden="true">/</span>
            <Link href={`/category/${mainCategory.slug}`}>{mainCategory.name}</Link>
          </>
        )}
      </nav>
      <header className="post-head">
        <h1>{post.title}</h1>
        <PostMeta post={post} minutes={minutes} photoCount={photoCount} />
      </header>
      {post.cover && cover && (
        <figure className="post-cover">
          <Image src={cover.src} alt={post.cover.alt ?? ""} width={cover.width} height={cover.height} sizes="(max-width: 72rem) 100vw, 68ch" priority />
        </figure>
      )}
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
      <PostShare title={post.title} url={absoluteUrl(`/${post.slug}`)} />
      <PostNeighbors prev={prev} next={next} />
    </article>
  );
}

function PostMeta({ post, minutes, photoCount }: { post: Post; minutes: number; photoCount: number }) {
  return (
    <div className="meta">
      {post.publishedAt && <time dateTime={post.publishedAt.toISOString()}>{formatDate(post.publishedAt)}</time>}
      <span>{minutes} мин чтения</span>
      {photoCount > 0 && <span>{photoCount} фото</span>}
      {post.authorName && <span>{post.authorName}</span>}
      {post.categories.map(({ category }) => (
        <Link key={category.slug} href={`/category/${category.slug}`}>
          {category.name}
        </Link>
      ))}
    </div>
  );
}

function PostNeighbors({
  prev,
  next,
}: {
  prev: Awaited<ReturnType<typeof getNeighbors>>["prev"];
  next: Awaited<ReturnType<typeof getNeighbors>>["next"];
}) {
  return (
    <nav className="neighbors" aria-label="Соседние записи">
      <div className="neighbor-card">
        <span className="label">← Раньше</span>
        {prev ? <NeighborLink post={prev} /> : <span className="neighbor-empty">—</span>}
      </div>
      <div className="neighbor-card neighbor-card--next">
        <span className="label">Позже →</span>
        {next ? <NeighborLink post={next} /> : <span className="neighbor-empty">—</span>}
      </div>
    </nav>
  );
}

function NeighborLink({ post }: { post: NonNullable<Awaited<ReturnType<typeof getNeighbors>>["prev"]> }) {
  const thumb = post.cover ? pickImage(post.cover, 400) : null;
  return (
    <Link href={`/${post.slug}`} className="neighbor-link">
      {thumb && <Image src={thumb.src} alt="" width={thumb.width} height={thumb.height} sizes="8rem" className="neighbor-thumb" />}
      <span>{post.title}</span>
    </Link>
  );
}
