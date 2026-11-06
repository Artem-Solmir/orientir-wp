import Link from "next/link";

// Placeholder layout for Phase 1. The real home page (Page Composer sections) lands in Phase 3.
export default function Home() {
  return (
    <main className="mx-auto flex min-h-screen max-w-3xl flex-col items-start justify-center gap-6 p-8">
      <h1 className="text-3xl font-semibold">Ориентир</h1>
      <p className="text-neutral-600">Сайт в разработке.</p>
      <nav className="flex gap-4 underline">
        <Link href="/login">Вход</Link>
        <Link href="/register">Регистрация</Link>
      </nav>
    </main>
  );
}
