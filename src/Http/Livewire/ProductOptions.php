<?php

namespace Fpaipl\Prody\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;
use Fpaipl\Prody\Models\Product;
use Illuminate\Support\Facades\DB;
use Fpaipl\Prody\Models\ProductOption;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ProductOptions extends Component
{
    use WithFileUploads;

    public $showForm;

    // Variables to hold form data
    public $productId;
    public $product;

    public $formType;
    public $routeValue;

    public $productOptionName;
    public $productOptionCode;
    public $productOptionImages;

    public $existingImages;
    public $productOptionId;
    public $proOptions;

    public $productOptions;

    public function mount($modelId)
    {
        $this->productId = $modelId;
        $this->showForm = false;
        $this->product = Product::find($modelId);
        $this->resetForm();
    }

    public function updateProductOptionName($id, $value)
    {
        $this->productOptions[$id]['name'] = $value;
    }

    public function updateProductOptionCode($id, $value)
    {
        $this->productOptions[$id]['code'] = $value;
    }

    public function toggleForm()
    {
        $this->showForm = !$this->showForm;
    }

    public function updatedProductOptionImages()
    {
        $this->validate([
            'productOptionImages.*' => ['required', 'image', 'mimes:jpg,jpeg,png,webp'],
        ]);

        foreach ($this->productOptionImages as $image) {
            $image->storePublicly('temp', 'public');
        }
    }

    public function resetForm()
    {
        $this->productOptionName = '';
        $this->productOptionCode = '';
        $this->productOptionImages = [];
        $this->existingImages = [];
        $this->formType = 'create';
        $this->reloadData();
    }

    public function reloadData()
    {
        $this->proOptions = $this->product->productOptions;
        $this->routeValue = [
            'tab' => request()->tab,
            'product' => $this->product->slug,
            'section' => request()->section,
        ];
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

        // Set the name and code for the product option
        $this->productOptionName = $productOption->name;
        $this->productOptionCode = $productOption->code;

        // Load existing images
        $this->existingImages = $productOption->getMedia(ProductOption::MEDIA_COLLECTION_NAME);

        // Set form type and other UI properties
        $this->productOptionId = $productOptionId;
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

    public function deleteImage($imageId, $productOptionId)
    {
        // Find the image
        $image = Media::find($imageId);
        // Check if the image belongs to the correct color option
        if ($image && $image->model_id === $productOptionId && $image->model_type === ProductOption::class) {
            // Delete the image
            $image->delete();
            // Flash a message to the user
            session()->flash('message', 'Image removed successfully.');
            // Get the product slug
            $product = ProductOption::find($productOptionId)->product->slug;
            // Redirect to the product show page, cause the model is not closing
            return redirect()->route('products.show', $this->routeValue);
            } else {
            session()->flash('message', 'The image could not be deleted. It might not belong to the correct product color option.');
        }
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

    public function clone($productOptionId)
    {
        // fetch the product option, then clone it like editing, and open create form
        $productOption = ProductOption::findOrFail($productOptionId);

        // Get the product and its materials
        $this->product = $productOption->product;

        // Reset existing productOptions array
        $this->productOptions = [];

        // Set the name and code for the product option
        $this->productOptionName = $productOption->name;
        $this->productOptionCode = $productOption->code;

        // Set form type and other UI properties
        $this->productOptionId = $productOptionId;
        $this->formType = 'create';
        $this->showForm = true;
    }

    public function stockout($productOptionId)
    {
        $productOption = ProductOption::findOrFail($productOptionId);
        $productOption->update(['active' => !$productOption->active]);
        return redirect()->route('products.show', $this->routeValue)->with('toast', [
            'class' => 'success',
            'text' => 'Product Option is ' . ($productOption->active ? 'Stocked In' : 'Stocked Out') . ' successfully.',
        ]);
    }

    public function render()
    {
        return view('prody::livewire.product-options');
    }
}


// Key Functionalities:
// The ProductOptions component is responsible for managing the product options for a product. It allows the user to create, edit, and delete product options. It also allows the user to upload images for each product option. The component also allows the user to clone an existing product option, which is useful when creating a new product option with similar properties to an existing one.
// The component also allows the user to stock in or stock out a product option. This is useful when a product option is out of stock and the user wants to hide it from the product page. The user can stock in the product option when it is available again.
// The component also allows the user to delete an image from a product option. This is useful when the user wants to remove an image from a product option.
// The component also allows the user to delete a product option. This is useful when the user wants to remove a product option from the product.
// The component also allows the user to edit a product option. This is useful when the user wants to update the code of a product option.
// The component also allows the user to clone a product option. This is useful when the user wants to create a new product option with similar properties to an existing one.
// The component also allows the user to create a new product option. This is useful when the user wants to add a new product option to the product.
// The component also allows the user to upload images for a product option. This is useful when the user wants to add images to a product option.

// Detailed Review:
// Mount Method: Initializes the component with necessary data for a specified product.
// Store Method: Validates and stores a new ProductOption, including the handling of images.
// Edit Method: Prepares the component for editing an existing ProductOption, loading associated images.
// Update Method: Updates an existing ProductOption, including its images. It also checks for non-updatable fields like name.
// Delete Method: Removes a ProductOption and its images.
// Auxiliary Methods: Additional methods like deleteImage, updateProductOptionName, updateProductOptionCode, support various component functionalities.
// Render Method: Renders the component view with the current state.
