<?php

declare(strict_types=1);

namespace Flute\Modules\PlausibleAnalytics\Admin\Package\Screens;

use Flute\Admin\Platform\Actions\Button;
use Flute\Admin\Platform\Fields\TextArea;
use Flute\Admin\Platform\Fields\Toggle;
use Flute\Admin\Platform\Layouts\LayoutFactory;
use Flute\Admin\Platform\Screen;
use Flute\Core\Services\ConfigurationService;

class PlausibleAnalyticsSettingsScreen extends Screen
{
    public ?string $name = null;

    public ?string $description = null;

    public ?string $permission = 'admin.plausible';

    public function mount(): void
    {
        $this->name = __('plausible.admin.title');
        $this->description = __('plausible.admin.description');

        breadcrumb()->add(__('def.admin_panel'), (string) url('/admin'))->add(__('plausible.admin.title'));
    }

    public function commandBar(): array
    {
        return [
            Button::make(__('def.save'))
                ->icon('ph.regular.floppy-disk')
                ->method('save'),
        ];
    }

    public function layout(): array
    {
        return [
            LayoutFactory::columns([
                LayoutFactory::blank([
                    LayoutFactory::block([
                        LayoutFactory::field(
                            Toggle::make('enabled')->checked(filter_var(
                                request()->input('enabled', config('plausible.enabled', false)),
                                FILTER_VALIDATE_BOOLEAN,
                            )),
                        )
                            ->label(__('plausible.admin.enabled'))
                            ->popover(__('plausible.admin.enabled_help')),

                        LayoutFactory::field(
                            TextArea::make('custom_snippet')
                                ->rows(8)
                                ->value(request()->input('custom_snippet', config('plausible.custom_snippet', '')))
                                ->placeholder("<!-- Privacy-friendly analytics by Plausible -->\n<script async src=\"https://plausible.io/js/pa-...js\"></script>\n<script>\n  window.plausible=window.plausible||function(){(plausible.q=plausible.q||[]).push(arguments)},plausible.init=plausible.init||function(i){plausible.o=i||{}};\n  plausible.init()\n</script>"),
                        )
                            ->label(__('plausible.admin.custom_snippet'))
                            ->popover(__('plausible.admin.custom_snippet_help')),

                        LayoutFactory::field(
                            Toggle::make('track_admin')->checked(filter_var(
                                request()->input('track_admin', config('plausible.track_admin', false)),
                                FILTER_VALIDATE_BOOLEAN,
                            )),
                        )
                            ->label(__('plausible.admin.track_admin'))
                            ->popover(__('plausible.admin.track_admin_help')),
                    ])->title(__('plausible.admin.section_general')),
                ]),
            ]),
        ];
    }

    public function save(): void
    {
        $data = request()->input();

        $config = [
            'enabled' => filter_var($data['enabled'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'custom_snippet' => trim((string) ($data['custom_snippet'] ?? '')),
            'track_admin' => filter_var($data['track_admin'] ?? false, FILTER_VALIDATE_BOOLEAN),
        ];

        $configPath = path('app/Modules/PlausibleAnalytics/Resources/config/plausible.php');
        $configDir = dirname($configPath);

        if (!is_dir($configDir)) {
            @mkdir($configDir, 0755, true);
        }

        $written = @file_put_contents($configPath, '<?php return ' . var_export($config, true) . ";\n");

        if (function_exists('opcache_invalidate')) {
            @opcache_invalidate($configPath, true);
        }

        if ($written === false) {
            $this->flashMessage(__('def.server_error'), 'error');

            return;
        }

        config()->set('plausible', $config);

        app(ConfigurationService::class)->loadCustomConfig($configPath, 'plausible');

        $this->flashMessage(__('def.success'), 'success');
    }
}
