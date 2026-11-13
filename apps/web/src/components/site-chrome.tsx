import Link from "next/link";
import { getNavCategories } from "@/lib/content";
import { SITE_NAME } from "@/lib/site";
import { SiteMenu } from "./site-menu";
import { ThemeToggle } from "./theme-toggle";

export async function SiteHeader() {
  const categories = await getNavCategories();
  return (
    <header className="site-header">
      <div className="container top-row">
        <div className="header-start">
          <Link href="/" className="wordmark" aria-label="Ориентир, на главную">
            <span className="logo" aria-hidden="true" />
          </Link>
          <SiteMenu>
            <div className="container menu-grid">
              <ul className="menu-list" aria-label="Рубрики">
                {categories.map((c) => (
                  <li key={c.slug}>
                    <Link href={`/category/${c.slug}`}>{c.name}</Link>
                  </li>
                ))}
                <li>
                  <Link href="/archive">Все записи</Link>
                </li>
              </ul>
              <Link href="/materials" className="btn btn--primary menu-cta">
                Прислать материалы
              </Link>
            </div>
          </SiteMenu>
          <Link href="/contact" className="header-contacts">
            Контакты
          </Link>
        </div>
        <form action="/search" role="search" className="search header-search">
          <label className="label" htmlFor="site-search" hidden>
            Поиск по сайту
          </label>
          <input id="site-search" className="input" type="search" name="s" placeholder="Поиск по сайту" maxLength={100} autoComplete="off" />
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
        <p className="footer-word" aria-hidden="true">
          Ориентир
        </p>
        <div className="footer-row">
          <p className="footer-name">{SITE_NAME}</p>
          <nav className="footer-links" aria-label="Ссылки в подвале">
            <ul className="footer-link-col">
              <li>
                <Link href="/archive">Все записи</Link>
              </li>
              <li>
                <Link href="/plany">Планы</Link>
              </li>
              <li>
                <Link href="/contact">Контакты</Link>
              </li>
            </ul>
            <ul className="footer-link-col">
              <li>
                <Link href="/materials">Прислать материалы</Link>
              </li>
              <li>
                <a href="/feed.xml">RSS-лента</a>
              </li>
            </ul>
          </nav>
          <div className="footer-tools">
            <ThemeToggle />
          </div>
        </div>
      </div>
    </footer>
  );
}
