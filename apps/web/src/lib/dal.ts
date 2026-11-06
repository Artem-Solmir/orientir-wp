import "server-only";
import { redirect } from "next/navigation";
import { auth } from "@/auth";
import { db } from "./db";

export type CurrentUser = { id: string; email: string; name: string; role: "user" | "owner" };

/**
 * Data Access Layer: the single place that turns a JWT into a trusted user.
 * Role and sessionVersion are re-read from the DB on every call, so password
 * changes (sessionVersion bump) and role changes take effect immediately.
 * The JWT's own claims are never trusted for authorization.
 */
export async function getCurrentUser(): Promise<CurrentUser | null> {
  const session = await auth();
  const id = session?.user?.id;
  if (!id) return null;

  const user = await db.user.findUnique({
    where: { id },
    select: { id: true, email: true, name: true, role: true, sessionVersion: true },
  });
  if (!user) return null;

  if (session.user.sv !== user.sessionVersion) return null;

  return { id: user.id, email: user.email, name: user.name, role: user.role };
}

export async function requireUser(): Promise<CurrentUser> {
  const user = await getCurrentUser();
  if (!user) redirect("/login");
  return user;
}

export async function requireOwner(): Promise<CurrentUser> {
  const user = await requireUser();
  if (user.role !== "owner") redirect("/");
  return user;
}
