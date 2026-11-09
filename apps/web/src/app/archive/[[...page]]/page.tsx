import type { Metadata } from "next";
import { notFound } from "next/navigation";
import { Listing } from "@/components/listing";
import { parsePageSegments } from "@/lib/pagination";
import { archiveParams } from "@/lib/static-params";
import { absoluteUrl } from "@/lib/site";

export const dynamicParams = false;

export const generateStaticParams = archiveParams;

type Props = { params: Promise<{ page?: string[] }> };

export async function generateMetadata({ params }: Props): Promise<Metadata> {
  const n = parsePageSegments((await params).page);
  if (n === null) return {};
  return {
    title: n > 1 ? `Все записи, страница ${n}` : "Все записи",
    alternates: { canonical: absoluteUrl(n > 1 ? `/archive/page/${n}` : "/archive") },
  };
}

export default async function ArchivePage({ params }: Props) {
  const n = parsePageSegments((await params).page);
  if (n === null) notFound();
  return <Listing title="Все записи" filter={{}} page={n} base="/archive" />;
}
