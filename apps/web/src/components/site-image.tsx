import Image, { type ImageProps } from "next/image";

type SiteImageProps = Omit<ImageProps, "loading" | "decoding"> & {
  priority?: boolean;
};

/** Static export uses unoptimized images; explicit loading/fetchPriority keeps LCP vs below-fold predictable. */
export function SiteImage({ priority = false, fetchPriority, alt = "", ...props }: SiteImageProps) {
  return (
    <Image
      {...props}
      alt={alt}
      priority={priority}
      loading={priority ? undefined : "lazy"}
      decoding={priority ? "sync" : "async"}
      fetchPriority={fetchPriority ?? (priority ? "high" : "low")}
    />
  );
}
