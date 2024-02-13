<?php

namespace Fpaipl\Prody\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;
use Fpaipl\Prody\Models\Product;
use Illuminate\Support\Facades\DB;
use Fpaipl\Prody\Models\ProductOption;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ProductStock extends Component
{
    use WithFileUploads;

    public $showForm;

    // Variables to hold form data
    public $productId;
    public $product;

    public $formType;
    public $routeValue;

    public $stock;
    public $stockItems;
    public $needToGenerateStock;

    public function mount($modelId)
    {
        $this->productId = $modelId;
        $this->showForm = false;
        $this->product = Product::find($modelId);
        $this->stock = $this->product->stock;
        $this->stockItems = $this->stock?->stockItems;
        $this->resetForm();
    }

    public function toggleForm()
    {
        $this->showForm = !$this->showForm;
    }

    public function resetForm()
    {
        $this->formType = 'create';
        $this->reloadData();
    }

    public function reloadData()
    {
        $this->checkAllStockExists();
        $this->routeValue = [
            'tab' => request()->tab,
            'product' => $this->product->slug,
            'section' => request()->section,
        ];
    }

    public function checkAllStockExists()
    {
        $this->needToGenerateStock = false;
        foreach ($this->product->productRanges as $productRange) {
            foreach ($this->product->productOptions as $productOption) {
                if ($this->getStockItemQuantity($productRange->id, $productOption->id) === 'NA') {
                    $this->needToGenerateStock = true;
                    break;
                }
            }
        }
    }

    public function getStockItemQuantity($productRangeId, $productOptionId)
    {
        $stockItem = $this->stockItems?->where('product_range_id', $productRangeId)->where('product_option_id', $productOptionId)->first();
        if (!$stockItem) {
            return 'NA';
        }
        return $stockItem->quantity;
    }

    public function generateStocks()
    {
        try {
            // Attempt to generate stocks
            $stockResult = $this->product->generateStocks();

            // Check the result of the stock generation
            if (isset($stockResult['status']) && $stockResult['status'] === 'error') {
                throw new \Exception($stockResult['message']);
            }

            // Redirect with success message if stocks were successfully generated
            return redirect()->route('products.show', $this->routeValue)->with('toast', [
                'class' => 'success',
                'text' => 'Stocks generated successfully.',
            ]);

        } catch (\Throwable $th) {
            // Redirect with error message if there was an issue generating stocks
            return redirect()->route('products.show', $this->routeValue)->with('toast', [
                'class' => 'danger',
                'text' => 'Failed to generate stocks: ' . $th->getMessage(),
            ]);
        }
    }

    public function deleteStocks()
    {
        try {
            // Attempt to delete stocks
            $stockResult = $this->product->deleteStocks();

            // Check the result of the stock deletion
            if (isset($stockResult['status']) && $stockResult['status'] === 'error') {
                throw new \Exception($stockResult['message']);
            }

            // Redirect with success message if stocks were successfully deleted
            return redirect()->route('products.show', $this->product->slug)->with('toast', [
                'class' => 'success',
                'text' => 'Stocks deleted successfully.',
            ]);

        } catch (\Throwable $th) {
            // Redirect with error message if there was an issue deleting stocks
            return redirect()->route('products.show', $this->product->slug)->with('toast', [
                'class' => 'danger',
                'text' => 'Failed to delete stocks: ' . $th->getMessage(),
            ]);
        }
    }

    public function store()
    {
        // Validate form inputs
        $this->validate([
            'productOptionName' => ['required', 'string', 'min:3', 'max:100'],
            'productOptionCode' => ['required', 'string', 'min:7', 'max:7'],
            'productOptionImages.*' => ['required', 'image', 'mimes:jpg,jpeg,png,webp'],
        ]);

        // Check if the product option already exists
        $productOption = ProductOption::where('product_id', $this->product->id)->where('slug', Str::slug($this->productOptionName))->first();
        if ($productOption) {
            return redirect()->route('products.show', $this->routeValue)->with('toast', [
                'class' => 'danger',
                'text' => 'Product Option already exists.',
            ]);
        }

        DB::transaction(function () {

            // Update or create a new ProductOption using the MaterialOption ID
            $productOption = ProductOption::updateOrCreate(
                [
                    'product_id' => $this->product->id,
                    'slug' => Str::slug($this->productOptionName),
                ],
                [
                    'name' => $this->productOptionName,
                    'code' => $this->productOptionCode,
                ]
            );

            foreach ($this->productOptionImages as $image) {
                try {
                    $productOption->addMedia($image->getRealPath())->toMediaCollection(ProductOption::MEDIA_COLLECTION_NAME);
                } catch (\Throwable $th) {
                    // Handle exceptions if needed
                }
            }
            
        });

        return redirect()->route('products.show', $this->routeValue)->with('toast', [
            'class' => 'success',
            'text' => 'Product Options created successfully.',
        ]);
    }

    public function edit($productOptionId)
    {
        // Find the product option with associated pomos
        $productOption = ProductOption::findOrFail($productOptionId);

        // Get the product and its materials
        $this->product = $productOption->product;
        $this->productId = $this->product->id;

        $this->formType = 'edit';
        $this->showForm = true;
    }

    public function update()
    {
        // Validate form inputs
        $this->validate([
            'productOptionCode' => ['required', 'string', 'min:7', 'max:7'],
            'productOptionImages.*' => ['sometimes', 'image', 'mimes:jpg,jpeg,png,webp'],
        ]);

        // DB::transaction(function () {

        // Update ProductOption
        $productOption = ProductOption::find($this->productOptionId);
        $productOption->update(['code' => $this->productOptionCode]);

        // check if user has updated name , then return warning that it cant be updated
        if ($productOption->name != $this->productOptionName) {
            return redirect()->route('products.show', $this->routeValue)->with('toast', [
                'class' => 'warning',
                'text' => 'Product Option name cannot be updated.',
            ]);
        }

        // Attach the uploaded images to the product option
        if (!empty($this->productOptionImages)) {
            foreach ($this->productOptionImages as $image) {
                $productOption->addMedia($image->getRealPath())->toMediaCollection(ProductOption::MEDIA_COLLECTION_NAME);
            }
        }

        return redirect()->route('products.show', $this->routeValue)->with('toast', [
            'class' => 'success',
            'text' => 'Product Options updated successfully.',
        ]);
    }

    public function delete($productOptionId)
    {
        // Find the product option
        $productOption = ProductOption::find($productOptionId);

        if ($productOption) {
            // Begin a transaction
            DB::transaction(function () use ($productOption) {

                // Delete all media associated with the product option
                $productOption->clearMediaCollection(ProductOption::MEDIA_COLLECTION_NAME);

                // Delete the product option itself
                $productOption->delete();
            });

            // Redirect to the product show page
            return redirect()->route('products.show', $this->routeValue)->with('toast', [
                'class' => 'success',
                'text' => 'Product Option is deleted successfully.',
            ]);
            
        } else {
            // Redirect to the product show page
            return redirect()->route('products.show', $this->routeValue)->with('toast', [
                'class' => 'error',
                'text' => 'Product Option is not found.',
            ]);
        }
    }

    public function render()
    {
        return view('prody::livewire.product-stock');
    }
}
