import { describe, expect, it } from "vitest";
import { prepareProseHtml } from "./prose-html";

describe("prepareProseHtml", () => {
  it("adds lazy loading to images missing the attribute", () => {
    const out = prepareProseHtml('<p><img src="/a.jpg" alt="x" width="100" height="50" /></p>');
    expect(out).toContain('loading="lazy"');
    expect(out).toContain('decoding="async"');
    expect(out).toContain('fetchpriority="low"');
  });

  it("does not duplicate loading when already present", () => {
    const out = prepareProseHtml('<img src="/a.jpg" loading="lazy" decoding="async" />');
    expect(out.match(/loading="lazy"/g)).toHaveLength(1);
  });

  it("defers iframes", () => {
    const out = prepareProseHtml(
      '<iframe width="560" height="315" src="https://www.youtube.com/embed/abc" title="x"></iframe>',
    );
    expect(out).toContain('loading="lazy"');
  });
});
