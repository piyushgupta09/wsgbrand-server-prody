<?php

namespace Fpaipl\Prody\Http\Coordinators;

use Illuminate\Http\Request;
use Fpaipl\Panel\Http\Coordinators\Coordinator;
use Fpaipl\Prody\Http\Resources\ProductResource;

class MyShopCoordinator extends Coordinator
{
    public function index(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'data' => [
                'name' => $user->account->name,
                'contact' => $user->account->contact,
                'address' => $user->account->city,
                'image' => $user->getFirstMediaUrl('profile'),
                'products' => ProductResource::collection($user->my_shop_products()),
            ],
            'message' => 'success',
        ]);
    }
}
