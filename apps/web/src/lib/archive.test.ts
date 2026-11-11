import { describe, expect, it } from "vitest";
import { filterArchiveEntries, isPostVisible, type ArchiveEntry } from "./archive";

const sample: ArchiveEntry[] = [
  { s: "a", t: "A", d: "2024-01-01", y: 2024, c: ["plany"], e: "" },
  { s: "b", t: "B", d: "2023-06-01", y: 2023, c: ["novosti"], e: "" },
  { s: "c", t: "C", d: "2024-02-01", y: 2024, c: ["novosti", "plany"], e: "" },
];

describe("filterArchiveEntries", () => {
  it("filters by year and category together", () => {
    expect(filterArchiveEntries(sample, "2024", "plany").map((e) => e.s)).toEqual(["a", "c"]);
    expect(filterArchiveEntries(sample, "", "novosti").map((e) => e.s)).toEqual(["b", "c"]);
    expect(filterArchiveEntries(sample, "2023", "")).toHaveLength(1);
  });
});

describe("isPostVisible", () => {
  const now = new Date("2026-06-01T12:00:00Z");

  it("respects publish and expiration dates", () => {
    expect(isPostVisible(new Date("2026-01-01"), null, now)).toBe(true);
    expect(isPostVisible(new Date("2026-12-01"), null, now)).toBe(false);
    expect(isPostVisible(new Date("2026-01-01"), new Date("2026-05-01"), now)).toBe(false);
    expect(isPostVisible(new Date("2026-01-01"), new Date("2026-12-01"), now)).toBe(true);
  });
});
