/** Tests FTP login and FTP_ROOT without uploading. Usage: npx tsx --env-file=.env scripts/ftp-probe.ts */
import { Client } from "basic-ftp";
import { ftpRemoteRoot } from "./ftp-remote-root";

async function main() {
  const host = process.env.FTP_HOST?.trim();
  const user = process.env.FTP_USER?.trim();
  const pass = process.env.FTP_PASS?.trim();
  if (!host || !user || !pass) throw new Error("В .env должны быть FTP_HOST, FTP_USER и FTP_PASS");
  const { cd: remoteCd, label: remoteLabel } = ftpRemoteRoot();
  const client = new Client(30_000);
  client.ftp.ipFamily = 4;
  try {
    await client.access({ host, user, password: pass, secure: process.env.FTP_SECURE === "1" });
  } catch (e) {
    const secure = process.env.FTP_SECURE === "1";
    throw new Error(
      secure
        ? "Не удалось войти. Проверьте логин/пароль и при необходимости поставьте FTP_SECURE=0."
        : `Не удалось войти (${e instanceof Error ? e.message : e}). Если хостинг требует шифрование, попробуйте FTP_SECURE=1 в .env.`,
    );
  }
  try {
    const home = await client.pwd();
    process.stdout.write(`После входа: ${home}\n`);
    try {
      if (remoteCd) await client.cd(remoteCd);
    } catch {
      process.stdout.write(`Папка ${remoteLabel} не найдена. Содержимое после входа:\n`);
      for (const e of await client.list()) process.stdout.write(`  ${e.isDirectory ? "[dir]" : "file"} ${e.name}\n`);
      throw new Error(`Измените FTP_ROOT в .env. Сейчас: ${remoteLabel}. Для activecloud часто достаточно FTP_ROOT=/`);
    }
    const entries = await client.list();
    const names = new Set(entries.map((e) => e.name));
    process.stdout.write(
      [
        `Подключение OK: ${host}`,
        `Рабочая папка: ${await client.pwd()}`,
        `Элементов в корне: ${entries.length}`,
        `index.html или index.php: ${names.has("index.html") || names.has("index.php") ? "есть" : "нет"}`,
        `wp-content: ${names.has("wp-content") ? "есть" : "нет"}`,
      ].join("\n") + "\n",
    );
  } finally {
    client.close();
  }
}

main().catch((e) => {
  console.error(e instanceof Error ? e.message : e);
  process.exitCode = 1;
});
