"use client";

import { useSyncExternalStore } from "react";

type Theme = "dark" | "light";

const OPTIONS: { value: Theme; label: string }[] = [
  { value: "dark", label: "Тёмная" },
  { value: "light", label: "Светлая" },
];

const listeners = new Set<() => void>();

function subscribe(listener: () => void) {
  listeners.add(listener);
  return () => listeners.delete(listener);
}

// ThemeInit in the root layout sets <html data-theme> on the client before paint when possible.
function readTheme(): Theme {
  return document.documentElement.dataset.theme === "light" ? "light" : "dark";
}

function saveTheme(next: Theme) {
  document.documentElement.dataset.theme = next;
  try {
    localStorage.setItem("theme", next);
  } catch {
    // Storage can be blocked; the choice then lasts until the page is closed.
  }
  listeners.forEach((l) => l());
}

export function ThemeToggle() {
  const theme = useSyncExternalStore(subscribe, readTheme, () => null);
  return (
    <div className="theme-toggle" role="group" aria-label="Тема оформления">
      {OPTIONS.map((o) => (
        <button key={o.value} type="button" className="theme-option" aria-pressed={theme === o.value} onClick={() => saveTheme(o.value)}>
          {o.label}
        </button>
      ))}
    </div>
  );
}
