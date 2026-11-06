const TRANSLIT: Record<string, string> = {
  а: "a", б: "b", в: "v", г: "g", д: "d", е: "e", ё: "e", ж: "zh", з: "z", и: "i", й: "j", к: "k",
  л: "l", м: "m", н: "n", о: "o", п: "p", р: "r", с: "s", т: "t", у: "u", ф: "f", х: "x", ц: "c",
  ч: "ch", ш: "sh", щ: "shh", ъ: "", ы: "y", ь: "", э: "e", ю: "yu", я: "ya", і: "i", ў: "u",
};

/** Same style as the legacy WordPress slugs (poxod-po-reke...): lowercase latin, digits, single dashes. */
export function slugify(text: string): string {
  const latin = [...text.toLowerCase()].map((ch) => TRANSLIT[ch] ?? ch).join("");
  return latin.replace(/[^a-z0-9]+/g, "-").replace(/^-+|-+$/g, "").slice(0, 100).replace(/-+$/, "");
}

/** Top-level URL segments owned by the app; a post or page slug must never shadow them. */
export const RESERVED_SLUGS = new Set([
  "api", "archive", "category", "tag", "search", "feed", "materials", "wp-content", "sitemap", "robots",
  "favicon", "_next", "page",
]);

export function isReservedSlug(slug: string): boolean {
  return RESERVED_SLUGS.has(slug);
}
