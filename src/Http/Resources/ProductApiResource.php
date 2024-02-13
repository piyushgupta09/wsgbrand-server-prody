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

class ProductApiResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $fpr = $this->productRanges->first();
        return [
            'name' => $this->name,
            'slug' => $this->slug,
            'sid' => $this->code,
            // 'hsn_code' => $this->taxation->hsncode,
            // 'gst_rate' => $this->taxation->gstrate,
            // 'mrp' => $fpr->mrp,
            // 'rate' => $fpr->rate,
            // 'image' => $this->productOptions?->first()?->getImage('s400'),
            // 'images' => $this->productOptions?->map(fn ($productOption) => $productOption->getImages('s1200'))->flatten()->toArray(),
            // 'brand' => new ProductBrandResource($this->brand),
            // 'category' => new ProductCategoryResource($this->category),
            // 'materials' => ProductMaterialResource::collection($this->productMaterials),
            // 'options' => ProductOptionResource::collection($this->productOptions),
            // 'ranges' => ProductRangeResource::collection($this->productRanges),
        ];
    }
}
