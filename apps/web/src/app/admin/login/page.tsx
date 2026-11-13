import { redirect } from "next/navigation";
import { AdminLogin } from "@/components/admin-login";
import { adminEmail } from "@/lib/admin/auth";
import { adminEnabled } from "@/lib/admin/config";

export default async function AdminLoginPage() {
  if (!adminEnabled()) return null;
  if (await adminEmail()) redirect("/admin");
  return <AdminLogin />;
}
