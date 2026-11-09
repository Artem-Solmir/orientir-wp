import { describe, expect, it } from "vitest";
import { MAX_PRUNE_SHARE, pruneAllowed } from "./load";

describe("pruneAllowed", () => {
  it("allows nothing to delete even with an empty table", () => {
    expect(pruneAllowed(0, 0)).toBe(true);
    expect(pruneAllowed(200, 0)).toBe(true);
  });

  it("allows deletions up to the share limit", () => {
    expect(MAX_PRUNE_SHARE).toBe(0.2);
    expect(pruneAllowed(100, 20)).toBe(true);
    expect(pruneAllowed(100, 21)).toBe(false);
  });

  it("refuses to delete anything when no legacy rows exist or the source is empty", () => {
    expect(pruneAllowed(0, 5)).toBe(false);
    expect(pruneAllowed(197, 197)).toBe(false);
  });
});
