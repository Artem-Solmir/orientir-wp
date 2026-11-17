/**
 * Uploads apps/web/content to private/content/ on the host (for online admin).
 * Usage: npm run content:push-private [-- --dry-run]
 */
import { existsSync, readdirSync } from "node:fs";
import { join, relative } from "node:path";
import { Client } from "basic-ftp";
import { ftpRemoteRoot } from "./ftp-remote-root";

const ROOT = join(import.meta.dirname, "..");
const CONTENT = join(ROOT, "content");
const dryRun = process.argv.includes("--dry-run");

function required(name: string): string {
  const v = process.env[name]?.trim();
  if (!v) throw new Error(`Set ${name} in .env`);
  return v;
}

function listFiles(dir: string, base = dir): string[] {
  return readdirSync(dir, { withFileTypes: true }).flatMap((e) => {
    const full = join(dir, e.name);
    return e.isDirectory() ? listFiles(full, base) : [relative(base, full).replaceAll("\\", "/")];
  });
}

async function main() {
  if (!existsSync(CONTENT)) throw new Error("content/ missing");
  const files = listFiles(CONTENT).filter((f) => !f.includes("audit.log"));
  const host = required("FTP_HOST");
  const user = required("FTP_USER");
  const pass = required("FTP_PASS");
  const { cd: siteRoot } = ftpRemoteRoot();
  const root = siteRoot ?? "/";

  if (dryRun) {
    process.stdout.write(`dry-run: ${files.length} files -> private/content/\n`);
    return;
  }

  const secure = process.env.FTP_SECURE === "1";
  const client = new Client(300_000);
  client.ftp.ipFamily = 4;
  try {
    await client.access({ host, user, password: pass, secure });
    await client.cd(root);
    await client.uploadFromDir(CONTENT, "private/content");
    process.stdout.write(`uploaded ${files.length} content files to private/content/\n`);
  } finally {
    client.close();
  }
}

main().catch((e) => {
  console.error(e);
  process.exitCode = 1;
});
