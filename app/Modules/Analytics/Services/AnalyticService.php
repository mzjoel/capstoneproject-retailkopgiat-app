<?php

namespace App\Modules\Analytics\Services;

use App\Modules\Analytics\Models\UserInteraction;
use App\Modules\Catalog\Models\Product;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

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
                // 'weather_condition' => 'null',
                // 'temperature' => 'null',
                'created_at' => $timestamp,
            ];
        }

        return UserInteraction::insert($preparedLogs);
    } 

     public function getHybridRecommendations($user)
    {
        $weather = $this->getCurrentWeatherSnapshot();
        
        $aiRecommendedIds = Redis::get("user_recommendations:{$user->id}");
        $aiRecommendedIds = $aiRecommendedIds ? json_decode($aiRecommendedIds) : [];

        $query = Product::with('category')->where('deleted_at', null);

        if ($weather['condition'] === 'Rainy') {
            $query->orderByRaw("CASE WHEN tags LIKE '%warm%' OR tags LIKE '%soup%' THEN 1 ELSE 2 END");
        } elseif ($weather['temp'] > 30) {
            $query->orderByRaw("CASE WHEN tags LIKE '%cold%' OR tags LIKE '%fresh%' THEN 1 ELSE 2 END");
        }

        if (!empty($aiRecommendedIds)) {
            $idsOrdered = implode(',', $aiRecommendedIds);
            $query->orderByRaw("FIELD(id, {$idsOrdered}) DESC");
        }

        return $query->take(6)->get();
    }

    private function getCurrentWeatherSnapshot()
    {
        return Cache::remember('current_weather', 600, function () {
            return [
                'condition' => 'Sunny',
                'temp' => 32,
            ];
        });
    }


}

