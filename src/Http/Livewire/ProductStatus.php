<?php

namespace Fpaipl\Prody\Http\Livewire;

use Fpaipl\Prody\Models\Product;
use Livewire\Component;

/**
 * This Livewire component handles product material
 */
class ProductStatus extends Component
{
    public $productStatus;
    public $product;

    public function mount($modelId)
    {
        $this->productStatus = Product::STATUS;
        $this->product = Product::find($modelId);
    }

    /**
     * Update the status of a product.
     * 
     * @param string $status The new status to set for the product.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStatus($status)
    {
        /** @var User $user */
        $user = auth()->user();

        // Check user permissions
        if (!$user->isSuperAdmin() && !$user->isOwnerBrand() && !$user->isManagerBrand()) {
            return redirect()->route('products.show', $this->product->slug)->with('toast', [
                'class' => 'danger',
                'text' => 'You are not authorized to perform this action.',
            ]);
        }

        // Making product Live
        if ($status == Product::STATUS[1]) {
            try {
                // Ensure product has options and ranges
                if ($this->product->productOptions->isEmpty() || $this->product->productRanges->isEmpty()) {
                    throw new \Exception('Product has no product options or ranges.');
                }

                // Generate stocks for the product
                $stockResult = $this->product->generateStocks();

                // Handle stock generation error
                if ($stockResult['status'] === 'error') {
                    throw new \Exception($stockResult['message']);
                }

                // Add product to collections and update status
                $this->product->addToRangedCollection();
                $this->product->addToRecommendedCollection();
                $this->product->status = $status;
                $this->product->save();

                return redirect()->route('products.show', $this->product->slug)->with('toast', [
                    'class' => 'success',
                    'text' => 'Product is Live Now.',
                ]);
            } catch (\Throwable $th) {
                // Handle exceptions
                return redirect()->route('products.show', $this->product->slug)->with('toast', [
                    'class' => 'danger',
                    'text' => $th->getMessage(),
                ]);
            }

            // Making product Draft
        } elseif ($status == Product::STATUS[0]) {
            try {
                // Remove product from collections and update status
                $this->product->removeFromRangedCollection();
                $this->product->removeFromRecommendedCollection();
                $this->product->status = $status;
                $this->product->save();

                return redirect()->route('products.show', $this->product->slug)->with('toast', [
                    'class' => 'success',
                    'text' => 'Product is Draft Now.',
                ]);
            } catch (\Throwable $th) {
                // Handle exceptions
                return redirect()->route('products.show', $this->product->slug)->with('toast', [
                    'class' => 'danger',
                    'text' => $th->getMessage(),
                ]);
            }
        } else {
            // Handle invalid status
            return redirect()->route('products.show', $this->product->slug)->with('toast', [
                'class' => 'danger',
                'text' => 'Invalid status.',
            ]);
        }
    }

    public function deleteProduct()
    {
        try {
            // Attempt to delete product
            $productResult = $this->product->deleteProduct();

            // Check the result of the product deletion
            if (isset($productResult['status']) && $productResult['status'] === 'error') {
                throw new \Exception($productResult['message']);
            }

            // Redirect with success message if product was successfully deleted
            return redirect()->route('products.index')->with('toast', [
                'class' => 'success',
                'text' => 'Product deleted successfully.',
            ]);

        } catch (\Throwable $th) {
            // Redirect with error message if there was an issue deleting product
            return redirect()->route('products.show', $this->product->slug)->with('toast', [
                'class' => 'danger',
                'text' => 'Failed to delete product: ' . $th->getMessage(),
            ]);
        }
    }

    public function render()
    {
        return view('prody::livewire.product-status');
    }
}
