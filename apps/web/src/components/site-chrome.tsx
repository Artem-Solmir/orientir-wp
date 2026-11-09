import Link from "next/link";
import { getNavCategories } from "@/lib/content";
import { SITE_NAME } from "@/lib/site";

export async function SiteHeader() {
  const categories = await getNavCategories();
  return (
    <header className="site-header">
      <div className="container top-row">
        <Link href="/" className="wordmark">
          Ориентир
          <span className="wordmark-sub">Мозырский клуб туризма</span>
        </Link>
        <form action="/search" role="search" className="search">
          <label className="label" htmlFor="site-search" hidden>
            Поиск по сайту
          </label>
          <input id="site-search" className="input" type="search" name="s" placeholder="Поиск" maxLength={100} autoComplete="off" />
          <button type="submit" className="btn btn--secondary">
            Найти
          </button>
        </form>
        <Link href="/materials" className="btn btn--primary">
          Прислать материалы
        </Link>
      </div>
      <nav aria-label="Основное меню" className="nav-bar">
        <ul className="nav container">
          {categories.map((c) => (
            <li key={c.slug}>
              <Link href={`/category/${c.slug}`}>{c.name}</Link>
            </li>
          ))}
          <li>
            <Link href="/contact">Контакты</Link>
          </li>
        </ul>
      </nav>
    </header>
  );
}

export function SiteFooter() {
  return (
    <footer className="site-footer">
      <div className="container">
        <div>
          <p className="wordmark">Ориентир</p>
          <p className="footer-name">{SITE_NAME}</p>
        </div>
        <ul>
          <li>
            <Link href="/archive">Все записи</Link>
          </li>
          <li>
            <Link href="/plany">Планы</Link>
          </li>
          <li>
            <Link href="/contact">Контакты</Link>
          </li>
          <li>
            <Link href="/materials">Прислать материалы</Link>
          </li>
          <li>
            <a href="/feed.xml">RSS-лента</a>
          </li>
        </ul>
      </div>
    </footer>
  );
}
