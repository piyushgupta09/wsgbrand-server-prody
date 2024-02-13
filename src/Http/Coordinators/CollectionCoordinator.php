<?php

namespace Fpaipl\Prody\Http\Coordinators;

use Fpaipl\Prody\Models\Product;
use Fpaipl\Prody\Models\Collection;
use Illuminate\Support\Facades\Cache;
use Fpaipl\Panel\Http\Coordinators\Coordinator;
use Fpaipl\Prody\Http\Resources\CollectionResource;

class CollectionCoordinator extends Coordinator
{
    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        $featured = array();
        $ranged = array();
        $recommended = array();

        if (config('settings.cache.enabled')) {
            $collections = Cache::remember('collections', config('settings.cache.duration'), function () {
                return Collection::active()->with('products')->get()->groupBy('type');
            });
        } else {
            Cache::forget('collections');
            $collections = Collection::active()->with('products')->get()->groupBy('type');
        }
        
        if ($collections->isNotEmpty()) {
            if ($collections->has('featured')) {
                $featured = CollectionResource::collection(
                    $collections->get('featured')->filter(function($collection) {
                        return $collection->products->isNotEmpty();
                    })
                );
            }
            if ($collections->has('ranged')) {
                $ranged = CollectionResource::collection(
                    $collections->get('ranged')->filter(function($collection) {
                        return $collection->products->isNotEmpty();
                    })
                );
            }
            if ($collections->has('recommended')) {
                $recommended = CollectionResource::collection(
                    $collections->get('recommended')->filter(function($collection) {
                        return $collection->products->isNotEmpty();
                    })
                );
            }
        }
        
        return response()->json([
            'data' => [
                'featured' => empty($featured) ? [] : $featured,
                'ranged' => empty($ranged) ? [] : $ranged,
                'recommended' => empty($recommended) ? [] : $recommended,
            ]
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Collection $collection)
    {
        $collection->load(['products' => function ($query) {
            $query->status(Product::STATUS[1]);
        }]);

        return new CollectionResource($collection);
    }
}
