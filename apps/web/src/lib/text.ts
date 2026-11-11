const ENTITIES: Record<string, string> = {
  "&nbsp;": " ", "&amp;": "&", "&lt;": "<", "&gt;": ">", "&quot;": '"', "&#39;": "'", "&laquo;": "«", "&raquo;": "»",
  "&ndash;": "–", "&mdash;": "—", "&hellip;": "…",
};

/** HTML -> plain text. Input is already sanitized; this only flattens it for summaries. */
export function htmlToText(html: string): string {
  return html
    .replace(/<(?:script|style)\b[\s\S]*?<\/(?:script|style)>/gi, " ")
    .replace(/<[^>]*>/g, " ")
    .replace(/&(?:nbsp|amp|lt|gt|quot|laquo|raquo|ndash|mdash|hellip|#39);/g, (e) => ENTITIES[e] ?? e)
    .replace(/\s+/g, " ")
    .trim();
}

/** A summary no longer than `max` characters, cut at a word boundary, with an ellipsis when cut. */
export function summarize(text: string, max = 160): string {
  if (text.length <= max) return text;
  const cut = text.slice(0, max);
  const lastSpace = cut.lastIndexOf(" ");
  return `${(lastSpace > max * 0.6 ? cut.slice(0, lastSpace) : cut).replace(/[\s,.;:–—-]+$/, "")}…`;
}

export function xmlEscape(value: string): string {
  return value
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&apos;");
}

const dateFormat = new Intl.DateTimeFormat("ru-RU", {
  day: "numeric",
  month: "long",
  year: "numeric",
  timeZone: "Europe/Minsk",
});

export function formatDate(date: Date): string {
  return dateFormat.format(date);
}

const WORDS_PER_MINUTE = 200;

/** Plain-text reading time for a report body. */
export function readingMinutes(text: string): number {
  const words = text.trim().split(/\s+/).filter(Boolean).length;
  return Math.max(1, Math.ceil(words / WORDS_PER_MINUTE));
}

export function countImagesInHtml(html: string): number {
  return (html.match(/<img\b/gi) ?? []).length;
}

/** Wrap query words in <mark> for search results (already escaped plain text). */
export function highlightPlain(text: string, query: string): string {
  const words = [...new Set(query.toLowerCase().split(/\s+/).filter(Boolean))];
  if (words.length === 0) return text;
  const pattern = new RegExp(`(${words.map((w) => w.replace(/[.*+?^${}()|[\]\\]/g, "\\$&")).join("|")})`, "gi");
  return text.replace(pattern, "<mark>$1</mark>");
}
