"use server";

import { AuthError } from "next-auth";
import { Prisma } from "@prisma/client";
import { signIn, signOut } from "@/auth";
import { clientIp } from "@/lib/client-ip";
import { db } from "@/lib/db";
import { hashPassword } from "@/lib/password";
import { isRateLimited } from "@/lib/rate-limit";
import { loginSchema, registerSchema } from "@/lib/validation";

export type FormState = { error?: string } | undefined;

// Fixed destination: never take a redirect target from user input (open redirect).
const AFTER_LOGIN = "/profile";

export async function loginAction(_prev: FormState, formData: FormData): Promise<FormState> {
  const parsed = loginSchema.safeParse({
    email: formData.get("email"),
    password: formData.get("password"),
  });
  if (!parsed.success) return { error: "Проверьте email и пароль" };

  try {
    await signIn("credentials", { ...parsed.data, redirectTo: AFTER_LOGIN });
  } catch (error) {
    if (error instanceof AuthError) return { error: "Неверный email или пароль" };
    throw error; // redirect() is implemented as a throw and must propagate
  }
}

export async function registerAction(_prev: FormState, formData: FormData): Promise<FormState> {
  const parsed = registerSchema.safeParse({
    email: formData.get("email"),
    name: formData.get("name"),
    password: formData.get("password"),
  });
  if (!parsed.success) {
    return { error: parsed.error.issues[0]?.message ?? "Проверьте введённые данные" };
  }

  const ip = await clientIp();
  const limited = await isRateLimited([
    { kind: "register-ip", identifier: ip, max: 5, windowSec: 60 * 60 },
  ]);
  if (limited) return { error: "Слишком много попыток. Попробуйте позже" };

  try {
    await db.user.create({
      data: {
        email: parsed.data.email,
        name: parsed.data.name,
        passwordHash: await hashPassword(parsed.data.password),
        role: "user", // owner is never assignable through public registration
      },
    });
  } catch (error) {
    if (error instanceof Prisma.PrismaClientKnownRequestError && error.code === "P2002") {
      return { error: "Не удалось зарегистрироваться с этими данными" };
    }
    throw error;
  }

  try {
    await signIn("credentials", {
      email: parsed.data.email,
      password: parsed.data.password,
      redirectTo: AFTER_LOGIN,
    });
  } catch (error) {
    if (error instanceof AuthError) return { error: "Аккаунт создан, войдите вручную" };
    throw error;
  }
}

export async function logoutAction(): Promise<void> {
  await signOut({ redirectTo: "/" });
}
