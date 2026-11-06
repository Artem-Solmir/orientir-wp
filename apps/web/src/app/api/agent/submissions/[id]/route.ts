import { z } from "zod";
import { agentRoute, AgentError, json, readJson } from "@/lib/agent-http";
import { audit } from "@/lib/agent-posts";
import { submissionPatchSchema } from "@/lib/agent-schemas";
import { db } from "@/lib/db";

export const dynamic = "force-dynamic";

export function PATCH(req: Request, { params }: { params: Promise<{ id: string }> }) {
  return agentRoute(req, async () => {
    const id = z.string().regex(/^[a-z0-9]{10,40}$/).parse((await params).id);
    const { status } = submissionPatchSchema.parse(await readJson(req));
    const result = await db.submission.updateMany({ where: { id }, data: { status } });
    if (result.count === 0) throw new AgentError(404, "Заявка не найдена");
    await audit("submission.update", "submission", id, { status });
    return json({ ok: true });
  });
}
