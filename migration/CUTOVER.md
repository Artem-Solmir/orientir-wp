# Запуск: статический сайт на activecloud (orientir.by)

На проде только файлы из `apps/web/out` (HTML, CSS, JS) плюс `send.php` и **существующая** папка `wp-content/uploads/`. Контент для сборки лежит в репозитории в `apps/web/content/` (JSON). Для разработки и сборки нужен только **Node.js** — без Postgres, Docker и XAMPP.

## Требования к хостингу
- Apache с `mod_rewrite`, `mod_headers`, `mod_expires` и `AllowOverride All`.
- PHP 8.1+ для `send.php` (см. `deploy/send-config.example.php`).
- FTP/SFTP (activecloud): заливка через `npm run deploy:ftp` или вручную.
- Место: сайт ~50 МБ + uploads (~5,5 ГБ), отдельно на сервере.

## Сборка
`npm run build:static` → `apps/web/out`, `.htaccess`, `send.php`, 301 из `content/redirects.json` и shortlink `/?p=ID` для записей с `legacyWpId`.

Перед первой сборкой: скопировать `deploy/send-config.example.php` → `deploy/send-config.php`.

Переменная `PUBLIC_SITE_URL` (по умолчанию `https://orientir.by`) задаёт canonical в HTML.

## Выкладка
```bash
cd apps/web
npm run build:static
npm run deploy:ftp    # FTP_* в .env, корень FTP_ROOT=/www/orientir.by
```

Скрипт **не загружает и не удаляет** `wp-content/`.

## Порядок переключения с WordPress
1. Резервная копия WP (файлы + БД) вне хостинга.
2. Чистка uploads от `.php` / `.phtml` / `.phar`, `.htaccess` из `deploy/uploads.htaccess`.
3. Локально: `npm run check`, `npm run build:static`, просмотр `out/` или `npm run dev`.
4. Заливка на staging (поддомен), проверка формы, редиректов, картинок из uploads.
5. На бою: WP в `old-wp` (закрыто), в корне остаётся `wp-content/uploads/`, залить `out/`.
6. Sitemap в вебмастерах, неделя мониторинга 404.

## После запуска
Правки: админка (`ENABLE_ADMIN=1`, `npm run dev`), CLI агента (`AGENT_CLI.md`) или правка JSON в `content/` → коммит → `build:static` → `deploy:ftp`.

## Ограничения статики
- Новые/изменённые записи на сайте только после пересборки и заливки.
- `publishedAt` / `expiresAt` учитываются при сборке.
- Поиск по `search-index.json` (клиент).
- Форма — только email через PHP, без сохранения в БД.
