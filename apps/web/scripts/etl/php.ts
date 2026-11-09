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

export type ImageVariant = { file: string; width: number; height: number };

/** Resized copies WordPress generated (the "sizes" section of _wp_attachment_metadata). */
export function imageVariants(serialized: string | null | undefined): ImageVariant[] {
  const blob = serialized ?? "";
  const start = blob.indexOf('s:5:"sizes";');
  if (start < 0) return [];
  const end = blob.indexOf('s:10:"image_meta"', start);
  const section = blob.slice(start, end < 0 ? undefined : end);
  const seen = new Set<string>();
  const out: ImageVariant[] = [];
  for (const m of section.matchAll(/s:4:"file";s:\d+:"([^"]+)";s:5:"width";i:(\d+);s:6:"height";i:(\d+);/g)) {
    const file = m[1]!;
    // Variants live next to the original: a bare file name, never a path.
    if (seen.has(file) || /[\\/:]|^\./.test(file)) continue;
    seen.add(file);
    out.push({ file, width: Number(m[2]), height: Number(m[3]) });
  }
  return out;
}
