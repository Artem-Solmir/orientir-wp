import { agentRoute, json, readJson } from "@/lib/agent-http";
import { updatePage } from "@/lib/agent-posts";
import { pagePatchSchema, slugSchema } from "@/lib/agent-schemas";

export const dynamic = "force-dynamic";

export function PATCH(req: Request, { params }: { params: Promise<{ slug: string }> }) {
  return agentRoute(req, async () => {
    const slug = slugSchema.parse((await params).slug);
    await updatePage(slug, pagePatchSchema.parse(await readJson(req)));
    return json({ ok: true });
  });
}
