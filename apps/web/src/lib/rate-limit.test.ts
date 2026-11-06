import { describe, expect, it } from "vitest";
import { rateLimitKey } from "./rate-limit";

describe("rateLimitKey", () => {
  it("is deterministic and does not contain the raw identifier", () => {
    const key = rateLimitKey("login-email", "victim@example.com");
    expect(key).toBe(rateLimitKey("login-email", "victim@example.com"));
    expect(key).not.toContain("victim");
    expect(key).toMatch(/^[0-9a-f]{64}$/);
  });

  it("separates namespaces by kind and identifiers by value", () => {
    expect(rateLimitKey("a", "x")).not.toBe(rateLimitKey("b", "x"));
    expect(rateLimitKey("a", "x")).not.toBe(rateLimitKey("a", "y"));
  });
});
