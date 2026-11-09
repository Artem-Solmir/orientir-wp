import { describe, expect, it } from "vitest";
import { escapeRegex, headerLines, redirectRule, shortlinkRule } from "./build-static";

describe("redirectRule", () => {
  it("builds an anchored 301 for a legacy path", () => {
    expect(redirectRule("/old/page/", "/new/")).toBe("RewriteRule ^old/page/?$ /new/ [R=301,L]");
  });

  it("escapes regex metacharacters in the source", () => {
    expect(redirectRule("/a.b", "/x/")).toBe("RewriteRule ^a\\.b/?$ /x/ [R=301,L]");
  });

  it.each([
    ["/", "/x/"],
    ["/ok", "/bad target"],
    ["/bad path", "/x/"],
    ["/x\nRewriteRule ^ /evil", "/x/"],
  ])("skips unsafe or empty input %j -> %j", (from, to) => {
    expect(redirectRule(from, to)).toBeNull();
  });
});

describe("shortlinkRule", () => {
  it("maps /?p=ID to the post", () => {
    expect(shortlinkRule(42, "my-post")).toContain("p=42(?:&|$)");
    expect(shortlinkRule(42, "my-post")).toContain("/my-post/? [R=301,L]");
  });

  it("rejects bad ids and slugs", () => {
    expect(shortlinkRule(1.5, "x")).toBeNull();
    expect(shortlinkRule(1, "Bad Slug")).toBeNull();
  });
});

describe("escapeRegex and headerLines", () => {
  it("escapes dots and parens", () => {
    expect(escapeRegex("a.b(c)")).toBe("a\\.b\\(c\\)");
  });

  it("emits production headers including HSTS and an escaped value", () => {
    const lines = headerLines();
    expect(lines).toContain("Strict-Transport-Security");
    expect(lines).toContain("Content-Security-Policy");
    expect(lines).not.toContain("unsafe-eval");
  });
});
