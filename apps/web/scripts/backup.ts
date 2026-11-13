/**
 * Copies apps/web (without node_modules, builds and .env) to <repo>/backups/<date-time>/.
 * Usage: npm run backup
 */
import { BACKUP_ROOT, KEEP_BACKUPS, createBackup } from "./backup-lib";

const name = createBackup();
process.stdout.write(`Резервная копия создана: ${name}\nПапка: ${BACKUP_ROOT}\nХранятся последние ${KEEP_BACKUPS} копий.\n`);
