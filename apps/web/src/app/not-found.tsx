import Link from "next/link";

export const metadata = { title: "Страница не найдена", robots: { index: false } };

export default function NotFound() {
  return (
    <div className="container not-found">
      <svg className="not-found-peak" viewBox="0 0 64 64" aria-hidden="true" focusable="false">
        <path d="M7 50 25 17l8 13 6-9 18 29z" fill="currentColor" opacity="0.4" />
        <path d="m25 17 5.5 9-4 2.5-3.5-3.2-4 3z" fill="currentColor" opacity="0.65" />
      </svg>
      <h1 className="page-title">Сбились с маршрута</h1>
      <p>Такой страницы нет. Попробуйте поиск или вернитесь к основным разделам клуба.</p>
      <form action="/search" role="search" className="search not-found-search">
        <label className="label" htmlFor="lost-search" hidden>
          Поиск по сайту
        </label>
        <input id="lost-search" className="input" type="search" name="s" placeholder="Поиск по сайту" maxLength={100} autoComplete="off" />
        <button type="submit" className="btn btn--secondary">
          Найти
        </button>
      </form>
      <p className="actions">
        <Link href="/" className="btn btn--primary">
          На главную
        </Link>
        <Link href="/archive" className="btn btn--secondary">
          Все записи
        </Link>
        <Link href="/plany" className="btn btn--ghost">
          Планы
        </Link>
      </p>
    </div>
  );
}
