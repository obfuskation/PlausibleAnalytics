<?php

return [
    'admin' => [
        'title' => 'Plausible Analytics',
        'description' => 'Connect Plausible (cloud or self-hosted CE) to measure traffic without cookies.',
        'section_general' => 'Tracking',
        'enabled' => 'Enable tracking',
        'enabled_help' => 'Loads the Plausible script on public pages.',
        'custom_snippet' => 'Plausible snippet',
        'custom_snippet_help' => 'Paste the full snippet from Plausible here. The module outputs it unchanged in the site <head>.',
        'track_admin' => 'Track admin panel',
        'track_admin_help' => 'If disabled, the script is not injected on /admin routes.',
    ],
];
