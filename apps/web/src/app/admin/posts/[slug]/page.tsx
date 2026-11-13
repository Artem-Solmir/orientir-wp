import { notFound, redirect } from "next/navigation";
import { AdminPostForm } from "@/components/admin-post-form";
import { adminEmail } from "@/lib/admin/auth";
import { adminEnabled } from "@/lib/admin/config";
import { adminGetPost } from "@/lib/admin/posts";
import { adminCategories } from "@/lib/admin/posts";

type Props = { params: Promise<{ slug: string }> };

export default async function AdminEditPostPage({ params }: Props) {
  if (!adminEnabled()) return null;
  if (!(await adminEmail())) redirect("/admin/login");
  const { slug } = await params;
  const [post, categories] = await Promise.all([adminGetPost(slug), adminCategories()]);
  if (!post) notFound();
  return (
    <>
      <h1 className="page-title">Редактирование</h1>
      <AdminPostForm mode="edit" categories={categories} initial={post} />
    </>
  );
}
