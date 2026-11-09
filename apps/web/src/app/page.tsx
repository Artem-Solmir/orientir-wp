import Image from "next/image";
import Link from "next/link";
import { Sections } from "@/components/sections";
import { getPage, listPosts } from "@/lib/content";
import { pickImage } from "@/lib/media";

const HOME_SLUG = "home";
const HERO_IMAGE_WIDTH = 1600;
/** Announcements carry text posters; trip reports carry real photographs, so the hero draws from them. */
const HERO_CATEGORIES = ["otchyoty-o-poxodax", "otchyoty-o-pvd-pokatushkax"];

/** Facts taken from the club's own headlines: "НАМ 20 ЛЕТ!", the tenth spring «Припятский», the fifth «Стрежень». */
const FACTS = [
  { figure: "20", label: "лет клубу" },
  { figure: "10", label: "весенних «Припятских»" },
  { figure: "5", label: "водных марафонов «Стрежень»" },
];

export default async function Home() {
  const [home, hero, all] = await Promise.all([
    getPage(HOME_SLUG),
    listPosts({ categorySlugs: HERO_CATEGORIES, withCover: true }, 1, 1),
    listPosts({}, 1, 1),
  ]);
  const lead = hero.items[0];
  const image = lead?.cover ? pickImage(lead.cover, HERO_IMAGE_WIDTH) : null;
  return (
    <>
      <section className="hero">
        {image && <Image className="hero-photo" src={image.src} alt="" width={image.width} height={image.height} sizes="100vw" priority />}
        <div className="container hero-copy">
          <h1>Клуб туризма «Ориентир»</h1>
          <p>Отчёты, планы и новости: походы, велопоходы и приключенческие гонки.</p>
          <div className="actions">
            <Link href="/archive" className="btn btn--primary">
              Свежие записи
            </Link>
            <Link href="/plany" className="btn btn--ghost">
              Планы клуба
            </Link>
          </div>
          {lead && (
            <p className="hero-credit">
              На фото: <Link href={`/${lead.slug}`}>{lead.title}</Link>
            </p>
          )}
        </div>
      </section>
      <div className="container">
        <dl className="facts">
          {FACTS.map((f) => (
            <div key={f.label}>
              <dt>{f.label}</dt>
              <dd>{f.figure}</dd>
            </div>
          ))}
          <div>
            <dt>записей в архиве</dt>
            <dd>{all.total}</dd>
          </div>
        </dl>
        {home && <Sections sections={home.sections} page={1} pageBase="/" />}
      </div>
    </>
  );
}
