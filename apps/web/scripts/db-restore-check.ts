/**
 * Proves a backup is restorable: dump -> restore into a scratch database -> compare table counts -> drop.
 * Usage: npm run db:restore-check
 */
import { backupToFile, compose, SERVICE } from "./db-backup";
import { readFileSync } from "node:fs";

const SCRATCH = "restore_check";

function psql(sql: string, db = "postgres"): string {
  return compose([
    "exec", "-T", SERVICE, "sh", "-c",
    `psql -U "$POSTGRES_USER" -d ${db} -tAc "${sql}"`,
  ]).toString().trim();
}

const file = backupToFile();
console.log(`backup: ${file}`);

try {
  psql(`DROP DATABASE IF EXISTS ${SCRATCH}`);
  psql(`CREATE DATABASE ${SCRATCH}`);
  compose(
    ["exec", "-T", SERVICE, "sh", "-c", `pg_restore -U "$POSTGRES_USER" -d ${SCRATCH} --no-owner`],
    readFileSync(file),
  );
  const tables = `SELECT count(*) FROM information_schema.tables WHERE table_schema='public'`;
  const users = `SELECT count(*) FROM \\"User\\"`;
  const original = { tables: psql(tables, "$POSTGRES_DB"), users: psql(users, "$POSTGRES_DB") };
  const restored = { tables: psql(tables, SCRATCH), users: psql(users, SCRATCH) };
  console.log("original:", original, "restored:", restored);
  if (JSON.stringify(original) !== JSON.stringify(restored)) throw new Error("restore mismatch");
  console.log("restore check OK");
} finally {
  psql(`DROP DATABASE IF EXISTS ${SCRATCH}`);
}
