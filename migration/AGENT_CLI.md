# Операции агента (локальный CLI)

Контент в **`apps/web/content/`**. CLI читает и пишет JSON-файлы. Postgres не нужен.

Запуск из `apps/web`:

| Операция | Команда |
|---|---|
| Список записей | `npm run agent -- post:list --q=текст --status=published --limit=20` |
| Одна запись | `npm run agent -- post:get <slug>` |
| Создать | `npm run agent -- post:create файл.json` |
| Изменить | `npm run agent -- post:update <slug> файл.json` |
| Убрать с сайта | `npm run agent -- post:archive <slug>` |
| Страница | `npm run agent -- page:update <slug> файл.json` |
| Фото с Я.Диска | `npm run agent -- photos:import <url> [--apply]` |

После изменений: `npm run build:static` и `npm run deploy:ftp`.

Импорт фото пишет файлы в `MEDIA_ROOT` (локально или на сервер отдельно) и дописывает `content/media.json`.

Лог действий: `content/audit.log` (не коммитить).

Поля записи — те же, что в Zod-схемах в `scripts/agent/schemas.ts`.
