/** Attachment ids from a PHP-serialized array of numeric strings, in stored order. */
export function serializedIds(serialized: string | null | undefined): number[] {
  if (!serialized) return [];
  return [...serialized.matchAll(/s:\d+:"(\d+)";/g)].map((m) => Number(m[1]));
}

/** Top-level width/height of a WordPress _wp_attachment_metadata blob. */
export function imageSize(serialized: string | null | undefined): { width: number; height: number } | null {
  const match = /s:5:"width";i:(\d+);s:6:"height";i:(\d+);/.exec(serialized ?? "");
  return match ? { width: Number(match[1]), height: Number(match[2]) } : null;
}
