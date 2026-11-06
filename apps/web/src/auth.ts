import "server-only";
import NextAuth from "next-auth";
import Credentials from "next-auth/providers/credentials";
import { db } from "@/lib/db";
import { env } from "@/lib/env";
import { verifyDummy, verifyPassword } from "@/lib/password";
import { isRateLimited } from "@/lib/rate-limit";
import { loginSchema } from "@/lib/validation";

const WEEK = 7 * 24 * 60 * 60;

export const { handlers, auth, signIn, signOut } = NextAuth(() => {
  env(); // fail fast on missing/weak secrets
  return {
    session: { strategy: "jwt", maxAge: WEEK },
    pages: { signIn: "/login" },
    providers: [
      Credentials({
        credentials: { email: {}, password: {} },
        async authorize(raw, request) {
          const parsed = loginSchema.safeParse(raw);
          if (!parsed.success) return null;
          const { email, password } = parsed.data;

          const ip = request.headers.get("x-forwarded-for")?.split(",")[0]?.trim() ?? "unknown";
          const limited = await isRateLimited([
            { kind: "login-email", identifier: email, max: 8, windowSec: 15 * 60 },
            { kind: "login-ip", identifier: ip, max: 30, windowSec: 15 * 60 },
          ]);
          if (limited) return null;

          const user = await db.user.findUnique({ where: { email } });
          if (!user) {
            await verifyDummy(password);
            return null;
          }
          if (!(await verifyPassword(user.passwordHash, password))) return null;

          return { id: user.id, email: user.email, name: user.name };
        },
      }),
    ],
    callbacks: {
      async jwt({ token, user }) {
        if (user?.id) {
          // Only at sign-in: snapshot role and sessionVersion from the DB.
          const row = await db.user.findUnique({
            where: { id: user.id },
            select: { role: true, sessionVersion: true },
          });
          if (row) {
            token.uid = user.id;
            token.role = row.role;
            token.sv = row.sessionVersion;
          }
        }
        return token;
      },
      session({ session, token }) {
        if (typeof token.uid === "string") {
          session.user.id = token.uid;
          session.user.sv = token.sv ?? -1;
        }
        return session;
      },
    },
  };
});
