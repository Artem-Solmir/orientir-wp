/** Temporarily move app/admin aside so static export does not pull in server actions. */
import { cpSync, existsSync, renameSync, rmSync } from "node:fs";
import { join } from "node:path";

const ROOT = join(import.meta.dirname, "..");
const ADMIN_DIR = join(ROOT, "src/app/admin");
const ADMIN_BAK = join(ROOT, "src/.admin-export-bak");

const rmOpts = { recursive: true, force: true, maxRetries: 12, retryDelay: 250 } as const;

export function hideAdminRoutes() {
  if (!existsSync(ADMIN_DIR)) return;
  rmSync(ADMIN_BAK, rmOpts);
  try {
    renameSync(ADMIN_DIR, ADMIN_BAK);
  } catch {
    cpSync(ADMIN_DIR, ADMIN_BAK, { recursive: true });
    rmSync(ADMIN_DIR, rmOpts);
    if (existsSync(ADMIN_DIR)) {
      throw new Error("Cannot hide src/app/admin (stop `npm run dev` and retry build:vercel)");
    }
  }
}

export function restoreAdminRoutes() {
  if (!existsSync(ADMIN_BAK)) return;
  if (existsSync(ADMIN_DIR)) return;
  try {
    renameSync(ADMIN_BAK, ADMIN_DIR);
  } catch {
    cpSync(ADMIN_BAK, ADMIN_DIR, { recursive: true });
    rmSync(ADMIN_BAK, rmOpts);
  }
}
