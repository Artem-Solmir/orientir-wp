/**
 * Builds the static site for Vercel into apps/web/out. Vercel serves files only: no PHP, no .htaccess,
 * so redirects and security headers go into out/vercel.json and the contact form is switched off.
 * Usage: npm run build:vercel   (PUBLIC_SITE_URL=https://<project>.vercel.app, VERCEL_NOINDEX=0 to allow indexing)
 */
import { spawnSync } from "node:child_process";
import { readdirSync, readFileSync, rmSync, statSync, writeFileSync } from "node:fs";
import { join } from "node:path";
import { PrismaClient } from "@prisma/client";
import { securityHeaders } from "../deploy/security-headers";

const ROOT = join(import.meta.dirname, "..");
const OUT = join(ROOT, "out");
const PUBLIC_URL = process.env.PUBLIC_SITE_URL ?? "https://orientir.vercel.app";
const SAFE_PATH = /^[A-Za-z0-9/_%.-]+$/;

type Redirect = { source: string; destination: string; permanent: true; has?: { type: "query"; key: string; value: string }[] };

export function legacyRedirect(from: string, to: string): Redirect[] {
  const path = from.replace(/^\/+|\/+$/g, "");
  if (!path || !SAFE_PATH.test(path) || !SAFE_PATH.test(to)) return [];
  return [`/${path}`, `/${path}/`].map((source) => ({ source, destination: to, permanent: true }));
}

/** Old shortlinks /?p=ID point to the migrated post. */
export function shortlinkRedirect(wpId: number, slug: string): Redirect[] {
  if (!Number.isInteger(wpId) || !/^[a-z0-9-]+$/.test(slug)) return [];
  return [{ source: "/", has: [{ type: "query", key: "p", value: String(wpId) }], destination: `/${slug}/`, permanent: true }];
}

export function vercelConfig(redirects: Redirect[], noindex: boolean) {
  const headers = securityHeaders(true);
  if (noindex) headers.push({ key: "X-Robots-Tag", value: "noindex, nofollow" });
  return { trailingSlash: true, cleanUrls: false, redirects, headers: [{ source: "/(.*)", headers }] };
}

async function redirects(): Promise<Redirect[]> {
  const db = new PrismaClient();
  try {
    const [legacy, posts] = await Promise.all([
      db.legacyRedirect.findMany(),
      db.post.findMany({ where: { legacyWpId: { not: null }, status: "published" }, select: { legacyWpId: true, slug: true } }),
    ]);
    return [...legacy.flatMap((r) => legacyRedirect(r.fromPath, r.toPath)), ...posts.flatMap((p) => shortlinkRedirect(p.legacyWpId!, p.slug))];
  } finally {
    await db.$disconnect();
  }
}

function listFiles(dir: string): string[] {
  return readdirSync(dir, { withFileTypes: true }).flatMap((e) => (e.isDirectory() ? listFiles(join(dir, e.name)) : [join(dir, e.name)]));
}

function problems(): string[] {
  const found: string[] = [];
  for (const f of listFiles(OUT)) {
    const rel = f.slice(OUT.length + 1).replaceAll("\\", "/");
    if (/\.(php|phtml|phar)$|^\.htaccess$/i.test(rel)) found.push(`file that must not ship to Vercel: ${rel}`);
    if (/\.(html|txt|xml|json)$/.test(rel) && /orientir\.local|localhost:3000/.test(readFileSync(f, "utf8"))) found.push(`local address leaked into: ${rel}`);
  }
  return found;
}

async function main() {
  rmSync(OUT, { recursive: true, force: true });
  const env = { ...process.env, NODE_ENV: "production" as const, APP_URL: PUBLIC_URL, NEXT_PUBLIC_FORM_DISABLED: "1" };
  if (spawnSync("npx", ["next", "build"], { cwd: ROOT, stdio: "inherit", shell: true, env }).status !== 0) throw new Error("next build failed");

  const config = vercelConfig(await redirects(), process.env.VERCEL_NOINDEX !== "0");
  writeFileSync(join(OUT, "vercel.json"), `${JSON.stringify(config, null, 2)}\n`);

  const issues = problems();
  const files = listFiles(OUT);
  process.stdout.write(`vercel build ready: ${files.length} files, ${(files.reduce((n, f) => n + statSync(f).size, 0) / 1e6).toFixed(1)} MB, ${config.redirects.length} redirects\n`);
  if (issues.length > 0) {
    process.stderr.write(`${issues.join("\n")}\n`);
    process.exitCode = 1;
  }
}

if (process.argv[1] && import.meta.filename === process.argv[1]) void main();