<?php

use App\Modules\Analytics\Controllers\InteractionController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/user/interactions', [InteractionController::class, 'storeInteraction']);
    Route::get('/user/recommendations', [InteractionController::class, 'getPersonalizedRecommendations']);
});

Route::get('/analytics/weather', [InteractionController::class, 'getWeather']);