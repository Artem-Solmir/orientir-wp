"use client";

import { useState } from "react";
import { loginAdminAction } from "@/lib/admin/actions";

export function AdminLogin() {
  const [message, setMessage] = useState<string | null>(null);
  const [pending, setPending] = useState(false);

  async function onSubmit(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault();
    setPending(true);
    setMessage(null);
    const res = await loginAdminAction(new FormData(e.currentTarget));
    setPending(false);
    if (!res.ok) setMessage(res.message);
  }

  return (
    <div className="admin-login">
      <h1 className="page-title">Вход в админку</h1>
      <p className="label">Локальный доступ по паролю.</p>
      <form className="form" onSubmit={onSubmit}>
        <div className="field">
          <label className="label" htmlFor="admin-password">
            Пароль
          </label>
          <input
            id="admin-password"
            name="password"
            className="input"
            type="password"
            required
            autoComplete="current-password"
            autoFocus
          />
        </div>
        <button type="submit" className="btn btn--primary" disabled={pending}>
          {pending ? "Входим…" : "Войти"}
        </button>
      </form>
      {message && <p className="error">{message}</p>}
    </div>
  );
}
