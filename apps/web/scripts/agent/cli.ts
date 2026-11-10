/**
 * Local CLI for the AI agent (no network surface, no tokens: whoever can run this already owns the machine).
 *
 *   npm run agent -- post:list [--q=text] [--status=published|archived] [--limit=20] [--offset=0]
 *   npm run agent -- post:get <slug>
 *   npm run agent -- post:create <file.json|->
 *   npm run agent -- post:update <slug> <file.json|->
 *   npm run agent -- post:archive <slug>
 *   npm run agent -- page:update <slug> <file.json|->
 *   npm run agent -- photos:import <public Yandex Disk url> [--apply]   (without --apply: list only, nothing is downloaded)
 *
 * Output is JSON on stdout; errors go to stderr with a non-zero exit code.
 */
import { readFileSync } from "node:fs";
import { PrismaClient } from "@prisma/client";
import { ZodError } from "zod";
import { resolve } from "node:path";
import { Agent, AgentError } from "./posts";
import { importPhotos } from "./photos";
import { listQuerySchema, pagePatchSchema, postCreateSchema, postPatchSchema, slugSchema } from "./schemas";

const MAX_INPUT_BYTES = 1_000_000;

function readInput(source: string | undefined): unknown {
  if (!source) throw new AgentError("Укажите файл JSON или - для stdin");
  const text = readFileSync(source === "-" ? 0 : source, "utf8");
  if (text.length > MAX_INPUT_BYTES) throw new AgentError("Входной JSON больше 1 МБ");
  try {
    return JSON.parse(text);
  } catch {
    throw new AgentError("Некорректный JSON");
  }
}

function flags(args: string[]): Record<string, string> {
  return Object.fromEntries(
    args.filter((a) => a.startsWith("--")).map((a) => {
      const [key, ...value] = a.slice(2).split("=");
      return [key!, value.join("=")];
    }),
  );
}

type Handler = (agent: Agent, args: string[], db: PrismaClient) => Promise<unknown>;

const COMMANDS: Record<string, Handler> = {
  "post:list": (agent, args) => agent.listPosts(listQuerySchema.parse(flags(args))),
  "post:get": (agent, [slug]) => agent.getPost(slugSchema.parse(slug)),
  "post:create": (agent, [file]) => agent.createPost(postCreateSchema.parse(readInput(file))),
  "post:update": (agent, [slug, file]) => agent.updatePost(slugSchema.parse(slug), postPatchSchema.parse(readInput(file))),
  "post:archive": async (agent, [slug]) => {
    await agent.archivePost(slugSchema.parse(slug));
    return { ok: true };
  },
  "photos:import": (_agent, [url], db) =>
    importPhotos(db, {
      url: url ?? "",
      apply: process.argv.includes("--apply"),
      root: resolve(process.env.MEDIA_ROOT ?? resolve(process.cwd(), "../../wp-content/uploads")),
    }),
  "page:update": async (agent, [slug, file]) => {
    await agent.updatePage(slugSchema.parse(slug), pagePatchSchema.parse(readInput(file)));
    return { ok: true };
  },
};

function describe(error: unknown): string {
  if (error instanceof ZodError) {
    return JSON.stringify({ error: "Validation failed", issues: error.issues.map((i) => ({ path: i.path, message: i.message })) });
  }
  return JSON.stringify({ error: error instanceof AgentError ? error.message : "Internal error", detail: String(error) });
}

async function main() {
  const [command, ...args] = process.argv.slice(2);
  const handler = command ? COMMANDS[command] : undefined;
  if (!handler) {
    process.stderr.write(`Команды: ${Object.keys(COMMANDS).join(", ")}\n`);
    process.exitCode = 2;
    return;
  }
  const db = new PrismaClient();
  try {
    process.stdout.write(`${JSON.stringify(await handler(new Agent(db), args, db), null, 2)}\n`);
  } catch (error) {
    process.stderr.write(`${describe(error)}\n`);
    process.exitCode = 1;
  } finally {
    await db.$disconnect();
  }
}

void main();
