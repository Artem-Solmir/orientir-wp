import { describe, expect, it } from "vitest";
import { countImagesInHtml, formatDate, highlightPlain, htmlToText, readingMinutes, summarize, xmlEscape } from "./text";

describe("htmlToText", () => {
  it("strips tags, decodes common entities and collapses whitespace", () => {
    expect(htmlToText("<p>Привет&nbsp;&laquo;мир&raquo; &amp; <b>все</b></p>\n<p>ok</p>")).toBe("Привет «мир» & все ok");
  });

  it("drops script and style content entirely", () => {
    expect(htmlToText("a<script>alert(1)</script>b<style>p{}</style>c")).toBe("a b c");
  });

  it("leaves unknown entities alone", () => {
    expect(htmlToText("x &unknown; y")).toBe("x &unknown; y");
  });
});

describe("summarize", () => {
  it("returns short text unchanged", () => {
    expect(summarize("short", 10)).toBe("short");
    expect(summarize("1234567890", 10)).toBe("1234567890");
  });

  it("cuts at a word boundary and adds an ellipsis", () => {
    expect(summarize("alpha beta gamma delta", 15)).toBe("alpha beta…");
  });

  it("hard-cuts when the only space is too early", () => {
    expect(summarize("ab cdefghijklmnopqrstuvwxyz", 10)).toBe("ab cdefghi…");
  });

  it("removes trailing punctuation before the ellipsis", () => {
    expect(summarize("alpha, beta, gamma, delta", 12)).toBe("alpha, beta…");
  });
});

describe("xmlEscape", () => {
  it("escapes all five XML special characters", () => {
    expect(xmlEscape(`<a href="x">&'</a>`)).toBe("&lt;a href=&quot;x&quot;&gt;&amp;&apos;&lt;/a&gt;");
  });
});

describe("readingMinutes", () => {
  it("returns at least one minute", () => {
    expect(readingMinutes("один")).toBe(1);
    expect(readingMinutes(Array(250).fill("слово").join(" "))).toBe(2);
  });
});

describe("countImagesInHtml", () => {
  it("counts img tags", () => {
    expect(countImagesInHtml('<p>x</p><img src="a"><IMG alt="">')).toBe(2);
  });
});

describe("highlightPlain", () => {
  it("wraps matches in mark", () => {
    expect(highlightPlain("Марафон Припятский", "припят")).toBe("Марафон <mark>Припят</mark>ский");
  });

  it("highlights every word in the query", () => {
    expect(highlightPlain("Марафон Припятский весенний", "марафон весен")).toBe(
      "<mark>Марафон</mark> Припятский <mark>весен</mark>ний",
    );
  });
});

describe("formatDate", () => {
  it("formats in Russian using the Minsk calendar day", () => {
    // 2026-10-09T22:30Z is already the 10th in Minsk (UTC+3).
    expect(formatDate(new Date("2026-10-09T22:30:00Z"))).toBe("10 октября 2026 г.");
  });
});
