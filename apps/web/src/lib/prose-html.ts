import { sanitize } from "./sanitize";

const IMG = /<img\b(?![^>]*\bloading=)([^>]*?)>/gi;
const IFRAME = /<iframe\b(?![^>]*\bloading=)([^>]*?)>/gi;

/**
 * Sanitize legacy HTML and ensure off-screen embeds do not compete with LCP (cover/hero load eagerly via SiteImage).
 */
export function prepareProseHtml(html: string): string {
  let out = sanitize(html);
  out = out.replace(IMG, '<img loading="lazy" decoding="async" fetchpriority="low"$1>');
  out = out.replace(IFRAME, '<iframe loading="lazy"$1>');
  return out;
}
