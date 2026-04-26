<?php

declare(strict_types=1);

namespace Flute\Modules\PlausibleAnalytics\Providers;

use Flute\Core\Events\ResponseEvent;
use Flute\Core\Support\ModuleServiceProvider;
use Flute\Modules\PlausibleAnalytics\Admin\Package\PlausibleAnalyticsPackage;
use Flute\Modules\PlausibleAnalytics\Listeners\PlausibleCspListener;
use Flute\Modules\PlausibleAnalytics\Support\PlausibleInjectionGuard;

class PlausibleAnalyticsProvider extends ModuleServiceProvider
{
    public array $extensions = [];

    public function boot(\DI\Container $container): void
    {
        $this->bootstrapModule();

        if (is_admin_path()) {
            $this->loadPackage(new PlausibleAnalyticsPackage());
        }

        if (is_installed()) {
            events()->addListener(ResponseEvent::NAME, [PlausibleCspListener::class, 'onResponse'], -200);
        }

        if (is_cli() || !PlausibleInjectionGuard::isActive()) {
            return;
        }

        $customSnippet = trim((string) config('plausible.custom_snippet', ''));

        // Plausible docs + automated "verify installation" expect the snippet in <head>.
        // Theme renders $sections['head'] inside <head> (see layouts/app.blade.php).
        template()->prependToSection('head', $customSnippet);
    }

    public function register(\DI\Container $container): void {}
}
