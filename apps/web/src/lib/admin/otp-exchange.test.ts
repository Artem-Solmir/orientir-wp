import { createHmac } from "node:crypto";
import { describe, expect, it } from "vitest";
import { verifyAdminOtpExchangeToken } from "./otp-exchange";

const secret = "test-admin-otp-secret-32-characters!!";

function makeToken(expMs: number): string {
  const body = `admin-otp:${expMs}`;
  const bodyB64 = Buffer.from(body, "utf8").toString("base64url");
  const sigB64 = createHmac("sha256", secret).update(body).digest("base64url");
  return `${bodyB64}.${sigB64}`;
}

describe("verifyAdminOtpExchangeToken", () => {
  it("accepts a fresh token", () => {
    const token = makeToken(Date.now() + 60_000);
    expect(verifyAdminOtpExchangeToken(token, secret)).toBe(true);
  });

  it("rejects expired token", () => {
    const token = makeToken(Date.now() - 1000);
    expect(verifyAdminOtpExchangeToken(token, secret)).toBe(false);
  });

  it("rejects wrong secret", () => {
    const token = makeToken(Date.now() + 60_000);
    expect(verifyAdminOtpExchangeToken(token, secret + "x")).toBe(false);
  });
});
