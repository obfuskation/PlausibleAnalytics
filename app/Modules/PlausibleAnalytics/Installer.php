<?php

declare(strict_types=1);

namespace Flute\Modules\PlausibleAnalytics;

use Flute\Core\Database\Entities\Permission;
use Flute\Core\ModulesManager\ModuleInformation;
use Flute\Core\Support\AbstractModuleInstaller;

class Installer extends AbstractModuleInstaller
{
    public function install(ModuleInformation &$module): bool
    {
        $permission = Permission::findOne(['name' => 'admin.plausible']);

        if (!$permission) {
            $permission = new Permission();
            $permission->name = 'admin.plausible';
            $permission->desc = 'Plausible Analytics';
        } else {
            // Plain label: avoid "plausible.*" keys so roles/uninstall never force loading
            // module lang files (e.g. missing en/plausible.php on server).
            if ($permission->desc === 'plausible.admin.title' || $permission->desc === '') {
                $permission->desc = 'Plausible Analytics';
            }
        }

        $permission->save();

        return true;
    }

    public function uninstall(ModuleInformation &$module): bool
    {
        $permission = Permission::findOne(['name' => 'admin.plausible']);

        if ($permission) {
            $permission->delete();
        }

        return true;
    }
}
