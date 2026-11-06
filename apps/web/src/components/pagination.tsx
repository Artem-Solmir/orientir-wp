import Link from "next/link";
import { pageWindow } from "@/lib/pagination";

type Props = { current: number; total: number; hrefFor: (page: number) => string };

export function Pagination({ current, total, hrefFor }: Props) {
  if (total <= 1) return null;
  return (
    <nav aria-label="Страницы">
      <ul className="pagination">
        {pageWindow(current, total).map((p, i) =>
          p === null ? (
            <li key={`gap-${i}`}>
              <span aria-hidden="true">…</span>
            </li>
          ) : (
            <li key={p}>
              <Link href={hrefFor(p)} aria-current={p === current ? "page" : undefined} aria-label={`Страница ${p}`}>
                {p}
              </Link>
            </li>
          ),
        )}
      </ul>
    </nav>
  );
}
