import { describe, expect, it } from "vitest";
import { isLivePost, loadStore } from "./reader";
import type { PostRecord } from "./types";

describe("store reader", () => {
  it("loads posts from content/", () => {
    const store = loadStore();
    expect(store.posts.length).toBeGreaterThan(100);
    expect(store.categories.length).toBeGreaterThan(5);
  });

  it("isLivePost respects publish and expiry", () => {
    const now = new Date("2026-06-01T12:00:00Z");
    const base: PostRecord = {
      slug: "x",
      title: "t",
      excerpt: null,
      body: "b",
      status: "published",
      publishedAt: "2020-01-01T00:00:00Z",
      expiresAt: null,
      updatedAt: null,
      authorName: null,
      seoTitle: null,
      seoDescription: null,
      legacyWpId: null,
      cover: null,
      categories: [],
      tags: [],
    };
    expect(isLivePost(base, now)).toBe(true);
    expect(isLivePost({ ...base, status: "draft" }, now)).toBe(false);
    expect(isLivePost({ ...base, publishedAt: "2030-01-01T00:00:00Z" }, now)).toBe(false);
    expect(isLivePost({ ...base, expiresAt: "2025-01-01T00:00:00Z" }, now)).toBe(false);
  });
});
