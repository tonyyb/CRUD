<?php

namespace Backpack\CRUD\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;

class PermissionsCommand extends Command
{
    protected $signature = 'permissions:generate';

    protected $description = 'Insert in database permissions for each CRUD controllers.';

    public function handle()
    {
        if (!$this->permissionsSystemAvailable()) {
            return $this->error('Pemissions system not available.');
        }

        $routes = collect(Route::getRoutes());
        $routes->each(function($route, $key) {
            if (str_contains($route->getName(), 'crud') ) {
                $controller = $route->getController();
                if (!empty($controller->crud) && method_exists($controller->crud, 'initPermissions')) {
                    $controller->crud->initPermissions(get_class($controller));
                }
            }
        });

        return $this->info('Permissions successfully installed.');
    }

    /**
     * Is the system of automatic permissions available ?
     *
     * @return bool
     */
    protected function permissionsSystemAvailable()
    {
        return class_exists('Backpack\PermissionManager\PermissionManagerServiceProvider') &&
            config('backpack.crud.activate_permissions_system', false);
    }
}
