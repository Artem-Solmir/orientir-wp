import type { Metadata, Viewport } from "next";
import { Golos_Text, JetBrains_Mono, Unbounded } from "next/font/google";
import { SiteFooter, SiteHeader } from "@/components/site-chrome";
import { ThemeInit } from "@/components/theme-init";
import { SITE_DESCRIPTION, SITE_NAME, siteUrl } from "@/lib/site";
import "./globals.css";

const unbounded = Unbounded({ weight: ["500", "600"], subsets: ["latin", "cyrillic"], variable: "--font-unbounded", display: "swap" });
const golos = Golos_Text({ weight: ["400", "500"], subsets: ["latin", "cyrillic"], variable: "--font-golos", display: "swap" });
const mono = JetBrains_Mono({ weight: "500", subsets: ["latin", "cyrillic"], variable: "--font-mono", display: "swap" });

export const viewport: Viewport = {
  themeColor: [
    { media: "(prefers-color-scheme: light)", color: "oklch(97.5% 0.008 85)" },
    { media: "(prefers-color-scheme: dark)", color: "oklch(15% 0.012 70)" },
  ],
};

export const metadata: Metadata = {
  metadataBase: new URL(siteUrl()),
  title: { default: SITE_NAME, template: `%s | ${SITE_NAME}` },
  description: SITE_DESCRIPTION,
  openGraph: { siteName: SITE_NAME, locale: "ru_RU", type: "website" },
  alternates: { types: { "application/rss+xml": "/feed.xml" } },
};

export default function RootLayout({ children }: { children: React.ReactNode }) {
  return (
    <html lang="ru" data-theme="dark" suppressHydrationWarning className={`${unbounded.variable} ${golos.variable} ${mono.variable}`}>
      <body>
        <ThemeInit />
        <a className="skip-link" href="#content">
          К содержимому
        </a>
        <SiteHeader />
        <main id="content">{children}</main>
        <SiteFooter />
      </body>
    </html>
  );
}
