import { resolve, sep } from "node:path";

const CONTENT_TYPES: Record<string, string> = {
  jpg: "image/jpeg", jpeg: "image/jpeg", png: "image/png", gif: "image/gif", webp: "image/webp",
  pdf: "application/pdf", zip: "application/zip", doc: "application/msword",
  docx: "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
  xls: "application/vnd.ms-excel", mp4: "video/mp4",
};

export type MediaTarget = { file: string; contentType: string };

function isSafeSegment(s: string): boolean {
  return s !== "" && !s.startsWith(".") && !/[\0\\/:]/.test(s);
}

/**
 * Maps URL segments under /wp-content/uploads to a file inside `root`, or null.
 * Rejects traversal, absolute paths, dotfiles, NULs, and any extension outside the allow-list
 * (so a stray .php or .env in uploads is never served).
 */
export function resolveMedia(root: string, segments: string[]): MediaTarget | null {
  if (segments.length === 0 || segments.length > 8) return null;
  if (!segments.every(isSafeSegment)) return null;
  const ext = segments[segments.length - 1]!.split(".").pop()?.toLowerCase() ?? "";
  const contentType = CONTENT_TYPES[ext];
  if (!contentType) return null;

  const base = resolve(root);
  const file = resolve(base, ...segments);
  if (!file.startsWith(base + sep)) return null;
  return { file, contentType };
}
