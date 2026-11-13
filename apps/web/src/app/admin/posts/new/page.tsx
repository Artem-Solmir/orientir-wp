import { redirect } from "next/navigation";
import { AdminPostForm } from "@/components/admin-post-form";
import { adminEmail } from "@/lib/admin/auth";
import { adminEnabled } from "@/lib/admin/config";
import { adminCategories } from "@/lib/admin/posts";

export default async function AdminNewPostPage() {
  if (!adminEnabled()) return null;
  if (!(await adminEmail())) redirect("/admin/login");
  const categories = await adminCategories();
  return (
    <>
      <h1 className="page-title">Новая запись</h1>
      <AdminPostForm mode="create" categories={categories} />
    </>
  );
}
