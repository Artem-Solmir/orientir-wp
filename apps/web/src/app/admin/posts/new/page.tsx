import { AdminPostPage } from "@/components/admin-post-page";
import { adminEnabled } from "@/lib/admin/config";

export default function AdminNewPostPage() {
  if (!adminEnabled()) return null;
  return <AdminPostPage mode="create" />;
}
