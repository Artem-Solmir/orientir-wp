"use client";

import Link from "next/link";
import { useSearchParams } from "next/navigation";
import { useEffect, useState } from "react";
import { normalizeQuery, searchEntries, type SearchEntry } from "@/lib/search";

type Index = { status: "loading" } | { status: "error" } | { status: "ready"; entries: SearchEntry[] };

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
  if (total === 0) return <p className="empty">Ничего не найдено.</p>;
  return (
    <>
      <p className="label">
        Найдено записей: {total}
        {total > items.length ? `, показаны первые ${items.length}` : ""}
      </p>
      <ul className="index">
        {items.map((r) => (
          <li key={r.s}>
            {r.d && <time className="label" dateTime={r.d}>{r.d.split("-").reverse().join(".")}</time>}
            <h3>
              <Link href={`/${r.s}`}>{r.t}</Link>
            </h3>
            {r.e && <p>{r.e}</p>}
          </li>
        ))}
      </ul>
    </>
  );
}
