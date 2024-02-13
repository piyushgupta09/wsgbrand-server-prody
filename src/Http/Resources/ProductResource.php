<?php

namespace Fpaipl\Prody\Http\Resources;

use Fpaipl\Prody\Models\Collection;
use Illuminate\Http\Request;
use Fpaipl\Prody\Http\Resources\CollectionResource;
use Fpaipl\Prody\Http\Resources\ProductBrandResource;
use Fpaipl\Prody\Http\Resources\ProductRangeResource;
use Fpaipl\Prody\Http\Resources\ProductOptionResource;
use Fpaipl\Prody\Http\Resources\ProductCategoryResource;
use Fpaipl\Prody\Http\Resources\ProductMaterialResource;
use Fpaipl\Shopy\Http\Resources\ReviewResource;
use Fpaipl\Prody\Http\Resources\ProductAttributeResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $inCart = $this->isInUserCart();
        // $inWishlist = $this->isInUserWishlist();

        $recommended = [];
        $collections = Collection::active()->with('products')->get()->groupBy('type');
        if ($collections->isNotEmpty()) {
            if ($collections->has('recommended')) {
                $recommended = CollectionResource::collection($collections->get('recommended'));
            }
        }

        $fpr = $this->productRanges->first();

        return [
            'name' => $this->name,
            'slug' => $this->slug,
            'code' => $this->code,
            'hsn_code' => $this->taxation->hsncode,
            'gst_rate' => $this->taxation->gstrate,
            'mrp' => $fpr->mrp,
            'discount' => number_format(($fpr->mrp - $fpr->rate) / $fpr->mrp * 100, 0),
            'rate' => $fpr->rate,
            'details' => $this->details,
            'moq' => $this->moq,
            'image' => $this->productOptions?->first()?->getImage('s400'),
            'base64' => $this->getBase64Image(),
            'images' => $this->productOptions?->map(fn ($productOption) => $productOption->getImages('s1200'))->flatten()->toArray(),
            'brand' => new ProductBrandResource($this->brand),
            'category' => new ProductCategoryResource($this->category),
            'materials' => ProductMaterialResource::collection($this->productMaterials),
            'options' => ProductOptionResource::collection($this->productOptions),
            'ranges' => ProductRangeResource::collection($this->productRanges),
            'attributes' => ProductAttributeResource::collection($this->attributes),
            'sharelink' => config('app.client_url') . '/product/' . $this->slug,
            'reviews' => new ReviewResource(''),
            'offers' => [
                (object) [
                    'icon' => 'bi bi-truck',
                    'title' => 'Pick-up & Drop Shipping facility',
                ],
                (object) [
                    'icon' => 'bi bi-shield-check',
                    'title' => 'Secure & Easy Payment System',
                ],
                (object) [
                    'icon' => 'bi bi-headset',
                    'title' => '10am to 6pm Support',
                ],
                (object) [
                    'icon' => 'bi bi-credit-card-2-front',
                    'title' => 'Defective Product Return Available',
                ],
            ],
            'recommended' => $recommended,
            'order_type' => $inCart ? $this->getUserCartProductOrderType() : null,
            'inMyCart' => $inCart,
            'cartProductItems' => $inCart ? $this->getUserCartProducts() : [],
        ];
    }
}
