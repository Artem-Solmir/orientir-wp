import { describe, expect, it } from "vitest";
import {
  IFRAME_HOSTS,
  PRESSO_SHORTCODES,
  SANITIZE,
  autop,
  convertCaptions,
  convertShortcodes,
  relativizeUrls,
  sanitize,
} from "./content";

describe("every Presso shortcode is stripped, opening and closing, keeping inner text", () => {
  it.each(PRESSO_SHORTCODES)("%s", (name) => {
    expect(convertShortcodes(`[${name} a="1"]X[/${name}]`)).toBe("X");
    expect(convertShortcodes(`[${name}]X[/${name}]`)).toBe("X");
  });

  it("does not strip a longer unknown shortcode that merely starts with a known name", () => {
    expect(convertShortcodes("[tabsx]X[/tabsx]")).toBe("[tabsx]X[/tabsx]");
  });
});

describe("every allowed tag survives sanitize", () => {
  const voidTags = new Set(["br", "hr", "img"]);
  it.each((SANITIZE.allowedTags as string[]).filter((t) => !voidTags.has(t) && !["iframe", "video"].includes(t)))(
    "%s",
    (tag) => {
      // <a> always gains rel="noopener", so allow attributes on the opening tag.
      expect(sanitize(`<${tag}>x</${tag}>`)).toMatch(new RegExp(`<${tag}\\b[^>]*>x</${tag}>`));
    },
  );

  it("keeps void tags", () => {
    expect(sanitize("a<br />b")).toBe("a<br />b");
    expect(sanitize("<hr />")).toBe("<hr />");
    expect(sanitize('<img src="/a.jpg" alt="x" />')).toBe('<img src="/a.jpg" alt="x" />');
  });

  it("keeps video with src and controls only", () => {
    expect(sanitize('<video controls src="/v.mp4" autoplay></video>')).toBe('<video controls src="/v.mp4"></video>');
  });

  it("keeps table cell spans", () => {
    expect(sanitize('<table><tbody><tr><td colspan="2" rowspan="3">x</td></tr></tbody></table>')).toContain(
      'colspan="2" rowspan="3"',
    );
  });
});

describe("every allowed iframe host is accepted", () => {
  it.each(IFRAME_HOSTS)("%s", (host) => {
    expect(sanitize(`<iframe src="https://${host}/embed/1"></iframe>`)).toContain(host);
  });
});

describe("allowed classes", () => {
  it.each(["aligncenter", "alignleft", "alignright", "alignnone", "size-full", "size-medium", "size-large", "size-thumbnail"])(
    "img keeps %s",
    (cls) => {
      expect(sanitize(`<img src="/a.jpg" class="${cls} evil" />`)).toContain(`class="${cls}"`);
    },
  );

  it.each(["wp-caption", "aligncenter", "alignleft", "alignright", "alignnone"])("figure keeps %s", (cls) => {
    expect(sanitize(`<figure class="${cls} evil">x</figure>`)).toContain(`class="${cls}"`);
  });
});

describe("regex edge cases", () => {
  it("caption: no align attribute, leading whitespace, multiline caption text, spaced anchor", () => {
    expect(convertCaptions("[caption]<img src=\"/a.jpg\" /> text[/caption]")).toContain('<figure class="wp-caption">');
    expect(convertCaptions('[caption]  \n <a href="/x">\n <img src="/a.jpg" />\n </a>\n line1\nline2[/caption]')).toContain(
      "<figcaption>line1\nline2</figcaption>",
    );
  });

  it("caption: text before the image means it is not a captioned image", () => {
    expect(convertCaptions('[caption]oops <img src="/a.jpg" />[/caption]')).toBe('oops <img src="/a.jpg" />');
  });

  it("caption: a non-linked image followed by closing anchor text is not swallowed", () => {
    const out = convertCaptions('[caption]<img src="/a.jpg" />   cap  [/caption]');
    expect(out).toContain('<img src="/a.jpg" /><figcaption>cap</figcaption>');
  });

  it("foogallery tolerates one or many spaces and quoted/unquoted ids", () => {
    expect(convertShortcodes('[foogallery   id="5"]')).toBe('<div data-gallery="5"></div>');
    expect(convertShortcodes("[foogallery id=5]")).toBe('<div data-gallery="5"></div>');
    expect(convertShortcodes('[foogallery id="5" template="x"]')).toBe('<div data-gallery="5"></div>');
  });

  it("video: src anywhere in attributes, with or without closing tag, and keeps following text", () => {
    expect(convertShortcodes('[video width="1" src="/v.mp4" height="2"]after')).toBe('<video controls src="/v.mp4"></video>after');
    expect(convertShortcodes('[video src="/v.mp4"] [/video]after')).toBe('<video controls src="/v.mp4"></video>after');
    expect(convertShortcodes('[video src="/a.mp4"][video src="/b.mp4"]')).toBe(
      '<video controls src="/a.mp4"></video><video controls src="/b.mp4"></video>',
    );
  });

  it("contact form shortcode with attributes", () => {
    expect(convertShortcodes('[contact-form-7 id="1"]')).toBe('<div data-form="contact"></div>');
  });

  it("relativizeUrls is case-insensitive and global", () => {
    expect(relativizeUrls('<a href="HTTP://ORIENTIR.BY/a">x</a><a href="http://orientir.by/b">y</a>')).toBe(
      '<a href="/a">x</a><a href="/b">y</a>',
    );
    expect(relativizeUrls('<a HREF="http://orientir.by">x</a><img SRC="https://orientir.by"/>')).toBe(
      '<a HREF="/">x</a><img SRC="/"/>',
    );
    expect(relativizeUrls('<a href="http://orientir.by/x" data-x="http://orientir.by">z</a>')).toBe(
      '<a href="/x" data-x="http://orientir.by">z</a>',
    );
  });

  it("autop handles every block start, lone CR and whitespace-only separators", () => {
    for (const tag of ["p", "div", "figure", "table", "ul", "ol", "li", "h1", "h2", "h6", "blockquote", "pre", "iframe", "hr", "section", "video"]) {
      expect(autop(`<${tag}>x</${tag}>`)).toBe(`<${tag}>x</${tag}>`);
    }
    expect(autop("<span>x</span>")).toBe("<p><span>x</span></p>");
    expect(autop("a\rb")).toBe("<p>a<br />b</p>");
    expect(autop("a\n \t\nb")).toBe("<p>a</p>\n<p>b</p>");
    expect(autop("<h7>x</h7>")).toBe("<p><h7>x</h7></p>");
  });
});
