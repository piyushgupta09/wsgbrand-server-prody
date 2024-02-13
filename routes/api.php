<?php

use Illuminate\Support\Facades\Route;
use Fpaipl\Prody\Http\Coordinators\ProductCoordinator;

$assignableRoles = config('panel.assignable-roles');
$brandAccessRoles = array_column($assignableRoles['user-brand'], 'id');

Route::middleware(['api', 'auth:sanctum'])->prefix('api')->group(
    function () use ($brandAccessRoles) {
    
    Route::middleware('role:' . implode('|', $brandAccessRoles))->prefix('brand')->group(function () {
        Route::controller(ProductCoordinator::class)->group(function () {
            Route::get('products', 'brand_index');
            Route::get('products/{product}', 'brand_show');
            Route::post('products/{product}', 'brand_query');
            Route::post('products', 'brand_store');
            Route::put('products/{product}', 'brand_update');
        });
    });

});
