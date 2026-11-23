"use client";

import { usePathname } from "next/navigation";
import { useEffect, useRef, type ReactNode } from "react";

/**
 * Full-width menu built on a native <details>, so it works without scripting.
 * The layout survives client navigation, so the panel is closed whenever the route changes.
 */
export function SiteMenu({ children }: { children: ReactNode }) {
  const ref = useRef<HTMLDetailsElement>(null);
  const pathname = usePathname();

  useEffect(() => {
    ref.current?.removeAttribute("open");
  }, [pathname]);

  useEffect(() => {
    const details = ref.current;
    if (!details) return;

    const syncPanelTop = () => {
      const header = document.querySelector<HTMLElement>(".site-header");
      if (!header) return;
      document.documentElement.style.setProperty("--menu-panel-top", `${header.offsetHeight}px`);
    };

    const onToggle = () => {
      if (details.open) syncPanelTop();
    };

    syncPanelTop();
    details.addEventListener("toggle", onToggle);
    window.addEventListener("resize", syncPanelTop);
    return () => {
      details.removeEventListener("toggle", onToggle);
      window.removeEventListener("resize", syncPanelTop);
    };
  }, []);

  return (
    <details className="menu" ref={ref}>
      <summary className="menu-toggle">
        <span className="menu-label">Меню</span>
        <span className="burger" aria-hidden="true" />
      </summary>
      <div className="menu-panel">{children}</div>
    </details>
  );
}
