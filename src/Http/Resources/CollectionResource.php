<?php

namespace Fpaipl\Prody\Http\Resources;

use Illuminate\Http\Request;
use Fpaipl\Prody\Http\Resources\ProductCardResource;
use Illuminate\Http\Resources\Json\JsonResource;

class CollectionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'slug' => $this->slug,
            'image' => $this->getImage('s800'),
            'info' => $this->info,
            'products'=> ProductCardResource::collection($this->products),
        ];
    }
}
