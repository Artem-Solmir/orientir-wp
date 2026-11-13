/** FTP login usually lands on site root (activecloud: /, not /www/orientir.by). */
export function ftpRemoteRoot(envRoot = process.env.FTP_ROOT): { cd: string | null; label: string } {
  const raw = (envRoot ?? "/www/orientir.by").trim();
  if (!raw || raw === "/" || raw === ".") return { cd: null, label: "/" };
  const cd = raw.replace(/\/$/, "");
  return { cd, label: cd };
}
