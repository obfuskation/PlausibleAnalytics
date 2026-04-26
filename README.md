# PlausibleAnalytics — модуль для Flute CMS

Модуль подключает [Plausible Analytics](https://plausible.io/) (облако или self-hosted) к сайту на **Flute CMS**: вставка сниппета в `<head>`, настройки в админке, учёт Content-Security-Policy.

**Версия модуля:** см. `app/Modules/PlausibleAnalytics/module.json` (текущий релиз: **1.0.0**).

## Требования

- Flute CMS `>= 1.0.0`
- PHP 8.1+ (как у ядра Flute)

## Установка

1. Скопируйте каталог модуля в проект Flute:

   ```
   <корень Flute>/app/Modules/PlausibleAnalytics/
   ```

   То есть структура репозитория повторяет путь внутри CMS: `app/Modules/PlausibleAnalytics/...`.

2. Установите модуль через стандартный механизм Flute (Modules / установщик), чтобы создалось право **`admin.plausible`**.

3. Назначьте право **`admin.plausible`** нужным ролям в админ-панели (и при необходимости **`admin`** — см. пакет модуля).

4. Откройте **Админка → Plausible Analytics** (`/admin/plausible-analytics`), вставьте готовый сниппет из Plausible и включите трекинг.

## Настройка

Файл конфигурации по умолчанию: `Resources/config/plausible.php`.

| Параметр | Описание |
|----------|-----------|
| `enabled` | Включить вывод сниппета на сайте |
| `custom_snippet` | Полный HTML/JS сниппет из Plausible (выводится в секцию `head` темы без изменений) |
| `track_admin` | Если `false`, сниппет не подключается на маршрутах `/admin` |

Сохранение из экрана админки перезаписывает этот файл и обновляет конфиг в памяти.

## Поведение

- Сниппет добавляется в секцию **`head`** шаблона (ожидается, что тема выводит `$sections['head']` внутри `<head>`).
- При включённом CSP модуль дополняет заголовок **Content-Security-Policy**: разрешает источники скриптов из `https://plausible.io` и хостов из `src` в вашем сниппете; при наличии inline-скрипта в сниппете может добавиться `'unsafe-inline'` для `script-src` / `script-src-elem` (см. `PlausibleCspListener`).
- Права: меню админки завязано на **`admin.plausible`**.

## Разработка и ветки

| Ветка | Назначение |
|-------|------------|
| **`main`** | Стабильная линия; на ней помечаются релизы (теги `v*`). |
| **`develop`** | Ветка для накопления изменений перед слиянием в `main`. |

Рекомендуемый поток: фичи в отдельных ветках от `develop` → PR в `develop` → после проверки merge в `main` → тег версии и GitHub Release.

## Релизы

См. [Releases](https://github.com/obfuskation/PlausibleAnalytics/releases). Версия в Git должна совпадать с полем `version` в `module.json`.

## Структура модуля

```
app/Modules/PlausibleAnalytics/
├── module.json
├── Installer.php
├── Admin/Package/          # экран настроек, маршруты, пункт меню
├── Listeners/              # CSP на ответе
├── Providers/              # сервис-провайдер модуля
├── Resources/
│   ├── config/plausible.php
│   └── lang/{en,ru}/plausible.php
└── Support/                # условия внедрения и разбор origin из сниппета
```

## Лицензия

Уточните лицензию у автора проекта Flute / владельца репозитория; при необходимости добавьте файл `LICENSE` в корень репозитория.

---

## PlausibleAnalytics — Flute CMS module (English)

This repository mirrors the path under a Flute project: copy `app/Modules/PlausibleAnalytics` into your Flute root. Configure the snippet and toggles in **Admin → Plausible Analytics**. See branch and release notes above.
