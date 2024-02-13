<?php

namespace Fpaipl\Prody\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;
use Fpaipl\Prody\Models\Material;
use Fpaipl\Prody\Models\MaterialOption;
use Illuminate\Support\Facades\Log;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * This Livewire component handles color options and images related to materials.
 */
class MaterialOptions extends Component
{
    use WithFileUploads;

    public $showForm;

    // Variables to hold form data
    public $materialId;
    public $material;
    public $matOptions;

    public $formType;

    public $materialOptionName;
    public $materialOptionCode;
    public $materialOptionImages;
    public $existingImages;

    public $materialOptionId;

    /**
     * Function that runs when the component is initialized
     */
    public function mount($modelId)
    {
        $this->materialId = $modelId;
        $this->showForm = false;
        $this->material = Material::find($modelId);
        $this->resetForm();
    }

    public function toggleForm()
    {
        $this->showForm = !$this->showForm;
    }

    /**
     * Validates and stores the images temporarily on the server when they are updated in the form
     */
    public function updatedMaterialOptionImages()
    {
        $this->validate([
            'materialOptionImages.*' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        foreach($this->materialOptionImages as $image) {
            $image->storePublicly('temp', 'public');
        }
    }

    /**
     * Resets the form fields to their default values
     */
    public function resetForm()
    {
        $this->materialOptionName = '';
        $this->materialOptionImages = [];
        $this->existingImages = [];
        $this->formType = 'create';
        $this->reloadData();
    }

    /**
     * Loads all the color options for the material
     */
    public function reloadData()
    {
        $this->matOptions = $this->material->materialOptions;
    }

    public function store()
    {
        // Validate form inputs
        $this->validate([
            'materialOptionName' => ['required', 'string', 'min:1', 'max:100'],
            'materialOptionCode' => ['nullable', 'string', 'min:7', 'max:7', 'regex:/^#([A-Fa-f0-9]{6})$/'],
            'materialOptionImages.*' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        // Either find or create
        $materialOption = MaterialOption::create([
            'name' => $this->materialOptionName,
            'slug' => Str::slug($this->materialOptionName),
            'material_id' => $this->materialId,
            'code' => $this->materialOptionCode ?? '#eeeeee',
        ]);

        // Attach the uploaded images to the new material option
        if (!empty($this->materialOptionImages)) {  
            foreach ($this->materialOptionImages as $image) {
                try {
                    $materialOption->addMedia($image->getRealPath())->toMediaCollection(MaterialOption::MEDIA_COLLECTION_NAME);
                } catch (\Throwable $th) {
                    Log::error($th);
                }
            }
        }

        return redirect()->route('materials.show', $this->material->id)->with('toast', [
            'class' => 'success',
            'text' => 'Material Option is created successfully.',
        ]);
    }

    public function edit($materialOptionId)
    {
        // Set the material option id to be used in the update() method
        $this->materialOptionId = $materialOptionId;
        // Find the material option
        $materialOption = MaterialOption::findOrFail($materialOptionId);
        $this->materialOptionName = $materialOption->name;
        $this->materialOptionCode = $materialOption->code;
        $this->existingImages = $materialOption->getMedia(MaterialOption::MEDIA_COLLECTION_NAME);
        // Change the form type to edit
        $this->formType = 'edit';
        $this->showForm = true;
    }

    public function update()
    {
        // Validate form inputs
        $this->validate([
            'materialOptionName' => ['required', 'string', 'min:1', 'max:100'],
            'materialOptionCode' => ['nullable', 'string', 'min:7', 'max:7', 'regex:/^#([A-Fa-f0-9]{6})$/'],
            'materialOptionImages.*' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        // Find the material option
        $materialOption = MaterialOption::findOrFail($this->materialOptionId);
        $materialOption->name = $this->materialOptionName;
        $materialOption->slug = Str::slug($this->materialOptionName);
        $materialOption->code = $this->materialOptionCode;
        $materialOption->save();

        // Attach the uploaded images to the new material option
        if(!empty($this->materialOptionImages)) {
            foreach($this->materialOptionImages as $image) {
                $materialOption->addMedia($image->getRealPath())->toMediaCollection(MaterialOption::MEDIA_COLLECTION_NAME);
            }
        }
       
        return redirect()->route('materials.show', $this->material->id)->with('toast', [
            'class' => 'success',
            'text' => 'Material Option is updated successfully.',
        ]);
    }

    /**
     * Removes an existing image of a color option
     */
    public function deleteImage($imageId, $materialOptionId)
    {
        // Find the image
        $image = Media::find($imageId);
        // Check if the image belongs to the correct color option
        if ($image && $image->model_id === $materialOptionId && $image->model_type === MaterialOption::class) {
            // Delete the image
            $image->delete();
            // Get the material slug
            $materialId = MaterialOption::find($materialOptionId)->material->id;
            // Redirect to the material show page, 
            return redirect()->route('materials.show', $materialId)->with('toast', [
                'class' => 'success',
                'text' => 'The image is deleted successfully.',
            ]);
        } else {
            // Redirect to the material show page, 
            return redirect()->route('materials.show', $this->material->id)->with('toast', [
                'class' => 'danger',
                'text' => 'The image is not found.',
            ]);
        }
    }

    /**
     * Deletes an existing color option
     */
    public function delete($materialOptionId)
    {
        // Find the color option
        $materialOption = MaterialOption::find($materialOptionId);
        // Delete the color option
        $materialOption->forceDelete();
        
        // Redirect to the material show page,
        return redirect()->route('materials.show', $this->material->id)->with('toast', [
            'class' => 'success',
            'text' => 'Material Option is deleted successfully.',
        ]);
    }

    /**
     * Renders the component
     */
    public function render()
    {
        return view('prody::livewire.material-options');
    }
}
