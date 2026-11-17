import { AdminHome } from "@/components/admin-home";
import { adminEnabled } from "@/lib/admin/config";
import { adminUsesRemoteApi } from "@/lib/admin/remote-api";

export default function AdminHomePage() {
  if (!adminEnabled()) {
    return (
      <p className="notice">
        Админка на этом сайте не включена. Обратитесь к администратору.
      </p>
    );
  }
  if (!adminUsesRemoteApi()) {
    return <p className="notice">Для локальной админки нужен npm run dev с ENABLE_ADMIN=1.</p>;
  }
  return <AdminHome />;
}
