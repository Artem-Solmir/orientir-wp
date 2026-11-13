"use client";

import Link from "next/link";
import { SiteImage } from "@/components/site-image";
import { useRef } from "react";

export type SlideItem = { href: string; title: string; image: { src: string; width: number; height: number } | null; date: string };

/**
 * Dependency-free carousel: a native scroll-snap track. It works without scripting
 * (the track simply scrolls); the buttons and arrow keys are an enhancement.
 */
export function Slider({ items, label }: { items: SlideItem[]; label: string }) {
  const track = useRef<HTMLUListElement>(null);

  const move = (direction: 1 | -1) => {
    const el = track.current;
    if (el) el.scrollBy({ left: direction * el.clientWidth * 0.8 });
  };

  const onKeyDown = (event: React.KeyboardEvent) => {
    if (event.key === "ArrowRight") move(1);
    else if (event.key === "ArrowLeft") move(-1);
    else return;
    event.preventDefault();
  };

  if (items.length === 0) return null;

  return (
    <section className="slider" aria-roledescription="carousel" aria-label={label}>
      <ul className="slider-track" ref={track} tabIndex={0} onKeyDown={onKeyDown} aria-label={`${label}, прокручивается стрелками`}>
        {items.map((item, i) => (
          <li key={item.href}>
            <article className="card">
              <Link href={item.href} className="card-media" tabIndex={-1} aria-hidden="true">
                {item.image && (
                  <SiteImage
                    src={item.image.src}
                    alt=""
                    width={item.image.width}
                    height={item.image.height}
                    sizes="(max-width: 40rem) 86vw, 30rem"
                    priority={i === 0}
                    className="card-photo"
                  />
                )}
              </Link>
              <div>
                <time className="label">{item.date}</time>
                <h3>
                  <Link href={item.href}>{item.title}</Link>
                </h3>
              </div>
            </article>
          </li>
        ))}
      </ul>
      <div className="slider-controls">
        <button type="button" className="btn btn--secondary" onClick={() => move(-1)} aria-label="Назад">
          Назад
        </button>
        <button type="button" className="btn btn--secondary" onClick={() => move(1)} aria-label="Вперёд">
          Вперёд
        </button>
      </div>
    </section>
  );
}
