import { describe, expect, it, vi } from "vitest";
import { parseEnv } from "./env";

const good = {
  DATABASE_URL: "postgresql://u:p@127.0.0.1:5432/db",
  AGENT_API_TOKEN: "t".repeat(32),
  APP_URL: "http://localhost:3000",
};

describe("parseEnv", () => {
  it("accepts a valid configuration", () => {
    expect(parseEnv(good).APP_URL).toBe("http://localhost:3000");
  });

  it("rejects weak secrets and names the key without leaking its value", () => {
    const weak = "short-secret-value";
    expect(() => parseEnv({ ...good, AGENT_API_TOKEN: weak })).toThrowError(/AGENT_API_TOKEN/);
    expect(() => parseEnv({ ...good, AGENT_API_TOKEN: weak })).not.toThrowError(new RegExp(weak));
  });

  it("rejects a missing database url", () => {
    expect(() => parseEnv({ ...good, DATABASE_URL: undefined })).toThrowError(/DATABASE_URL/);
  });

  it("lists every invalid key once, comma separated", () => {
    expect(() => parseEnv({ ...good, AGENT_API_TOKEN: "x", DATABASE_URL: "bad" })).toThrowError(
      "Invalid environment configuration: DATABASE_URL, AGENT_API_TOKEN",
    );
  });

  it("env() reads process.env once and caches the result", async () => {
    for (const [k, v] of Object.entries(good)) vi.stubEnv(k, v);
    vi.resetModules();
    const { env } = await import("./env");
    const first = env();
    vi.stubEnv("APP_URL", "http://changed.example");
    expect(env()).toBe(first);
    vi.unstubAllEnvs();
  });

  it("accepts secrets of exactly 32 chars and rejects 31", () => {
    expect(() => parseEnv({ ...good, AGENT_API_TOKEN: "t".repeat(31) })).toThrowError(/AGENT_API_TOKEN/);
    expect(parseEnv({ ...good, AGENT_API_TOKEN: "t".repeat(32) }).AGENT_API_TOKEN).toHaveLength(32);
  });
});
