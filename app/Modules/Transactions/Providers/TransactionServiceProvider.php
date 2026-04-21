<?php

namespace App\Modules\Transactions\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class TransactionServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        if (file_exists($path = app_path('Modules/Transactions/Routes/api.php'))) {
            Route::prefix('api/v1')
                ->middleware('api')
                ->group($path);
        }
    }
}
