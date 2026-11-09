import { describe, expect, it } from "vitest";
import { normalizeQuery, searchEntries, type SearchEntry } from "./search";

const e = (s: string, t: string, x: string): SearchEntry => ({ s, t, d: "2026-01-01", x, e: "" });
const entries = [
  e("a", "Марафон Припятский", "Бег и велосипед"),
  e("b", "Поход по Птичи", "Марафон упомянут в тексте"),
  e("c", "Планы", "Ничего общего"),
];

describe("searchEntries", () => {
  it("finds by title or text and ranks title hits first", () => {
    const r = searchEntries(entries, "марафон");
    expect(r.items.map((i) => i.s)).toEqual(["a", "b"]);
    expect(r.total).toBe(2);
  });

  it("requires every word", () => {
    expect(searchEntries(entries, "марафон велосипед").items.map((i) => i.s)).toEqual(["a"]);
  });

  it("returns nothing for blank queries and respects the limit", () => {
    expect(searchEntries(entries, "   ").total).toBe(0);
    const r = searchEntries(entries, "а", 1);
    expect(r.items).toHaveLength(1);
    expect(r.total).toBeGreaterThan(1);
  });
});

describe("normalizeQuery", () => {
  it("trims, caps at 100 chars and tolerates null", () => {
    expect(normalizeQuery("  hi ")).toBe("hi");
    expect(normalizeQuery("a".repeat(150))).toHaveLength(100);
    expect(normalizeQuery(null)).toBe("");
  });
});
