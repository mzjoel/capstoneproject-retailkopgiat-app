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
            $recommendations = $this->analyticService->fallbackRecommendations($user);
            return response()->json([
                'result' => ['status' => 'Success 200', 'message' => 'Recommendations generated'],
                'data' => $recommendations
            ], 200);

        } catch (\Exception $e) {
            Log::error("Recommendation Error: " . $e->getMessage());
            return response()->json([
                'result' => [
                    'status' => 'Error 500',
                    'message' => 'Failed to generate recommendations: ' . $e->getMessage()
                ]
            ], 500);
        }
    }

     public function getWeather()
    {
        try {
            $weather = $this->analyticService->getCurrentWeather();

            return response()->json([
                'result' => [
                    'status' => 'Success 200', 
                    'message' => 'Weather context retrieved successfully.'
                ],
                'data' => $weather
            ], 200);

        } catch (\Exception $e) {
            Log::error("Get Weather Error: " . $e->getMessage());
            return response()->json([
                'result' => [
                    'status' => 'Error 500',
                    'message' => 'Failed to retrieve weather context: ' . $e->getMessage()
                ]
            ], 500);
        }
    }

}