/** Tests FTP login and FTP_ROOT without uploading. Usage: npx tsx --env-file=.env scripts/ftp-probe.ts */
import { Client } from "basic-ftp";
import { ftpRemoteRoot } from "./ftp-remote-root";

function loginError(e: unknown): string {
  if (process.env.FTP_SECURE === "1") {
    return "Не удалось войти. Проверьте логин/пароль и при необходимости поставьте FTP_SECURE=0.";
  }
  const detail = e instanceof Error ? e.message : e;
  return `Не удалось войти (${detail}). Если хостинг требует шифрование, попробуйте FTP_SECURE=1 в .env.`;
}

async function cdOrListHome(client: Client, remoteCd: string | null, remoteLabel: string) {
  const home = await client.pwd();
  process.stdout.write(`После входа: ${home}\n`);
  if (!remoteCd) return;
  try {
    await client.cd(remoteCd);
  } catch {
    process.stdout.write(`Папка ${remoteLabel} не найдена. Содержимое после входа:\n`);
    for (const e of await client.list()) process.stdout.write(`  ${e.isDirectory ? "[dir]" : "file"} ${e.name}\n`);
    throw new Error(`Измените FTP_ROOT в .env. Сейчас: ${remoteLabel}. Для activecloud часто достаточно FTP_ROOT=/`);
  }
}

async function report(client: Client, host: string) {
  const entries = await client.list();
  const names = new Set(entries.map((e) => e.name));
  const hasIndex = names.has("index.html") || names.has("index.php");
  const lines = [
    `Подключение OK: ${host}`,
    `Рабочая папка: ${await client.pwd()}`,
    `Элементов в корне: ${entries.length}`,
    `index.html или index.php: ${hasIndex ? "есть" : "нет"}`,
    `wp-content: ${names.has("wp-content") ? "есть" : "нет"}`,
  ];
  process.stdout.write(`${lines.join("\n")}\n`);
}

async function main() {
  const host = process.env.FTP_HOST?.trim();
  const user = process.env.FTP_USER?.trim();
  const pass = process.env.FTP_PASS?.trim();
  if (!host || !user || !pass) throw new Error("В .env должны быть FTP_HOST, FTP_USER и FTP_PASS");

  const { cd: remoteCd, label: remoteLabel } = ftpRemoteRoot();
  const client = new Client(30_000);
  client.ftp.ipFamily = 4;
  try {
    try {
      await client.access({ host, user, password: pass, secure: process.env.FTP_SECURE === "1" });
    } catch (e) {
      throw new Error(loginError(e));
    }
    await cdOrListHome(client, remoteCd, remoteLabel);
    await report(client, host);
    try {
      await client.cd("_next/static/chunks");
      const chunks = await client.list();
      process.stdout.write(`_next/static/chunks на сервере: ${chunks.filter((e) => !e.isDirectory).length} файлов\n`);
    } catch {
      process.stdout.write("_next/static/chunks на сервере: нет (отсюда 404 на orientir.by)\n");
    }
  } finally {
    client.close();
  }
}

main().catch((e) => {
  console.error(e instanceof Error ? e.message : e);
  process.exitCode = 1;
});
