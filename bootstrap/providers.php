<?php

use App\Providers\AppServiceProvider;
use App\Modules\Catalog\Providers\CatalogServiceProvider;
use App\Modules\Transactions\Providers\TransactionServiceProvider;
use App\Modules\Analytics\Providers\AnalyticServiceProvider;

return [
    AppServiceProvider::class,
    CatalogServiceProvider::class,
    TransactionServiceProvider::class,
    AnalyticServiceProvider::class,
];
