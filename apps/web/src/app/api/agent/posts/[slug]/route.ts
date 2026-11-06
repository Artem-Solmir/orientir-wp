import { agentRoute, json, readJson } from "@/lib/agent-http";
import { archivePost, getPostForAgent, updatePost } from "@/lib/agent-posts";
import { postPatchSchema, slugSchema } from "@/lib/agent-schemas";

export const dynamic = "force-dynamic";

type Ctx = { params: Promise<{ slug: string }> };

export function GET(req: Request, { params }: Ctx) {
  return agentRoute(req, async () => json(await getPostForAgent(slugSchema.parse((await params).slug))));
}

export function PATCH(req: Request, { params }: Ctx) {
  return agentRoute(req, async () => {
    const slug = slugSchema.parse((await params).slug);
    return json(await updatePost(slug, postPatchSchema.parse(await readJson(req))));
  });
}

export function DELETE(req: Request, { params }: Ctx) {
  return agentRoute(req, async () => {
    await archivePost(slugSchema.parse((await params).slug));
    return json({ ok: true });
  });
}
