import { AdminLogin } from "@/components/admin-login";
import { adminEnabled, adminLoginEmailHint, adminOtpEndpoint } from "@/lib/admin/config";

export default function AdminLoginPage() {
  if (!adminEnabled()) {
    return (
      <p className="notice">
        Админка на этом сайте не включена.
      </p>
    );
  }
  return <AdminLogin emailHint={adminLoginEmailHint()} otpUrl={adminOtpEndpoint()} />;
}
