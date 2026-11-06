import { z } from "zod";

const email = z.string().trim().toLowerCase().email().max(254);

// Length is capped to bound argon2 work (DoS) and floored per NIST 800-63B.
const password = z.string().min(10, "Минимум 10 символов").max(128);

export const loginSchema = z.object({
  email,
  password: z.string().min(1).max(128),
});

export const registerSchema = z.object({
  email,
  name: z.string().trim().min(2).max(80),
  password,
});

export type LoginInput = z.infer<typeof loginSchema>;
export type RegisterInput = z.infer<typeof registerSchema>;
