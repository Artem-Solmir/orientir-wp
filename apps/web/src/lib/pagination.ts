export const PER_PAGE = 12;

export function pageCount(total: number, perPage = PER_PAGE): number {
  return Math.max(1, Math.ceil(total / perPage));
}

/**
 * Old WordPress URLs look like /category/x/page/2/. The optional catch-all gives
 * [] for the first page and ["page", "2"] for the rest; anything else is not a page.
 * Returns the 1-based page number, or null when the segments are not a valid page.
 */
export function parsePageSegments(segments: string[] | undefined): number | null {
  if (!segments || segments.length === 0) return 1;
  const [word, n, ...rest] = segments;
  if (word !== "page" || rest.length > 0 || n === undefined || !/^[1-9]\d{0,5}$/.test(n)) return null;
  return Number(n);
}

/** Page numbers to show: first, last, and a window around the current page, with null as a gap. */
export function pageWindow(current: number, total: number, radius = 2): (number | null)[] {
  const keep = new Set<number>([1, total]);
  for (let p = current - radius; p <= current + radius; p++) {
    if (p >= 1 && p <= total) keep.add(p);
  }
  const sorted = [...keep].sort((a, b) => a - b);
  const out: (number | null)[] = [];
  sorted.forEach((p, i) => {
    if (i > 0 && p - sorted[i - 1]! > 1) out.push(null);
    out.push(p);
  });
  return out;
}

export function pageHref(base: string, page: number): string {
  return page <= 1 ? base : `${base.replace(/\/$/, "")}/page/${page}`;
}
