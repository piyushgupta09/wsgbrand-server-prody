<?php

namespace Fpaipl\Prody\Http\Coordinators;

use Fpaipl\Prody\Models\Brand;
use Fpaipl\Prody\Models\Product;
use Fpaipl\Prody\Http\Resources\BrandResource;
use Illuminate\Support\Facades\Cache;
use Fpaipl\Panel\Http\Coordinators\Coordinator;
use Fpaipl\Prody\Http\Resources\ProductResource;

class BrandCoordinator extends Coordinator
{
    /**
     * Get all categories
     */
    public function index()
    {
        Cache::forget('brands');
        $brands = Cache::remember('brands', 24 * 60 * 60, function () {
            return Brand::all();
        });
        return BrandResource::collection($brands);
    }

    /**
     * Display the specified resource.
     */
    public function show(Brand $brand)
    {
        $brand_id = $brand->id;
        Cache::forget('products'.$brand_id);
        $products = Cache::remember('products'.$brand_id, 24 * 60 * 60, function () use($brand_id) {
            return Product::with('category')->with('taxation')->with('colors')->where('brand_id', $brand_id)->get();
        });
        return ProductResource::collection($products);
    }
}
