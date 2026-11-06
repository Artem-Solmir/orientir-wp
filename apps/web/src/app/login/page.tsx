import Link from "next/link";
import { loginAction } from "../actions";
import { AuthForm } from "../auth-form";

export const metadata = { title: "Вход", robots: { index: false } };

export default function LoginPage() {
  return (
    <div className="container">
      <h1 className="page-title">Вход</h1>
      <AuthForm action={loginAction} mode="login" />
      <p>
        <Link href="/register">Регистрация</Link>
      </p>
    </div>
  );
}
