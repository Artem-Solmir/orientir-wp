import { join } from "node:path";

export function contentRoot(): string {
  return join(process.cwd(), "content");
}

export const postsDir = () => join(contentRoot(), "posts");
export const pagesDir = () => join(contentRoot(), "pages");
