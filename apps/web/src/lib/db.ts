import "server-only";
import { PrismaClient } from "@prisma/client";

const globalForPrisma = globalThis as unknown as { prisma?: PrismaClient };

function createClient(): PrismaClient {
  const url = process.env.DATABASE_URL;
  if (!url || url.includes("connection_limit=")) return new PrismaClient();
  return new PrismaClient({ datasourceUrl: `${url}${url.includes("?") ? "&" : "?"}connection_limit=3` });
}

export const db = globalForPrisma.prisma ?? createClient();

if (process.env.NODE_ENV !== "production") {
  globalForPrisma.prisma = db;
}
