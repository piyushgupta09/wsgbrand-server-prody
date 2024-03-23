<?php

namespace Fpaipl\Prody\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductRangeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // optimized for wsg

        return [
            'name' => $this->name,
            'slug' => $this->slug,
            'mrp' => $this->mrp,
            'rate' => $this->rate,
            'active' => $this->active,
        ];
    }
}
