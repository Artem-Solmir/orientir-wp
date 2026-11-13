import type { NextConfig } from "next";
import { securityHeaders } from "./deploy/security-headers";

const isProd = process.env.NODE_ENV === "production";
// Static export cannot host server actions or force-dynamic pages; local admin turns export off (ENABLE_ADMIN=1).
const staticExport = process.env.ENABLE_ADMIN !== "1";

// The public site is exported to plain HTML (see docs in migration/CUTOVER.md). Security headers and
// redirects for production live in deploy/htaccess.template; the dev server below mirrors them.
const nextConfig: NextConfig = {
  ...(staticExport ? { output: "export" as const } : {}),
  trailingSlash: true, // same URLs as WordPress (/slug/) so nothing needs redirecting
  images: { unoptimized: true },
  experimental: { cpus: 4 },
  turbopack: {
    rules: {
      "*.css": {
        loaders: ["@tailwindcss/turbopack"],
        as: "*.css",
      },
    },
  },
  ...(isProd
    ? {}
    : {
        // Dev: proxy uploads from production host (uploads stay on the server).
        async rewrites() {
          const origin = (process.env.MEDIA_DEV_ORIGIN ?? "https://orientir.by").replace(/\/$/, "");
          return [{ source: "/wp-content/uploads/:path*", destination: `${origin}/wp-content/uploads/:path*` }];
        },
        async headers() {
          return [{ source: "/:path*", headers: securityHeaders(false) }];
        },
      }),
};

export default nextConfig;