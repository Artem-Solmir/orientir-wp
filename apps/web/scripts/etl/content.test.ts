import { describe, expect, it } from "vitest";
import { autop, convertCaptions, convertShortcodes, relativizeUrls, sanitize, transformBody } from "./content";

describe("relativizeUrls", () => {
  it("strips the site host for orientir.by and orientir.local, keeps external hosts", () => {
    const html =
      '<a href="http://orientir.by/x/">a</a> <img src="https://www.orientir.by/wp-content/uploads/a.jpg"> ' +
      '<a href="http://orientir.local/y">b</a> <a href="http://example.com/z">c</a>';
    expect(relativizeUrls(html)).toBe(
      '<a href="/x/">a</a> <img src="/wp-content/uploads/a.jpg"> <a href="/y">b</a> <a href="http://example.com/z">c</a>',
    );
  });

  it("turns a bare site link without a trailing slash into /, keeping the visible text", () => {
    expect(relativizeUrls('<a href="http://orientir.by">orientir.by</a>')).toBe('<a href="/">orientir.by</a>');
    expect(relativizeUrls("<a href='https://www.orientir.by'>x</a>")).toBe("<a href='/'>x</a>");
  });

  it("does not touch lookalike domains", () => {
    expect(relativizeUrls("http://orientir.by.evil.com/a")).toBe("http://orientir.by.evil.com/a");
  });
});

describe("convertCaptions", () => {
  const real =
    '[caption id="attachment_5395" align="aligncenter" width="478"]<a href="/wp-content/uploads/a.jpg"><img class="wp-image-5395 size-medium" src="/wp-content/uploads/a-478x850.jpg" alt="" width="478" height="850" /></a> Заготовка чума[/caption]';

  it("turns the real-world caption shortcode into a figure with alignment and caption", () => {
    const out = convertCaptions(real);
    expect(out).toContain('<figure class="wp-caption aligncenter">');
    expect(out).toContain("<figcaption>Заготовка чума</figcaption>");
    expect(out).toContain('<a href="/wp-content/uploads/a.jpg"><img');
  });

  it("omits figcaption when the caption text is empty", () => {
    const out = convertCaptions('[caption align="alignleft"]<img src="/a.jpg" />[/caption]');
    expect(out).toContain('<figure class="wp-caption alignleft">');
    expect(out).not.toContain("figcaption");
  });

  it("keeps inner content when the caption has no image", () => {
    expect(convertCaptions("[caption]just text[/caption]")).toBe("just text");
  });
});

describe("convertShortcodes", () => {
  it("maps foogallery, contact form and video to inert placeholders", () => {
    expect(convertShortcodes('[foogallery id="123"]')).toBe('<div data-gallery="123"></div>');
    expect(convertShortcodes("[foogallery id=7 x=1]")).toBe('<div data-gallery="7"></div>');
    expect(convertShortcodes('[contact-form-7 id="5" title="c"]')).toBe('<div data-form="contact"></div>');
    expect(convertShortcodes('[video src="/wp-content/uploads/v.mp4" width="640"][/video]')).toBe(
      '<video controls src="/wp-content/uploads/v.mp4"></video>',
    );
  });

  it("drops Presso wrappers but keeps their text, including closing tags with attributes", () => {
    expect(convertShortcodes('[one_half]left[/one_half][one_half_last]right[/one_half_last]')).toBe("leftright");
    expect(convertShortcodes('[button url="/x" color="red"]Go[/button]')).toBe("Go");
  });

  it("leaves bracketed plain text alone", () => {
    expect(convertShortcodes("см. [0] и [1], [d]")).toBe("см. [0] и [1], [d]");
  });
});

describe("autop", () => {
  it("wraps blank-line separated text in paragraphs and single newlines in br", () => {
    expect(autop("one\ntwo\n\nthree")).toBe("<p>one<br />two</p>\n<p>three</p>");
  });

  it("does not wrap block-level html and handles CRLF", () => {
    expect(autop("<figure>x</figure>\r\n\r\ntext")).toBe("<figure>x</figure>\n<p>text</p>");
  });

  it("returns an empty string for empty input", () => {
    expect(autop("  \n\n ")).toBe("");
  });
});

describe("sanitize", () => {
  it("removes scripts, event handlers and javascript: urls", () => {
    const dirty =
      '<p onclick="x()">a</p><script>alert(1)</script><a href="javascript:alert(1)">l</a><img src="x" onerror="alert(1)">';
    const clean = sanitize(dirty);
    expect(clean).not.toMatch(/script|onclick|onerror|javascript:/i);
    expect(clean).toContain("<p>a</p>");
  });

  it("allows only whitelisted iframe hosts", () => {
    expect(sanitize('<iframe src="https://www.youtube.com/embed/abc"></iframe>')).toContain("youtube.com");
    expect(sanitize('<iframe src="https://evil.example/x"></iframe>')).not.toContain("evil.example");
  });

  it("adds rel=noopener to links and keeps gallery placeholders", () => {
    expect(sanitize('<a href="http://x.y" target="_blank">l</a>')).toContain('rel="noopener"');
    expect(sanitize('<div data-gallery="9"></div>')).toBe('<div data-gallery="9"></div>');
  });

  it("drops unknown classes and inline styles", () => {
    expect(sanitize('<p style="color:red" class="evil">a</p>')).toBe("<p>a</p>");
  });
});

describe("transformBody (golden)", () => {
  it("converts a legacy post body end to end", () => {
    const raw =
      'Первый абзац\nвторая строка\n\n[caption id="a" align="aligncenter"]<img src="http://orientir.by/wp-content/uploads/a.jpg" alt="x" /> Подпись[/caption]\n\nКонец';
    const out = transformBody(raw);
    expect(out).toBe(
      '<p>Первый абзац<br />вторая строка</p>\n' +
        '<figure class="wp-caption aligncenter"><img src="/wp-content/uploads/a.jpg" alt="x" /><figcaption>Подпись</figcaption></figure>\n' +
        "<p>Конец</p>",
    );
  });
});
