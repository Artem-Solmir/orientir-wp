export const SITE_NAME = "Клуб туризма «Ориентир»";
export const SITE_DESCRIPTION = "Походы, велопоходы и приключенческие гонки: отчёты, планы и новости клуба.";

/** Public base URL without a trailing slash. Read lazily so unit tests and builds can set it. */
export function siteUrl(): string {
  return (process.env.APP_URL ?? "http://localhost:3000").replace(/\/$/, "");
}

export function absoluteUrl(path: string): string {
  return `${siteUrl()}${path.startsWith("/") ? path : `/${path}`}`;
}

/** Cover images are served from the legacy uploads path, which stays valid after migration. */
export function mediaUrl(path: string): string {
  return `/wp-content/uploads/${path.split("/").map(encodeURIComponent).join("/")}`;
}
