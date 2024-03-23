<?php

namespace Fpaipl\Prody\Http\Livewire;

use Livewire\Component;
use Fpaipl\Prody\Models\Product;
use Fpaipl\Prody\Models\Collection;
use Fpaipl\Prody\Models\CollectionProduct;

class ProductCollections extends Component
{
    public $collections;
    public $selectedCollection;
    public int $productId;
    public $product;
    public $productOptions;
    public $selectedProductOption;
    public $productCollections;
    public $routeValue;

    public function mount(int $modelId): void
    {
        $this->productId = $modelId;
        $this->product = Product::find($modelId);
        $this->productOptions = $this->product->productOptions;
        $this->collections = Collection::active()->get();
        $this->selectedProductOption = $this->productOptions?->first()->id;
        $this->productCollections = $this->product->collections;
        $this->routeValue = [
            'product' => $this->product->slug, 
            'section' => 'collections',
            'tab' => 'advance-details',
        ];
    }

    public function delete(int $collectionId)
    {
        $collectionProduct = CollectionProduct::where('collection_id', $collectionId)->where('product_id', $this->productId)->first();
        $collectionProduct->delete();
        return redirect()->route('products.show', $this->routeValue)->with('toast', [
            'class' => 'success',
            'text' => 'Product removed from collection successfully'
        ]);
    }

    public function store()
    {
        $this->validate([
            'selectedCollection' => 'required',
            'selectedProductOption' => 'required'
        ]);

        $collection = Collection::find($this->selectedCollection);

        CollectionProduct::updateOrCreate(
            [
                'wsg_collection_id' => $collection->wsg_id,
                'collection_id' => $collection->id,
                'product_id' => $this->productId,
            ],
            [
                'product_option_id' => $this->selectedProductOption
            ]
        );
        
        return redirect()->route('products.show', $this->routeValue)->with('toast', [
            'class' => 'success',
            'text' => 'Product added to collection successfully'
        ]);
    }

    public function getProductOption($collectionId)
    {
        return CollectionProduct::where('collection_id', $collectionId)->where('product_id', $this->productId)->first()->productOption;
    }

    public function render()
    {
        return view('prody::livewire.product-collections');
    }
}
