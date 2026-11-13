import "server-only";
import { timingSafeEqual } from "node:crypto";
import { cookies } from "next/headers";
import { db } from "@/lib/db";
import { adminPassword, sessionSecret } from "./config";
import { randomToken, sha256 } from "./crypto";

const SESSION_TTL_MS = 12 * 60 * 60 * 1000;
const SESSION_LABEL = "admin";
export const SESSION_COOKIE = "orientir_admin";

function passwordsMatch(given: string, expected: string): boolean {
  const a = Buffer.from(given);
  const b = Buffer.from(expected);
  if (a.length !== b.length) return false;
  return timingSafeEqual(a, b);
}

export async function loginWithPassword(passwordRaw: string): Promise<{ ok: true } | { ok: false; message: string }> {
  const given = passwordRaw.trim();
  if (!given) return { ok: false, message: "Введите пароль" };
  let expected: string;
  try {
    expected = adminPassword();
  } catch {
    return { ok: false, message: "Пароль админки не настроен (ADMIN_PASSWORD)" };
  }
  if (!passwordsMatch(given, expected)) return { ok: false, message: "Неверный пароль" };

  const token = randomToken();
  await db.adminSession.create({
    data: { email: SESSION_LABEL, tokenHash: sha256(`${token}:${sessionSecret()}`), expiresAt: new Date(Date.now() + SESSION_TTL_MS) },
  });
  const jar = await cookies();
  jar.set(SESSION_COOKIE, token, {
    httpOnly: true,
    sameSite: "lax",
    secure: process.env.NODE_ENV === "production",
    path: "/admin",
    maxAge: SESSION_TTL_MS / 1000,
  });
  return { ok: true };
}

export async function clearSession(): Promise<void> {
  const jar = await cookies();
  const token = jar.get(SESSION_COOKIE)?.value;
  jar.delete(SESSION_COOKIE);
  if (token) await db.adminSession.deleteMany({ where: { tokenHash: sha256(`${token}:${sessionSecret()}`) } });
}

export async function adminEmail(): Promise<string | null> {
  const jar = await cookies();
  const token = jar.get(SESSION_COOKIE)?.value;
  if (!token) return null;
  const row = await db.adminSession.findFirst({
    where: { tokenHash: sha256(`${token}:${sessionSecret()}`), expiresAt: { gt: new Date() } },
  });
  return row?.email ?? null;
}

export async function requireAdmin(): Promise<string> {
  const email = await adminEmail();
  if (!email) throw new Error("UNAUTHORIZED");
  return email;
}
