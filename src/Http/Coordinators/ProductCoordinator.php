<?php

namespace Fpaipl\Prody\Http\Coordinators;

use Illuminate\Http\Request;
use Fpaipl\Prody\Models\Product;
use Illuminate\Support\Facades\Cache;
use Fpaipl\Panel\Events\ReloadDataEvent;
use Fpaipl\Panel\Http\Responses\ApiResponse;
use Fpaipl\Panel\Http\Coordinators\Coordinator;
use Fpaipl\Prody\Http\Resources\ProductResource;
use Fpaipl\Prody\Http\Resources\ProductApiResource;
use Fpaipl\Prody\Http\Resources\BrandProductsResource;
use Fpaipl\Prody\Http\Resources\BrandProductDetailResource;

class ProductCoordinator extends Coordinator
{

    public function index()
    {
        $products = Product::with('productOptions', 'productRanges')
                            ->where('status', 'live')
                            ->paginate(10);

        return response()->json([
            'status' => 'success',
            'data' => ProductApiResource::collection($products)
        ]);
    }

    public function brand_index()
    {
        $products = Product::live()->with('productOptions', 'productRanges', 'stock', 'stock.stockItems')->paginate(10);

        if ($products->isEmpty()) {
            return ApiResponse::success([
                'data' => collect(),
                'pagination' => [
                    'total' => $products->total(),
                    'perPage' => $products->perPage(),
                    'currentPage' => $products->currentPage(),
                    'lastPage' => $products->lastPage(),
                ],
            ]);
        }

        return ApiResponse::success([
            'data' => BrandProductsResource::collection($products),
            'pagination' => [
                'total' => $products->total(),
                'perPage' => $products->perPage(),
                'currentPage' => $products->currentPage(),
                'lastPage' => $products->lastPage(),
            ],
        ]);
    }

    public function brand_show(Product $product)
    {
        $product->load('productOptions', 'productRanges', 'ledgers');
        return ApiResponse::success(new BrandProductDetailResource($product));
    }

    public function brand_update(Request $request, Product $product)
    {
        $request->validate([
            'qtype' => 'required|in:toggle,delete',
        ]);

        try {
            switch ($request->qtype) {
                case 'toggle':
                    $product->status = $product->status == 'live' ? 'draft' : 'live';
                    $product->save();
                    break;

                case 'delete':
                    // We can not delete stock if quantity of any sku of a product is greater than 0
                    // even if any one result is available then we can not delete any on the related also
                    break;

                default: break;
            }

            ReloadDataEvent::dispatch(Product::UPDATE_EVENT . '#' . $product->slug);
            return ApiResponse::success(new BrandProductDetailResource($product));
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 404);
        }
    }

    public function brand_query(Request $request, Product $product)
    {
        $request->validate([
            'qtype' => 'required|in:exists,stock,stock_sku,sku_count',
            'sku_id' => 'required_if:qtype,stock_sku|exists:stocks,sku'
        ]);

        $sid = null;
        $result = null;
        $result = 11;

        try {
            switch ($request->qtype) {
                case 'exists':
                    $result = $product->exists();
                    break;

                case 'stock_sku':
                    // $stock = Stock::where('product_sid', $request->product_sid)->where('sku', $request->sku_id)->firstOrFail();
                    // $sid = $stock->product_option_sid;
                    // $result = $stock->quantity;
                    break;

                case 'stock':
                    $result = $product->stocks->sum('quantity');
                    break;

                case 'sku_count':
                    $result = $product->stocks->count();
                    break;

                default:
                    break;
            }
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 404);
        }

        return ApiResponse::success([
            'sid' => $sid ?? null,
            $request->qtype => $result
        ]);
    }

    /**
     * Get Details of a product
     */
    public function show(Product $product)
    {
        $id = $product->id;
        Cache::forget('products'.$id);  // Is this line needed? Usually, you'd keep the cache.
        
        $product = Cache::remember('products'.$id, 24 * 60 * 60, function () use($id) {
            return Product::with('category', 'taxation', 'colors')
                        ->where('status', 'live')
                        ->find($id);
        });

        if (!$product) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product is not live or does not exist'
            ], 200);
        }

        return response()->json([
            'status' => 'success',
            'data' => new ProductResource($product)
        ]);
    }

    public function surprise()
    {
        $product = Product::inRandomOrder()->first();
        return new ProductResource($product);
    }
}
