import { describe, expect, it } from "vitest";
import { parseComposer, resolveCategories } from "./composer";
import { imageSize, serializedIds } from "./php";

describe("parseComposer", () => {
  const home = {
    vw_composer_1: "featured_post_slider",
    vw_composer_1_number: "15",
    vw_composer_1_show_posts: "only_featured",
    vw_composer_2: "latest",
    vw_composer_2_title: "Последние записи",
    vw_composer_2_number: "8",
    vw_composer_3: "latest_category",
    vw_composer_3_category: "19",
    vw_composer_10_orphan: "x",
    unrelated: "y",
  };

  it("builds ordered sections and coerces numbers", () => {
    const sections = parseComposer(home);
    expect(sections.map((s) => [s.order, s.type])).toEqual([
      [0, "featured_post_slider"],
      [1, "latest"],
      [2, "latest_category"],
    ]);
    expect(sections[0]!.config).toEqual({ number: 15, show_posts: "only_featured" });
    expect(sections[1]!.config.title).toBe("Последние записи");
  });

  it("stops at the first gap and never leaks section 10 into section 1", () => {
    expect(parseComposer({ vw_composer_1: "a", vw_composer_3: "c" })).toHaveLength(1);
    expect(parseComposer(home)[0]!.config).not.toHaveProperty("orphan");
  });

  it("returns no sections for a regular page", () => {
    expect(parseComposer({ vw_page_sidebar: "x" })).toEqual([]);
  });
});

describe("resolveCategories", () => {
  const slugs = new Map([[19, "otchyoty"], [1, "uncategorized"], [2, "news"]]);

  it("adds slugs for category and excluded categories", () => {
    expect(resolveCategories({ category: 19, exclude_categories: "1, 2" }, slugs)).toEqual({
      category: 19,
      exclude_categories: "1, 2",
      categorySlug: "otchyoty",
      excludeCategorySlugs: "uncategorized,news",
    });
  });

  it("ignores zero and unknown ids", () => {
    expect(resolveCategories({ category: 0, exclude_categories: "99" }, slugs)).toEqual({
      category: 0,
      exclude_categories: "99",
    });
  });
});

describe("php helpers", () => {
  it("extracts ids in stored order", () => {
    expect(serializedIds('a:2:{i:0;s:3:"761";i:1;s:3:"760";}')).toEqual([761, 760]);
    expect(serializedIds(null)).toEqual([]);
  });

  it("reads top-level image size only", () => {
    expect(imageSize('a:2:{s:5:"width";i:800;s:6:"height";i:600;s:4:"file";s:5:"a.jpg";}')).toEqual({
      width: 800,
      height: 600,
    });
    expect(imageSize("garbage")).toBeNull();
    expect(imageSize(undefined)).toBeNull();
  });
});
