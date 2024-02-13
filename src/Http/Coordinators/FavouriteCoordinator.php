<?php

namespace Fpaipl\Prody\Http\Coordinators;

use App\Models\User;
use Fpaipl\Prody\Models\Product;
use Fpaipl\Panel\Http\Coordinators\Coordinator;
use Fpaipl\Prody\Http\Resources\ProductResource;

class FavouriteCoordinator extends Coordinator
{
    public function index()
    {
        $authUser = User::find(auth()->user()->id);
        $productSlugs = $authUser->favourites->pluck('slug');
        return response()->json($productSlugs);
    }

    public function favourites()
    {
        $authUser = User::find(auth()->user()->id);
        $favourites = $authUser->favourites;
        if ($favourites->isEmpty()) {
            return response()->json('No favourites yet');
        } else {
            return ProductResource::collection($favourites);
        }
    }

    public function toggle(Product $product)
    {
        $authUser = User::find(auth()->user()->id);
        $authUser->favourites()->toggle($product->id);
        return response()->json(['message' => 'success']);
    }

}
