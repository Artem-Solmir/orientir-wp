/**
 * Create a user from the CLI (dev/ops only).
 * Usage: CREATE_USER_PASSWORD=... npx tsx scripts/create-user.ts <email> <name> [user|owner]
 * The password comes from the environment so it never lands in shell history or argv.
 */
import { PrismaClient } from "@prisma/client";
import { hashPassword } from "../src/lib/password";
import { registerSchema } from "../src/lib/validation";

async function main() {
  const [email, name, role = "user"] = process.argv.slice(2);
  if (role !== "user" && role !== "owner") throw new Error("role must be user or owner");

  const input = registerSchema.parse({ email, name, password: process.env.CREATE_USER_PASSWORD });
  const db = new PrismaClient();
  try {
    const user = await db.user.upsert({
      where: { email: input.email },
      create: { email: input.email, name: input.name, role, passwordHash: await hashPassword(input.password) },
      update: {
        name: input.name,
        role,
        passwordHash: await hashPassword(input.password),
        sessionVersion: { increment: 1 },
      },
    });
    console.log(`ok: ${user.email} (${user.role})`);
  } finally {
    await db.$disconnect();
  }
}

main().catch((error: unknown) => {
  console.error(error instanceof Error ? error.message : error);
  process.exit(1);
});
