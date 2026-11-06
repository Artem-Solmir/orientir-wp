"use client";

import { useActionState } from "react";
import { submitMaterialsAction } from "./actions";

export function MaterialsForm() {
  const [state, action, pending] = useActionState(submitMaterialsAction, undefined);

  if (state?.sent) {
    return (
      <p role="status" className="notice">
        Спасибо! Материалы получены, мы свяжемся с вами по указанным контактам.
      </p>
    );
  }

  return (
    <form action={action} className="form">
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
        />
      </label>
      <div className="hp" aria-hidden="true">
        <label>
          Не заполняйте это поле
          <input name="website" type="text" tabIndex={-1} autoComplete="off" />
        </label>
      </div>
      {state?.error && (
        <p role="alert" className="error">
          {state.error}
        </p>
      )}
      <button type="submit" disabled={pending} className="btn btn--primary">
        Отправить
      </button>
    </form>
  );
}
