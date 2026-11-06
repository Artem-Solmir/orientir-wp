import Link from "next/link";
import { loginAction } from "../actions";
import { AuthForm } from "../auth-form";

export const metadata = { title: "Вход" };

export default function LoginPage() {
  return (
    <main className="mx-auto flex min-h-screen max-w-md flex-col items-start gap-6 p-8">
      <h1 className="text-2xl font-semibold">Вход</h1>
      <AuthForm action={loginAction} mode="login" />
      <Link href="/register" className="underline">
        Регистрация
      </Link>
    </main>
  );
}
