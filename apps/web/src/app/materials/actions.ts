"use server";

import { clientIp } from "@/lib/client-ip";
import { db } from "@/lib/db";
import { isRateLimited } from "@/lib/rate-limit";
import { submissionSchema } from "@/lib/validation";

export type SubmitState = { error?: string; sent?: boolean } | undefined;

export async function submitMaterialsAction(_prev: SubmitState, formData: FormData): Promise<SubmitState> {
  // Honeypot: real visitors never see or fill this field. Pretend success to bots.
  if (String(formData.get("website") ?? "") !== "") return { sent: true };

  const parsed = submissionSchema.safeParse({
    name: formData.get("name"),
    contact: formData.get("contact"),
    title: formData.get("title"),
    message: formData.get("message"),
    photosUrl: formData.get("photosUrl") ?? "",
  });
  if (!parsed.success) return { error: parsed.error.issues[0]?.message ?? "Проверьте введённые данные" };

  const ip = await clientIp();
  const limited = await isRateLimited([
    { kind: "submission-ip", identifier: ip, max: 5, windowSec: 60 * 60 },
    { kind: "submission-all", identifier: "global", max: 100, windowSec: 24 * 60 * 60 },
  ]);
  if (limited) return { error: "Слишком много отправок. Попробуйте позже" };

  await db.submission.create({ data: parsed.data });
  return { sent: true };
}
