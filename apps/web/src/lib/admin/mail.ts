import "server-only";
import nodemailer from "nodemailer";

export type OtpDelivery =
  | { mode: "smtp" }
  | { mode: "console"; smtpError?: string }
  | { mode: "failed"; message: string };

function transporter() {
  const host = process.env.SMTP_HOST?.trim();
  if (!host) return null;
  const port = Number(process.env.SMTP_PORT ?? "587");
  return nodemailer.createTransport({
    host,
    port,
    secure: port === 465,
    auth: process.env.SMTP_USER ? { user: process.env.SMTP_USER, pass: process.env.SMTP_PASS ?? "" } : undefined,
  });
}

export function smtpConfigured(): boolean {
  return Boolean(process.env.SMTP_HOST?.trim());
}

export async function sendOtpEmail(to: string, code: string): Promise<OtpDelivery> {
  const from = process.env.SMTP_FROM ?? process.env.SMTP_USER ?? "orientir@localhost";
  const subject = "Код входа в админку «Ориентир»";
  const text = `Код для входа: ${code}\n\nДействует 10 минут. Если вы не запрашивали вход — проигнорируйте письмо.`;

  const tx = transporter();
  if (!tx) {
    if (process.env.NODE_ENV !== "production") {
      console.warn(`[admin] SMTP не настроен — OTP для ${to}: ${code}`);
      return { mode: "console" };
    }
    return { mode: "failed", message: "SMTP не настроен (SMTP_HOST в .env)" };
  }

  try {
    await tx.sendMail({ from, to, subject, text });
    return { mode: "smtp" };
  } catch (e) {
    const message = e instanceof Error ? e.message : "Не удалось отправить письмо";
    console.error("[admin] SMTP error:", e);
    if (process.env.NODE_ENV !== "production") {
      console.warn(`[admin] OTP для ${to}: ${code}`);
      return { mode: "console", smtpError: message };
    }
    return { mode: "failed", message: `Ошибка почты: ${message}` };
  }
}
