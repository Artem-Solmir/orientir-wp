export type SectionConfig = Record<string, string | number>;
export type ParsedSection = { order: number; type: string; config: SectionConfig };

function coerce(value: string): string | number {
  return /^-?\d+$/.test(value) ? Number(value) : value;
}

function sectionConfig(meta: Record<string, string>, n: number): SectionConfig {
  const prefix = `vw_composer_${n}_`;
  const config: SectionConfig = {};
  for (const [key, value] of Object.entries(meta)) {
    if (key.startsWith(prefix)) config[key.slice(prefix.length)] = coerce(value);
  }
  return config;
}

/** Presso Page Composer meta (vw_composer_N, vw_composer_N_*) -> ordered sections. Stops at the first gap. */
export function parseComposer(meta: Record<string, string>): ParsedSection[] {
  const sections: ParsedSection[] = [];
  for (let n = 1; meta[`vw_composer_${n}`] !== undefined; n++) {
    sections.push({ order: n - 1, type: meta[`vw_composer_${n}`]!, config: sectionConfig(meta, n) });
  }
  return sections;
}

/** Legacy category ids inside a section config -> slugs (ids are meaningless after migration). */
export function resolveCategories(config: SectionConfig, slugById: Map<number, string>): SectionConfig {
  const resolved: SectionConfig = { ...config };
  const category = Number(config.category);
  const slug = slugById.get(category);
  if (slug) resolved.categorySlug = slug;

  const excluded = String(config.exclude_categories ?? "")
    .split(",")
    .map((id) => slugById.get(Number(id.trim())))
    .filter((s): s is string => Boolean(s));
  if (excluded.length) resolved.excludeCategorySlugs = excluded.join(",");
  return resolved;
}
