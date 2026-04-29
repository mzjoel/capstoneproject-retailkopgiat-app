<?php

namespace App\Modules\Catalog\Services;

use App\Modules\Catalog\Models\Product;



class CatalogService
{

    public function getProductForCheckout($productId)
    {
        return Product::where('is_available', true)->find($productId);
    }
    

    public function getProductStats()
    {
        return Product::count();
    }
}