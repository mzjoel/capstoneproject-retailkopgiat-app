<?php

namespace App\Modules\Analytics\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

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
    }
}