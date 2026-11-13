import { describe, expect, it } from "vitest";
import { ftpRemoteRoot } from "./ftp-remote-root";

describe("ftpRemoteRoot", () => {
  it("stays on login home for / and .", () => {
    expect(ftpRemoteRoot("/")).toEqual({ cd: null, label: "/" });
    expect(ftpRemoteRoot(".")).toEqual({ cd: null, label: "/" });
    expect(ftpRemoteRoot("")).toEqual({ cd: null, label: "/" });
  });

  it("cd into nested path without trailing slash", () => {
    expect(ftpRemoteRoot("/www/orientir.by/")).toEqual({ cd: "/www/orientir.by", label: "/www/orientir.by" });
  });
});
