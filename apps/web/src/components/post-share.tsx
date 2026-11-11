"use client";

import { useState } from "react";

export function PostShare({ title, url }: { title: string; url: string }) {
  const [hint, setHint] = useState<string | null>(null);

  async function share() {
    setHint(null);
    try {
      if (typeof navigator !== "undefined" && navigator.share) {
        await navigator.share({ title, url });
        return;
      }
      await navigator.clipboard.writeText(url);
      setHint("Ссылка скопирована");
    } catch {
      setHint("Не удалось поделиться");
    }
  }

  return (
    <div className="post-share">
      <button type="button" className="btn btn--secondary" onClick={() => void share()}>
        Поделиться
      </button>
      {hint && <span className="label post-share-hint">{hint}</span>}
    </div>
  );
}
