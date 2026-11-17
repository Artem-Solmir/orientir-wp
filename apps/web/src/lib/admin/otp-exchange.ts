import { createHmac, timingSafeEqual } from "node:crypto";

const LABEL = "admin-otp";

function b64urlDecode(s: string): Buffer | null {
  try {
    const pad = "=".repeat((4 - (s.length % 4)) % 4);
    return Buffer.from((s + pad).replace(/-/g, "+").replace(/_/g, "/"), "base64");
  } catch {
    return null;
  }
}

function hmacMatches(body: string, sigB64: string, secret: string): boolean {
  const sigBuf = b64urlDecode(sigB64);
  if (sigBuf === null) return false;
  const expected = createHmac("sha256", secret).update(body).digest();
  return sigBuf.length === expected.length && timingSafeEqual(sigBuf, expected);
}

/** Short-lived token returned by admin-otp.php after a valid email code. */
export function verifyAdminOtpExchangeToken(token: string, secret: string): boolean {
  if (!secret || secret.length < 32) return false;
  const dot = token.lastIndexOf(".");
  if (dot < 0) return false;
  const bodyBuf = b64urlDecode(token.slice(0, dot));
  if (bodyBuf === null) return false;
  const body = bodyBuf.toString("utf8");
  if (!body.startsWith(`${LABEL}:`)) return false;
  const exp = Number(body.slice(LABEL.length + 1));
  if (!Number.isFinite(exp) || exp <= Date.now()) return false;
  return hmacMatches(body, token.slice(dot + 1), secret);
}
