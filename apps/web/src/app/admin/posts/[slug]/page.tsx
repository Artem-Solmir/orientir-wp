import { AdminPostPage } from "@/components/admin-post-page";
import { adminEnabled } from "@/lib/admin/config";
import { readdirSync } from "node:fs";
import { join } from "node:path";

export function generateStaticParams() {
  const postsDir = join(process.cwd(), "content", "posts");
  try {
    return readdirSync(postsDir)
      .filter((f) => f.endsWith(".json"))
      .map((f) => ({ slug: f.replace(/\.json$/, "") }));
  } catch {
    return [];
  }
}

type Props = { params: Promise<{ slug: string }> };

export default async function AdminEditPostPage({ params }: Props) {
  if (!adminEnabled()) return null;
  const { slug } = await params;
  return <AdminPostPage slug={slug} mode="edit" />;
}
