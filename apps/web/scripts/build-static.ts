/**
 * Builds the static site into apps/web/out and prepares it for upload:
 * next build (export) -> .htaccess (headers, HTTPS, legacy redirects) -> send.php -> sanity checks.
 * Usage: npm run build:static
 */
import { spawnSync } from "node:child_process";
import { copyFileSync, existsSync, readdirSync, readFileSync, statSync, writeFileSync } from "node:fs";
import { join } from "node:path";
import { legacyRedirects, wpShortlinks } from "../src/lib/store/redirects";
import { securityHeaders } from "../deploy/security-headers";
import { hideAdminRoutes, restoreAdminRoutes } from "./admin-export";

const ROOT = join(import.meta.dirname, "..");
const OUT = join(ROOT, "out");
// Canonical URLs, sitemap and feed are baked into the HTML, so the production address is fixed here.
const PUBLIC_URL = process.env.PUBLIC_SITE_URL ?? "https://orientir.by";
const SAFE_PATH = /^[A-Za-z0-9/_%.-]+$/;

export function escapeRegex(path: string): string {
  return path.replace(/[.*+?^${}()|[\]\\]/g, "\\$&");
}

/** One 301 per legacy path; anything with unexpected characters is skipped, never interpolated. */
export function redirectRule(from: string, to: string): string | null {
  const path = from.replace(/^\/+|\/+$/g, "");
  if (!path || !SAFE_PATH.test(path) || !SAFE_PATH.test(to)) return null;
  return `RewriteRule ^${escapeRegex(path)}/?$ ${to} [R=301,L]`;
}

/** Old shortlinks /?p=ID point to the migrated post. */
export function shortlinkRule(wpId: number, slug: string): string | null {
  if (!Number.isInteger(wpId) || !/^[a-z0-9-]+$/.test(slug)) return null;
  return `RewriteCond %{QUERY_STRING} (?:^|&)p=${wpId}(?:&|$)\nRewriteRule ^(?:index\\.php)?$ /${slug}/? [R=301,L]`;
}

export function headerLines(): string {
  return securityHeaders(true)
    .map((h) => `  Header always set ${h.key} "${h.value.replace(/"/g, '\\"')}"`)
    .join("\n");
}

function buildRedirects(): string {
  const rules = [
    ...legacyRedirects().map((r) => redirectRule(r.from, r.to)),
    ...wpShortlinks().map((p) => shortlinkRule(p.wpId, p.slug)),
  ];
  return rules.filter((r): r is string => r !== null).join("\n");
}

function listFiles(dir: string): string[] {
  return readdirSync(dir, { withFileTypes: true }).flatMap((e) => (e.isDirectory() ? listFiles(join(dir, e.name)) : [join(dir, e.name)]));
}

function sanityChecks(): string[] {
  const files = listFiles(OUT);
  const problems: string[] = [];
  for (const f of files) {
    const rel = f.slice(OUT.length + 1).replaceAll("\\", "/");
    if (/\.(php|phtml|phar)$/i.test(rel) && rel !== "send.php" && rel !== "send-config.php") problems.push(`unexpected executable file: ${rel}`);
    if (/\.(html|txt|xml|json)$/.test(rel) && /orientir\.local|localhost:3000/.test(readFileSync(f, "utf8"))) {
      problems.push(`local address leaked into: ${rel}`);
    }
  }
  if (!files.some((f) => f.endsWith("404.html"))) problems.push("404.html is missing");
  return problems;
}

async function main() {
  hideAdminRoutes();
  try {
    const build = spawnSync("npx", ["next", "build"], {
      cwd: ROOT,
      stdio: "inherit",
      shell: true,
      env: { ...process.env, NODE_ENV: "production", APP_URL: PUBLIC_URL, ENABLE_ADMIN: "0" },
    });
    if (build.status !== 0) throw new Error("next build failed");
  } finally {
    restoreAdminRoutes();
  }

  const template = readFileSync(join(ROOT, "deploy", "htaccess.template"), "utf8");
  const htaccess = template.replace("__HEADERS__", headerLines()).replace("__REDIRECTS__", buildRedirects());
  writeFileSync(join(OUT, ".htaccess"), htaccess);

  copyFileSync(join(ROOT, "deploy", "send.php"), join(OUT, "send.php"));
  const config = join(ROOT, "deploy", "send-config.php");
  if (existsSync(config)) copyFileSync(config, join(OUT, "send-config.php"));
  else process.stdout.write("WARNING: deploy/send-config.php is missing, the contact form will answer 500 until it is added\n");

  const problems = sanityChecks();
  const size = listFiles(OUT).reduce((sum, f) => sum + statSync(f).size, 0);
  process.stdout.write(`static site ready: ${listFiles(OUT).length} files, ${(size / 1e6).toFixed(1)} MB in ${OUT}\n`);
  if (problems.length > 0) {
    process.stderr.write(`${problems.join("\n")}\n`);
    process.exitCode = 1;
  }
}

if (process.argv[1] && import.meta.filename === process.argv[1]) void main();
