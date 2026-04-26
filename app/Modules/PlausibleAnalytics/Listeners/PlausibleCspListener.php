<?php

declare(strict_types=1);

namespace Flute\Modules\PlausibleAnalytics\Listeners;

use Flute\Core\Events\ResponseEvent;
use Flute\Modules\PlausibleAnalytics\Support\PlausibleInjectionGuard;

/**
 * Merges Plausible origins into Content-Security-Policy.
 *
 * @see https://plausible.io/docs/troubleshoot-integration
 */
final class PlausibleCspListener
{
    public static function onResponse(ResponseEvent $event): void
    {
        if (!PlausibleInjectionGuard::isActive()) {
            return;
        }

        $response = $event->getResponse();
        $contentType = (string) $response->headers->get('Content-Type', '');

        if ($response->headers->has('Content-Type') && !str_contains($contentType, 'text/html')) {
            return;
        }

        $current = (string) $response->headers->get('Content-Security-Policy', '');
        if ($current === '') {
            return;
        }

        $origins = PlausibleInjectionGuard::cspOrigins();
        if ($origins === []) {
            return;
        }

        $merged = self::mergeCsp($current, $origins);
        if ($merged !== $current) {
            $response->headers->set('Content-Security-Policy', $merged);
        }
    }

    /**
     * @param list<string> $origins
     */
    private static function mergeCsp(string $header, array $origins): string
    {
        $directives = self::parseCsp($header);
        $hasScriptRestriction = isset($directives['script-src']) || isset($directives['default-src']);
        $hasScriptElemRestriction = isset($directives['script-src-elem']) || isset($directives['default-src']);
        $hasConnectRestriction = isset($directives['connect-src']) || isset($directives['default-src']);

        if ($hasScriptRestriction && !isset($directives['script-src']) && isset($directives['default-src'])) {
            $directives['script-src'] = array_values($directives['default-src']);
        }

        if (
            $hasScriptElemRestriction
            && !isset($directives['script-src-elem'])
            && isset($directives['default-src'])
        ) {
            $directives['script-src-elem'] = array_values($directives['default-src']);
        }

        if ($hasConnectRestriction && !isset($directives['connect-src']) && isset($directives['default-src'])) {
            $directives['connect-src'] = array_values($directives['default-src']);
        }

        foreach ($origins as $origin) {
            if ($hasScriptRestriction && !in_array($origin, $directives['script-src'], true)) {
                $directives['script-src'] = array_values(array_diff($directives['script-src'], ["'none'"]));
                $directives['script-src'][] = $origin;
            }
            if ($hasScriptElemRestriction && !in_array($origin, $directives['script-src-elem'], true)) {
                $directives['script-src-elem'] = array_values(array_diff(
                    $directives['script-src-elem'],
                    ["'none'"],
                ));
                $directives['script-src-elem'][] = $origin;
            }
            if ($hasConnectRestriction && !in_array($origin, $directives['connect-src'], true)) {
                $directives['connect-src'] = array_values(array_diff($directives['connect-src'], ["'none'"]));
                $directives['connect-src'][] = $origin;
            }
        }

        if (
            $hasScriptRestriction
            && PlausibleInjectionGuard::hasInlineSnippet()
            && !in_array("'unsafe-inline'", $directives['script-src'], true)
        ) {
            $directives['script-src'][] = "'unsafe-inline'";
        }
        if (
            $hasScriptElemRestriction
            && PlausibleInjectionGuard::hasInlineSnippet()
            && !in_array("'unsafe-inline'", $directives['script-src-elem'], true)
        ) {
            $directives['script-src-elem'][] = "'unsafe-inline'";
        }

        return self::buildCsp($directives);
    }

    /**
     * @return array<string, list<string>>
     */
    private static function parseCsp(string $header): array
    {
        $directives = [];
        $parts = array_filter(array_map('trim', explode(';', $header)));

        foreach ($parts as $part) {
            if ($part === '') {
                continue;
            }
            if (!preg_match('/^([a-z0-9\-]+)\s*(.*)$/i', $part, $m)) {
                continue;
            }
            $name = strtolower($m[1]);
            $rest = trim((string) $m[2]);
            if ($rest === '') {
                $directives[$name] = [];

                continue;
            }
            $tokens = preg_split('/\s+/', $rest, -1, PREG_SPLIT_NO_EMPTY);
            $directives[$name] = $tokens !== false ? array_values($tokens) : [];
        }

        return $directives;
    }

    /**
     * @param array<string, list<string>> $directives
     */
    private static function buildCsp(array $directives): string
    {
        $segments = [];

        foreach ($directives as $name => $sources) {
            if ($sources === []) {
                $segments[] = $name;
            } else {
                $segments[] = $name . ' ' . implode(' ', $sources);
            }
        }

        return implode('; ', $segments);
    }
}
