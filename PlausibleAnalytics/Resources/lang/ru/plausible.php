<?php

return [
    'admin' => [
        'title' => 'Plausible Analytics',
        'description' => 'Подключение Plausible (облако или self-hosted CE) для статистики без cookie.',
        'section_general' => 'Сбор статистики',
        'enabled' => 'Включить трекинг',
        'enabled_help' => 'Подключает скрипт Plausible на публичных страницах.',
        'custom_snippet' => 'Готовый сниппет Plausible',
        'custom_snippet_help' => 'Вставьте сюда код из Plausible целиком. Модуль выведет его без изменений в <head> сайта.',
        'track_admin' => 'Считать админ-панель',
        'track_admin_help' => 'Если выключено, скрипт не подключается на маршрутах /admin.',
    ],
];
