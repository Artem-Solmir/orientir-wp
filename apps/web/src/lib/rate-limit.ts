import "server-only";
import { createHash } from "node:crypto";
import { db } from "./db";

export function rateLimitKey(kind: string, identifier: string): string {
  return createHash("sha256").update(`${kind}:${identifier}`).digest("hex");
}

export type Limit = { kind: string; identifier: string; max: number; windowSec: number };

/**
 * Records one hit per limit and reports whether ANY limit is exceeded.
 * Hits are always recorded, so hammering an endpoint keeps the window full.
 */
export async function isRateLimited(limits: Limit[]): Promise<boolean> {
  let limited = false;
  for (const { kind, identifier, max, windowSec } of limits) {
    const key = rateLimitKey(kind, identifier);
    const since = new Date(Date.now() - windowSec * 1000);
    const count = await db.rateLimitHit.count({ where: { key, createdAt: { gte: since } } });
    if (count >= max) limited = true;
    await db.rateLimitHit.create({ data: { key } });
  }
  return limited;
}

/** Housekeeping: drop hits older than a day. Called opportunistically. */
export async function pruneRateLimitHits(): Promise<void> {
  await db.rateLimitHit.deleteMany({ where: { createdAt: { lt: new Date(Date.now() - 86_400_000) } } });
}
