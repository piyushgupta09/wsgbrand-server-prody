<?php

use Illuminate\Support\Facades\Route;
use Fpaipl\Prody\Http\Controllers\TaxController;
use Fpaipl\Prody\Http\Controllers\UnitController;
use Fpaipl\Prody\Http\Controllers\BrandController;
use Fpaipl\Prody\Http\Controllers\ProductController;
use Fpaipl\Prody\Http\Controllers\AttrikeyController;
use Fpaipl\Prody\Http\Controllers\CategoryController;
use Fpaipl\Prody\Http\Controllers\MaterialController;
use Fpaipl\Prody\Http\Controllers\OverheadController;
use Fpaipl\Prody\Http\Controllers\SupplierController;
use Fpaipl\Prody\Http\Controllers\FixedcostController;
use Fpaipl\Prody\Http\Controllers\CollectionController;
use Fpaipl\Prody\Http\Controllers\ConsumableController;
use Fpaipl\Prody\Http\Controllers\MeasurekeyController;

Route::middleware(['web','auth'])->group(function () {
    
    Route::resource('units', UnitController::class);
    Route::resource('taxes', TaxController::class);
    Route::resource('suppliers', SupplierController::class);
    Route::resource('brands', BrandController::class)->withTrashed(['show']);
    Route::resource('categories', CategoryController::class)->withTrashed(['show']);
    Route::resource('attrikeys', AttrikeyController::class);
    Route::resource('measurekeys', MeasurekeyController::class);
    Route::resource('products', ProductController::class)->withTrashed(['show']);
    Route::resource('collections', CollectionController::class)->withTrashed(['show']);
    Route::resource('materials', MaterialController::class);

    Route::resource('fixedcosts', FixedcostController::class);
    Route::resource('overheads', OverheadController::class);
    Route::resource('consumables', ConsumableController::class);
      
    Route::get('brands/advance-delete/{brand}', [BrandController::class, 'advance_delete'])->name('brands.advance.delete');
    Route::get('categories/advance-delete/{category}', [CategoryController::class, 'advance_delete'])->name('categories.advance.delete');
    Route::get('products/advance-delete/{product}', [ProductController::class, 'advance_delete'])->name('products.advance.delete');
    Route::get('collections/advance-delete/{collection}', [CollectionController::class, 'advance_delete'])->name('collections.advance.delete');

});
