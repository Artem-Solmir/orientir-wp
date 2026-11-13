"use client";

import { useLayoutEffect } from "react";

/** Same logic as the old blocking inline script; safe with React 19 dynamic routes. */
export function ThemeInit() {
  useLayoutEffect(() => {
    try {
      let t = localStorage.getItem("theme");
      if (t !== "light" && t !== "dark") {
        t = matchMedia("(prefers-color-scheme: light)").matches ? "light" : "dark";
      }
      document.documentElement.dataset.theme = t;
    } catch {
      document.documentElement.dataset.theme = "dark";
    }
  }, []);
  return null;
}
