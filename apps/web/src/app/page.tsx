import Link from "next/link";
import { Sections } from "@/components/sections";
import { getPage } from "@/lib/content";


const HOME_SLUG = "home";

export default async function Home() {
  const home = await getPage(HOME_SLUG);
  return (
    <div className="container">
      <section className="hero">
        <h1>Клуб туризма «Ориентир»</h1>
        <p>Отчёты, планы и новости: походы, велопоходы и приключенческие гонки.</p>
        <div className="actions">
          <Link href="/archive" className="btn btn--primary">
            Свежие записи
          </Link>
          <Link href="/plany" className="btn btn--secondary">
            Планы клуба
          </Link>
        </div>
      </section>
      {home && <Sections sections={home.sections} page={1} pageBase="/" />}
    </div>
  );
}
