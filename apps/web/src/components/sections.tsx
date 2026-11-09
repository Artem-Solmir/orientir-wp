import Link from "next/link";
import { listPosts, getCategory, type CardPost } from "@/lib/content";
import { pageCount, pageHref } from "@/lib/pagination";
import { csv, isLatestSection, sectionConfig, type SectionConfig } from "@/lib/section-config";
import { pickImage } from "@/lib/media";
import { formatDate } from "@/lib/text";
import { Pagination } from "./pagination";
import { PostGrid } from "./post-card";
import { Slider, type SlideItem } from "./slider";

type Section = { id: string; type: string; config: unknown };

const SLIDE_IMAGE_WIDTH = 640;

function slideOf(p: CardPost): SlideItem {
  return {
    href: `/${p.slug}`,
    title: p.title,
    date: p.publishedAt ? formatDate(p.publishedAt) : "",
    image: p.cover ? pickImage(p.cover, SLIDE_IMAGE_WIDTH) : null,
  };
}

async function SliderSection({ config }: { config: SectionConfig }) {
  const { items } = await listPosts({ categorySlugs: csv(config.categorySlug), withCover: true }, 1, config.number);
  return (
    <section className="section">
      <Slider items={items.map(slideOf)} label="Избранные материалы" />
    </section>
  );
}

async function LatestSection({ config, page, pageBase }: { config: SectionConfig; page: number; pageBase: string }) {
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
        if (isLatestSection(s.type)) {
          return <LatestSection key={s.id} config={parsed.data} page={page} pageBase={pageBase} />;
        }
        return null; // unsupported legacy section types are skipped, never rendered blindly
      })}
    </>
  );
}
