import { createReadStream } from "node:fs";
import { stat } from "node:fs/promises";
import { resolve } from "node:path";
import { Readable } from "node:stream";
import { resolveMedia } from "@/lib/media-path";

export const dynamic = "force-dynamic";

const mediaRoot = () => process.env.MEDIA_ROOT ?? resolve(process.cwd(), "../../wp-content/uploads");

/**
 * Serves legacy upload URLs (/wp-content/uploads/...) so old post bodies and external links keep working.
 * Local/dev implementation; production moves this behind object storage or a CDN rewrite (Phase 6).
 */
export async function GET(request: Request, { params }: { params: Promise<{ path: string[] }> }) {
  const segments = (await params).path;
  let decoded: string[];
  try {
    decoded = segments.map(decodeURIComponent);
  } catch {
    return new Response("Not found", { status: 404 });
  }

  const target = resolveMedia(mediaRoot(), decoded);
  if (!target) return new Response("Not found", { status: 404 });

  const info = await stat(target.file).catch(() => null);
  if (!info?.isFile()) return new Response("Not found", { status: 404 });

  const etag = `"${info.size.toString(16)}-${Math.floor(info.mtimeMs).toString(16)}"`;
  const headers = {
    "Content-Type": target.contentType,
    "Cache-Control": "public, max-age=604800",
    ETag: etag,
    "X-Content-Type-Options": "nosniff",
    // Documents and archives download; images and video render inline.
    "Content-Disposition": target.contentType.startsWith("image/") || target.contentType.startsWith("video/") ? "inline" : "attachment",
  };
  if (request.headers.get("if-none-match") === etag) return new Response(null, { status: 304, headers });

  const body = Readable.toWeb(createReadStream(target.file)) as ReadableStream;
  return new Response(body, { headers: { ...headers, "Content-Length": String(info.size) } });
}
