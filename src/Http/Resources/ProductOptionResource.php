<?php

namespace Fpaipl\Prody\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductOptionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'images' => [
                's100' => $this->getImage('s100'),
                's400' => $this->getImage('s400'),
                's800' => $this->getImage('s800'),
                's1200' => $this->getImage('s1200'),
                // 'base64' => $this->getBase64Image(),
            ],
            'fabric' => $this->materialOptions->first()->getImage('s100'),
        ];
    }
}
