"use client";

import Link from "next/link";
import { useState } from "react";
import { createPostAction, updatePostAction } from "@/lib/admin/actions";

type Category = { slug: string; name: string };

type Initial = {
  slug: string;
  title: string;
  excerpt: string | null;
  body: string;
  authorName: string | null;
  categories: string[];
};

function PostFields({
  mode,
  categories,
  initial,
}: {
  mode: "create" | "edit";
  categories: Category[];
  initial?: Initial;
}) {
  return (
    <>
      <div className="field">
        <label className="label" htmlFor="title">
          Заголовок
        </label>
        <input id="title" name="title" className="input" required defaultValue={initial?.title} maxLength={200} />
      </div>
      {mode === "create" && (
        <div className="field">
          <label className="label" htmlFor="slug">
            Slug (необязательно)
          </label>
          <input id="slug" name="slug" className="input" pattern="[a-z0-9]+(?:-[a-z0-9]+)*" placeholder="avto-iz-zagolovka" />
        </div>
      )}
      <div className="field">
        <label className="label" htmlFor="excerpt">
          Краткое описание
        </label>
        <textarea id="excerpt" name="excerpt" className="input" rows={2} defaultValue={initial?.excerpt ?? ""} maxLength={500} />
      </div>
      <div className="field">
        <label className="label" htmlFor="authorName">
          Автор
        </label>
        <input id="authorName" name="authorName" className="input" defaultValue={initial?.authorName ?? ""} maxLength={80} />
      </div>
      <div className="field">
        <label className="label" htmlFor="categories">
          Рубрики (slug через запятую)
        </label>
        <input
          id="categories"
          name="categories"
          className="input"
          defaultValue={initial?.categories.join(", ") ?? ""}
          placeholder={categories.slice(0, 3).map((c) => c.slug).join(", ")}
        />
        <p className="label">Доступно: {categories.map((c) => c.slug).join(", ")}</p>
      </div>
      <div className="field">
        <label className="label" htmlFor="body">
          Текст (HTML)
        </label>
        <textarea id="body" name="body" className="input admin-body" required rows={18} defaultValue={initial?.body} />
      </div>
    </>
  );
}

export function AdminPostForm({
  mode,
  categories,
  initial,
}: {
  mode: "create" | "edit";
  categories: Category[];
  initial?: Initial;
}) {
  const [pending, setPending] = useState(false);
  const [message, setMessage] = useState<string | null>(null);

  async function onSubmit(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault();
    setPending(true);
    setMessage(null);
    const fd = new FormData(e.currentTarget);
    const res =
      mode === "create" ? await createPostAction(fd) : await updatePostAction(initial!.slug, fd);
    if (res && !res.ok) {
      setMessage(res.message ?? "Ошибка");
      setPending(false);
    }
  }

  return (
    <form className="form admin-form" onSubmit={onSubmit}>
      {message && <p className="error">{message}</p>}
      <PostFields mode={mode} categories={categories} initial={initial} />
      <div className="actions">
        <button type="submit" className="btn btn--primary" disabled={pending}>
          {pending ? "Сохраняем…" : mode === "create" ? "Опубликовать" : "Сохранить"}
        </button>
        <Link href="/admin" className="btn btn--secondary">
          К списку
        </Link>
        {initial && (
          <Link href={`/${initial.slug}`} className="btn btn--ghost" target="_blank" rel="noreferrer">
            На сайте
          </Link>
        )}
      </div>
    </form>
  );
}
