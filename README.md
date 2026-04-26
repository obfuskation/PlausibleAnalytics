# PlausibleAnalytics — модуль для Flute CMS

Модуль подключает [Plausible Analytics](https://plausible.io/) (облако или self-hosted) к сайту на **Flute CMS**: вставка сниппета в `<head>`, настройки в админке, учёт Content-Security-Policy.

**Версия модуля:** см. `PlausibleAnalytics/module.json` (текущий релиз: **1.0.0**).

## Требования

- Flute CMS `>= 1.0.0`
- PHP 8.1+ (как у ядра Flute)

## Установка

В корне репозитория лежит каталог **`PlausibleAnalytics/`** — это сам модуль. Его нужно положить во Flute так:

```
<корень Flute>/app/Modules/PlausibleAnalytics/
```

Пример (PowerShell из корня Flute):

```powershell
Copy-Item -Path ".\PlausibleAnalytics" -Destination ".\app\Modules\PlausibleAnalytics" -Recurse
```

(путь к исходной папке укажите откуда клонировали этот репозиторий.)

Дальше:

1. Установите модуль через стандартный механизм Flute (Modules / установщик), чтобы создалось право **`admin.plausible`**.
2. Назначьте право **`admin.plausible`** нужным ролям (и при необходимости **`admin`** — см. пакет модуля).
3. Откройте **Админка → Plausible Analytics** (`/admin/plausible-analytics`), вставьте сниппет из Plausible и включите трекинг.

## Настройка

Внутри установленного модуля конфиг по умолчанию: `Resources/config/plausible.php` (относительно `app/Modules/PlausibleAnalytics/`).

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

См. [Releases](https://github.com/obfuskation/PlausibleAnalytics/releases). Версия в Git должна совпадать с полем `version` в `PlausibleAnalytics/module.json`.

## Структура репозитория

```
.
├── README.md
├── .gitignore
└── PlausibleAnalytics/     # скопировать в app/Modules/PlausibleAnalytics во Flute
    ├── module.json
    ├── Installer.php
    ├── Admin/Package/
    ├── Listeners/
    ├── Providers/
    ├── Resources/
    │   ├── config/plausible.php
    │   └── lang/{en,ru}/plausible.php
    └── Support/
```

## Лицензия

Уточните лицензию у автора проекта Flute / владельца репозитория; при необходимости добавьте файл `LICENSE` в корень репозитория.

---

## PlausibleAnalytics — Flute CMS module (English)

The **`PlausibleAnalytics/`** directory at the repo root is the module. Copy it to `<Flute root>/app/Modules/PlausibleAnalytics/`, then install and configure in **Admin → Plausible Analytics**. Branching and releases are described above.
