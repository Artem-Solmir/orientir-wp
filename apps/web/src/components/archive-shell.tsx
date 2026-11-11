"use client";

import Link from "next/link";
import { useRouter, useSearchParams } from "next/navigation";
import { useEffect, useMemo, useState } from "react";
import { filterArchiveEntries, type ArchiveEntry } from "@/lib/archive";
import type { CardPost } from "@/lib/content";
import { pageHref } from "@/lib/pagination";
import { Pagination } from "./pagination";
import { PostGrid } from "./post-card";

type Cat = { slug: string; name: string };

function ArchiveFilteredList({ items }: { items: ArchiveEntry[] }) {
  if (items.length === 0) return <p className="empty">По этим фильтрам записей нет.</p>;
  return (
    <ul className="index">
      {items.map((r) => (
        <li key={r.s}>
          {r.d && (
            <time className="label" dateTime={r.d}>
              {r.d.split("-").reverse().join(".")}
            </time>
          )}
          <h3>
            <Link href={`/${r.s}`}>{r.t}</Link>
          </h3>
          {r.e && <p>{r.e}</p>}
        </li>
      ))}
    </ul>
  );
}

export function ArchiveShell({
  total,
  page,
  pages,
  base,
  items,
  years,
  categories,
}: {
  total: number;
  page: number;
  pages: number;
  base: string;
  items: CardPost[];
  years: number[];
  categories: Cat[];
}) {
  const params = useSearchParams();
  const router = useRouter();
  const year = params.get("year") ?? "";
  const cat = params.get("cat") ?? "";
  const filtered = year !== "" || cat !== "";
  const filterKey = `${year}|${cat}`;

  const [index, setIndex] = useState<ArchiveEntry[] | null>(null);
  const [readyKey, setReadyKey] = useState("");

  useEffect(() => {
    if (!filtered) return;
    let cancelled = false;
    fetch("/archive-index.json")
      .then((r) => (r.ok ? (r.json() as Promise<ArchiveEntry[]>) : Promise.reject()))
      .then((entries) => {
        if (!cancelled) {
          setIndex(entries);
          setReadyKey(filterKey);
        }
      })
      .catch(() => !cancelled && setIndex([]));
    return () => {
      cancelled = true;
    };
  }, [filtered, filterKey]);

  const filteredItems = useMemo(() => (index ? filterArchiveEntries(index, year, cat) : []), [index, year, cat]);

  const loading = filtered && readyKey !== filterKey;

  const filterHref = (y: string, c: string) => {
    const q = new URLSearchParams();
    if (y) q.set("year", y);
    if (c) q.set("cat", c);
    const s = q.toString();
    return s ? `${base}?${s}` : base;
  };

  const go = (href: string) => (e: React.MouseEvent) => {
    e.preventDefault();
    router.push(href);
  };

  const countLabel = filtered ? `Показано: ${filteredItems.length} из ${total} записей` : `Всего записей: ${total}`;

  return (
    <>
      <p className="archive-count label">{countLabel}</p>
      <nav className="archive-filters" aria-label="Фильтры архива">
        <FilterChips label="Год" chips={[{ key: "", label: "Все годы" }, ...years.map((y) => ({ key: String(y), label: String(y) }))]} active={year} hrefFor={(y) => filterHref(y, cat)} onPick={go} />
        <FilterChips
          label="Рубрика"
          chips={[{ key: "", label: "Все рубрики" }, ...categories.map((c) => ({ key: c.slug, label: c.name }))]}
          active={cat}
          hrefFor={(c) => filterHref(year, c)}
          onPick={go}
        />
      </nav>
      {filtered ? (
        loading ? (
          <p className="empty">Загружаем архив…</p>
        ) : (
          <ArchiveFilteredList items={filteredItems} />
        )
      ) : (
        <>
          <PostGrid posts={items} />
          <Pagination current={page} total={pages} hrefFor={(p) => pageHref("/archive", p)} />
        </>
      )}
    </>
  );
}

function FilterChips({
  label,
  chips,
  active,
  hrefFor,
  onPick,
}: {
  label: string;
  chips: { key: string; label: string }[];
  active: string;
  hrefFor: (key: string) => string;
  onPick: (href: string) => (e: React.MouseEvent) => void;
}) {
  return (
    <div className="chip-row" role="group" aria-label={label}>
      {chips.map((chip) => {
        const href = hrefFor(chip.key);
        return (
          <Link key={chip.key || "all"} href={href} className={`chip${active === chip.key ? " chip--on" : ""}`} scroll={false} onClick={onPick(href)}>
            {chip.label}
          </Link>
        );
      })}
    </div>
  );
}
