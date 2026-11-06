import Link from "next/link";
import { getNavCategories } from "@/lib/content";
import { SITE_NAME } from "@/lib/site";

export async function SiteHeader() {
  const categories = await getNavCategories();
  return (
    <header className="site-header">
      <div className="container">
        <Link href="/" className="wordmark">
          Ориентир
        </Link>
        <nav aria-label="Основное меню">
          <ul className="nav">
            {categories.map((c) => (
              <li key={c.slug}>
                <Link href={`/category/${c.slug}`}>{c.name}</Link>
              </li>
            ))}
            <li>
              <Link href="/contact">Контакты</Link>
            </li>
            <li>
              <Link href="/materials">Прислать материалы</Link>
            </li>
          </ul>
        </nav>
        <form action="/search" role="search" className="search">
          <label className="label" htmlFor="site-search" hidden>
            Поиск по сайту
          </label>
          <input id="site-search" className="input" type="search" name="s" placeholder="Поиск" maxLength={100} autoComplete="off" />
          <button type="submit" className="btn btn--secondary">
            Найти
          </button>
        </form>
      </div>
    </header>
  );
}

export function SiteFooter() {
  return (
    <footer className="site-footer">
      <div className="container">
        <div>
          <p className="wordmark">Ориентир</p>
          <p className="label">{SITE_NAME}</p>
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
            {/* eslint-disable-next-line @next/next/no-html-link-for-pages -- route handler, not a page */}
            <a href="/feed">RSS-лента</a>
          </li>
        </ul>
      </div>
    </footer>
  );
}
