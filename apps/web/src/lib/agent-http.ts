import "server-only";
import { ZodError } from "zod";
import { clientIp } from "./client-ip";
import { isValidAgentToken } from "./agent-auth";
import { env } from "./env";
import { isRateLimited } from "./rate-limit";

const MAX_BODY_BYTES = 1_000_000;

export class AgentError extends Error {
  constructor(
    readonly status: number,
    message: string,
  ) {
    super(message);
  }
}

export function json(data: unknown, status = 200): Response {
  return Response.json(data, { status, headers: { "Cache-Control": "no-store" } });
}

async function authenticate(req: Request): Promise<void> {
  if (isValidAgentToken(req.headers.get("authorization"), env().AGENT_API_TOKEN)) {
    const limited = await isRateLimited([{ kind: "agent-ok", identifier: "token", max: 600, windowSec: 3600 }]);
    if (limited) throw new AgentError(429, "Слишком много запросов");
    return;
  }
  const limited = await isRateLimited([{ kind: "agent-fail", identifier: await clientIp(), max: 10, windowSec: 900 }]);
  throw new AgentError(limited ? 429 : 401, limited ? "Слишком много попыток" : "Unauthorized");
}

export async function readJson(req: Request): Promise<unknown> {
  const text = await req.text();
  if (text.length > MAX_BODY_BYTES) throw new AgentError(413, "Тело запроса слишком большое");
  try {
    return JSON.parse(text);
  } catch {
    throw new AgentError(400, "Некорректный JSON");
  }
}

function toResponse(error: unknown): Response {
  if (error instanceof AgentError) return json({ error: error.message }, error.status);
  if (error instanceof ZodError) {
    return json({ error: "Validation failed", issues: error.issues.map((i) => ({ path: i.path, message: i.message })) }, 422);
  }
  console.error("agent api error", error);
  return json({ error: "Internal error" }, 500);
}

/** Every Agent API route goes through here: bearer auth, rate limits, uniform error mapping. */
export async function agentRoute(req: Request, handler: () => Promise<Response>): Promise<Response> {
  try {
    await authenticate(req);
    return await handler();
  } catch (error) {
    return toResponse(error);
  }
}
