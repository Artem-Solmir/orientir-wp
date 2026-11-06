import { logoutAction } from "../actions";
import { requireUser } from "@/lib/dal";

export const metadata = { title: "Профиль", robots: { index: false } };

export default async function ProfilePage() {
  const user = await requireUser();
  return (
    <main className="mx-auto flex min-h-screen max-w-md flex-col items-start gap-4 p-8">
      <h1 className="text-2xl font-semibold">Профиль</h1>
      <p>{user.name}</p>
      <p className="text-neutral-600">{user.email}</p>
      <form action={logoutAction}>
        <button type="submit" className="rounded border border-neutral-400 px-4 py-2">
          Выйти
        </button>
      </form>
    </main>
  );
}
