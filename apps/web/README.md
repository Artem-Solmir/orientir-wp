# Сайт «Ориентир» (Next.js)

Контент хранится в **`content/`** (JSON): записи, страницы, рубрики, медиа-метаданные, редиректы. Для разработки нужны только **Node.js** и зависимости npm; Postgres, Docker и XAMPP не требуются.

## Локально

```bash
cd apps/web
npm install
npm run dev
```

Фото в dev подтягиваются с продакшена: в `.env` задайте `MEDIA_DEV_ORIGIN=https://orientir.by` (по умолчанию так и есть).

Админка (опционально): `ENABLE_ADMIN=1`, `ADMIN_PASSWORD=...` в `.env`, перезапустить dev, открыть `/admin/login/`.

## Сборка и выкладка на activecloud (FTP)

```bash
npm run build:static
npm run deploy:ftp          # или --dry-run
```

В `.env`: `FTP_HOST`, `FTP_USER`, `FTP_PASS`, `FTP_ROOT=/www/orientir.by`. Скрипт **не трогает** `wp-content/uploads` на сервере.

Подробнее: [migration/CUTOVER.md](../../migration/CUTOVER.md), агент: [migration/AGENT_CLI.md](../../migration/AGENT_CLI.md).

## Проверки

```bash
npm run check
```
