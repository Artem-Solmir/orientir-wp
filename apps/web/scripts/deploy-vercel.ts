/**
 * Uploads apps/web/out to the Vercel project from deploy/vercel-project.json (ids only, no secrets;
 * authentication comes from `vercel login`). Refuses to upload anything that is not a finished build.
 * Usage: npm run build:vercel && npm run deploy:vercel
 */
import { spawnSync } from "node:child_process";
import { existsSync, readFileSync } from "node:fs";
import { join } from "node:path";

const ROOT = join(import.meta.dirname, "..");
const OUT = join(ROOT, "out");

const project = JSON.parse(readFileSync(join(ROOT, "deploy", "vercel-project.json"), "utf8")) as { projectId: string; orgId: string };
for (const required of ["index.html", "404.html", "vercel.json"]) {
  if (!existsSync(join(OUT, required))) throw new Error(`out/${required} is missing: run npm run build:vercel first`);
}
const result = spawnSync("vercel", ["deploy", OUT, "--prod", "--yes"], {
  cwd: ROOT,
  stdio: "inherit",
  shell: true,
  env: { ...process.env, VERCEL_PROJECT_ID: project.projectId, VERCEL_ORG_ID: project.orgId },
});
process.exitCode = result.status ?? 1;