import { describe, expect, it } from "vitest";
import { SANITIZE, sanitize } from "./content";

const attrs = SANITIZE.allowedAttributes as Record<string, string[]>;

const sample: Record<string, string> = {
  href: "/x", title: "t", target: "_blank", rel: "nofollow", src: "/a.jpg", alt: "a", width: "10", height: "20",
  class: "aligncenter", "data-gallery": "5", "data-form": "contact", colspan: "2", rowspan: "3",
  allowfullscreen: "", frameborder: "0", controls: "", loading: "eager", decoding: "sync",
};

function html(tag: string, attr: string): string {
  // iframes only accept whitelisted hosts, so their src needs a real embed URL.
  const value = tag === "iframe" && attr === "src" ? "https://www.youtube.com/embed/1" : sample[attr]!;
  const attribute = value === "" ? ` ${attr}` : ` ${attr}="${value}"`;
  const extra = tag === "iframe" ? ' src="https://www.youtube.com/embed/1"' : tag === "video" && attr !== "src" ? ' src="/v.mp4"' : "";
  return `<${tag}${attr === "src" ? "" : extra}${attribute}></${tag}>`;
}

describe("every allowed attribute survives on its tag", () => {
  const cases = Object.entries(attrs).flatMap(([tag, list]) => list.map((attr) => [tag, attr] as const));
  it.each(cases)("%s[%s]", (tag, attr) => {
    const out = sanitize(html(tag, attr));
    expect(out).toContain(attr);
  });
});

describe("link schemes", () => {
  it.each(["http://a.b/x", "https://a.b/x", "mailto:a@b.c", "tel:+375291112233"])("%s is kept", (href) => {
    expect(sanitize(`<a href="${href}">l</a>`)).toContain(`href="${href}"`);
  });

  it.each(["javascript:alert(1)", "data:text/html;base64,AAAA", "vbscript:x", "ftp://a.b/x"])("%s is dropped", (href) => {
    expect(sanitize(`<a href="${href}">l</a>`)).not.toContain("href=");
  });
});

describe("rel handling", () => {
  it("keeps an existing rel value and still forces noopener", () => {
    const out = sanitize('<a href="/x" rel="nofollow">l</a>');
    expect(out).toContain("noopener");
  });

  it("adds noopener to a link that has no rel", () => {
    expect(sanitize('<a href="/x">l</a>')).toBe('<a href="/x" rel="noopener">l</a>');
  });
});
