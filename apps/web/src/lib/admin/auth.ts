import "server-only";
import { createHmac, timingSafeEqual } from "node:crypto";
import { cookies } from "next/headers";
import { adminOtpSecret, sessionSecret } from "./config";
import { verifyAdminOtpExchangeToken } from "./otp-exchange";

const SESSION_TTL_MS = 12 * 60 * 60 * 1000;
export const SESSION_COOKIE = "orientir_admin";
const SESSION_LABEL = "admin";

function sign(body: string): string {
  return createHmac("sha256", sessionSecret()).update(body).digest("base64url");
}

function makeSessionToken(): string {
  const exp = Date.now() + SESSION_TTL_MS;
  const body = `${SESSION_LABEL}:${exp}`;
  return `${body}.${sign(body)}`;
}

function verifySessionToken(token: string): boolean {
  const dot = token.lastIndexOf(".");
  if (dot < 0) return false;
  const body = token.slice(0, dot);
  const sig = token.slice(dot + 1);
  const expected = sign(body);
  const a = Buffer.from(sig);
  const b = Buffer.from(expected);
  if (a.length !== b.length || !timingSafeEqual(a, b)) return false;
  const exp = Number(body.split(":")[1]);
  return Number.isFinite(exp) && exp > Date.now();
}

async function setAdminSession(): Promise<void> {
  const jar = await cookies();
  jar.set(SESSION_COOKIE, makeSessionToken(), {
    httpOnly: true,
    sameSite: "lax",
    secure: process.env.NODE_ENV === "production",
    path: "/admin",
    maxAge: SESSION_TTL_MS / 1000,
  });
}

export async function loginWithOtpExchange(
  tokenRaw: string,
): Promise<{ ok: true } | { ok: false; message: string }> {
  const token = tokenRaw.trim();
  if (!token) return { ok: false, message: "Сначала введите код из письма" };
  let secret: string;
  try {
    secret = adminOtpSecret();
  } catch {
    return { ok: false, message: "В .env не задан ADMIN_OTP_SECRET" };
  }
  if (!verifyAdminOtpExchangeToken(token, secret)) {
    return { ok: false, message: "Код устарел. Запросите новый" };
  }
  await setAdminSession();
  return { ok: true };
}

export async function clearSession(): Promise<void> {
  const jar = await cookies();
  jar.delete(SESSION_COOKIE);
}

export async function adminEmail(): Promise<string | null> {
  const jar = await cookies();
  const token = jar.get(SESSION_COOKIE)?.value;
  if (!token || !verifySessionToken(token)) return null;
  return SESSION_LABEL;
}

export async function requireAdmin(): Promise<string> {
  const email = await adminEmail();
  if (!email) throw new Error("UNAUTHORIZED");
  return email;
}
