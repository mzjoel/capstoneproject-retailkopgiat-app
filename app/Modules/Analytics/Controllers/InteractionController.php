<?php

namespace App\Modules\Analytics\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Analytics\Services\AnalyticService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class InteractionController extends Controller{

    protected $analyticService;

    public function __construct(AnalyticService $analyticService)
    {
        $this->analyticService = $analyticService;
    }

    public function storeInteraction(Request $request){
        $validator = Validator::make($request->all(), [
            'interactions' => 'required|array|min:1|max:50',
            'interactions.*.product_id' => 'required|exists:products,id',
            'interactions.*.type' => 'required|string',
            'interactions.*.payload' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['result' => ['status' => 'Error 422', 'message' => $validator->errors()->first()]], 422);
        }

        try {
            $user = $request->user();
            if (!$user->customerProfile) {
                return response()->json([
                    'result' => ['status' => 'Error 404', 'message' => 'User profile not found. Please complete registration.']
                ], 404);
            }
            $customerProfileId = $user->customerProfile->id;
            $this->analyticService->logBatchInteractions($customerProfileId, $request->interactions);

            return response()->json([
                'result' => [
                    'status' => 'Success 201',
                    'message' => 'Interactions logged with context.'
                ]
            ], 201);
        } catch (\Exception $e) {
            Log::error("Tracking Error: " . $e->getMessage());
            return response()->json([
                'result' => [
                    'status' => 'Error 500',
                    'message' => $e->getMessage()
                ]
            ], 500);
        }
    }

     public function getPersonalizedRecommendations(Request $request)
    {
        try {
            $user = $request->user();
            $products = $this->interactionService->getHybridRecommendations($user);

            return response()->json([
                'result' => ['status' => 'Success 200', 'message' => 'Recommendations generated'],
                'data' => $products
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['result' => ['status' => 'Error 500', 'message' => $e->getMessage()]], 500);
        }
    }

    // private function getCurrentWeather()
    // {
    //     return Cache::remember('current_weather', 3600, function () {
    //         // Dalam produksi, gunakan OpenWeather API:
    //         // $response = Http::get("https://api.openweathermap.org/data/2.5/weather?q=Bandung&appid=".env('OPENWEATHER_KEY'));
    //         // return ['temp' => $response->json('main.temp') - 273.15, 'condition' => $response->json('weather.0.main')];

    //         // Mocking untuk pengembangan
    //         $conditions = ['Cloudy', 'Rain', 'Clear', 'Sunny'];
    //         return [
    //             'temp' => rand(24, 33),
    //             'condition' => $conditions[array_rand($conditions)],
    //             'location' => 'Kampus Giat'
    //         ];
    //     });
    // }

    // private function getCurrentWeatherContext()
    // {
    //     return Cache::remember('sys_weather_context', 1800, function () {
    //         // Simulasi/Integrasi API Cuaca
    //         $mockConditions = ['Sunny', 'Rainy', 'Cloudy', 'Overcast'];
    //         return [
    //             'temp' => rand(24, 33),
    //             'condition' => $mockConditions[array_rand($mockConditions)]
    //         ];
    //     });
    // }
}