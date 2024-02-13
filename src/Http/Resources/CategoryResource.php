<?php

namespace Fpaipl\Prody\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            'parent' => isset($this->parent) ? $this->parent->name : 'Top',
            'name' => $this->name,
            'slug' => $this->slug,
            'info' => $this->info,
            'image' => $this->getImage('s400'),
            // 'secondary_images' => $this->getImage('s400', 'secondary', true),
        ];
    }
}
