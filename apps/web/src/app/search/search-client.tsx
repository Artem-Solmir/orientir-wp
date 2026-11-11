"use client";

import Link from "next/link";
import { useSearchParams } from "next/navigation";
import { useEffect, useState } from "react";
import { normalizeQuery, searchEntries, type SearchEntry } from "@/lib/search";
import { highlightPlain } from "@/lib/text";

type Index = { status: "loading" } | { status: "error" } | { status: "ready"; entries: SearchEntry[] };

const SUGGEST = [
  { href: "/category/otchyoty-o-poxodax", label: "Отчёты о походах" },
  { href: "/plany", label: "Планы" },
  { href: "/category/novosti", label: "Новости" },
  { href: "/archive", label: "Все записи" },
];

export function SearchClient() {
  const query = normalizeQuery(useSearchParams().get("s"));
  const [index, setIndex] = useState<Index>({ status: "loading" });

  useEffect(() => {
    if (query.length < 2) return;
    let cancelled = false;
    fetch("/search-index.json")
      .then((r) => (r.ok ? (r.json() as Promise<SearchEntry[]>) : Promise.reject(new Error("bad status"))))
      .then((entries) => !cancelled && setIndex({ status: "ready", entries }))
      .catch(() => !cancelled && setIndex({ status: "error" }));
    return () => {
      cancelled = true;
    };
  }, [query]);

  if (query.length < 2) return <p className="empty">Введите не меньше двух символов.</p>;
  if (index.status === "error") return <p className="error">Не удалось загрузить поиск. Попробуйте позже.</p>;
  if (index.status === "loading") return <p className="empty">Ищем…</p>;

  const { total, items } = searchEntries(index.entries, query);
  if (total === 0) {
    return (
      <div className="search-empty">
        <p className="empty">Ничего не найдено по запросу «{query}».</p>
        <p className="label">Попробуйте другие слова или перейдите в раздел:</p>
        <ul className="search-suggest">
          {SUGGEST.map((s) => (
            <li key={s.href}>
              <Link href={s.href} className="chip">
                {s.label}
              </Link>
            </li>
          ))}
        </ul>
      </div>
    );
  }
  return (
    <>
      <p className="label">
        Найдено записей: {total}
        {total > items.length ? `, показаны первые ${items.length}` : ""}
      </p>
      <ul className="index">
        {items.map((r) => (
          <li key={r.s}>
            {r.d && (
              <time className="label" dateTime={r.d}>
                {r.d.split("-").reverse().join(".")}
              </time>
            )}
            <h3>
              <Link href={`/${r.s}`} dangerouslySetInnerHTML={{ __html: highlightPlain(r.t, query) }} />
            </h3>
            {r.e && <p dangerouslySetInnerHTML={{ __html: highlightPlain(r.e, query) }} />}
          </li>
        ))}
      </ul>
    </>
  );
}
