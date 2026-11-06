import { agentRoute, json } from "@/lib/agent-http";
import { listQuerySchema } from "@/lib/agent-schemas";
import { db } from "@/lib/db";

export const dynamic = "force-dynamic";

const STATUSES = ["new", "processed", "rejected"] as const;

export function GET(req: Request) {
  return agentRoute(req, async () => {
    const { limit, offset, status } = listQuerySchema.parse(Object.fromEntries(new URL(req.url).searchParams));
    const wanted = STATUSES.find((s) => s === status);
    const items = await db.submission.findMany({
      where: wanted ? { status: wanted } : {},
      orderBy: { createdAt: "desc" },
      take: limit,
      skip: offset,
    });
    return json({ items });
  });
}
