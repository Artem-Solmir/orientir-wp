import "server-only";

export function adminEnabled(): boolean {
  return process.env.ENABLE_ADMIN === "1";
}

/** Local admin password; default 1111 only in non-production when unset. */
export function adminPassword(): string {
  const p = process.env.ADMIN_PASSWORD?.trim();
  if (p) return p;
  if (adminEnabled() && process.env.NODE_ENV !== "production") return "1111";
  throw new Error("ADMIN_PASSWORD must be set when ENABLE_ADMIN=1 in production");
}

export function sessionSecret(): string {
  const s = process.env.ADMIN_SESSION_SECRET;
  if (!s || s.length < 32) {
    if (adminEnabled() && process.env.NODE_ENV !== "production") return "dev-only-admin-session-secret-32chars!!";
    throw new Error("ADMIN_SESSION_SECRET must be at least 32 characters when ENABLE_ADMIN=1");
  }
  return s;
}
