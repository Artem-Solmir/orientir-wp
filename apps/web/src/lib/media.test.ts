import { describe, expect, it } from "vitest";
import { parseVariants, pickImage } from "./media";

const cover = {
  path: "2023/05/photo.jpg",
  width: 1600,
  height: 1200,
  variants: [
    { file: "photo-150x150.jpg", width: 150, height: 150 },
    { file: "photo-360x270.jpg", width: 360, height: 270 },
    { file: "photo-360x200.jpg", width: 360, height: 200 },
    { file: "photo-800x600.jpg", width: 800, height: 600 },
  ],
};

describe("pickImage", () => {
  it("picks the smallest same-shape copy that is wide enough", () => {
    expect(pickImage(cover, 300)).toEqual({ src: "/wp-content/uploads/2023/05/photo-360x270.jpg", width: 360, height: 270 });
    expect(pickImage(cover, 500)).toEqual({ src: "/wp-content/uploads/2023/05/photo-800x600.jpg", width: 800, height: 600 });
  });

  it("ignores cropped copies of another shape when proportional ones exist", () => {
    const picked = pickImage(cover, 360);
    expect(picked.src).toContain("360x270");
  });

  it("falls back to the original when nothing is sharp enough or no variants exist", () => {
    expect(pickImage(cover, 1200).src).toBe("/wp-content/uploads/2023/05/photo.jpg");
    expect(pickImage({ ...cover, variants: null }, 300).src).toBe("/wp-content/uploads/2023/05/photo.jpg");
  });

  it("returns the original when it is already small", () => {
    expect(pickImage({ ...cover, width: 300, height: 225 }, 400).src).toBe("/wp-content/uploads/2023/05/photo.jpg");
  });

  it("uses the widest copy when it is within 75% of the target", () => {
    const small = { ...cover, variants: [{ file: "photo-360x270.jpg", width: 360, height: 270 }] };
    expect(pickImage(small, 460).src).toContain("360x270");
    expect(pickImage(small, 500).src).toBe("/wp-content/uploads/2023/05/photo.jpg");
  });

  it("is not fooled by a tiny proportional icon next to larger cropped copies", () => {
    const c = { path: "p.jpg", width: 800, height: 534, variants: [{ file: "p-80x53.jpg", width: 80, height: 53 }, { file: "p-750x420.jpg", width: 750, height: 420 }] };
    expect(pickImage(c, 400).src).toBe("/wp-content/uploads/p-750x420.jpg");
  });

  it("handles paths without a directory and uses all variants when none match the shape", () => {
    const flat = { path: "a.jpg", width: 1000, height: 1000, variants: [{ file: "a-400x200.jpg", width: 400, height: 200 }] };
    expect(pickImage(flat, 300).src).toBe("/wp-content/uploads/a-400x200.jpg");
  });
});

describe("parseVariants", () => {
  it("keeps valid entries only", () => {
    expect(parseVariants([{ file: "a.jpg", width: 1, height: 2 }, { file: 1 }, null, { file: "b.jpg", width: 0, height: 5 }])).toHaveLength(1);
    expect(parseVariants("nope")).toEqual([]);
  });
});