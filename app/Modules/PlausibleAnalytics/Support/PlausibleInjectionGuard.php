<?php

declare(strict_types=1);

namespace Flute\Modules\PlausibleAnalytics\Support;

final class PlausibleInjectionGuard
{
    public static function isActive(): bool
    {
        if (!is_installed()) {
            return false;
        }

        if (!filter_var(config('plausible.enabled', false), FILTER_VALIDATE_BOOLEAN)) {
            return false;
        }

        if (is_admin_path() && !filter_var(config('plausible.track_admin', false), FILTER_VALIDATE_BOOLEAN)) {
            return false;
        }

        return trim((string) config('plausible.custom_snippet', '')) !== '';
    }

    /**
     * @return list<string> script-src / connect-src origins (scheme + host)
     */
    public static function cspOrigins(): array
    {
        $origins = ['https://plausible.io'];

        $customSnippet = trim((string) config('plausible.custom_snippet', ''));
        if ($customSnippet !== '') {
            foreach (self::originsFromSnippet($customSnippet) as $origin) {
                if (!in_array($origin, $origins, true)) {
                    $origins[] = $origin;
                }
            }
        }

        return array_values(array_unique($origins));
    }

    public static function hasInlineSnippet(): bool
    {
        $customSnippet = trim((string) config('plausible.custom_snippet', ''));
        if ($customSnippet === '') {
            return false;
        }

        return preg_match('/<script\b(?![^>]*\bsrc\s*=)[^>]*>/i', $customSnippet) === 1;
    }

    /**
     * @return list<string>
     */
    private static function originsFromSnippet(string $snippet): array
    {
        if (!preg_match_all('/<script\b[^>]*\bsrc\s*=\s*([\'"])(.*?)\1/i', $snippet, $matches)) {
            return [];
        }

        $origins = [];
        foreach ($matches[2] as $src) {
            $origin = self::originFromUrl((string) $src);
            if ($origin !== null && !in_array($origin, $origins, true)) {
                $origins[] = $origin;
            }
        }

        return $origins;
    }

    private static function originFromUrl(string $url): ?string
    {
        $parts = parse_url($url);
        if (empty($parts['scheme']) || empty($parts['host'])) {
            return null;
        }

        $scheme = $parts['scheme'];
        $host = $parts['host'];
        $port = isset($parts['port']) ? ':' . $parts['port'] : '';

        return $scheme . '://' . $host . $port;
    }
}
