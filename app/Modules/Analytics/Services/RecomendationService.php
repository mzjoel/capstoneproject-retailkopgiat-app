<?php

namespace App\Modules\Analytics\Services;

use App\Modules\Catalog\Models\Product;
use App\Modules\Analytics\Services\WeatherService;
use Illuminate\Support\Facades\Cache;

class RecommendationService
{
    protected $weatherService;

    public function __construct(WeatherService $weatherService)
    {
        $this->weatherService = $weatherService;
    }

    /**
     * Menghasilkan rekomendasi produk berdasarkan cuaca saat ini 
     * dan preferensi rasa user (Initial Smart Logic).
     */
    public function getSmartRecommendations($user)
    {
        // 1. Dapatkan konteks cuaca
        $weather = $this->weatherService->getCurrentWeather();
        
        // 2. Dapatkan preferensi rasa user dari profil (diisi saat registrasi)
        $preferences = $user->customerProfile->preferences ?? [];
        $userTaste = $preferences['taste'] ?? 'general';

        // 3. Query Produk (Baseline)
        $query = Product::where('is_available', true)->with('category');

        /**
         * STRATEGI REKOMENDASI V1 (Rule-based)
         * Logika ini akan digantikan oleh AWS Lambda ML di fase selanjutnya.
         */
        
        // A. Filter berdasarkan Cuaca (Environment Context)
        if ($weather['condition'] === 'Rainy' || $weather['temp'] < 25) {
            // Cuaca dingin/hujan -> utamakan tag 'warm' atau 'soup'
            $query->orderByRaw("CASE WHEN tags LIKE '%warm%' OR tags LIKE '%soup%' THEN 1 ELSE 2 END");
        } elseif ($weather['temp'] > 30) {
            // Cuaca panas -> utamakan tag 'cold' atau 'fresh'
            $query->orderByRaw("CASE WHEN tags LIKE '%cold%' OR tags LIKE '%fresh%' THEN 1 ELSE 2 END");
        }

        // B. Filter berdasarkan Preferensi User (User Context)
        if ($userTaste !== 'general') {
            $query->orderByRaw("CASE WHEN tags LIKE '%$userTaste%' THEN 1 ELSE 2 END");
        }

        // Ambil 6 produk teratas hasil pengurutan cerdas
        return $query->take(6)->get();
    }
}