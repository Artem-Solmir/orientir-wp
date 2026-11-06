import { describe, expect, it } from "vitest";
import { isValidAgentToken } from "./agent-auth";

const token = "t".repeat(40);

describe("isValidAgentToken", () => {
  it("accepts the exact bearer token", () => {
    expect(isValidAgentToken(`Bearer ${token}`, token)).toBe(true);
  });

  it.each([
    [null],
    [""],
    [token],
    [`bearer ${token}`],
    [`Bearer ${token}x`],
    [`Bearer ${token.slice(1)}`],
    [`Bearer  ${token}`],
    ["Bearer short"],
    [`Basic ${token}`],
  ])("rejects %s", (header) => {
    expect(isValidAgentToken(header, token)).toBe(false);
  });
});
