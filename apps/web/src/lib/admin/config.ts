import "server-only";

export function adminEnabled(): boolean {
  if (process.env.NEXT_PUBLIC_ADMIN_ENABLED === "1") return true;
  return process.env.ENABLE_ADMIN === "1";
}

/** Shared with deploy/admin-otp.php (private config on the host). */
export function adminOtpSecret(): string {
  const s = process.env.ADMIN_OTP_SECRET?.trim();
  if (s && s.length >= 32) return s;
  if (adminEnabled() && process.env.NODE_ENV !== "production") {
    return "dev-only-admin-otp-secret-32-chars!!!!";
  }
  throw new Error("ADMIN_OTP_SECRET must be at least 32 characters when ENABLE_ADMIN=1");
}

export function adminLoginEmailHint(): string {
  return process.env.NEXT_PUBLIC_ADMIN_LOGIN_EMAIL?.trim() || "email не указан в настройках";
}

export function adminOtpEndpoint(): string {
  return process.env.NEXT_PUBLIC_ADMIN_OTP_URL?.trim() || "https://orientir.by/admin-otp.php";
}

export function sessionSecret(): string {
  const s = process.env.ADMIN_SESSION_SECRET;
  if (!s || s.length < 32) {
    if (adminEnabled() && process.env.NODE_ENV !== "production") return "dev-only-admin-session-secret-32chars!!";
    throw new Error("ADMIN_SESSION_SECRET must be at least 32 characters when ENABLE_ADMIN=1");
  }
  return s;
}
