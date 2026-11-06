import { agentRoute, json, readJson } from "@/lib/agent-http";
import { createPost, listPostsForAgent } from "@/lib/agent-posts";
import { listQuerySchema, postCreateSchema } from "@/lib/agent-schemas";

export const dynamic = "force-dynamic";

export function GET(req: Request) {
  return agentRoute(req, async () => {
    const query = listQuerySchema.parse(Object.fromEntries(new URL(req.url).searchParams));
    return json(await listPostsForAgent(query));
  });
}

export function POST(req: Request) {
  return agentRoute(req, async () => {
    const input = postCreateSchema.parse(await readJson(req));
    return json(await createPost(input), 201);
  });
}
