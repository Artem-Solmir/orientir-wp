import { describe, expect, it } from "vitest";
import { resolveMedia } from "./media-path";

const root = process.platform === "win32" ? "C:\\data\\uploads" : "/data/uploads";

describe("resolveMedia", () => {
  it("maps a normal upload path to a file inside the root with its content type", () => {
    const hit = resolveMedia(root, ["2023", "05", "photo-300x200.JPG"]);
    expect(hit?.contentType).toBe("image/jpeg");
    expect(hit?.file.toLowerCase()).toContain("photo-300x200.jpg");
  });

  it.each([
    [[]], [[".."]], [["..", "etc", "passwd.png"]], [["2023", "..", "..", "x.png"]], [["a", "", "b.png"]],
    [[".htaccess"]], [[".env.png"]], [["a\\b.png"]], [["a:b.png"]], [["x\0.png"]],
    [["shell.php"]], [["noext"]], [["x.php.txt"]], [["x.svg"]], [["x.html"]], [["a", "b", "c", "d", "e", "f", "g", "h", "i.png"]],
  ])("rejects %j", (segments) => {
    expect(resolveMedia(root, segments)).toBeNull();
  });

  it("allows documents and video used by old posts", () => {
    for (const name of ["a.pdf", "a.zip", "a.docx", "a.xls", "a.mp4", "a.webp"]) {
      expect(resolveMedia(root, ["2020", name])).not.toBeNull();
    }
  });

  it("keeps a file named like the root prefix inside the root only", () => {
    expect(resolveMedia(root, ["uploads-evil", "a.png"])?.file.startsWith(root)).toBe(true);
  });
});
