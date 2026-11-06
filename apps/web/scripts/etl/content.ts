import sanitizeHtml from "sanitize-html";

const SITE = "https?:\\/\\/(?:www\\.)?(?:orientir\\.by|orientir\\.local)";
const SITE_URL = new RegExp(`${SITE}(?=\\/)`, "gi");
const BARE_SITE_ATTR = new RegExp(`\\b(href|src)=(["'])${SITE}\\2`, "gi");

/** Absolute links to this site become root-relative, so uploads keep working under any domain. */
export function relativizeUrls(html: string): string {
  return html.replace(BARE_SITE_ATTR, "$1=$2/$2").replace(SITE_URL, "");
}

const CAPTION = /\[caption([^\]]*)\]([\s\S]*?)\[\/caption\]/gi;
const MEDIA_LEAD = /^\s*((?:<a\b[^>]*>\s*)?<img\b[^>]*>(?:\s*<\/a>)?)\s*([\s\S]*)$/i;

function captionToFigure(_match: string, attrs: string, inner: string): string {
  const parts = MEDIA_LEAD.exec(inner);
  if (!parts) return inner;
  const align = /align="(align\w+)"/.exec(attrs)?.[1];
  const className = ["wp-caption", align].filter(Boolean).join(" ");
  const caption = (parts[2] ?? "").trim();
  const figcaption = caption ? `<figcaption>${caption}</figcaption>` : "";
  return `\n\n<figure class="${className}">${parts[1]}${figcaption}</figure>\n\n`;
}

export function convertCaptions(text: string): string {
  return text.replace(CAPTION, captionToFigure);
}

const GALLERY = /\[foogallery\s+id="?(\d+)"?[^\]]*\]/gi;
const CONTACT_FORM = /\[contact-form-7[^\]]*\]/gi;
const VIDEO = /\[video\s[^\]]*?src="([^"]+)"[^\]]*\](?:\s*\[\/video\])?/gi;

// Presso layout/typography shortcodes: the wrapper is dropped, inner text is kept.
export const PRESSO_SHORTCODES = [
  "infobox", "accordion", "tabs", "tab", "list", "list_item", "button", "dropcap", "quote", "mark",
  "one_half", "one_half_last", "one_third", "one_third_last", "two_third", "one_fourth",
  "one_fourth_last", "three_fourth", "one_fifth", "one_fifth_last",
];
const PRESSO_TAG = new RegExp(`\\[\\/?(?:${PRESSO_SHORTCODES.join("|")})(?:\\s[^\\]]*)?\\]`, "gi");

export function convertShortcodes(text: string): string {
  return text
    .replace(GALLERY, '<div data-gallery="$1"></div>')
    .replace(CONTACT_FORM, '<div data-form="contact"></div>')
    .replace(VIDEO, '<video controls src="$1"></video>')
    .replace(PRESSO_TAG, "");
}

const BLOCK_START = /^<(?:p|div|figure|table|ul|ol|li|h[1-6]|blockquote|pre|iframe|hr|section|video)\b/i;

/** Minimal wpautop: blank-line separated chunks become <p>, single newlines become <br />. */
export function autop(text: string): string {
  return text
    .replace(/\r\n?/g, "\n")
    .split(/\n\s*\n/)
    .map((chunk) => chunk.trim())
    .filter(Boolean)
    .map((chunk) => (BLOCK_START.test(chunk) ? chunk : `<p>${chunk.replace(/\n/g, "<br />")}</p>`))
    .join("\n");
}

export const IFRAME_HOSTS = ["www.youtube.com", "www.youtube-nocookie.com", "player.vimeo.com", "www.google.com"];

export const SANITIZE: sanitizeHtml.IOptions = {
  allowedTags: [
    "p", "br", "a", "strong", "b", "em", "i", "u", "s", "sub", "sup", "span", "div", "hr", "pre", "code",
    "h2", "h3", "h4", "h5", "h6", "blockquote", "ul", "ol", "li", "img", "figure", "figcaption",
    "table", "thead", "tbody", "tr", "th", "td", "iframe", "video",
  ],
  allowedAttributes: {
    a: ["href", "title", "target", "rel"],
    img: ["src", "alt", "width", "height", "class", "title"],
    figure: ["class"],
    div: ["data-gallery", "data-form"],
    th: ["colspan", "rowspan"],
    td: ["colspan", "rowspan"],
    iframe: ["src", "width", "height", "allowfullscreen", "frameborder", "title"],
    video: ["src", "controls"],
  },
  allowedClasses: {
    img: ["aligncenter", "alignleft", "alignright", "alignnone", "size-full", "size-medium", "size-large", "size-thumbnail"],
    figure: ["wp-caption", "aligncenter", "alignleft", "alignright", "alignnone"],
  },
  allowedSchemes: ["http", "https", "mailto", "tel"],
  allowedIframeHostnames: IFRAME_HOSTS,
  transformTags: { a: sanitizeHtml.simpleTransform("a", { rel: "noopener" }, true) },
};

export function sanitize(html: string): string {
  return sanitizeHtml(html, SANITIZE);
}

/** Full legacy post/page body pipeline: WordPress storage format -> sanitized HTML. */
export function transformBody(raw: string): string {
  return sanitize(autop(convertShortcodes(convertCaptions(relativizeUrls(raw)))));
}
