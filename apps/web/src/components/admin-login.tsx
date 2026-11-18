"use client";

import { useRouter } from "next/navigation";
import { useState } from "react";
import { adminUsesRemoteApi } from "@/lib/admin/remote-api";

type Props = { emailHint: string; otpUrl: string };

async function postOtp(url: string, body: URLSearchParams, creds: RequestCredentials) {
  const res = await fetch(url, {
    method: "POST",
    credentials: creds,
    headers: { "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8" },
    body,
  });
  const data = (await res.json().catch(() => ({}))) as { error?: string; token?: string; hint?: string };
  if (!res.ok) return { ok: false as const, message: data.error ?? "Не удалось выполнить запрос" };
  return { ok: true as const, data };
}

export function AdminLogin({ emailHint, otpUrl }: Props) {
  const router = useRouter();
  const [message, setMessage] = useState<string | null>(null);
  const [phase, setPhase] = useState<"request" | "verify">("request");
  const [pending, setPending] = useState(false);
  const [code, setCode] = useState("");
  const creds: RequestCredentials = "include";

  async function onRequestCode() {
    setPending(true);
    setMessage(null);
    const res = await postOtp(otpUrl, new URLSearchParams({ action: "request" }), creds);
    setPending(false);
    if (!res.ok) {
      setMessage(res.message);
      return;
    }
    setPhase("verify");
    const target = res.data.hint ?? emailHint;
    setMessage(`Код отправлен на ${target}. Проверьте входящие и папку «Спам».`);
  }

  async function onSubmitCode(e: React.FormEvent) {
    e.preventDefault();
    setPending(true);
    setMessage(null);
    const res = await postOtp(otpUrl, new URLSearchParams({ action: "verify", code: code.trim() }), creds);
    if (!res.ok) {
      setPending(false);
      setMessage(res.message);
      return;
    }
    setPending(false);
    if (!adminUsesRemoteApi()) {
      setMessage("Настройте NEXT_PUBLIC_ADMIN_API_URL");
      return;
    }
    router.replace("/admin/");
    router.refresh();
  }

  return (
    <div className="admin-login">
      <h1 className="page-title">Вход в админку</h1>
      <p className="label">
        Код приходит на почту <strong>{emailHint}</strong>.
      </p>
      {phase === "request" ? (
        <button type="button" className="btn btn--primary" disabled={pending} onClick={onRequestCode}>
          {pending ? "Отправляем…" : "Получить код"}
        </button>
      ) : (
        <form className="form" onSubmit={onSubmitCode}>
          <div className="field">
            <label className="label" htmlFor="admin-code">
              Код из письма
            </label>
            <input
              id="admin-code"
              name="code"
              className="input"
              type="text"
              inputMode="numeric"
              pattern="\d{6}"
              maxLength={6}
              required
              autoComplete="one-time-code"
              autoFocus
              value={code}
              onChange={(e) => setCode(e.target.value.replace(/\D/g, "").slice(0, 6))}
            />
          </div>
          <div className="form-actions">
            <button type="submit" className="btn btn--primary" disabled={pending || code.length !== 6}>
              {pending ? "Проверяем…" : "Войти"}
            </button>
            <button
              type="button"
              className="btn btn--secondary"
              disabled={pending}
              onClick={() => {
                setPhase("request");
                setCode("");
                setMessage(null);
              }}
            >
              Запросить код снова
            </button>
          </div>
        </form>
      )}
      {message && <p className={message.includes("отправлен") ? "notice" : "error"}>{message}</p>}
    </div>
  );
}
