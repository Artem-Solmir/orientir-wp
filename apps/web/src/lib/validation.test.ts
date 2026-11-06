import { describe, expect, it } from "vitest";
import { loginSchema, registerSchema } from "./validation";

describe("registerSchema", () => {
  const valid = { email: "  User@Example.COM ", name: " Ann ", password: "1234567890" };

  it("normalizes email and trims name", () => {
    const parsed = registerSchema.parse(valid);
    expect(parsed.email).toBe("user@example.com");
    expect(parsed.name).toBe("Ann");
  });

  it("rejects passwords shorter than 10 chars", () => {
    expect(registerSchema.safeParse({ ...valid, password: "123456789" }).success).toBe(false);
  });

  it("reports a human-readable message for a short password", () => {
    const result = registerSchema.safeParse({ ...valid, password: "short" });
    expect(result.success ? "" : result.error.issues[0]?.message).toBe("Минимум 10 символов");
  });

  it("accepts 10 and rejects 129 chars (bounds argon2 work)", () => {
    expect(registerSchema.safeParse({ ...valid, password: "a".repeat(10) }).success).toBe(true);
    expect(registerSchema.safeParse({ ...valid, password: "a".repeat(128) }).success).toBe(true);
    expect(registerSchema.safeParse({ ...valid, password: "a".repeat(129) }).success).toBe(false);
  });

  it("rejects invalid email and too-short name", () => {
    expect(registerSchema.safeParse({ ...valid, email: "nope" }).success).toBe(false);
    expect(registerSchema.safeParse({ ...valid, name: "A" }).success).toBe(false);
  });
});

describe("loginSchema", () => {
  it("does not enforce password policy on login", () => {
    expect(loginSchema.safeParse({ email: "a@b.co", password: "x" }).success).toBe(true);
  });

  it("rejects empty password and oversize password", () => {
    expect(loginSchema.safeParse({ email: "a@b.co", password: "" }).success).toBe(false);
    expect(loginSchema.safeParse({ email: "a@b.co", password: "a".repeat(129) }).success).toBe(false);
  });
});
