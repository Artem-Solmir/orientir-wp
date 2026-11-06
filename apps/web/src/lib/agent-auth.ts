import { createHash, timingSafeEqual } from "node:crypto";

const BEARER = /^Bearer ([\x21-\x7e]{16,256})$/;

function digest(value: string): Buffer {
  return createHash("sha256").update(value).digest();
}

/** Constant-time comparison of a presented Authorization header against the configured token. */
export function isValidAgentToken(header: string | null, expected: string): boolean {
  const presented = header ? BEARER.exec(header)?.[1] : undefined;
  // Hash both sides so lengths are equal and the comparison time does not leak the token.
  const ok = timingSafeEqual(digest(presented ?? ""), digest(expected));
  return presented !== undefined && ok;
}
