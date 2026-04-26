<?php

use Flute\Core\Router\Router;
use Flute\Modules\PlausibleAnalytics\Admin\Package\Screens\PlausibleAnalyticsSettingsScreen;

Router::screen('/admin/plausible-analytics', PlausibleAnalyticsSettingsScreen::class);
