import type { Metadata } from "next";
import { Suspense } from "react";
import { SearchClient } from "./search-client";

export const metadata: Metadata = { title: "Поиск", robots: { index: false, follow: true } };

export default function SearchPage() {
  return (
    <div className="container">
      <h1 className="page-title">Поиск</h1>
      <Suspense fallback={<p className="empty">Ищем…</p>}>
        <SearchClient />
      </Suspense>
    </div>
  );
}
