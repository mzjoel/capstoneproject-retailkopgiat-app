<?php

namespace App\Modules\Analytics\Services;

use App\Modules\Analytics\Models\UserInteraction;
use App\Modules\Catalog\Models\Product;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class AnalyticService{
    public function logBatchInteractions(int $customerProfileId, array $interactions)
    {
        $timestamp = now();
        $preparedLogs = [];

        foreach ($interactions as $item) {
            $preparedLogs[] = [
                'customer_profile_id' => $customerProfileId,
                'product_id' => $item['product_id'],
                'type' => $item['type'],
                'duration_seconds' => $item['payload']['duration'] ?? null,
                'created_at' => $timestamp,
            ];
        }

        return UserInteraction::insert($preparedLogs);
    }
    
    public function getWishlistProducts(int $customerProfileId)
    {
        $latestInteractions = UserInteraction::where('customer_profile_id', $customerProfileId)
            ->whereIn('type', ['wishlist', 'unwishlist'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->unique('product_id');

        return $latestInteractions->where('type', 'wishlist')
            ->pluck('product_id')
            ->values()
            ->toArray();
    }
    
    public function fallbackRecommendations($user){
        $weather = $this->getCurrentWeather();
        $userTaste = $user->customerProfile->preferences['taste'] ?? 'general';
        $query = Product::where('is_available', true)->with('category');
        try {
            $aiRecommendedIds = Redis::get("user_recommendations:{$user->id}");
            $aiRecommendedIds = $aiRecommendedIds ? json_decode($aiRecommendedIds) : [];
        } catch (\Throwable $e) {
            Log::warning("Redis tidak tersedia atau gagal mengambil data: " . $e->getMessage());
            $aiRecommendedIds = [];
        }

        if ($weather['condition'] === 'Rainy' || $weather['temp'] < 25) {
            $query->orderByRaw("CASE WHEN tags LIKE '%warm%' OR tags LIKE '%soup%' THEN 1 ELSE 2 END");
        } elseif ($weather['temp'] > 30) {
            $query->orderByRaw("CASE WHEN tags LIKE '%cold%' OR tags LIKE '%fresh%' THEN 1 ELSE 2 END");
        }

        if ($userTaste !== 'general') {
            $query->orderByRaw("CASE WHEN tags LIKE '%$userTaste%' THEN 1 ELSE 2 END");
        }

        if (!empty($aiRecommendedIds)) {
            $idsOrdered = implode(',', $aiRecommendedIds);
            $query->orderByRaw("FIELD(id, {$idsOrdered}) DESC");
        }

        $products = $query->take(6)->get();

        return [
            'context' => [
                'weather' => $weather, 
                'user_preference' => $userTaste,
                'algorithm' => empty($aiRecommendedIds) ? 'Weather-based + Profiling' : 'Hybrid AI + Weather'
            ],
            'recommendations' => $products->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => (float) $product->price,
                    'category' => $product->category->name ?? 'Uncategorized',
                    'tags' => $product->tags,
                    'image_url' => $product->image_url
                ];
            })
        ];

    }


    public function getCurrentWeather(){
        return Cache::remember('real_weather_context', 1800, function(){
            try{
                $apiKey = env('OPENWEATHER_API_KEY');
                $city = env('WEATHER_CITY', 'Bandung');

                if (empty($apiKey)){
                    Log::info("[WeatherService] OPENWEATHER_API_KEY kosong. Menggunakan Mock Data.");
                    return $this->getMockWeather();
                }

                $response = Http::timeout(5)->get("https://api.openweathermap.org/data/2.5/weather", [
                    'q' => $city,
                    'appid' => $apiKey,
                    'units' => 'metric'
                ]);

                if ($response->successful()){
                    $data = $response->json();
                    return [
                        'temp' => (int) round($data['main']['temp']),
                        'condition' => $this->mapCondition($data['weather'][0]['main']),
                        'location' => $data['name']
                    ];
                }

                Log::warning("[WeatherService] Gagal fetch Weather. Error: " . $response->body());
                return $this->getMockWeather();

            }catch (\Exception $e){
                Log::error("Error Weather Service : " . $e->getMessage());
                return $this->getMockWeather();
            }
        });
    }

    private function mapCondition($apiCondition){
        $condition = strtolower($apiCondition);

        if (in_array($condition, ['clear'])) return 'Sunny';
        if (in_array($condition, ['clouds'])) return 'Cloudy';
        if (in_array($condition, ['rain', 'drizzle', 'thunderstorm', 'squall'])) return 'Rainy';
        if (in_array($condition, ['snow'])) return 'Snowy';

        return 'Overcast';
    }

    private function getMockWeather(){
        $mockConditions = ['Sunny', 'Rainy', 'Cloudy', 'Overcast'];
        return [
            'temp' => rand(24, 33),
            'condition' => $mockConditions[array_rand($mockConditions)],
            'location' => 'Kampus Giat (Mock)'
        ];
    }


}

