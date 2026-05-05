<?php

namespace App\Modules\Analytics\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use App\Modules\Analytics\Console\Commands\CheckIntegrationML;

class AnalyticServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Daftarkan route otomatis untuk modul Analytics
        if (file_exists($path = app_path('Modules/Analytics/Routes/api.php'))) {
            Route::prefix('api/v1')
                ->middleware('api')
                ->group($path);
        }

        if (file_exists($webPath = app_path('Modules/Analytics/Routes/web.php'))) {
            Route::middleware('web')->group($webPath);
        }

        if ($this->app->runningInConsole()) {
            $this->commands([
                CheckIntegrationML::class,
            ]);
        }
    }
}