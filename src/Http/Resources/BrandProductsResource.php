<?php

namespace Fpaipl\Prody\Http\Resources;

use Illuminate\Http\Request;
use Fpaipl\Prody\Models\ProductOption;
use Illuminate\Http\Resources\Json\JsonResource;
use Fpaipl\Brandy\Http\Resources\BrandStockResource;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class BrandProductsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        try {
            $hasStock = $this->stock;
        } catch (\Exception $e) {
            $hasStock = false;
        }

        $assignedparties = collect($this->ledgers)->map(function ($ledger) {
            return [
                'sid' => $ledger->party->sid,
                'name' => $ledger->party->user->name,
                'last_activity' => $ledger->last_activity,
                'updated_at' => $ledger->updated_at,
            ];
        });
        
        $assignedPartiesLastActivity = collect($assignedparties->sortByDesc('updated_at'))->first();
        $lastActivityName = 'Not available';
        if ($assignedPartiesLastActivity) {
            $lastActivityName = $assignedPartiesLastActivity->last_activity ?? 'No Activity';
        }

        return [
            // 'stock' => $this->quantity,
            'active' => $this->status == 'live',
            'brand' => [
                'name' => $this->brand->name,
                'image' => $this->brand->getImage(),
            ],
            'category' => [
                'name' => $this->category->name,
                'image' => $this->category->getImage(),
            ],
            'tax' => $this->taxation,
            'name' => $this->name,
            'slug' => $this->slug,
            'code' => $this->code,
            'details' => $this->details,
            'moq' => $this->moq,
            // Desicion making
            'ecomm' => $this->ecomm ? 'yes' : 'no',
            'retail' => $this->retail ? 'yes' : 'no',
            'inbulk' => $this->inbulk ? 'yes' : 'no',
            'offline' => $this->offline ? 'yes' : 'no',
            'vendor' => $this->vendor ? 'yes' : 'no',
            'factory' => $this->factory ? 'yes' : 'no',
            'market' => $this->market ? 'yes' : 'no',
            // Card
            'image' => $this->getImage(ProductOption::MEDIA_CONVERSION_THUMB),
            'preview' => $this->getImage(ProductOption::MEDIA_CONVERSION_PREVIEW),
            'options' =>  collect($this->productOptions)->map(function ($option) {
                return [
                    'sid' => $option->slug,
                    'name' => $option->name,
                    'image' => $option->getImage(ProductOption::MEDIA_CONVERSION_THUMB),
                    'preview' => $option->getImage(ProductOption::MEDIA_CONVERSION_PREVIEW),
                    'images' => collect($option->getMedia(ProductOption::MEDIA_COLLECTION_NAME))->map(function (Media $media) {
                        return [
                            'preview' => $media->getUrl(ProductOption::MEDIA_CONVERSION_PREVIEW),
                            'thumb' => $media->getUrl(ProductOption::MEDIA_CONVERSION_THUMB),
                            'banner' => $media->getUrl(ProductOption::MEDIA_CONVERSION_BANNER),
                            'full' => $media->getUrl(ProductOption::MEDIA_CONVERSION_FULL),
                        ];
                    }),
                ];
            }),
            'ranges' => collect($this->productRanges)->map(function ($range) {
                return [
                    'id' => $this->id,
                    'sid' => $range->slug,
                    'name' => config('prody.sizes')[$range->slug],
                    'abbr' => config('prody.size-abbr')[$range->slug],
                    'mrp' => round((float)$range->mrp),
                    'rate' => round((float)$range->price),                    
                ];
            }),
            'tags' => $this->tags,
            'assigned_parties' => $assignedparties,
            'last_activity' => $lastActivityName,
            'stock' => $hasStock ? new BrandStockResource($this->stock) : null,
        ];
    }
}