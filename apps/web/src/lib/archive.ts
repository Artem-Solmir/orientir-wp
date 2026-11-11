export type ArchiveEntry = { s: string; t: string; d: string; y: number; c: string[]; e: string };

export function filterArchiveEntries(entries: ArchiveEntry[], year: string, cat: string): ArchiveEntry[] {
  return entries.filter((e) => {
    if (year && e.y !== Number(year)) return false;
    if (cat && !e.c.includes(cat)) return false;
    return true;
  });
}

/** WordPress expiration: hide after expiresAt; do not show before publishedAt. */
export function isPostVisible(publishedAt: Date | null, expiresAt: Date | null, now: Date): boolean {
  if (!publishedAt || publishedAt > now) return false;
  if (expiresAt && expiresAt <= now) return false;
  return true;
}
