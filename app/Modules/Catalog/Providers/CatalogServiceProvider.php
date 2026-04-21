<?php

namespace App\Modules\Catalog\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class CatalogServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        if (file_exists($path = app_path('Modules/Catalog/Routes/api.php'))) {
            Route::prefix('api/v1')
                ->middleware('api')
                ->group($path);
        }
        
        if (is_dir($migrationPath = app_path('Modules/Catalog/Database/Migrations'))) {
            $this->loadMigrationsFrom($migrationPath);
        }
    }
}
