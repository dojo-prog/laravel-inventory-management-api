<?php

use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\ProductQuantityController;
use App\Http\Controllers\Api\V1\ProductSupplierController;
use App\Http\Controllers\Api\V1\StockMovementController;
use App\Http\Controllers\Api\V1\SupplierController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::apiResource('categories', CategoryController::class);

    Route::apiResource('suppliers', SupplierController::class);

    Route::apiResource('products', ProductController::class);

    Route::prefix('/products/{product}')->group(function () {
        Route::get('suppliers', [ProductSupplierController::class, 'index']);
        Route::post('suppliers', [ProductSupplierController::class, 'store']);
        Route::get('suppliers/{supplier}', [ProductSupplierController::class, 'show']);
        Route::put('suppliers/{supplier}', [ProductSupplierController::class, 'update']);
        Route::delete('suppliers/{supplier}', [ProductSupplierController::class, 'destroy']);
    });

    Route::prefix('/products/{product}')->group(function () {
        Route::post('stock-in', [ProductQuantityController::class, 'stockIn']);
        Route::post('stock-out', [ProductQuantityController::class, 'stockOut']);
        Route::post('adjust', [ProductQuantityController::class, 'adjust']);
    });

    Route::prefix('/stock-movements')->group(function () {
        Route::get('/', [StockMovementController::class, 'index']);
        Route::get('/{stockMovement}', [StockMovementController::class, 'show']);
    });
});

require __DIR__.'/auth.php';
