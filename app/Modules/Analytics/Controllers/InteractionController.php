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
                    'result' => [
                        'status' => 'Success 200',
                        'message' => 'Tracking skipped: User profile not found.'
                    ]
                ], 200);
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

    public function getPersonalizedRecommendations(Request $request){
        try{
            $user = $request->user();
            $recommendations = $this->analyticService->getSmartRecommendations($user);

            return response()->json([
                'result' => [
                    'status' => 'Success 200',
                    'message' => 'Recommendations generated successfully.'
                ],
                'data' => $recommendations
            ], 200);

        }catch(\Exception $e){
            Log::error("Recommendation Error: " . $e->getMessage());
            return response()->json([
                'result' => [
                    'status' => 'Error 500',
                    'message' => 'Failed to generate recommendations: ' . $e->getMessage()
                ]
            ], 500);
        }
    }    

    public function fetchWishlist(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user || !$user->customerProfile) {
                return response()->json([
                    'result' => ['status' => 'Success 200', 'message' => 'User profile not found, returning empty wishlist.'],
                    'data' => []
                ], 200);
            }
            $customerProfileId = $user->customerProfile->id;
            
            $wishlistedProductIds = $this->analyticService->getWishlistProducts($customerProfileId);
            
            if ($request->query('include_products')) {
                $products = \App\Modules\Catalog\Models\Product::with('category')->whereIn('id', $wishlistedProductIds)->get();
                $formattedData = $products->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'price' => 'Rp ' . number_format($product->price, 0, ',', '.'),
                        'category' => $product->category->name ?? 'Uncategorized',
                        'description' => $product->description,
                        'image' => $product->image ?: 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=800&q=80',
                        'isFavorite' => true
                    ];
                });
                return response()->json([
                    'result' => ['status' => 'Success 200', 'message' => 'Wishlist retrieved'],
                    'data' => $wishlistedProductIds,
                    'products' => $formattedData
                ], 200);
            }
                
            return response()->json([
                'result' => ['status' => 'Success 200', 'message' => 'Wishlist retrieved'],
                'data' => $wishlistedProductIds
            ], 200);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Fetch Wishlist Error: " . $e->getMessage());
            return response()->json([
                'result' => [
                    'status' => 'Error 500',
                    'message' => 'Failed to fetch wishlist: ' . $e->getMessage()
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