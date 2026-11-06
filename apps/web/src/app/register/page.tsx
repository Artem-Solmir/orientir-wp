import Link from "next/link";
import { registerAction } from "../actions";
import { AuthForm } from "../auth-form";

export const metadata = { title: "Регистрация", robots: { index: false } };

export default function RegisterPage() {
  return (
    <div className="container">
      <h1 className="page-title">Регистрация</h1>
      <AuthForm action={registerAction} mode="register" />
      <p>
        <Link href="/login">Уже есть аккаунт</Link>
      </p>
    </div>
  );
}
