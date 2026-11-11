/** Mountain silhouette when a post has no cover — same peak as the favicon. */
export function CardPlaceholder({ label }: { label?: string }) {
  return (
    <div className="card-placeholder" aria-hidden="true">
      <svg viewBox="0 0 64 48" className="card-placeholder__peak" focusable="false">
        <path d="M4 44 22 12l7 11 5-7 18 28z" fill="currentColor" opacity="0.35" />
        <path d="m22 12 5 9-4 2-3-3-4 3z" fill="currentColor" opacity="0.55" />
      </svg>
      {label && <span className="card-placeholder__label">{label}</span>}
    </div>
  );
}
