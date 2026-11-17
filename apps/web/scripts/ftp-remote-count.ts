/** Counts files on FTP (rough) and checks key paths. npm run deploy:ftp:count */
import { Client } from "basic-ftp";
import { ftpRemoteRoot } from "./ftp-remote-root";

async function main() {
  const host = process.env.FTP_HOST?.trim();
  const user = process.env.FTP_USER?.trim();
  const pass = process.env.FTP_PASS?.trim();
  if (!host || !user || !pass) throw new Error("FTP_* in .env");

  const client = new Client(600_000);
  client.ftp.ipFamily = 4;
  let count = 0;
  const { cd } = ftpRemoteRoot();
  await client.access({ host, user, password: pass, secure: process.env.FTP_SECURE === "1" });
  if (cd) await client.cd(cd);

  async function scan() {
    const list = await client.list();
    for (const e of list) {
      if (e.name === "." || e.name === "..") continue;
      if (e.isDirectory) {
        await client.cd(e.name);
        await scan();
        await client.cd("..");
      } else {
        count += 1;
      }
    }
  }
  await scan();

  process.stdout.write(`файлов на сервере: ${count}\n`);
  for (const rel of [
    "admin/login/index.html",
    "admin/index.html",
    "admin-api.php",
    "admin-otp.php",
    "send.php",
    "private/orientir-send-config.php",
    "private/orientir-admin-otp-config.php",
  ]) {
    try {
      await client.size(rel);
      process.stdout.write(`${rel}: есть\n`);
    } catch {
      process.stdout.write(`${rel}: нет\n`);
    }
  }
  client.close();
}

main().catch((e) => {
  console.error(e);
  process.exitCode = 1;
});