import { z } from "zod";

export const slugSchema = z
  .string()
  .max(100)
  .regex(/^[a-z0-9]+(?:-[a-z0-9]+)*$/, "slug: только a-z, 0-9 и дефисы");

const mediaPath = z
  .string()
  .max(300)
  .regex(/^(?!.*\.\.)[^\0\\:]+$/, "Недопустимый путь к медиафайлу");

const postFields = {
  title: z.string().trim().min(3).max(200),
  slug: slugSchema,
  excerpt: z.string().trim().max(500).nullable(),
  body: z.string().min(1).max(200_000),
  categories: z.array(slugSchema).max(10),
  tags: z.array(z.string().trim().min(1).max(60)).max(30),
  publishedAt: z.coerce.date(),
  expiresAt: z.coerce.date().nullable(),
  coverPath: mediaPath.nullable(),
  authorName: z.string().trim().max(80).nullable(),
  seoTitle: z.string().trim().max(200).nullable(),
  seoDescription: z.string().trim().max(300).nullable(),
};

export const postCreateSchema = z
  .object({
    title: postFields.title,
    body: postFields.body,
    slug: postFields.slug.optional(),
    excerpt: postFields.excerpt.optional(),
    categories: postFields.categories.default([]),
    tags: postFields.tags.default([]),
    publishedAt: postFields.publishedAt.optional(),
    expiresAt: postFields.expiresAt.optional(),
    coverPath: postFields.coverPath.optional(),
    authorName: postFields.authorName.optional(),
    seoTitle: postFields.seoTitle.optional(),
    seoDescription: postFields.seoDescription.optional(),
  })
  .strict();

export const postPatchSchema = z
  .object({
    title: postFields.title,
    body: postFields.body,
    excerpt: postFields.excerpt,
    categories: postFields.categories,
    tags: postFields.tags,
    publishedAt: postFields.publishedAt,
    expiresAt: postFields.expiresAt,
    coverPath: postFields.coverPath,
    authorName: postFields.authorName,
    seoTitle: postFields.seoTitle,
    seoDescription: postFields.seoDescription,
    status: z.enum(["published", "archived"]),
  })
  .partial()
  .strict()
  .refine((v) => Object.keys(v).length > 0, "Нужно передать хотя бы одно поле");

export const pagePatchSchema = z
  .object({ title: postFields.title, body: postFields.body })
  .partial()
  .strict()
  .refine((v) => Object.keys(v).length > 0, "Нужно передать хотя бы одно поле");

export const listQuerySchema = z.object({
  limit: z.coerce.number().int().min(1).max(100).default(20),
  offset: z.coerce.number().int().min(0).max(100_000).default(0),
  q: z.string().trim().min(2).max(100).optional(),
  status: z.string().max(20).optional(),
});

export type PostCreate = z.infer<typeof postCreateSchema>;
export type PostPatch = z.infer<typeof postPatchSchema>;
