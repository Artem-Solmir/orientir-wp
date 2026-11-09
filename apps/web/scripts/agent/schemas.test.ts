import { describe, expect, it } from "vitest";
import { pagePatchSchema, postCreateSchema, postPatchSchema, slugSchema } from "./schemas";

describe("postCreateSchema", () => {
  const valid = { title: "Поход выходного дня", body: "<p>Текст</p>" };

  it("applies defaults for relations", () => {
    const parsed = postCreateSchema.parse(valid);
    expect(parsed.categories).toEqual([]);
    expect(parsed.tags).toEqual([]);
  });

  it("rejects unknown fields (strict) and bad slugs", () => {
    expect(postCreateSchema.safeParse({ ...valid, status: "draft" }).success).toBe(false);
    expect(postCreateSchema.safeParse({ ...valid, slug: "Bad Slug" }).success).toBe(false);
    expect(postCreateSchema.safeParse({ ...valid, slug: "ok-slug-1" }).success).toBe(true);
  });

  it("rejects path traversal in cover paths", () => {
    expect(postCreateSchema.safeParse({ ...valid, coverPath: "../secret.jpg" }).success).toBe(false);
    expect(postCreateSchema.safeParse({ ...valid, coverPath: "2023/05/a.jpg" }).success).toBe(true);
  });

  it("enforces title and body bounds", () => {
    expect(postCreateSchema.safeParse({ ...valid, title: "ab" }).success).toBe(false);
    expect(postCreateSchema.safeParse({ ...valid, body: "" }).success).toBe(false);
    expect(postCreateSchema.safeParse({ ...valid, body: "a".repeat(200_001) }).success).toBe(false);
  });
});

describe("patch schemas", () => {
  it("need at least one field", () => {
    expect(postPatchSchema.safeParse({}).success).toBe(false);
    expect(pagePatchSchema.safeParse({}).success).toBe(false);
  });

  it("accept a single field and only known statuses", () => {
    expect(postPatchSchema.safeParse({ excerpt: null }).success).toBe(true);
    expect(postPatchSchema.safeParse({ status: "archived" }).success).toBe(true);
    expect(postPatchSchema.safeParse({ status: "draft" }).success).toBe(false);
  });
});

describe("slugSchema", () => {
  it.each([["a", true], ["a-b-1", true], ["-a", false], ["a--b", false], ["A", false], ["a".repeat(101), false]])("%s -> %s", (v, ok) => {
    expect(slugSchema.safeParse(v).success).toBe(ok);
  });
});
