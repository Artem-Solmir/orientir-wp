import { describe, expect, it } from "vitest";
import { backupName, expiredBackups, isSkipped, sortBackups } from "./backup-lib";

describe("isSkipped", () => {
  it("skips dependencies, build output and secrets", () => {
    for (const p of ["node_modules", "node_modules/next/x.js", ".next", "out/index.html", ".env", "tsconfig.tsbuildinfo"]) {
      expect(isSkipped(p)).toBe(true);
    }
  });

  it("skips the audit log but keeps content", () => {
    expect(isSkipped("content/audit.log")).toBe(true);
    expect(isSkipped("content\\audit.log.1")).toBe(true);
    expect(isSkipped("content/posts/a.json")).toBe(false);
  });

  it("keeps sources and config", () => {
    for (const p of ["", "src/app/page.tsx", ".env.example", "package.json", "deploy/security-headers.ts"]) {
      expect(isSkipped(p)).toBe(false);
    }
  });
});

describe("backup names", () => {
  it("formats local date-time sortably", () => {
    expect(backupName(new Date(2026, 10, 3, 9, 5, 7))).toBe("2026-11-03_09-05-07");
    expect(backupName(new Date(2026, 10, 3, 9, 5, 7), "pered-otkatom")).toBe("2026-11-03_09-05-07_pered-otkatom");
  });

  it("ignores foreign folders and sorts oldest first", () => {
    expect(sortBackups(["notes", "2026-11-03_10-00-00", "2026-11-02_10-00-00_pered-otkatom"])).toEqual([
      "2026-11-02_10-00-00_pered-otkatom",
      "2026-11-03_10-00-00",
    ]);
  });

  it("expires everything beyond the newest N", () => {
    const names = ["2026-01-03_00-00-00", "2026-01-01_00-00-00", "2026-01-02_00-00-00"];
    expect(expiredBackups(names, 2)).toEqual(["2026-01-01_00-00-00"]);
    expect(expiredBackups(names, 5)).toEqual([]);
  });
});
