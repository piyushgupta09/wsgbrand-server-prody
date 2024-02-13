<?php

namespace Fpaipl\Prody\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Fpaipl\Prody\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Fpaipl\Prody\Models\ProductRange;

/**
 * This Livewire component handles product material
 */
class ProductRanges extends Component
{
    public $showForm;

    public $ranges;

    public $rangeType; // selected size range
    public $rangeMrp;
    public $rangeRate;

    public $productRanges;

    public $productRangeId;
    public $productId;
    public $product;
    public $formType;
    public $routeValue;

    public function mount($modelId)
    {
        $this->ranges = config('prody.sizes');
        $this->showForm = false;
        $this->productId = $modelId;
        $this->product = Product::find($this->productId);
        $this->routeValue = ['product' => $this->product->slug, 'section' => 'size-range'];
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->rangeType = 'free-size';
        $this->formType = 'create';
        $this->rangeMrp = null;
        $this->rangeRate = null;
        $this->reloadData();
    }

    public function reloadData()
    {
        $this->productRanges = $this->product->productRanges;
        $this->routeValue = [
            'tab' => request()->tab,
            'product' => $this->product->slug,
            'section' => request()->section,
        ];
    }

    public function toggleForm()
    {
        $this->showForm = !$this->showForm;
    }

    public function store()
    {
        try {
            // Start transaction
            DB::beginTransaction();

            // Step 1: Validate basic fields
            $validatedData = $this->validate([
                'rangeType' => ['required', Rule::in(array_keys($this->ranges))],
                'rangeMrp' => ['required', 'numeric'],
                'rangeRate' => ['required', 'numeric'],
            ]);

            // replace - with space and capitalize the first letter of each word
            $productRangeName = Str::title(str_replace('-', ' ', $validatedData['rangeType']));

            // Step 2: Find or Create ProductRange
            ProductRange::firstOrCreate(
                [
                    'product_id' => $this->productId,
                    'name' => $productRangeName,
                ],
                [
                    'mrp' => $validatedData['rangeMrp'],
                    'rate' => $validatedData['rangeRate']
                ]
            );

            // Commit transaction
            DB::commit();

            // Step 5: Redirect with success message
            return redirect()->route('products.show', $this->routeValue)->with('toast', [
                'class' => 'success',
                'text' => 'Product range created successfully.',
            ]);

        } catch (\Exception $e) {
            // Rollback transaction in case of an error
            DB::rollBack();
        
            // Get the error message
            $errorMessage = $e->getMessage() . ' ' . $e->getLine();

            // Log the exception
            Log::error('Error in creating product range: ' . $errorMessage);
        
            // Start with a generic user-friendly message
            $userFriendlyMessage = 'Failed to create product range.';
        
            // Check if the error message contains the specific SQL error for duplicate entry
            if (preg_match('/1062 Duplicate entry/', $errorMessage) || Str::contains($errorMessage, '1062 Duplicate entry')) {
                $userFriendlyMessage .= ' Duplicate entry found.';
            } else {
                // Include the full error message for other types of errors
                $userFriendlyMessage .= ' Error: ' . $errorMessage;
            }
        
            // Redirect with error message
            return redirect()->route('products.show', $this->routeValue)->with('toast', [
                'class' => 'danger',
                'text' => $userFriendlyMessage,
            ]);
        }
              
    }

    public function edit($productRangeId)
    {
        // Load the product range along with its related pomr records
        $productRange = ProductRange::find($productRangeId);
        $this->productRangeId = $productRange->id;

        if (!$productRange) {
            // Handle the case where the product range is not found
            return;
        }

        // Populate the form fields with the existing data
        $this->rangeType = Str::slug($productRange->name, '-');
        $this->rangeMrp = $productRange->mrp;
        $this->rangeRate = $productRange->rate;

        // Change the form type to edit
        $this->formType = 'edit';
        $this->showForm = true;
    }

    public function update()
    {
        try {
            // Start transaction
            DB::beginTransaction();

            // Step 1: Validate basic fields
            $validatedData = $this->validate([
                'rangeType' => ['required', Rule::in(array_keys($this->ranges))],
                'rangeMrp' => ['required', 'numeric'],
                'rangeRate' => ['required', 'numeric'],
            ]);

            // Update the ProductRange
            $productRange = ProductRange::findOrFail($this->productRangeId);
            $productRangeName = Str::title(str_replace('-', ' ', $validatedData['rangeType']));
            $productRange->update([
                'name' => $productRangeName,
                'mrp' => $validatedData['rangeMrp'],
                'rate' => $validatedData['rangeRate']
            ]);

            // Commit transaction
            DB::commit();

            // Redirect with success message
            return redirect()->route('products.show', $this->routeValue)->with('toast', [
                'class' => 'success',
                'text' => 'Product range updated successfully.',
            ]);
            
        } catch (\Exception $e) {
            // Rollback transaction in case of an error
            DB::rollBack();
        
            // Get the error message
            $errorMessage = $e->getMessage() . ' ' . $e->getLine();

            // Log the exception
            Log::error('Error in creating product range: ' . $errorMessage);
        
            // Start with a generic user-friendly message
            $userFriendlyMessage = 'Failed to update product range.';
        
            // Check if the error message contains the specific SQL error for duplicate entry
            if (preg_match('/1062 Duplicate entry/', $errorMessage) || Str::contains($errorMessage, '1062 Duplicate entry')) {
                $userFriendlyMessage .= ' Duplicate entry found.';
            } else {
                // Include the full error message for other types of errors
                $userFriendlyMessage .= ' Error: ' . $errorMessage;
            }
        
            // Redirect with error message
            return redirect()->route('products.show', $this->routeValue)->with('toast', [
                'class' => 'danger',
                'text' => $userFriendlyMessage,
            ]);
        }
    }

    public function delete($productRangeId)
    {
        $productRange = ProductRange::find($productRangeId);
        $productRange->delete();

        return redirect()->route('products.show', $this->routeValue)->with('toast', [
            'class' => 'success',
            'text' => 'Product range deleted successfully.',
        ]);
    }

    public function clone($productRangeId)
    {
        // Fetch the product range with its related pomr records
        $productRange = ProductRange::with('pomrs.materialRange')->findOrFail($productRangeId);

        // Set the range type, mrp, and rate for cloning
        $this->rangeType = Str::slug($productRange->name, '-');
        $this->rangeMrp = $productRange->mrp;
        $this->rangeRate = $productRange->rate;

        // Set form type and other UI properties for creating a new range
        $this->formType = 'create';
        $this->showForm = true;
    }

    public function stockout($productRangeId)
    {
        $productRange = ProductRange::findOrFail($productRangeId);
        $productRange->update(['active' => !$productRange->active]);
        return redirect()->route('products.show', $this->routeValue)->with('toast', [
            'class' => 'success',
            'text' => 'Product Range is ' . ($productRange->active ? 'Stocked In' : 'Stocked Out') . ' successfully.',
        ]);
    }

    public function render()
    {
        return view('prody::livewire.product-ranges');
    }
}
