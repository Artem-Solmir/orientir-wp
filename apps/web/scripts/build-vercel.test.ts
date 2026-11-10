import { describe, expect, it } from "vitest";
import { collectMediaUrls, legacyRedirect, shortlinkRedirect, vercelConfig } from "./build-vercel";

describe("legacyRedirect", () => {
  it("covers the path with and without a trailing slash", () => {
    expect(legacyRedirect("/old-page/", "/new-page/").map((r) => r.source)).toEqual(["/old-page", "/old-page/"]);
  });

  it("drops anything with unexpected characters instead of interpolating it", () => {
    expect(legacyRedirect("/a(b)", "/x/")).toEqual([]);
    expect(legacyRedirect("/a", "/x?y=(1)")).toEqual([]);
    expect(legacyRedirect("/", "/x/")).toEqual([]);
  });
});

describe("shortlinkRedirect", () => {
  it("maps /?p=ID to the migrated post", () => {
    expect(shortlinkRedirect(42, "pohod")[0]).toMatchObject({ source: "/", destination: "/pohod/", has: [{ key: "p", value: "42" }] });
  });

  it("rejects bad slugs and ids", () => {
    expect(shortlinkRedirect(1.5, "pohod")).toEqual([]);
    expect(shortlinkRedirect(1, "Bad Slug")).toEqual([]);
  });
});

describe("vercelConfig", () => {
  it("adds noindex only when asked", () => {
    const keys = (noindex: boolean) => vercelConfig([], noindex).headers[0]!.headers.map((h) => h.key);
    expect(keys(true)).toContain("X-Robots-Tag");
    expect(keys(false)).not.toContain("X-Robots-Tag");
    expect(keys(false)).toContain("Content-Security-Policy");
  });
});

describe("collectMediaUrls", () => {
  it("lists each upload once and ignores other files and path tricks", () => {
    const html = `<img src="/wp-content/uploads/2024/05/a-400x300.jpg"><img src="/wp-content/uploads/2024/05/a-400x300.jpg"><a href="/wp-content/uploads/x.pdf"></a><img src="/wp-content/uploads/../../secret.jpg"><img src="/wp-content/uploads/2024/b%20c.png">`;
    expect(collectMediaUrls(html)).toEqual(["/wp-content/uploads/2024/05/a-400x300.jpg", "/wp-content/uploads/2024/b c.png"]);
  });
});
