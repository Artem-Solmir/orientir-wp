import { z } from "zod";

/** Photo hosts we know how to pull from later. Anything else is rejected to avoid arbitrary links. */
export const PHOTO_HOSTS = ["disk.yandex.ru", "disk.yandex.by", "disk.yandex.kz", "disk.yandex.com", "disk.yandex.uz", "yadi.sk"];

const SHARE_PATH = /^\/[di]\/[\w-]+\/?$/;
const EXAMPLE = "https://disk.yandex.ru/d/...";

/** A human hint for the first thing wrong with the link, or null when it is acceptable (an empty value is fine: the field is optional). */
export function photoUrlProblem(value: string): string | null {
  const text = value.trim();
  if (text === "") return null;
  if (/^http:\/\//i.test(text)) return `Ссылка должна начинаться с https://, например ${EXAMPLE}`;
  let url: URL;
  try {
    url = new URL(text);
  } catch {
    return `Это не похоже на ссылку. Скопируйте её целиком из адресной строки, например ${EXAMPLE}`;
  }
  if (url.protocol !== "https:") return `Ссылка должна начинаться с https://, например ${EXAMPLE}`;
  if (url.username || url.password) return "Уберите из ссылки логин и пароль";
  if (!PHOTO_HOSTS.includes(url.hostname)) return `Принимаются только ссылки Яндекс Диска (${EXAMPLE} или https://yadi.sk/d/...)`;
  if (!SHARE_PATH.test(url.pathname)) {
    return "Это ссылка на ваш личный кабинет, её никто кроме вас не откроет. На Яндекс Диске нажмите «Поделиться», включите доступ по ссылке и скопируйте её (она содержит /d/ или /i/)";
  }
  return null;
}

export function isPhotoUrl(value: string): boolean {
  return value.trim() !== "" && photoUrlProblem(value) === null;
}

const optionalPhotoUrl = z
  .string()
  .trim()
  .max(500, "Ссылка слишком длинная")
  .superRefine((v, ctx) => {
    const problem = photoUrlProblem(v);
    if (problem) ctx.addIssue({ code: "custom", message: problem });
  })
  .transform((v) => (v === "" ? null : v));
export const submissionSchema = z.object({
  name: z.string().trim().min(2, "Укажите имя").max(80),
  contact: z.string().trim().min(3, "Укажите email или телефон").max(120),
  title: z.string().trim().min(3, "Укажите название похода или мероприятия").max(200),
  message: z.string().trim().min(10, "Расскажите о походе хотя бы в двух предложениях").max(10_000),
  photosUrl: optionalPhotoUrl,
});

export type SubmissionInput = z.infer<typeof submissionSchema>;
