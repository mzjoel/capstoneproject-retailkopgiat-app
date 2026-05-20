<?php

namespace App\Modules\Analytics\Services;

use App\Modules\Analytics\Models\UserInteraction;
use App\Modules\Catalog\Models\Product;
use Illuminate\Support\Facades\Cache;
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
    
    public function getWishlistProducts(?int $customerProfileId)
    {
        if (!$customerProfileId) return [];
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

    public function getSmartRecommendations($user){
        try{
            $mlUrl = env('ML_ENGINE_URL', 'http://127.0.0.1:5000');
            $endpoint = $mlUrl . '/recommend/' . $user->id;
            $response = Http::timeout(5)->get($endpoint);

            if($response->successful() && $response->json('status') === 'success'){
                $data = $response->json();
                $recommendedItems = $data['recommendations'] ?? [];

                if(!empty($recommendedItems)){
                    $productIds = array_column($recommendedItems, 'id');
                    $idString = implode(',', $productIds);
                     $products = Product::where('is_available', true)
                        ->with('category')
                        ->whereIn('id', $productIds)
                        ->orderByRaw("FIELD(id, {$idString})")
                        ->get();

                    $formattedRecommendations = $products->map(function($product) use ($recommendedItems){
                        $mlInfo = collect($recommendedItems)->firstWhere('id', $product->id);
                        return [
                            'id' => $product->id,
                            'name' => $product->name,
                            'price' => (float) $product->price,
                            'category' => $product->category->name ?? 'Uncategorized',
                            'tags' => $product->tags,
                            'image_url' => $product->image_url,
                            'ai_score' => $mlInfo['score'] ?? 0
                        ];
                    });

                    return [
                        'context' => [
                            'weather' => ['condition' => $data['context']['weather']],
                            'user_preference' => $data['context']['user_prefs'],
                             'algorithm' => 'Hybrid Machine Learning (Python SVD)'
                        ],
                        'recommendations' => $formattedRecommendations
                    ];
                }
            }

            throw new \Exception("Response ML tidak valid");

        }catch(\Exception $e){
            Log::error("Analytic Service (ML Error) : " . $e->getMessage());
            return $this->fallbackRecommendations($user);
        }
    }
    
    public function fallbackRecommendations($user)
    {
        $weather = $this->getCurrentWeather();
        $userTaste = $user->customerProfile?->preferences['taste'] ?? 'general';

        $query = Product::where('is_available', true)->with('category');

        // A. Filter Cuaca (Rule-Based)
        if ($weather['condition'] === 'Rainy' || $weather['temp'] < 25) {
            $query->orderByRaw("CASE WHEN tags LIKE '%warm%' OR tags LIKE '%soup%' THEN 1 ELSE 2 END");
        } elseif ($weather['temp'] > 30) {
            $query->orderByRaw("CASE WHEN tags LIKE '%cold%' OR tags LIKE '%fresh%' THEN 1 ELSE 2 END");
        }

        // B. Filter Selera User (Rule-Based)
        if ($userTaste !== 'general') {
            $query->orderByRaw("CASE WHEN tags LIKE '%$userTaste%' THEN 1 ELSE 2 END");
        }

        $products = $query->take(5)->get();

        return [
            'context' => [
                'weather' => ['condition' => $weather['condition'], 'temp' => $weather['temp']],
                'user_preference' => $userTaste,
                'algorithm' => 'Weather-based Fallback (Laravel)'
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


    public function getCurrentWeather()
    {
        return Cache::remember('real_weather_context', 1800, function(){
            try {
                $apiKey = env('OPENWEATHER_API_KEY');
                $city = env('WEATHER_CITY', 'Bandung');

                if (empty($apiKey)) {
                    // Hanya info log biasa, tidak masalah di production
                    Log::info("[AnalyticService] OPENWEATHER_API_KEY kosong. Menggunakan Mock Data.");
                    return $this->getMockWeather();
                }

                $response = Http::timeout(5)->get("https://api.openweathermap.org/data/2.5/weather", [
                    'q' => $city,
                    'appid' => $apiKey,
                    'units' => 'metric'
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    return [
                        'temp' => (int) round($data['main']['temp']),
                        'condition' => $this->mapCondition($data['weather'][0]['main']),
                        'location' => $data['name']
                    ];
                }

                Log::warning("[AnalyticService] Gagal fetch Weather. Error: " . $response->body());
                return $this->getMockWeather();

            } catch (\Exception $e) {
                Log::error("Error Analytic Service (Weather) : " . $e->getMessage());
                return $this->getMockWeather();
            }
        });
    }

    private function mapCondition($apiCondition)
    {
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

