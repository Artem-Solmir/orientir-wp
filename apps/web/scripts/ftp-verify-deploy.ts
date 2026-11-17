/** Checks key paths on FTP vs local out/. Usage: npx tsx --env-file=.env scripts/ftp-verify-deploy.ts */
import { existsSync, readdirSync, statSync } from "node:fs";
import { join } from "node:path";
import { Client } from "basic-ftp";
import { ftpRemoteRoot } from "./ftp-remote-root";

const ROOT = join(import.meta.dirname, "..");
const OUT = join(ROOT, "out");

const CHECKS = [
  "index.html",
  ".htaccess",
  "admin/login/index.html",
  "admin/index.html",
  "admin/posts/new/index.html",
  "materials/index.html",
  "send.php",
  "admin-api.php",
  "admin-otp.php",
  "admin-session.php",
  "_next/static/chunks",
  "private/orientir-send-config.php",
  "private/orientir-admin-otp-config.php",
  "private/content/categories.json",
] as const;

function localCount(dir: string): number {
  return readdirSync(dir, { withFileTypes: true }).reduce((n, e) => {
    const full = join(dir, e.name);
    return n + (e.isDirectory() ? localCount(full) : 1);
  }, 0);
}

function localBytes(rel: string): number | null {
  const local = join(OUT, rel);
  if (!existsSync(local)) return null;
  return statSync(local).isDirectory() ? -1 : statSync(local).size;
}

async function remoteChunks(client: Client, siteRoot: string | null): Promise<number | null> {
  try {
    await client.cd("_next/static/chunks");
    const n = (await client.list()).filter((e) => !e.isDirectory).length;
    await client.cd(siteRoot ?? "/");
    return n;
  } catch {
    return null;
  }
}

async function remoteFile(client: Client, rel: string): Promise<number | null> {
  try {
    return await client.size(rel);
  } catch {
    return null;
  }
}

async function checkPath(client: Client, rel: string, siteRoot: string | null): Promise<boolean> {
  const remote = rel.endsWith("chunks") ? await remoteChunks(client, siteRoot) : await remoteFile(client, rel);
  const status = remote !== null ? "есть" : "нет";
  const extra = typeof remote === "number" && remote >= 0 ? ` (${remote} б/файлов)` : "";
  process.stdout.write(`${rel}: сервер ${status}${extra}\n`);
  const local = localBytes(rel);
  if (local !== null && local > 0 && remote === null) {
    process.stdout.write(`  → локально есть (${local} б), на сервер не попало\n`);
  }
  return remote !== null;
}

function ftpCreds(): { host: string; user: string; pass: string } {
  const host = process.env.FTP_HOST?.trim();
  const user = process.env.FTP_USER?.trim();
  const pass = process.env.FTP_PASS?.trim();
  if (!host || !user || !pass) throw new Error("FTP_* in .env");
  return { host, user, pass };
}

async function main() {
  const { host, user, pass } = ftpCreds();
  process.stdout.write(`локально out/: ${localCount(OUT)} файлов\n`);

  const client = new Client(60_000);
  client.ftp.ipFamily = 4;
  const { cd } = ftpRemoteRoot();
  await client.access({ host, user, password: pass, secure: process.env.FTP_SECURE === "1" });
  if (cd) await client.cd(cd);

  let ok = 0;
  for (const rel of CHECKS) {
    if (await checkPath(client, rel, cd)) ok += 1;
  }
  client.close();
  process.stdout.write(`проверено важных путей: ${ok}/${CHECKS.length}\n`);
  if (ok < CHECKS.length) process.exitCode = 1;
}

main().catch((e) => {
  console.error(e);
  process.exitCode = 1;
});