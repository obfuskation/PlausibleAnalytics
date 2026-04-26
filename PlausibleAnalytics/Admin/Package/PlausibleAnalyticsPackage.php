<?php

declare(strict_types=1);

namespace Flute\Modules\PlausibleAnalytics\Admin\Package;

use Flute\Admin\Support\AbstractAdminPackage;

class PlausibleAnalyticsPackage extends AbstractAdminPackage
{
    public function initialize(): void
    {
        parent::initialize();

        $this->loadRoutesFromFile('routes.php');
    }

    public function getPermissions(): array
    {
        return ['admin', 'admin.plausible'];
    }

    public function getMenuItems(): array
    {
        return [
            [
                'title' => __('plausible.admin.title'),
                'icon' => 'ph.bold.chart-line-bold',
                'url' => url('/admin/plausible-analytics'),
                'permission' => ['admin.plausible'],
                'permission_mode' => 'any',
            ],
        ];
    }

    public function getPriority(): int
    {
        return 92;
    }
}
