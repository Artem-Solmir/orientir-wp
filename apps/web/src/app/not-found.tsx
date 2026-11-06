import Link from "next/link";

export const metadata = { title: "Страница не найдена", robots: { index: false } };

export default function NotFound() {
  return (
    <div className="container">
      <h1 className="page-title">Страница не найдена</h1>
      <p>Возможно, адрес изменился. Попробуйте поиск или вернитесь на главную.</p>
      <p className="actions">
        <Link href="/" className="btn btn--primary">
          На главную
        </Link>
        <Link href="/archive" className="btn btn--secondary">
          Все записи
        </Link>
      </p>
    </div>
  );
}
