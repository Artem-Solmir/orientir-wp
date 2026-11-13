export type ContentStatus = "draft" | "published" | "archived";

export type CoverRecord = {
  path: string;
  width: number | null;
  height: number | null;
  alt: string | null;
  variants: unknown;
};

export type PostRecord = {
  slug: string;
  title: string;
  excerpt: string | null;
  body: string;
  status: ContentStatus;
  publishedAt: string | null;
  expiresAt: string | null;
  updatedAt: string | null;
  authorName: string | null;
  seoTitle: string | null;
  seoDescription: string | null;
  legacyWpId: number | null;
  cover: CoverRecord | null;
  categories: string[];
  tags: string[];
};

export type PageSection = { order: number; type: string; config: unknown };

export type PageRecord = {
  slug: string;
  title: string;
  body: string;
  template: "default" | "composer";
  status: ContentStatus;
  seoTitle: string | null;
  seoDescription: string | null;
  legacyWpId: number | null;
  updatedAt: string | null;
  sections: PageSection[];
};

export type TaxonRecord = { slug: string; name: string; legacyWpId?: number | null };

export type RedirectRecord = { from: string; to: string };

export type GalleryRecord = {
  slug: string;
  title: string;
  status: ContentStatus;
  legacyWpId: number | null;
  items: { path: string; order: number }[];
};

export type MediaMeta = {
  mimeType: string;
  width: number | null;
  height: number | null;
  alt: string | null;
  variants: unknown;
  legacyWpId: number | null;
};

export type DataStore = {
  posts: PostRecord[];
  postsBySlug: Map<string, PostRecord>;
  pages: PageRecord[];
  pagesBySlug: Map<string, PageRecord>;
  categories: TaxonRecord[];
  categoriesBySlug: Map<string, TaxonRecord>;
  tags: TaxonRecord[];
  tagsBySlug: Map<string, TaxonRecord>;
  media: Record<string, MediaMeta>;
  galleries: GalleryRecord[];
  redirects: RedirectRecord[];
};
