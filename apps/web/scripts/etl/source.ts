import mysql from "mysql2/promise";

/** Read-only access to the legacy WordPress database. Only SELECTs are issued. */
export type Source = {
  query<T>(sql: string): Promise<T[]>;
  close(): Promise<void>;
};

export async function openSource(url: string): Promise<Source> {
  const conn = await mysql.createConnection({ uri: url, charset: "utf8mb4" });
  return {
    async query<T>(sql: string) {
      const [rows] = await conn.query(sql);
      return rows as T[];
    },
    close: () => conn.end(),
  };
}

export type TermRow = { term_id: number; slug: string; name: string; taxonomy: "category" | "post_tag" };
export type RelRow = { object_id: number; term_id: number; taxonomy: string };
export type AttachmentRow = {
  ID: number;
  mime: string;
  file: string | null;
  alt: string | null;
  meta: string | null;
};
export type PostRow = {
  ID: number;
  post_name: string;
  post_title: string;
  post_content: string;
  post_excerpt: string;
  post_type: "post" | "page";
  published: Date | null;
  post_author: number;
};
export type MetaRow = { post_id: number; meta_key: string; meta_value: string | null };
export type ViewRow = { id: number; views: string };
export type UserRow = { ID: number; display_name: string };
export type GalleryRow = { ID: number; post_name: string; post_title: string; items: string | null };

// Pages worth keeping. Everything else among the 34 is Presso demo content (typography, columns,
// home-2/3, shortcodes...), WooCommerce/Theme-My-Login/bbPress scaffolding or empty stubs.
export const IMPORT_PAGE_IDS = [54, 88, 90, 473, 476, 479];

const GMT = "NULLIF(post_date_gmt, '0000-00-00 00:00:00')";

export const sql = {
  terms: `SELECT t.term_id, t.slug, t.name, x.taxonomy FROM wp_term_taxonomy x
          JOIN wp_terms t ON t.term_id = x.term_id WHERE x.taxonomy IN ('category','post_tag')`,
  relationships: `SELECT r.object_id, x.term_id, x.taxonomy FROM wp_term_relationships r
          JOIN wp_term_taxonomy x ON x.term_taxonomy_id = r.term_taxonomy_id
          WHERE x.taxonomy IN ('category','post_tag')`,
  attachments: `SELECT p.ID, p.post_mime_type AS mime,
          (SELECT meta_value FROM wp_postmeta WHERE post_id=p.ID AND meta_key='_wp_attached_file' LIMIT 1) AS file,
          (SELECT meta_value FROM wp_postmeta WHERE post_id=p.ID AND meta_key='_wp_attachment_image_alt' LIMIT 1) AS alt,
          (SELECT meta_value FROM wp_postmeta WHERE post_id=p.ID AND meta_key='_wp_attachment_metadata' LIMIT 1) AS meta
          FROM wp_posts p WHERE p.post_type='attachment'`,
  posts: `SELECT ID, post_name, post_title, post_content, post_excerpt, post_type, post_author,
          ${GMT} AS published FROM wp_posts
          WHERE post_status='publish' AND (post_type='post' OR (post_type='page' AND ID IN (${IMPORT_PAGE_IDS.join(",")})))`,
  postMeta: `SELECT post_id, meta_key, meta_value FROM wp_postmeta
          WHERE meta_key IN ('_thumbnail_id','_expiration-date','_wp_page_template') OR meta_key LIKE 'vw\\_composer\\_%'`,
  views: `SELECT id, SUM(count) AS views FROM wp_post_views WHERE type=0 GROUP BY id`,
  users: `SELECT ID, display_name FROM wp_users`,
  galleries: `SELECT g.ID, g.post_name, g.post_title,
          (SELECT meta_value FROM wp_postmeta WHERE post_id=g.ID AND meta_key='foogallery_attachments' LIMIT 1) AS items
          FROM wp_posts g WHERE g.post_type='foogallery' AND g.post_status='publish'`,
};
