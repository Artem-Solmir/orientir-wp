import type { Metadata } from "next";
import { adminEnabled } from "@/lib/admin/config";

export const metadata: Metadata = { title: "Админка", robots: { index: false, follow: false } };

export default function AdminLayout({ children }: { children: React.ReactNode }) {
  if (!adminEnabled()) {
    return (
      <div className="container admin-shell">
        <h1 className="page-title">Админка</h1>
        <p className="notice">На этом окружении панель отключена. Для локальной работы добавьте в <code>.env</code> переменную <code>ENABLE_ADMIN=1</code> и перезапустите <code>npm run dev</code>.</p>
      </div>
    );
  }
  return <div className="container admin-shell">{children}</div>;
}
