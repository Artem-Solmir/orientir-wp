import type { Metadata } from "next";
import { Manrope, Prata } from "next/font/google";
import { SiteFooter, SiteHeader } from "@/components/site-chrome";
import { SITE_DESCRIPTION, SITE_NAME, siteUrl } from "@/lib/site";
import "./globals.css";

const prata = Prata({ weight: "400", subsets: ["latin", "cyrillic"], variable: "--font-prata", display: "swap" });
const manrope = Manrope({ subsets: ["latin", "cyrillic"], variable: "--font-manrope", display: "swap" });

export const metadata: Metadata = {
  metadataBase: new URL(siteUrl()),
  title: { default: SITE_NAME, template: `%s | ${SITE_NAME}` },
  description: SITE_DESCRIPTION,
  openGraph: { siteName: SITE_NAME, locale: "ru_RU", type: "website" },
  alternates: { types: { "application/rss+xml": "/feed.xml" } },
};

export default function RootLayout({ children }: { children: React.ReactNode }) {
  return (
    <html lang="ru" className={`${prata.variable} ${manrope.variable}`}>
      <body>
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
