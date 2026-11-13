/**
 * Uploads apps/web/out to production FTP (does not touch wp-content/).
 * Usage: npm run build:static && npm run deploy:ftp [-- --dry-run]
 */
import { createReadStream, existsSync, readdirSync, statSync } from "node:fs";
import { join, relative } from "node:path";
import { Client } from "basic-ftp";
import { ftpRemoteRoot } from "./ftp-remote-root";

const ROOT = join(import.meta.dirname, "..");
const OUT = join(ROOT, "out");
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
  for (const requiredFile of ["index.html", ".htaccess"]) {
    if (!existsSync(join(OUT, requiredFile))) throw new Error(`out/${requiredFile} missing — run npm run build:static first`);
  }

  const host = required("FTP_HOST");
  const user = required("FTP_USER");
  const pass = required("FTP_PASS");
  const { cd: remoteCd, label: remoteLabel } = ftpRemoteRoot();
  const files = listFiles(OUT);

  if (dryRun) {
    process.stdout.write(`dry-run: ${files.length} files -> ${remoteLabel}\n`);
    return;
  }

  const client = new Client(60_000);
  client.ftp.ipFamily = 4;
  try {
    await client.access({ host, user, password: pass, secure: process.env.FTP_SECURE === "1" });
    if (remoteCd) await client.cd(remoteCd);
    for (const rel of files) {
      if (rel.startsWith("wp-content/")) {
        throw new Error(`refusing to upload ${rel} — wp-content is managed on the server`);
      }
      const local = join(OUT, rel);
      const remoteDir = rel.includes("/") ? rel.slice(0, rel.lastIndexOf("/")) : "";
      if (remoteDir) await client.ensureDir(remoteDir);
      const st = statSync(local);
      if (st.isFile()) {
        await client.uploadFrom(createReadStream(local), rel);
      }
    }
    process.stdout.write(`uploaded ${files.length} files to ${host}:${remoteLabel}\n`);
  } finally {
    client.close();
  }
}

main().catch((e) => {
  console.error(e);
  process.exitCode = 1;
});
