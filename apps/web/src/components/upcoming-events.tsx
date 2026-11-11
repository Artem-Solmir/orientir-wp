import Link from "next/link";
import { listUpcomingPlans } from "@/lib/content";
import { formatDate } from "@/lib/text";

export async function UpcomingEvents() {
  const items = await listUpcomingPlans(5);
  if (items.length === 0) return null;

  return (
    <section className="section" aria-labelledby="upcoming-events-title">
      <div className="section-head">
        <h2 id="upcoming-events-title">Ближайшие мероприятия</h2>
        <Link href="/plany">Все планы</Link>
      </div>
      <ol className="event-list">
        {items.map((p) => (
          <li key={p.slug}>
            {p.expiresAt && (
              <time className="event-date" dateTime={p.expiresAt.toISOString()}>
                {formatDate(p.expiresAt)}
              </time>
            )}
            <h3>
              <Link href={`/${p.slug}`}>{p.title}</Link>
            </h3>
          </li>
        ))}
      </ol>
    </section>
  );
}
