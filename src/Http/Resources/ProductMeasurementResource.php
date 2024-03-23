<?php

namespace Fpaipl\Prody\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductMeasurementResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->measurekey->name,
            'value' => $this->measureval->value,
            'unit' => $this->measurekey->unit,
            'size' => $this->productRange->name ?? 'N/A',
            'product_range_slug' => $this->productRange->slug,
        ];
    }
}
