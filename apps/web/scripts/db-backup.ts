/**
 * pg_dump of the local docker Postgres into migration/private/backups (git-ignored).
 * Usage: npm run db:backup
 */
import { execFileSync } from "node:child_process";
import { mkdirSync, writeFileSync } from "node:fs";
import { resolve } from "node:path";

export const SERVICE = "postgres";

export function compose(args: string[], input?: Buffer): Buffer {
  return execFileSync("docker", ["compose", ...args], {
    input,
    maxBuffer: 1024 * 1024 * 1024,
    stdio: ["pipe", "pipe", "inherit"],
  });
}

export function backupToFile(): string {
  const dir = resolve(process.cwd(), "../../migration/private/backups");
  mkdirSync(dir, { recursive: true });
  const stamp = new Date().toISOString().replace(/[:.]/g, "-");
  const file = resolve(dir, `orientir-${stamp}.dump`);
  // Credentials stay inside the container environment; nothing secret on our argv.
  const dump = compose([
    "exec", "-T", SERVICE, "sh", "-c",
    'pg_dump -U "$POSTGRES_USER" -d "$POSTGRES_DB" --format=custom --no-owner',
  ]);
  writeFileSync(file, dump);
  return file;
}

if (process.argv[1]?.endsWith("db-backup.ts")) {
  console.log(`backup written: ${backupToFile()}`);
}
