import type { MetadataRoute } from "next";
import { absoluteUrl } from "@/lib/site";

export default function robots(): MetadataRoute.Robots {
  return {
    rules: [{ userAgent: "*", allow: "/", disallow: ["/api/", "/profile", "/dashboard", "/search", "/login", "/register"] }],
    sitemap: absoluteUrl("/sitemap.xml"),
  };
}
