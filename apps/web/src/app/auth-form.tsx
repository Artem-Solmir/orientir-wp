"use client";

import { useActionState } from "react";
import type { FormState } from "./actions";

type Props = {
  action: (prev: FormState, data: FormData) => Promise<FormState>;
  mode: "login" | "register";
};

export function AuthForm({ action, mode }: Props) {
  const [state, formAction, pending] = useActionState(action, undefined);
  const register = mode === "register";

  return (
    <form action={formAction} className="form">
      {register && (
        <label className="field">
          Имя
          <input name="name" type="text" autoComplete="name" required minLength={2} maxLength={80} className="input" />
        </label>
      )}
      <label className="field">
        Email
        <input name="email" type="email" autoComplete="email" required maxLength={254} className="input" />
      </label>
      <label className="field">
        Пароль
        <input
          name="password"
          type="password"
          autoComplete={register ? "new-password" : "current-password"}
          required
          minLength={register ? 10 : 1}
          maxLength={128}
          className="input"
        />
      </label>
      {state?.error && (
        <p role="alert" className="error">
          {state.error}
        </p>
      )}
      <button type="submit" disabled={pending} className="btn btn--primary">
        {register ? "Зарегистрироваться" : "Войти"}
      </button>
    </form>
  );
}
