import { createHash } from "node:crypto";
import { mkdirSync, writeFileSync } from "node:fs";
import { join } from "node:path";
import type { Prisma, PrismaClient } from "@prisma/client";
import sharp from "sharp";
import { slugify } from "../../src/lib/slug";

export const MAX_FILE_BYTES = 25 * 1024 * 1024;
export const MAX_FILES = 200;
const MAX_DEPTH = 2;
const ORIGINAL_MAX_WIDTH = 2400;
const THUMB_WIDTHS = [400, 800];
const ALLOWED_FORMATS = new Set(["jpeg", "png", "webp"]);
const API = "https://cloud-api.yandex.net/v1/disk/public/resources";
const FETCH_TIMEOUT_MS = 60_000;

export class PhotoError extends Error {}

/** Share links come from the owner, but the agent must still never be pointed at an arbitrary host. */
const SHARE_HOST = /^(?:disk\.yandex\.(?:ru|kz|by|com|com\.tr|uz|az|co\.il)|yadi\.sk)$/;
const DOWNLOAD_HOST = /^(?:[a-z0-9-]+\.)*(?:disk\.yandex\.(?:ru|kz|by|com|com\.tr|uz|az|co\.il)|yandex\.net|storage\.yandex\.net)$/;

export function parseShareUrl(raw: string): string {
  let url: URL;
  try {
    url = new URL(raw);
  } catch {
    throw new PhotoError("Некорректная ссылка");
  }
  if (url.protocol !== "https:" || !SHARE_HOST.test(url.hostname) || !/^\/[di]\/[\w-]+\/?$/.test(url.pathname)) {
    throw new PhotoError("Нужна публичная ссылка Яндекс Диска вида https://disk.yandex.ru/d/... или /i/...");
  }
  return `${url.origin}${url.pathname}`;
}

export function isDownloadUrl(raw: string): boolean {
  try {
    const url = new URL(raw);
    return url.protocol === "https:" && DOWNLOAD_HOST.test(url.hostname);
  } catch {
    return false;
  }
}

/** Latin, lowercase, no path separators; a short content hash keeps names unique and idempotent. */
export function safeBaseName(original: string, hash: string): string {
  const stem = original.replace(/\.[^.]*$/, "");
  const slug = slugify(stem) || "photo";
  return `${slug.slice(0, 60)}-${hash.slice(0, 8)}`;
}

export type RemoteFile = { name: string; size: number; mime: string; url: string; folder: string };

type ApiItem = { type: string; name: string; path?: string; size?: number; mime_type?: string; media_type?: string; file?: string };
type ApiListing = ApiItem & { _embedded?: { items?: ApiItem[]; total?: number } };

async function api(publicKey: string, path?: string): Promise<ApiListing> {
  const query = new URLSearchParams({ public_key: publicKey, limit: String(MAX_FILES) });
  if (path) query.set("path", path);
  const response = await fetch(`${API}?${query}`, { signal: AbortSignal.timeout(FETCH_TIMEOUT_MS) });
  if (!response.ok) throw new PhotoError(`Яндекс Диск ответил ${response.status}: ссылка закрыта или неверна`);
  return (await response.json()) as ApiListing;
}

function toRemote(item: ApiItem, folder: string): RemoteFile | null {
  if (item.type !== "file" || item.media_type !== "image" || !item.file) return null;
  return { name: item.name, size: item.size ?? 0, mime: item.mime_type ?? "", url: item.file, folder };
}

export async function listRemote(shareUrl: string): Promise<{ files: RemoteFile[]; skipped: string[] }> {
  const files: RemoteFile[] = [];
  const skipped: string[] = [];
  const visit = async (path: string | undefined, folder: string, depth: number) => {
    const listing = await api(shareUrl, path);
    const items = listing.type === "dir" ? (listing._embedded?.items ?? []) : [listing];
    for (const item of items) {
      if (item.type === "dir" && depth < MAX_DEPTH && item.path) await visit(item.path, `${folder}/${item.name}`, depth + 1);
      else {
        const remote = toRemote(item, folder);
        if (remote) files.push(remote);
        else skipped.push(`${folder}/${item.name}`);
      }
    }
  };
  await visit(undefined, "", 0);
  return { files, skipped };
}

