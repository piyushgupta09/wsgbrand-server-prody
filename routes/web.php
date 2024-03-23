<?php

use Illuminate\Support\Facades\Route;
use Fpaipl\Prody\Http\Controllers\TaxController;
use Fpaipl\Prody\Http\Controllers\SyncController;
use Fpaipl\Prody\Http\Controllers\UnitController;
use Fpaipl\Prody\Http\Controllers\BrandController;
use Fpaipl\Prody\Http\Controllers\ProductController;
use Fpaipl\Prody\Http\Controllers\AttrikeyController;
use Fpaipl\Prody\Http\Controllers\CategoryController;
use Fpaipl\Prody\Http\Controllers\DiscountController;
use Fpaipl\Prody\Http\Controllers\MaterialController;
use Fpaipl\Prody\Http\Controllers\OverheadController;
use Fpaipl\Prody\Http\Controllers\StrategyController;
use Fpaipl\Prody\Http\Controllers\SupplierController;
use Fpaipl\Prody\Http\Controllers\FixedcostController;
use Fpaipl\Prody\Http\Controllers\CollectionController;
use Fpaipl\Prody\Http\Controllers\ConsumableController;
use Fpaipl\Prody\Http\Controllers\MeasurekeyController;
use Fpaipl\Prody\Http\Controllers\RefundPolicyController;
use Fpaipl\Prody\Http\Controllers\ReturnPolicyController;

Route::middleware(['web','auth'])->group(function () 
{   
    // Forward Routes

    Route::get('taxes', [TaxController::class, 'index'])->name('taxes.index');
    Route::get('taxes/{tax}', [TaxController::class, 'show'])->name('taxes.show');
    Route::get('sync/taxes', [SyncController::class, 'loadTaxes'])->name('sync.taxes');

    Route::get('units', [UnitController::class, 'index'])->name('units.index');
    Route::get('units/{unit}', [UnitController::class, 'show'])->name('units.show');
    Route::get('sync/units', [SyncController::class, 'loadUnits'])->name('sync.units');

    Route::get('brands', [BrandController::class, 'index'])->name('brands.index');
    Route::get('brands/{brand}', [BrandController::class, 'show'])->name('brands.show');
    Route::get('sync/brands', [SyncController::class, 'loadBrands'])->name('sync.brands');

    Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('categories/{category}', [CategoryController::class, 'show'])->name('categories.show');
    Route::get('sync/categories', [SyncController::class, 'loadCategories'])->name('sync.categories');
    
    Route::get('collections', [CollectionController::class, 'index'])->name('collections.index');
    Route::get('collections/{collection}', [CollectionController::class, 'show'])->name('collections.show');
    Route::get('sync/collections', [SyncController::class, 'loadCollections'])->name('sync.collections');
    
    // Backward Routes
    Route::resource('suppliers', SupplierController::class);
    Route::resource('materials', MaterialController::class);
    Route::get('sync/materials', [SyncController::class, 'loadMaterials'])->name('sync.materials');
    
    // App Routes
    Route::resource('products', ProductController::class)->withTrashed(['show']);
    Route::resource('attrikeys', AttrikeyController::class);
    Route::resource('measurekeys', MeasurekeyController::class);
    Route::resource('fixedcosts', FixedcostController::class);
    Route::resource('overheads', OverheadController::class);
    Route::resource('consumables', ConsumableController::class);
    Route::resource('strategies', StrategyController::class);
    Route::resource('discounts', DiscountController::class);
    Route::resource('refund-policies', RefundPolicyController::class);
    Route::resource('return-policies', ReturnPolicyController::class);
});
