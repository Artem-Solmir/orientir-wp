import Link from "next/link";
import { getNavCategories } from "@/lib/content";
import { SITE_NAME } from "@/lib/site";
import { SiteMenu } from "./site-menu";
import { ThemeToggle } from "./theme-toggle";

/** The two sections readers come for stay one click away; everything else lives in the menu. */
const QUICK_SLUGS = ["otchyoty-o-poxodax", "plany"];

export async function SiteHeader() {
  const categories = await getNavCategories();
  const quick = QUICK_SLUGS.map((slug) => categories.find((c) => c.slug === slug)).filter((c) => c !== undefined);
  return (
    <header className="site-header">
      <div className="container top-row">
        <Link href="/" className="wordmark" aria-label="Ориентир, на главную">
          <span className="logo" aria-hidden="true" />
        </Link>
        <nav aria-label="Основное меню" className="quick">
          <ul>
            {quick.map((c) => (
              <li key={c.slug}>
                <Link href={`/category/${c.slug}`}>{c.name}</Link>
              </li>
            ))}
            <li>
              <Link href="/contact">Контакты</Link>
            </li>
          </ul>
        </nav>
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
              <li>
                <Link href="/contact">Контакты</Link>
              </li>
            </ul>
            <form action="/search" role="search" className="search">
              <label className="label" htmlFor="site-search" hidden>
                Поиск по сайту
              </label>
              <input id="site-search" className="input" type="search" name="s" placeholder="Поиск по сайту" maxLength={100} autoComplete="off" />
              <button type="submit" className="btn btn--secondary">
                Найти
              </button>
            </form>
            <Link href="/materials" className="btn btn--primary menu-cta">
              Прислать материалы
            </Link>
          </div>
        </SiteMenu>
        <Link href="/materials" className="btn btn--primary header-cta">
          Прислать материалы
        </Link>
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
          <ThemeToggle />
        </div>
      </div>
    </footer>
  );
}
