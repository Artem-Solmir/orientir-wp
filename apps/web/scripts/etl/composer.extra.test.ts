import { describe, expect, it } from "vitest";
import { parseComposer, resolveCategories } from "./composer";
import { imageSize, serializedIds } from "./php";

describe("composer value coercion", () => {
  it("only fully numeric strings become numbers", () => {
    const [section] = parseComposer({
      vw_composer_1: "x",
      vw_composer_1_a: "12",
      vw_composer_1_b: "-3",
      vw_composer_1_c: "12abc",
      vw_composer_1_d: "abc12",
      vw_composer_1_e: "",
    });
    expect(section!.config).toEqual({ a: 12, b: -3, c: "12abc", d: "abc12", e: "" });
  });
});

describe("resolveCategories details", () => {
  const slugs = new Map([[19, "a"], [2, "b"], [3, "c"]]);

  it("does not add categorySlug when the id is unknown", () => {
    expect(resolveCategories({ category: 77 }, slugs)).toEqual({ category: 77 });
  });

  it("splits on commas only and trims each id", () => {
    expect(resolveCategories({ exclude_categories: " 2 ,3 " }, slugs).excludeCategorySlugs).toBe("b,c");
    expect(resolveCategories({ exclude_categories: "23" }, slugs).excludeCategorySlugs).toBeUndefined();
  });

  it("works when no category keys exist at all and does not mutate its input", () => {
    const input = { title: "t" };
    expect(resolveCategories(input, slugs)).toEqual({ title: "t" });
    resolveCategories({ ...input, category: 19 }, slugs);
    expect(input).toEqual({ title: "t" });
  });
});

describe("php helpers details", () => {
  it("ignores non-numeric strings and keeps order and duplicates", () => {
    expect(serializedIds('a:3:{i:0;s:1:"5";i:1;s:3:"abc";i:2;s:1:"5";}')).toEqual([5, 5]);
    expect(serializedIds("")).toEqual([]);
  });

  it("requires width immediately followed by height", () => {
    expect(imageSize('s:5:"width";i:10;s:4:"file";s:1:"x";s:6:"height";i:20;')).toBeNull();
    expect(imageSize('xx s:5:"width";i:10;s:6:"height";i:20; yy')).toEqual({ width: 10, height: 20 });
  });
});
