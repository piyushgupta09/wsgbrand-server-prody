<?php

namespace Fpaipl\Prody\Http\Resources;

use Illuminate\Http\Request;
use Fpaipl\Prody\Http\Resources\ProductOptionResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductCardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $images = $this->productOptions->map(function ($option) {
            return $option->getImage('s400');
        })->filter();

        return [
            'name' => $this->name,
            'slug' => $this->slug,
            'code' => $this->code,
            'mrp' => 500,
            'rate' => 500,
            'moq' => $this->moq,
            'image' => $images->first(),
            'images' => $images,
            'sizes' => $this->productRanges->pluck('name'),
            'options' => ProductOptionResource::collection($this->productOptions),
        ];
    }
}
