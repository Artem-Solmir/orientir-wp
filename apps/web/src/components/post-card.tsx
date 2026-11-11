import Image from "next/image";
import Link from "next/link";
import { CardPlaceholder } from "@/components/card-placeholder";
import { RevealOnScroll } from "@/components/reveal-on-scroll";
import type { CardPost } from "@/lib/content";
import { pickImage } from "@/lib/media";
import { formatDate } from "@/lib/text";

/** Cards are ~290 CSS px wide; 400 px of source covers 1.25x screens without shipping the original. */
const CARD_IMAGE_WIDTH = 400;

export function PostCard({ post, priority = false }: { post: CardPost; priority?: boolean }) {
  const href = `/${post.slug}`;
  const image = post.cover ? pickImage(post.cover, CARD_IMAGE_WIDTH) : null;
  return (
    <RevealOnScroll>
      <article className="card">
        <Link href={href} className="card-media" tabIndex={-1} aria-hidden="true">
          {image ? (
            <Image
              src={image.src}
              alt=""
              width={image.width}
              height={image.height}
              sizes="(max-width: 40rem) 86vw, 18rem"
              priority={priority}
              className="card-photo"
            />
          ) : (
            <CardPlaceholder label={post.primaryCategory?.name} />
          )}
        </Link>
      <div>
        {post.publishedAt && (
          <time className="label" dateTime={post.publishedAt.toISOString()}>
            {formatDate(post.publishedAt)}
          </time>
        )}
        <h3>
          <Link href={href}>{post.title}</Link>
        </h3>
        {post.summary && <p>{post.summary}</p>}
      </div>
      </article>
    </RevealOnScroll>
  );
}

export function PostGrid({ posts }: { posts: CardPost[] }) {
  if (posts.length === 0) return <p className="empty">Здесь пока ничего нет.</p>;
  return (
    <ul className="cards">
      {posts.map((p) => (
        <li key={p.slug}>
          <PostCard post={p} />
        </li>
      ))}
    </ul>
  );
}
