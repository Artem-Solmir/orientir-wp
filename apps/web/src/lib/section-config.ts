import { z } from "zod";

// Section configs come from the ETL (Presso Page Composer) and, later, from the agent.
export const sectionConfig = z.object({
  title: z.string().optional(),
  number: z.coerce.number().int().min(1).max(30).default(6),
  categorySlug: z.string().optional(),
  excludeCategorySlugs: z.string().optional(),
  show_pagination: z.coerce.number().default(0),
});

export type SectionConfig = z.infer<typeof sectionConfig>;

export const csv = (value: string | undefined) => (value ? value.split(",").filter(Boolean) : undefined);

export const isLatestSection = (type: string) => type === "latest" || type === "latest_category";
