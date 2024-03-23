<?php

namespace Fpaipl\Prody\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Str;
use Fpaipl\Prody\Models\Pomo;
use Livewire\WithFileUploads;
use Fpaipl\Prody\Models\Product;
use Fpaipl\Prody\Models\Material;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Fpaipl\Prody\Models\ProductOption;
use Fpaipl\Prody\Models\MaterialOption;
use Illuminate\Support\Facades\Validator;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ProductOptionsMaterial extends Component
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
    public $colorList;

    public $materialOptions;
    public $productMaterials;

    public function mount($modelId)
    {
        $this->productId = $modelId;
        $this->showForm = config('prody.show_add_form');
        $this->product = Product::find($modelId);
        $this->productMaterials = $this->product->productMaterials;
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

    public function updatedProductOptions($value, $name)
    {
        $materialOption = MaterialOption::find($value);
        if ($materialOption) {
            array_push($this->colorList, [
                'id' => $materialOption->id,
                'name' => $materialOption->name,
                'code' => $materialOption->code,
            ]);
        } else {
            $this->addError('productOptions', 'The selected material option is invalid.');
        }
    }

    public function selectColor($id)
    {
        $materialOption = MaterialOption::find($id);
        $this->productOptionName = $materialOption->name;
        $this->productOptionCode = $materialOption->code;
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
        $this->colorList = [];
        $this->productOptionName = '';
        $this->productOptionCode = '';
        $this->productOptionImages = [];
        $this->existingImages = [];
        $this->formType = 'create';
        foreach ($this->productMaterials as $material) {
            $index = $material->material_id . '-' . $material->grade;
            $this->productOptions[$index] = [
                'id' => null,
            ];
        }
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
        // Validate form inputs including images
        $this->validate([
            'productOptions.*.id' => 'required|exists:material_options,id',
            'productOptionName' => 'required|string|min:3|max:100',
            'productOptionCode' => 'required|string|min:7|max:7',
            'productOptionImages' => 'required', // Make the images required
            'productOptionImages.*' => 'image|mimes:jpg,jpeg,png,webp', // Validate each image file
        ], [
            'productOptions.*.id.required' => 'The selected material option is invalid.',
            'productOptionName.required' => 'The product option name is required.',
            'productOptionCode.required' => 'The product option color code is required.',
            'productOptionImages.required' => 'At least one image is required.', // Custom message for no image
            'productOptionImages.*.image' => 'The uploaded file must be an image.',
        ]);


        // Additional validation to ensure at least one image is uploaded
        if (empty($this->productOptionImages) || count($this->productOptionImages) === 0) {
            return redirect()->route('products.show', $this->routeValue)->with('toast', [
                'class' => 'danger',
                'text' => 'At least one image is required.',
            ]);
        }

        // Check if the product option already exists
        $productOption = ProductOption::where('product_id', $this->product->id)->where('slug', Str::slug($this->productOptionName))->first();
        if ($productOption) {
            return redirect()->route('products.show', $this->routeValue)->with('toast', [
                'class' => 'danger',
                'text' => 'Product Option already exists.',
            ]);
        }

        try {

            // Start transaction
            DB::beginTransaction();

            // Loop through productOptions array
            foreach ($this->productOptions as $key => $materialOptionId) {

                // Extract material ID and grade from the key
                [$materialId, $grade] = explode('-', $key);

                // Validate the material option ID
                $validator = Validator::make($materialOptionId, [
                    'id' => ['required', 'integer', 'exists:material_options,id'],
                ]);

                if ($validator->fails()) {
                    // Add an error for this specific key
                    $this->addError('productOptions.' . $key . '.id', 'The selected material option is invalid.');
                }

                // Find MaterialOption
                $materialOption = MaterialOption::find($materialOptionId['id']);

                // Check if materialOption is not found
                if (!$materialOption) {
                    throw new \Exception("Forget to choose color of material");
                }

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

                // Extract those ProductMaterial which has same material_id as the currect material_option has
                $productMaterials = $materialOption->material->productMaterials;
                // Alternative way to get productMaterials
                // $productMaterials = Material::find($materialId)->productMaterials;

                // Now from those productMaterials, extract the one which has same product_id as the current product has
                $productMaterialId = $productMaterials->where('product_id', $this->product->id)->first()->id;

                // Attach the MaterialOption to the ProductOption with the grade
                Pomo::updateOrCreate(
                    [
                        'product_material_id' => $productMaterialId,
                        'material_option_id' => $materialOption->id,
                        'product_option_id' => $productOption->id,
                        'grade' => $grade,
                    ]
                );

                // Attach the uploaded images to the new product option
                // Assuming $this->productOptionImages holds the Livewire uploaded files
                foreach ($this->productOptionImages as $image) {
                    // Store the image in the correct directory and get the path
                    $path = $image->storePublicly('public/temp', 'local'); // Adjusted to use the 'public/temp' directory explicitly

                    // Generate the full path for addMedia
                    $fullPath = storage_path('app/' . $path);

                    try {
                        // Use the full path to attach the image to the media collection
                        $productOption->addMedia($fullPath)->toMediaCollection(ProductOption::MEDIA_COLLECTION_NAME);
                    } catch (\Throwable $th) {
                        Log::error("Unable to add image to media collection.", ['error' => $th->getMessage()]);
                        throw new \Exception("Unable to add image: " . $th->getMessage());
                    }
                }
            }

            DB::commit();

            return redirect()->route('products.show', $this->routeValue)->with('toast', [
                'class' => 'success',
                'text' => 'Product Options created successfully.',
            ]);
        } catch (\Exception $e) {
            // Rollback transaction in case of an error
            DB::rollBack();

            // Log the exception
            Log::error('Error in creating product range: ' . $e->getMessage());

            if (get_class($e) == 'Illuminate\Database\UniqueConstraintViolationException') {
                return redirect()->route('products.show', $this->routeValue)->with('toast', [
                    'class' => 'danger',
                    'text' => 'Duplicate Entry',
                ]);
            }

            // Redirect with error message
            return redirect()->route('products.show', $this->routeValue)->with('toast', [
                'class' => 'danger',
                'text' => 'Failed to create product option.',
            ]);
        }
    }

    public function edit($productOptionId)
    {
        // Find the product option with associated pomos
        $productOption = ProductOption::with('pomos.materialOption')->findOrFail($productOptionId);

        // Get the product and its materials
        $this->product = $productOption->product;
        $this->productId = $this->product->id;

        // Reset existing productOptions array
        $this->productOptions = [];

        // Fetch all pomos associated with this product option
        $pomos = $productOption->pomos;

        // Loop through each pomo
        foreach ($pomos as $pomo) {
            // Create a unique index using material_id and grade
            $index = $pomo->materialOption->material_id . '-' . $pomo->grade;
            $this->productOptions[$index] = [
                'id' => $pomo->materialOption->id,
            ];
        }

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
        // Validate form inputs including images
        $this->validate([
            'productOptions.*.id' => 'required|exists:material_options,id',
            'productOptionName' => 'required|string|min:3|max:100',
            'productOptionCode' => 'required|string|min:7|max:7',
            'productOptionImages.*' => 'image|mimes:jpg,jpeg,png,webp', // Validate each image file
        ], [
            'productOptions.*.id.required' => 'The selected material option is invalid.',
            'productOptionName.required' => 'The product option name is required.',
            'productOptionCode.required' => 'The product option color code is required.',
            'productOptionImages.*.image' => 'The uploaded file must be an image.',
        ]);

        $productOption = ProductOption::find($this->productOptionId);

        // check if user has updated name , then return warning that it cant be updated
        if ($productOption->name != $this->productOptionName) {
            return redirect()->route('products.show', $this->routeValue)->with('toast', [
                'class' => 'warning',
                'text' => 'Product Option name cannot be updated.',
            ]);
        }

        // Update the product option code
        $productOption->update(['code' => $this->productOptionCode]);

        // Remove existing pomos associated with this product option
        Pomo::where('product_option_id', $productOption->id)->delete();

        foreach ($this->productOptions as $key => $optionData) {

            // Extract material ID and grade from the key
            [$materialId, $grade] = explode('-', $key);

            // Validate the material option ID
            $validator = Validator::make($optionData, [
                'id' => ['required', 'integer', 'exists:material_options,id'],
            ]);

            if ($validator->fails()) {
                // Add an error for this specific key
                $this->addError('productOptions.' . $key . '.id', 'The selected material option is invalid.');
            }

            // Find MaterialOption
            $materialOption = MaterialOption::find($optionData['id']);

            // Attach the MaterialOption to the ProductOption with the grade
            Pomo::create(
                [
                    'material_option_id' => $materialOption->id,
                    'product_option_id' => $productOption->id,
                    'grade' => $grade,
                ]
            );
        }

        // Attach the uploaded images to the product option
        if (!empty($this->productOptionImages)) {
            foreach ($this->productOptionImages as $image) {
                $productOption->addMedia($image->getRealPath())->toMediaCollection(ProductOption::MEDIA_COLLECTION_NAME);
            }
        }
        // });

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
        try {
            // Find the product option
            $productOption = ProductOption::find($productOptionId);

            // Check if the product option has stock item, if yes then return warning that it cant be deleted
            if ($productOption->stockItems->count() > 0) {
                return redirect()->route('products.show', $this->routeValue)->with('toast', [
                    'class' => 'danger',
                    'text' => 'Cannot delete, Stock exits.',
                ]);
            }

            if ($productOption) {
                // Begin a transaction
                DB::transaction(function () use ($productOption) {

                    // Delete associated Pomo records
                    Pomo::where('product_option_id', $productOption->id)->delete();

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
            } // Find the product option
            $productOption = ProductOption::find($productOptionId);

            // Check if the product option has stock item, if yes then return warning that it cant be deleted
            if ($productOption->stockItems->count() > 0) {
                return redirect()->route('products.show', $this->routeValue)->with('toast', [
                    'class' => 'danger',
                    'text' => 'Cannot delete, Stock exits.',
                ]);
            }

            if ($productOption) {
                // Begin a transaction
                DB::transaction(function () use ($productOption) {

                    // Delete associated Pomo records
                    Pomo::where('product_option_id', $productOption->id)->delete();

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
        } catch (\Throwable $th) {
            Log::error('Error in deleting product option: ' . $th->getMessage());
            // Redirect to the product show page
            return redirect()->route('products.show', $this->routeValue)->with('toast', [
                'class' => 'danger',
                'text' => 'Something went wrong. Failed to delete product option.',
            ]);
        }
    }

    public function getMaterialOptionName($material)
    {
        return $material->type . ' | ' . $material->name;
    }

    public function clone($productOptionId)
    {
        // fetch the product option, then clone it like editing, and open create form
        $productOption = ProductOption::with('pomos.materialOption')->findOrFail($productOptionId);

        // Get the product and its materials
        $this->product = $productOption->product;

        // Reset existing productOptions array
        $this->productOptions = [];

        // Fetch all pomos associated with this product option
        $pomos = $productOption->pomos;

        // Loop through each pomo
        foreach ($pomos as $pomo) {
            // Create a unique index using material_id and grade
            $index = $pomo->materialOption->material_id . '-' . $pomo->grade;
            $this->productOptions[$index] = [
                'id' => $pomo->materialOption->id,
            ];
        }

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
        return view('prody::livewire.product-options-material');
    }
}


// Key Functionalities:
// File Uploads: Utilizes Livewire's WithFileUploads trait for handling image uploads.
// Dynamic Form Fields: Manages form fields for creating and updating product options, including names, codes, and images.
// CRUD Operations: Implements Create, Read (view/render), Update, and Delete operations for ProductOption entities.
// Form State Management: Includes methods for form state initialization (resetForm) and toggling visibility (toggleForm).
// Data Validation: Validates user input, especially for image files and required fields.
// Database Transactions: Employs DB::transaction to ensure atomicity of CRUD operations.
// Media Management: Handles the association of images with ProductOption and their deletion.

// Detailed Review:
// Mount Method: Initializes the component with necessary data for a specified product.
// Store Method: Validates and stores a new ProductOption, including the handling of images and creation of Pomo records.
// Edit Method: Prepares the component for editing an existing ProductOption, loading associated materials and images.
// Update Method: Updates an existing ProductOption, including its images and related Pomo records. It also checks for non-updatable fields like name.
// Delete Method: Removes a ProductOption and its related Pomo records and images.
// Auxiliary Methods: Additional methods like deleteImage, updateProductOptionName, updateProductOptionCode, and getMaterialOptionName support various component functionalities.
// Render Method: Renders the component view with the current state.
