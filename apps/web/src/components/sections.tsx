import { z } from "zod";
import Link from "next/link";
import { listPosts, getCategory, type CardPost } from "@/lib/content";
import { pageCount, pageHref } from "@/lib/pagination";
import { mediaUrl } from "@/lib/site";
import { formatDate } from "@/lib/text";
import { Pagination } from "./pagination";
import { PostGrid } from "./post-card";
import { Slider, type SlideItem } from "./slider";

// Section configs come from the ETL (Presso Page Composer) and, later, from the agent API.
const sectionConfig = z.object({
  title: z.string().optional(),
  number: z.coerce.number().int().min(1).max(30).default(6),
  categorySlug: z.string().optional(),
  excludeCategorySlugs: z.string().optional(),
  show_pagination: z.coerce.number().default(0),
});

type Section = { id: string; type: string; config: unknown };

function slideOf(p: CardPost): SlideItem {
  return {
    href: `/${p.slug}`,
    title: p.title,
    date: p.publishedAt ? formatDate(p.publishedAt) : "",
    image: p.cover ? { src: mediaUrl(p.cover.path), width: p.cover.width ?? 640, height: p.cover.height ?? 480 } : null,
  };
}

const csv = (value: string | undefined) => (value ? value.split(",").filter(Boolean) : undefined);

async function SliderSection({ config }: { config: z.infer<typeof sectionConfig> }) {
  const { items } = await listPosts({ categorySlugs: csv(config.categorySlug), withCover: true }, 1, config.number);
  return (
    <section className="section">
      <Slider items={items.map(slideOf)} label="Избранные материалы" />
    </section>
  );
}

async function LatestSection({ config, page, pageBase }: { config: z.infer<typeof sectionConfig>; page: number; pageBase: string }) {
  const filter = { categorySlugs: csv(config.categorySlug), excludeCategorySlugs: csv(config.excludeCategorySlugs) };
  const paginated = config.show_pagination === 1;
  const { items, total } = await listPosts(filter, paginated ? page : 1, config.number);
  const category = config.categorySlug ? await getCategory(config.categorySlug) : null;
  // Legacy titles such as "Отчёты о..." were truncated by hand; the category's own name is better.
  const title = category?.name ?? (config.title || "Последние записи");

  return (
    <section className="section">
      <div className="section-head">
        <h2>{title}</h2>
        {category && <Link href={`/category/${category.slug}`}>Все материалы рубрики</Link>}
      </div>
      <PostGrid posts={items} />
      {paginated && (
        <Pagination current={page} total={pageCount(total, config.number)} hrefFor={(p) => pageHref(pageBase, p)} />
      )}
    </section>
  );
}

export async function Sections({ sections, page, pageBase }: { sections: Section[]; page: number; pageBase: string }) {
  return (
    <>
      {sections.map((s) => {
        const parsed = sectionConfig.safeParse(s.config);
        if (!parsed.success) return null;
        if (s.type === "featured_post_slider") return <SliderSection key={s.id} config={parsed.data} />;
        if (s.type === "latest" || s.type === "latest_category") {
          return <LatestSection key={s.id} config={parsed.data} page={page} pageBase={pageBase} />;
        }
        return null; // unsupported legacy section types are skipped, never rendered blindly
      })}
    </>
  );
}
