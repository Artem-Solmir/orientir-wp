import { mediaUrl } from "./site";

export type Variant = { file: string; width: number; height: number };
export type SizedImage = { src: string; width: number; height: number };
type CoverLike = { path: string; width: number | null; height: number | null; variants?: unknown };

/** Variants come from a JSON column; anything malformed is dropped rather than trusted. */
export function parseVariants(value: unknown): Variant[] {
  if (!Array.isArray(value)) return [];
  return value.filter(
    (v): v is Variant =>
      typeof v === "object" && v !== null && typeof v.file === "string" && Number.isInteger(v.width) && Number.isInteger(v.height) && v.width > 0 && v.height > 0,
  );
}

const ASPECT_TOLERANCE = 0.03;
const MIN_SHARPNESS = 0.75;

/**
 * Smallest resized copy that is at least `target` px wide, preferring copies that keep the original
 * proportions (hard-cropped WordPress sizes lose parts of the picture). Falls back to the widest
 * copy that is still reasonably sharp, then to the original file.
 */
export function pickImage(cover: CoverLike, target: number): SizedImage {
  const originalW = cover.width ?? target;
  const originalH = cover.height ?? Math.round((target * 3) / 4);
  const original: SizedImage = { src: mediaUrl(cover.path), width: originalW, height: originalH };
  const variants = parseVariants(cover.variants);
  if (variants.length === 0 || originalW <= target) return original;

  const ratio = originalW / originalH;
  const sameShape = variants.filter((v) => Math.abs(v.width / v.height - ratio) <= ratio * ASPECT_TOLERANCE);
  const sharpEnough = (v: Variant) => v.width >= target * MIN_SHARPNESS;
  // Tiny proportional icons (80x53) must not hide larger cropped copies that are usable.
  const pool = sameShape.some(sharpEnough) ? sameShape : variants;
  const sorted = [...pool].sort((a, b) => a.width - b.width);
  const chosen = sorted.find((v) => v.width >= target) ?? sorted.at(-1)!;
  if (!sharpEnough(chosen)) return original;

  const dir = cover.path.includes("/") ? cover.path.slice(0, cover.path.lastIndexOf("/") + 1) : "";
  return { src: mediaUrl(`${dir}${chosen.file}`), width: chosen.width, height: chosen.height };
}