import { describe, expect, it } from "vitest";
import { isReservedSlug, slugify } from "./slug";

describe("slugify", () => {
  it.each([
    ["Поход по реке Птичь", "poxod-po-reke-ptich"],
    ["Припятский марафон 2026", "pripyatskij-marafon-2026"],
    ["  Щука!!!  ", "shhuka"],
    ["Объезд, ёлка", "obezd-elka"],
    ["Hello   World", "hello-world"],
    ["---", ""],
  ])("%s -> %s", (input, expected) => {
    expect(slugify(input)).toBe(expected);
  });

  it("caps length without leaving a trailing dash", () => {
    const slug = slugify("а-".repeat(80));
    expect(slug.length).toBeLessThanOrEqual(100);
    expect(slug.endsWith("-")).toBe(false);
  });
});

describe("isReservedSlug", () => {
  it("blocks app routes but not content slugs", () => {
    expect(isReservedSlug("api")).toBe(true);
    expect(isReservedSlug("archive")).toBe(true);
    expect(isReservedSlug("my-trip")).toBe(false);
  });
});
