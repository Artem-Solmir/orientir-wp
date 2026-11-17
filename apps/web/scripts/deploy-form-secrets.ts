/**
 * Puts form mail settings outside the public tree when possible; removes legacy send-config.php from site root.
 * Usage: npm run deploy:form-secrets [-- --dry-run]
 */
import { createReadStream, existsSync } from "node:fs";
import { join } from "node:path";
import { Client } from "basic-ftp";
import { ftpRemoteRoot } from "./ftp-remote-root";

const ROOT = join(import.meta.dirname, "..");
const FORM_CONFIG = join(ROOT, "deploy", "send-config.php");
const OTP_CONFIG = join(ROOT, "deploy", "admin-otp-config.php");
const REMOTE_FORM = "orientir-send-config.php";
const REMOTE_OTP = "orientir-admin-otp-config.php";
const dryRun = process.argv.includes("--dry-run");

function required(name: string): string {
  const v = process.env[name]?.trim();
  if (!v) throw new Error(`Set ${name} in .env`);
  return v;
}

async function main() {
  if (!existsSync(FORM_CONFIG)) throw new Error("deploy/send-config.php missing — copy from send-config.example.php");

  const host = required("FTP_HOST");
  const user = required("FTP_USER");
  const pass = required("FTP_PASS");
  const { cd: siteRoot, label } = ftpRemoteRoot();
  const root = siteRoot ?? "/";

  if (dryRun) {
    process.stdout.write(`dry-run: upload private config, delete send-config.php at ${label}\n`);
    return;
  }

  const secure = process.env.FTP_SECURE === "1";
  const client = new Client(60_000);
  client.ftp.ipFamily = 4;
  try {
    await client.access({ host, user, password: pass, secure });
    await client.cd(root);

    let uploaded = false;
    await client.cd(root);
    if (await uploadBesideSite(client, root, FORM_CONFIG, REMOTE_FORM)) {
      uploaded = true;
      process.stdout.write("настройки формы: папка ../private/ на сервере\n");
    } else {
      await client.cd(root);
      await mkdirPrivate(client);
      await client.uploadFrom(createReadStream(FORM_CONFIG), `private/${REMOTE_FORM}`);
      uploaded = true;
      process.stdout.write("настройки формы: orientir.by/private/ (закрыто правилами .htaccess)\n");
    }

    if (!uploaded) throw new Error("could not upload form config");

    await client.cd(root);
    await mkdirPrivate(client);
    if (existsSync(OTP_CONFIG)) {
      await client.uploadFrom(createReadStream(OTP_CONFIG), `private/${REMOTE_OTP}`);
      process.stdout.write("настройки входа в админку: private/\n");
    } else {
      process.stdout.write("WARNING: deploy/admin-otp-config.php нет — вход по коду не заработает\n");
    }

    await client.cd(root);
    try {
      await client.remove("send-config.php");
      process.stdout.write("удалён публичный send-config.php в корне сайта\n");
    } catch {
      process.stdout.write("публичного send-config.php в корне не было\n");
    }
  } finally {
    client.close();
  }
}

async function mkdirPrivate(client: Client) {
  try {
    await client.ensureDir("private");
  } catch {
    /* directory may already exist */
  }
}

/** @returns true when FTP allows stepping above the site root */
async function uploadBesideSite(client: Client, siteRoot: string, local: string, remoteName: string): Promise<boolean> {
  const cwd = await client.pwd();
  try {
    await client.cd("..");
    if ((await client.pwd()) === cwd) return false;
    await client.ensureDir("private");
    await client.cd("private");
    await client.uploadFrom(createReadStream(local), remoteName);
    return true;
  } catch {
    return false;
  } finally {
    await client.cd(siteRoot);
  }
}

main().catch((e) => {
  console.error(e);
  process.exitCode = 1;
});
