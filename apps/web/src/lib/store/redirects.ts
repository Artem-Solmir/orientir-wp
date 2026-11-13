import { loadStore, listShortlinkRedirects } from "./reader";

export function legacyRedirects(): { from: string; to: string }[] {
  const store = loadStore();
  return store.redirects.map((r) => ({ from: r.from, to: r.to }));
}

export function wpShortlinks(): { wpId: number; slug: string }[] {
  return listShortlinkRedirects();
}
