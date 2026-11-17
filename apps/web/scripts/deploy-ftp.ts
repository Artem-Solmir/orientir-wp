/**
 * Uploads apps/web/out to production FTP (does not touch wp-content/).
 * Usage: npm run build:static && npm run deploy:ftp [-- --dry-run] [-- --only=_next] [-- --paths=send.php,materials]
 */
import { createReadStream, existsSync, readdirSync, statSync } from "node:fs";
import { join, relative } from "node:path";
import { Client } from "basic-ftp";
import { ftpRemoteRoot } from "./ftp-remote-root";

const ROOT = join(import.meta.dirname, "..");
const OUT = join(ROOT, "out");
const dryRun = process.argv.includes("--dry-run");
const onlyArg = process.argv.find((a) => a.startsWith("--only="));
const onlySubdir = onlyArg?.slice("--only=".length).replace(/\\/g, "/").replace(/^\/+|\/+$/g, "");
const pathsArg = process.argv.find((a) => a.startsWith("--paths="));
const pathList = pathsArg?.slice("--paths=".length).split(",").map((p) => p.trim().replace(/\\/g, "/")).filter(Boolean);

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

const RETRYABLE = new Set(["ECONNRESET", "ETIMEDOUT", "EPIPE", "ECONNABORTED"]);

function sleep(ms: number) {
  return new Promise((r) => setTimeout(r, ms));
}

function isRetryable(err: unknown): boolean {
  return typeof err === "object" && err !== null && "code" in err && RETRYABLE.has(String((err as { code: string }).code));
}

function assertOutReady() {
  for (const requiredFile of ["index.html", ".htaccess"]) {
    if (!existsSync(join(OUT, requiredFile))) throw new Error(`out/${requiredFile} missing — run npm run build:static first`);
  }
}

function refuseWpContentInOut() {
  if (onlySubdir || pathList?.length) return;
  if (listFiles(OUT).some((rel) => rel.startsWith("wp-content/"))) {
    throw new Error("out contains wp-content — refusing to upload");
  }
}

function resolveListedFiles(): { uploadLocal: string; listedFiles: string[] } {
  if (onlySubdir && pathList?.length) throw new Error("Use either --only= or --paths=, not both");
  const uploadLocal = onlySubdir ? join(OUT, onlySubdir) : OUT;
  if (onlySubdir && !existsSync(uploadLocal)) throw new Error(`out/${onlySubdir} missing — run npm run build:static`);
  const listedFiles = pathList?.length ? pathList.map(resolveOnePath) : listFiles(uploadLocal, uploadLocal);
  refuseWpContentInOut();
  return { uploadLocal, listedFiles };
}

function resolveOnePath(rel: string): string {
  if (rel.includes("..")) throw new Error(`bad path: ${rel}`);
  const local = join(OUT, rel);
  if (!existsSync(local)) throw new Error(`out/${rel} missing — run npm run build:static`);
  return rel;
}

async function uploadRel(client: Client, siteRoot: string, rel: string) {
  const local = join(OUT, rel);
  if (statSync(local).isDirectory()) {
    await client.uploadFromDir(local, rel);
    return;
  }
  const parts = rel.split("/");
  const fileName = parts.pop()!;
  const remoteDir = parts.join("/");
  await client.cd(siteRoot);
  if (remoteDir) await client.ensureDir(remoteDir);
  await client.uploadFrom(createReadStream(local), fileName);
}

type UploadPlan = { uploadLocal: string; listedFiles: string[]; usePaths: boolean };

async function doUpload(client: Client, siteRoot: string, plan: UploadPlan) {
  const { uploadLocal, listedFiles, usePaths } = plan;
  if (usePaths) {
    for (const rel of listedFiles) await uploadRel(client, siteRoot, rel);
    return;
  }
  if (onlySubdir) await client.uploadFromDir(uploadLocal, onlySubdir!);
  else await client.uploadFromDir(OUT);
}

async function tryUploadOnce(
  client: Client,
  host: string,
  user: string,
  pass: string,
  siteRoot: string,
  plan: UploadPlan,
) {
  const secure = process.env.FTP_SECURE === "1";
  await client.access({ host, user, password: pass, secure });
  await client.cd(siteRoot);
  console.log(
    `заливка ${plan.listedFiles.length} ${plan.usePaths ? "путей" : "файлов"}${onlySubdir ? ` (out/${onlySubdir}/)` : plan.usePaths ? "" : " (out/)"}…`,
  );
  await doUpload(client, siteRoot, plan);
}

async function uploadWithRetry(host: string, user: string, pass: string, siteRoot: string, plan: UploadPlan): Promise<void> {
  let client = new Client(300_000);
  client.ftp.ipFamily = 4;
  let lastErr: unknown;
  for (let attempt = 1; attempt <= 3; attempt++) {
    try {
      await tryUploadOnce(client, host, user, pass, siteRoot, plan);
      client.close();
      return;
    } catch (err) {
      lastErr = err;
      client.close();
      if (!isRetryable(err) || attempt === 3) throw err;
      console.log(`сбой сети, повтор ${attempt}/3…`);
      await sleep(2000 * attempt);
      client = new Client(300_000);
      client.ftp.ipFamily = 4;
    }
  }
  throw lastErr;
}

async function main() {
  assertOutReady();
  const host = required("FTP_HOST");
  const user = required("FTP_USER");
  const pass = required("FTP_PASS");
  const { cd: remoteCd, label: remoteLabel } = ftpRemoteRoot();
  const { uploadLocal, listedFiles } = resolveListedFiles();

  if (dryRun) {
    const target = onlySubdir ? `${remoteLabel}${onlySubdir}/` : remoteLabel;
    process.stdout.write(`dry-run: ${listedFiles.length} paths -> ${target}\n`);
    return;
  }

  const siteRoot = remoteCd ?? "/";
  const plan: UploadPlan = { uploadLocal, listedFiles, usePaths: Boolean(pathList?.length) };
  await uploadWithRetry(host, user, pass, siteRoot, plan);
  const label = onlySubdir ? `${remoteLabel}${onlySubdir}/` : remoteLabel;
  process.stdout.write(`uploaded ${listedFiles.length} to ${host}:${label}\n`);
}

main().catch((e) => {
  console.error(e);
  process.exitCode = 1;
});
