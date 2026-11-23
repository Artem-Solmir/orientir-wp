/**
 * Remove legacy WordPress files from production FTP (never touches wp-content/uploads).
 * Usage: npm run ftp:cleanup [-- --download | --apply]
 */
import { mkdirSync } from "node:fs";
import { dirname, join } from "node:path";
import { Client } from "basic-ftp";
import { BACKUP_ROOT, backupName } from "./backup-lib";
import { ftpRemoteRoot } from "./ftp-remote-root";

const DIRS = [
  "wp-admin",
  "wp-includes",
  "wp-content/plugins",
  "wp-content/themes",
  "wp-content/cache",
  "wp-content/w3tc-config",
  "wp-content/upgrade",
  "wp-content/languages",
  "wp-content/ai1wm-backups",
] as const;

const FILES = [
  "wp-config.php",
  "wp-login.php",
  "wp-load.php",
  "wp-settings.php",
  "wp-cron.php",
  "wp-mail.php",
  "wp-signup.php",
  "wp-activate.php",
  "wp-trackback.php",
  "wp-comments-post.php",
  "wp-blog-header.php",
  "wp-links-opml.php",
  "wp-config-sample.php",
  "xmlrpc.php",
  "readme.html",
  "license.txt",
  "index.php.old",
  ".htaccess.20251202183719",
  "wp-content/index.php",
  "wp-content/db.php",
  "wp-content/object-cache.php",
  "wp-content/advanced-cache.php",
] as const;

const ALLOWED = new Set<string>([...DIRS, ...FILES]);

const doDownload = process.argv.includes("--download");
const doApply = process.argv.includes("--apply");

function required(name: string): string {
  const v = process.env[name]?.trim();
  if (!v) throw new Error(`Set ${name} in .env`);
  return v;
}

function assertAllowed(rel: string): void {
  if (rel.includes("..")) throw new Error(`bad path: ${rel}`);
  if (rel.includes("wp-content/uploads")) throw new Error(`refusing uploads path: ${rel}`);
  if (!ALLOWED.has(rel)) throw new Error(`not on cleanup list: ${rel}`);
}

const RETRYABLE = new Set(["ECONNRESET", "ETIMEDOUT", "EPIPE", "ECONNABORTED"]);

function sleep(ms: number) {
  return new Promise((r) => setTimeout(r, ms));
}

async function connect(): Promise<Client> {
  const host = required("FTP_HOST");
  const user = required("FTP_USER");
  const pass = required("FTP_PASS");
  const secure = process.env.FTP_SECURE === "1";
  let lastErr: unknown;
  for (let attempt = 1; attempt <= 3; attempt++) {
    const client = new Client(300_000);
    client.ftp.ipFamily = 4;
    try {
      await client.access({ host, user, password: pass, secure });
      const { cd: remoteCd } = ftpRemoteRoot();
      if (remoteCd) await client.cd(remoteCd);
      return client;
    } catch (err) {
      lastErr = err;
      client.close();
      const code = typeof err === "object" && err !== null && "code" in err ? String((err as { code: string }).code) : "";
      if (!RETRYABLE.has(code) || attempt === 3) throw err;
      process.stdout.write(`сбой сети, повтор ${attempt}/3…\n`);
      await sleep(3000 * attempt);
    }
  }
  throw lastErr;
}

async function dirStats(client: Client, dir: string): Promise<{ files: number; bytes: number }> {
  let files = 0;
  let bytes = 0;
  const walk = async (rel: string) => {
    const items = await client.list(rel);
    for (const e of items) {
      const child = rel ? `${rel}/${e.name}` : e.name;
      if (e.isDirectory) await walk(child);
      else {
        files += 1;
        bytes += e.size;
      }
    }
  };
  await walk(dir);
  return { files, bytes };
}

type EntryStat = { path: string; kind: "dir" | "file"; files: number; bytes: number; missing: boolean };

