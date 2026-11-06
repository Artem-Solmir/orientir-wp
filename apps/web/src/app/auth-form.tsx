"use client";

import { useActionState } from "react";
import type { FormState } from "./actions";

type Props = {
  action: (prev: FormState, data: FormData) => Promise<FormState>;
  mode: "login" | "register";
};

const field =
  "w-full rounded border border-neutral-300 px-3 py-2 text-neutral-900 focus:outline-2 focus:outline-offset-2 focus:outline-blue-700";

export function AuthForm({ action, mode }: Props) {
  const [state, formAction, pending] = useActionState(action, undefined);
  const register = mode === "register";

  return (
    <form action={formAction} className="flex w-full max-w-sm flex-col gap-4">
      {register && (
        <label className="flex flex-col gap-1">
          Имя
          <input name="name" type="text" autoComplete="name" required minLength={2} maxLength={80} className={field} />
        </label>
      )}
      <label className="flex flex-col gap-1">
        Email
        <input name="email" type="email" autoComplete="email" required maxLength={254} className={field} />
      </label>
      <label className="flex flex-col gap-1">
        Пароль
        <input
          name="password"
          type="password"
          autoComplete={register ? "new-password" : "current-password"}
          required
          minLength={register ? 10 : 1}
          maxLength={128}
          className={field}
        />
      </label>
      {state?.error && (
        <p role="alert" className="text-red-700">
          {state.error}
        </p>
      )}
      <button
        type="submit"
        disabled={pending}
        className="rounded bg-neutral-900 px-4 py-2 text-white disabled:opacity-60"
      >
        {register ? "Зарегистрироваться" : "Войти"}
      </button>
    </form>
  );
}
