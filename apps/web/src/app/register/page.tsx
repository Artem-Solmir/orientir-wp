import Link from "next/link";
import { registerAction } from "../actions";
import { AuthForm } from "../auth-form";

export const metadata = { title: "Регистрация" };

export default function RegisterPage() {
  return (
    <main className="mx-auto flex min-h-screen max-w-md flex-col items-start gap-6 p-8">
      <h1 className="text-2xl font-semibold">Регистрация</h1>
      <AuthForm action={registerAction} mode="register" />
      <Link href="/login" className="underline">
        Уже есть аккаунт
      </Link>
    </main>
  );
}
