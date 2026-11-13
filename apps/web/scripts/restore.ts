/**
 * npm run restore            — list backups
 * npm run restore -- <name>  — roll apps/web back to that backup (current state is backed up first)
 */
import { listBackups, restoreBackup } from "./backup-lib";

const name = process.argv[2];

if (!name) {
  const all = listBackups();
  if (all.length === 0) {
    process.stdout.write("Резервных копий пока нет. Создать: npm run backup\n");
  } else {
    process.stdout.write(`Резервные копии (последняя — внизу):\n${all.map((n) => `  ${n}`).join("\n")}\n\nВосстановить: npm run restore -- <имя>\n`);
  }
} else {
  try {
    const safety = restoreBackup(name);
    process.stdout.write(`Восстановлено из ${name}.\nСостояние до отката сохранено в ${safety}.\nПерезапустите npm run dev, если он был запущен.\n`);
  } catch (e) {
    console.error(e instanceof Error ? e.message : e);
    process.exitCode = 1;
  }
}
