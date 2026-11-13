import "server-only";
import { createHash, randomBytes, timingSafeEqual } from "node:crypto";

export function randomToken(): string {
  return randomBytes(32).toString("base64url");
}

export function sha256(value: string): string {
  return createHash("sha256").update(value).digest("hex");
}

export function constantTimeEqual(a: string, b: string): boolean {
  const ab = Buffer.from(a);
  const bb = Buffer.from(b);
  if (ab.length !== bb.length) return false;
  return timingSafeEqual(ab, bb);
}

export function otpCode(): string {
  return String(Math.floor(100_000 + Math.random() * 900_000));
}
