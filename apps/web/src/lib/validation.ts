import { z } from "zod";

/** Photo hosts we know how to pull from later. Anything else is rejected to avoid arbitrary links. */
export const PHOTO_HOSTS = ["disk.yandex.ru", "disk.yandex.by", "disk.yandex.com", "yadi.sk"];

export function isPhotoUrl(value: string): boolean {
  try {
    const url = new URL(value);
    return url.protocol === "https:" && !url.username && !url.password && PHOTO_HOSTS.includes(url.hostname);
  } catch {
    return false;
  }
}

const optionalPhotoUrl = z
  .string()
  .trim()
  .max(500)
  .refine((v) => v === "" || isPhotoUrl(v), "Нужна публичная ссылка на Яндекс Диск (https://disk.yandex.ru/...)")
  .transform((v) => (v === "" ? null : v));

export const submissionSchema = z.object({
  name: z.string().trim().min(2, "Укажите имя").max(80),
  contact: z.string().trim().min(3, "Укажите email или телефон").max(120),
  title: z.string().trim().min(3, "Укажите название похода или мероприятия").max(200),
  message: z.string().trim().min(10, "Расскажите о походе хотя бы в двух предложениях").max(10_000),
  photosUrl: optionalPhotoUrl,
});

export type SubmissionInput = z.infer<typeof submissionSchema>;
