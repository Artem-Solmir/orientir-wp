import Image from "next/image";
import Link from "next/link";
import type { CardPost } from "@/lib/content";
import { mediaUrl } from "@/lib/site";
import { formatDate } from "@/lib/text";

export function PostCard({ post, priority = false }: { post: CardPost; priority?: boolean }) {
  const href = `/${post.slug}`;
  return (
    <article className="card">
      <Link href={href} className="card-media" tabIndex={-1} aria-hidden="true">
        {post.cover && (
          <Image
            src={mediaUrl(post.cover.path)}
            alt=""
            width={post.cover.width ?? 640}
            height={post.cover.height ?? 480}
            sizes="(max-width: 40rem) 86vw, 18rem"
            priority={priority}
          />
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
