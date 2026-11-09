export type SearchEntry = { s: string; t: string; d: string; x: string; e: string };

const MAX_QUERY = 100;

export function normalizeQuery(raw: string | null | undefined): string {
  return (raw ?? "").trim().slice(0, MAX_QUERY);
}

/** Every word must occur in the title or text (case-insensitive); title hits rank first. */
export function searchEntries(entries: SearchEntry[], query: string, limit = 24) {
  const words = query.toLowerCase().split(/\s+/).filter(Boolean);
  if (words.length === 0) return { total: 0, items: [] as SearchEntry[] };
  const hits = entries
    .map((entry) => {
      const title = entry.t.toLowerCase();
      const text = `${title} ${entry.x.toLowerCase()}`;
      if (!words.every((w) => text.includes(w))) return null;
      return { entry, score: words.filter((w) => title.includes(w)).length };
    })
    .filter((h): h is { entry: SearchEntry; score: number } => h !== null)
    .sort((a, b) => b.score - a.score);
  return { total: hits.length, items: hits.slice(0, limit).map((h) => h.entry) };
}