async function download(file: RemoteFile): Promise<Buffer> {
  if (!isDownloadUrl(file.url)) throw new PhotoError("Ссылка на скачивание ведёт не на Яндекс");
  if (file.size > MAX_FILE_BYTES) throw new PhotoError(`Файл больше ${MAX_FILE_BYTES / 1024 / 1024} МБ`);
  const response = await fetch(file.url, { signal: AbortSignal.timeout(FETCH_TIMEOUT_MS), redirect: "follow" });
  if (!response.ok || !isDownloadUrl(response.url)) throw new PhotoError(`Не удалось скачать (${response.status})`);
  const bytes = Buffer.from(await response.arrayBuffer());
  if (bytes.length > MAX_FILE_BYTES) throw new PhotoError("Файл больше лимита");
  return bytes;
}

export type Variant = { file: string; width: number; height: number };
export type Processed = { original: Buffer; width: number; height: number; variants: { meta: Variant; data: Buffer }[]; base: string };

/**
 * Type is decided by the bytes, never by name or header. Re-encoding drops EXIF (GPS included)
 * and anything that is not really a picture (scripts, polyglots) cannot survive it.
 */
export async function processImage(bytes: Buffer, originalName: string): Promise<Processed> {
  const image = sharp(bytes, { failOn: "error", limitInputPixels: 100_000_000 });
  const info = await image.metadata().catch(() => null);
  if (!info?.format || !ALLOWED_FORMATS.has(info.format)) throw new PhotoError("Это не JPEG, PNG или WebP");

  const base = safeBaseName(originalName, createHash("sha256").update(bytes).digest("hex"));
  const encode = (width: number) =>
    sharp(bytes).rotate().resize({ width, withoutEnlargement: true }).jpeg({ quality: 85, mozjpeg: true }).toBuffer({ resolveWithObject: true });

  const main = await encode(ORIGINAL_MAX_WIDTH);
  const variants: Processed["variants"] = [];
  for (const width of THUMB_WIDTHS) {
    if (main.info.width <= width) continue;
    const thumb = await encode(width);
    variants.push({ meta: { file: `${base}-${thumb.info.width}x${thumb.info.height}.jpg`, width: thumb.info.width, height: thumb.info.height }, data: thumb.data });
  }
  return { original: main.data, width: main.info.width, height: main.info.height, variants, base };
}

export type ImportResult = { path: string; status: "imported" | "exists" | "failed" | "planned"; detail?: string };

function monthFolder(now: Date): string {
  return `${now.getFullYear()}/${String(now.getMonth() + 1).padStart(2, "0")}`;
}

async function importOne(file: RemoteFile, ctx: { db: PrismaClient; root: string; now: Date }): Promise<ImportResult> {
  const processed = await processImage(await download(file), file.name);
  const dir = monthFolder(ctx.now);
  const path = `${dir}/${processed.base}.jpg`;
  if (await ctx.db.mediaAsset.findUnique({ where: { path } })) return { path, status: "exists" };

  mkdirSync(join(ctx.root, dir), { recursive: true });
  writeFileSync(join(ctx.root, path), processed.original);
  for (const v of processed.variants) writeFileSync(join(ctx.root, dir, v.meta.file), v.data);
  const variants: Prisma.InputJsonArray = processed.variants.map((v) => ({ ...v.meta }));
  const asset = await ctx.db.mediaAsset.create({
    data: { path, mimeType: "image/jpeg", width: processed.width, height: processed.height, variants },
  });
  await ctx.db.auditLog.create({ data: { actorType: "agent", action: "photo.import", entity: "media", entityId: asset.id, meta: { path } } });
  return { path, status: "imported" };
}

export async function importPhotos(
  db: PrismaClient,
  options: { url: string; apply: boolean; root: string; now?: Date },
): Promise<{ found: number; skipped: string[]; results: ImportResult[] }> {
  const { files, skipped } = await listRemote(parseShareUrl(options.url));
  if (files.length > MAX_FILES) throw new PhotoError(`В папке больше ${MAX_FILES} файлов; разделите её`);
  const ctx = { db, root: options.root, now: options.now ?? new Date() };
  const results: ImportResult[] = [];
  for (const file of files) {
    if (!options.apply) {
      results.push({ path: `${file.folder}/${file.name}`, status: "planned", detail: `${file.size} байт` });
      continue;
    }
    try {
      results.push(await importOne(file, ctx));
    } catch (error) {
      results.push({ path: `${file.folder}/${file.name}`, status: "failed", detail: error instanceof Error ? error.message : "ошибка обработки" });
    }
  }
  return { found: files.length, skipped, results };
}
