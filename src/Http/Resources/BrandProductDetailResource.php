<?php

namespace Fpaipl\Prody\Http\Resources;

use Illuminate\Http\Request;
use Fpaipl\Prody\Models\ProductOption;
use Illuminate\Http\Resources\Json\JsonResource;
use Fpaipl\Brandy\Http\Resources\BrandStockResource;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class BrandProductDetailResource extends JsonResource
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
            $stockItems = $this->stock->stockItems->map(function ($stockItem) {
                return [
                    'key' => $stockItem->product_option_sid . '-' . $stockItem->product_range_sid,
                    'quantity' => $stockItem->quantity,
                    'mrp' => $stockItem->mrp,
                    // 'rate' => $stockItem->rate,
                    // 'discount' => $stockItem->discount,
                    // 'tax' => $stockItem->tax,
                    // 'hsn' => $stockItem->hsn,
                    // 'barcode' => $stockItem->barcode,
                    // 'expiry' => $stockItem->expiry,
                    // 'batch' => $stockItem->batch,
                    // 'status' => $stockItem->status,
                ];
            });
        } catch (\Exception $e) {
            $hasStock = false;
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
                    'mrp' => $range->mrp,
                    'rate' => $range->rate,
                ];
            }),
            'tags' => $this->tags,
            'ledgers' => collect($this->ledgers)->map(function ($ledger) {
                return [
                    'sid' => $ledger->sid,
                    'party' => [
                        'name' => $ledger->party->user->name,
                        'business' => $ledger->party->business,
                        'image' => $ledger->party->getImage(),
                    ],
                    'balance_qty' => $ledger->balance_qty,
                    'readyable_qty' => $ledger->readyable_qty,
                    'demandable_qty' => $ledger->demandable_qty,
                    'dispatchable_qty' => $ledger->dispatchable_qty,
                    'stockable_qty' => $ledger->getNetStockableQty(),
                ];
            }),
            'stock' => $hasStock ? new BrandStockResource($this->stock) : null,
            'stockitems' => $hasStock ? $stockItems  : [],
        ];
    }
}