async function measure(client: Client, path: string): Promise<EntryStat> {
  assertAllowed(path);
  try {
    if (DIRS.includes(path as (typeof DIRS)[number])) {
      const { files, bytes } = await dirStats(client, path);
      return { path, kind: "dir", files, bytes, missing: false };
    }
    const bytes = await client.size(path);
    return { path, kind: "file", files: 1, bytes, missing: false };
  } catch {
    return { path, kind: DIRS.includes(path as (typeof DIRS)[number]) ? "dir" : "file", files: 0, bytes: 0, missing: true };
  }
}

function fmtMb(n: number): string {
  return `${(n / 1048576).toFixed(2)} MB`;
}

async function preview(client: Client): Promise<EntryStat[]> {
  const stats: EntryStat[] = [];
  for (const p of DIRS) stats.push(await measure(client, p));
  for (const p of FILES) stats.push(await measure(client, p));
  return stats;
}

function printPreview(stats: EntryStat[]) {
  let totalFiles = 0;
  let totalBytes = 0;
  for (const s of stats) {
    if (s.missing) {
      process.stdout.write(`  (нет) ${s.path}\n`);
      continue;
    }
    totalFiles += s.files;
    totalBytes += s.bytes;
    const label = s.kind === "dir" ? `[dir] ${s.files} файлов` : "[file]";
    process.stdout.write(`  ${label} ${s.path}  ${fmtMb(s.bytes)}\n`);
  }
  process.stdout.write(`Итого: ${totalFiles} файлов, ${fmtMb(totalBytes)}\n`);
}

async function downloadAll(client: Client, localRoot: string): Promise<number> {
  mkdirSync(localRoot, { recursive: true });
  let count = 0;
  const pullDir = async (remote: string, local: string) => {
    mkdirSync(local, { recursive: true });
    await client.downloadToDir(local, remote);
    count += (await dirStats(client, remote)).files;
  };
  for (const p of DIRS) {
    assertAllowed(p);
    await pullDir(p, join(localRoot, p));
  }
  for (const p of FILES) {
    assertAllowed(p);
    const local = join(localRoot, p);
    mkdirSync(dirname(local), { recursive: true });
    try {
      await client.downloadTo(local, p);
      count += 1;
    } catch {
      process.stdout.write(`  пропуск (нет на сервере): ${p}\n`);
    }
  }
  return count;
}

async function applyDeletes(client: Client) {
  for (const p of FILES) {
    assertAllowed(p);
    try {
      await client.remove(p);
      process.stdout.write(`  удалён файл ${p}\n`);
    } catch {
      process.stdout.write(`  пропуск (нет): ${p}\n`);
    }
  }
  for (const p of [...DIRS].reverse()) {
    assertAllowed(p);
    try {
      await client.removeDir(p);
      process.stdout.write(`  удалена папка ${p}\n`);
    } catch {
      process.stdout.write(`  пропуск (нет): ${p}\n`);
    }
  }
}

async function main() {
  if (doDownload && doApply) throw new Error("Use either --download or --apply");
  const client = await connect();
  try {
    const stats = await preview(client);
    if (!doDownload && !doApply) {
      process.stdout.write("План удаления (WordPress, без uploads):\n");
      printPreview(stats);
      process.stdout.write("Скачать копию: npm run ftp:cleanup -- --download\n");
      process.stdout.write("Удалить на сервере: npm run ftp:cleanup -- --apply\n");
      return;
    }
    if (doDownload) {
      const localRoot = join(BACKUP_ROOT, `server-cleanup-${backupName(new Date())}`);
      process.stdout.write(`Скачивание в ${localRoot}…\n`);
      const serverFiles = stats.filter((s) => !s.missing).reduce((n, s) => n + s.files, 0);
      const got = await downloadAll(client, localRoot);
      process.stdout.write(`Скачано файлов: ${got} (на сервере в списке: ${serverFiles})\n`);
      printPreview(stats);
      return;
    }
    process.stdout.write("Удаление по списку…\n");
    await applyDeletes(client);
    process.stdout.write("Готово.\n");
  } finally {
    client.close();
  }
}

main().catch((e) => {
  console.error(e instanceof Error ? e.message : e);
  process.exitCode = 1;
});