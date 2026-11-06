import { describe, expect, it } from "vitest";
import { pageCount, pageHref, pageWindow, parsePageSegments } from "./pagination";

describe("pageCount", () => {
  it("rounds up and never returns less than one page", () => {
    expect(pageCount(0)).toBe(1);
    expect(pageCount(12)).toBe(1);
    expect(pageCount(13)).toBe(2);
    expect(pageCount(25, 5)).toBe(5);
  });
});

describe("parsePageSegments", () => {
  it("treats no segments as the first page", () => {
    expect(parsePageSegments(undefined)).toBe(1);
    expect(parsePageSegments([])).toBe(1);
  });

  it("accepts /page/N", () => {
    expect(parsePageSegments(["page", "2"])).toBe(2);
    expect(parsePageSegments(["page", "123456"])).toBe(123456);
  });

  it.each([
    [["page"]], [["page", "0"]], [["page", "02"]], [["page", "-1"]], [["page", "1.5"]], [["page", "abc"]],
    [["page", "2", "x"]], [["pages", "2"]], [["x"]], [["page", "1234567"]],
  ])("rejects %j", (segments) => {
    expect(parsePageSegments(segments)).toBeNull();
  });
});

describe("pageWindow", () => {
  it("shows everything for few pages", () => {
    expect(pageWindow(1, 3)).toEqual([1, 2, 3]);
  });

  it("collapses gaps with null and keeps first and last", () => {
    expect(pageWindow(10, 20)).toEqual([1, null, 8, 9, 10, 11, 12, null, 20]);
    expect(pageWindow(1, 20)).toEqual([1, 2, 3, null, 20]);
    expect(pageWindow(20, 20)).toEqual([1, null, 18, 19, 20]);
  });

  it("does not add a gap between neighbours", () => {
    expect(pageWindow(4, 8)).toEqual([1, 2, 3, 4, 5, 6, null, 8]);
  });

  it("handles a single page", () => {
    expect(pageWindow(1, 1)).toEqual([1]);
  });
});

describe("pageHref", () => {
  it("returns the base for page one and /page/N otherwise, without double slashes", () => {
    expect(pageHref("/category/novosti", 1)).toBe("/category/novosti");
    expect(pageHref("/category/novosti", 3)).toBe("/category/novosti/page/3");
    expect(pageHref("/", 2)).toBe("/page/2");
    expect(pageHref("/category/novosti/", 2)).toBe("/category/novosti/page/2");
  });
});
