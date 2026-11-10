"use client";

import { useState, type FormEvent } from "react";
import { photoUrlProblem, submissionSchema } from "@/lib/validation";

// The static site has no server: the form posts to a small PHP script on the hosting (deploy/send.php).
const ENDPOINT = process.env.NEXT_PUBLIC_FORM_ENDPOINT ?? "/send.php";

type State = { kind: "idle" } | { kind: "sending" } | { kind: "sent" } | { kind: "error"; message: string };

async function send(payload: Record<string, string>): Promise<State> {
  try {
    const res = await fetch(ENDPOINT, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(payload),
    });
    if (res.ok) return { kind: "sent" };
    const data = (await res.json().catch(() => ({}))) as { error?: string };
    return { kind: "error", message: data.error ?? "Не удалось отправить. Попробуйте позже" };
  } catch {
    return { kind: "error", message: "Нет связи с сервером. Попробуйте позже" };
  }
}

export function MaterialsForm() {
  const [state, setState] = useState<State>({ kind: "idle" });
  const [photoHint, setPhotoHint] = useState<string | null>(null);

  async function onSubmit(event: FormEvent<HTMLFormElement>) {
    event.preventDefault();
    const data = Object.fromEntries(new FormData(event.currentTarget)) as Record<string, string>;
    const photoProblem = photoUrlProblem(data.photosUrl ?? "");
    if (photoProblem) {
      setPhotoHint(photoProblem);
      (event.currentTarget.elements.namedItem("photosUrl") as HTMLInputElement | null)?.focus();
      return;
    }
    const parsed = submissionSchema.safeParse(data);
    if (!parsed.success) {
      setState({ kind: "error", message: parsed.error.issues[0]?.message ?? "Проверьте введённые данные" });
      return;
    }
    setState({ kind: "sending" });
    setState(await send({ ...data }));
  }

  if (state.kind === "sent") {
    return (
      <p role="status" className="notice">
        Спасибо! Материалы получены, мы свяжемся с вами по указанным контактам.
      </p>
    );
  }

  return (
    <form onSubmit={onSubmit} className="form" noValidate>
      <label className="field">
        Ваше имя
        <input name="name" type="text" autoComplete="name" required minLength={2} maxLength={80} className="input" />
      </label>
      <label className="field">
        Email или телефон для связи
        <input name="contact" type="text" autoComplete="email" required minLength={3} maxLength={120} className="input" />
      </label>
      <label className="field">
        Название похода или мероприятия
        <input name="title" type="text" required minLength={3} maxLength={200} className="input" />
      </label>
      <label className="field">
        Рассказ, даты, маршрут, участники
        <textarea name="message" required minLength={10} maxLength={10000} rows={8} className="input" />
      </label>
      <label className="field">
        Ссылка на фотографии в Яндекс Диске
        <input
          name="photosUrl"
          type="url"
          inputMode="url"
          maxLength={500}
          placeholder="https://disk.yandex.ru/d/..."
          className="input"
          aria-invalid={photoHint ? true : undefined}
          aria-describedby="photos-help"
          onBlur={(e) => setPhotoHint(photoUrlProblem(e.currentTarget.value))}
          onChange={(e) => photoHint && setPhotoHint(photoUrlProblem(e.currentTarget.value))}
        />
        <span id="photos-help" role={photoHint ? "alert" : undefined} className={photoHint ? "error" : "label"}>
          {photoHint ?? "Необязательно. Папка с фото: «Поделиться» → доступ по ссылке."}
        </span>
      </label>
      <div className="hp" aria-hidden="true">
        <label>
          Не заполняйте это поле
          <input name="website" type="text" tabIndex={-1} autoComplete="off" />
        </label>
      </div>
      {state.kind === "error" && (
        <p role="alert" className="error">
          {state.message}
        </p>
      )}
      <button type="submit" disabled={state.kind === "sending"} className="btn btn--primary">
        Отправить
      </button>
    </form>
  );
}
