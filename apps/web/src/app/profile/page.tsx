import { logoutAction } from "../actions";
import { requireUser } from "@/lib/dal";

export const metadata = { title: "Профиль", robots: { index: false } };

export default async function ProfilePage() {
  const user = await requireUser();
  return (
    <div className="container">
      <h1 className="page-title">Профиль</h1>
      <p>{user.name}</p>
      <p className="label">{user.email}</p>
      <form action={logoutAction}>
        <button type="submit" className="btn btn--secondary">
          Выйти
        </button>
      </form>
    </div>
  );
}
