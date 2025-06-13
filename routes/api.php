<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// 產品相關路由
Route::prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index']);
    Route::get('/{id}', [ProductController::class, 'show'])->where('id', '[0-9A-Z]{26}');
});

// 訂單相關路由
Route::prefix('orders')->group(function () {
    Route::get('/', [OrderController::class, 'index']);
    Route::post('/', [OrderController::class, 'store']);
    Route::get('/stats', [OrderController::class, 'stats']);
    Route::get('/{id}', [OrderController::class, 'show'])->where('id', '[0-9A-Z]{26}');
    Route::put('/{id}/status', [OrderController::class, 'updateStatus'])->where('id', '[0-9A-Z]{26}');
});
