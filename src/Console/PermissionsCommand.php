<?php

namespace Backpack\CRUD\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;

class PermissionsCommand extends Command
{
    protected $signature = 'permissions:generate';

    protected $description = 'Discovers permissions for each CRUD controllers and creates them in database.';

    /**
     * Create permissions in database for each CRUD controllers.
     *
     * Available only if Backpack\PermissionManager is installed and if "apply_permissions"
     * is enabled (see the configuration file config/backpack/crud.php).
     */
    public function handle()
    {
        // Checks if the PermissionManagerServiceProvider exists
        if (!class_exists('Backpack\PermissionManager\PermissionManagerServiceProvider')) {
            return $this->error('Pemissions system not available.');
        }

        collect(Route::getRoutes())
            // Keeps only the routes handled by a CRUD controller
            ->filter(function($route) {
                return is_subclass_of($route->getController(), CrudController::class);
            })
            // Creates the missing permissions for each of them
            ->each(function($route) {
                if (method_exists($route->getController()->crud, 'createMissingPermissions')) {
                    $route->getController()->crud->createMissingPermissions();
                }
            });

        return $this->info('Permissions successfully installed.');
    }
}
