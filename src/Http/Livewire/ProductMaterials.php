<?php

namespace Fpaipl\Prody\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Str;
use Fpaipl\Prody\Models\Unit;
use Fpaipl\Prody\Models\Product;
use Fpaipl\Prody\Models\Material;
use Fpaipl\Prody\Models\Supplier;
use Fpaipl\Prody\Actions\LoadMaterials;
use Fpaipl\Prody\Models\ProductMaterial;
use Fpaipl\Prody\Actions\LoadMaterialCount;

class ProductMaterials extends Component
{
    public $showForm;

    // Api Data
    public $types; // material types
    public $units; // list of units
    public $suppliers; // list of materials
    public $materials; // list of materials
    public $selectedMaterial; // selected material

    // New Material Form + Attach Material Form
    public $materialSupplier; // selected supplier
    public $materialType; // selected category
    public $materialUnit; // selected unit
    public $materialName; // entered material name
    public $materialSid; // entered material name
    public $materialPrice; // entered material price

    public $attachedMaterials; // list of materials attached to the product
    public $productMaterialId; // id of the material being edited

    public $selectedSupplier; // selected supplier
    public $supplierMaterialCount; // count of materials for the selected supplier

    public $productId;
    public $product;
    public $routeValue;
    public $formType;

    public function mount($modelId)
    {
        $this->types = config('prody.fabric_category_types');
        $this->showForm = config('prody.show_add_form');
        $this->productId = $modelId;
        $this->product = Product::find($this->productId);
        $this->resetForm();
    }

    public function toggleForm()
    {
        $this->showForm = !$this->showForm;
    }

    public function resetForm()
    {
        $this->units = Unit::pluck('name', 'id');
        $this->suppliers = Supplier::active()->get();
        $this->materials = collect();

        $this->selectedMaterial = '';

        $this->materialSupplier = $this->suppliers->first()->id;
        $this->updatedMaterialSupplier();

        $this->materialType = '';
        $this->materialUnit = null;
        $this->materialName = '';
        $this->materialSid = '';
        $this->materialPrice = null;
        $this->formType = 'create';
        $this->reloadData();
    }

    public function checkSupplierNewMaterialCount()
    {
        $count = LoadMaterialCount::execute($this->selectedSupplier->website);
        $newCount = $count - $this->supplierMaterialCount;
        if ($newCount) {
            return redirect()->route('products.show', $this->routeValue)->with('toast', [
                'class' => 'success',
                'text' => 'Monaal has ' . $newCount . ' new materials.',
            ]);
        } else {
            return redirect()->route('products.show', $this->routeValue)->with('toast', [
                'class' => 'success',
                'text' => 'Monaal has ' . $count . ' materials.',
            ]);
        }
    }

    public function loadSupplierMaterials()
    {
        $selectedSupplier = Supplier::find($this->materialSupplier);
        $newCount = LoadMaterials::execute(
            $selectedSupplier->website, 
            $selectedSupplier->id,
            // true
        );
        return redirect()->route('products.show', $this->routeValue)->with('toast', [
            'class' => 'success',
            'text' => $newCount . ' New Materials added to the system.',
        ]);
    }

    public function updatedMaterialSupplier()
    {
        $this->selectedSupplier = Supplier::find($this->materialSupplier);
        $this->materials = $this->selectedSupplier->materials;
        $this->supplierMaterialCount = $this->materials->count();;
    }

    public function updatedSelectedMaterial()
    {
        $selectedMaterial = Str::of(Str::after($this->selectedMaterial, '|'))->trim();
        $material = Material::where('name', $selectedMaterial)->firstOrFail();
        $this->materialType = $material->category_type;
        $this->materialUnit = $material->unit_abbr;
        $this->materialName = $material->name;
        $this->materialSid = $material->sid;
        $this->materialPrice = $material->price;
    }

    public function reloadData()
    {
        $this->attachedMaterials = $this->product->productMaterials->load('material');
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
            'materialType' => ['required', 'string', 'min:3', 'max:50'],
            'materialName' => ['required', 'string', 'min:3', 'max:250'],
            'materialSid' => ['required', 'string', 'min:3', 'max:50'],
            'materialSupplier' => ['required', 'integer', 'exists:suppliers,id'],
            // 'materialUnit' => ['required', 'integer', 'exists:units,id'],
            'materialPrice' => ['required', 'numeric', 'min:1'],
        ]);

        // Fetch Existing Material or create new one
        $material = $this->getMaterial();

        // Find the grade of the material, i.e. how many times this material is used in this product
        $grade = $this->product->productMaterials()->where('material_id', $material->id)->count();

        // Attach the material to the product
        try {
            $this->product->productMaterials()->create([
                'grade' => $grade,
                'material_id' => $material->id,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('products.show', $this->routeValue)->with('toast', [
                'class' => 'danger',
                'text' => 'Material is already attached to the product.',
            ]);
        }

        return redirect()->route('products.show', $this->routeValue)->with('toast', [
            'class' => 'success',
            'text' => 'Material is attached successfully.',
        ]);
    }

    public function edit($materialId)
    {
        // Find the product material
        $productMaterial = $this->getProductMaterial($materialId);
        $this->productMaterialId = $productMaterial->id;

        // populate the form fields with the existing data
        $this->materialType = $productMaterial->material->category_type;
        $this->materialName = $productMaterial->material->name;
        $this->materialSid = $productMaterial->material->sid;
        $this->materialSupplier = $productMaterial->material->supplier_id;
        // $this->materialUnit = $productMaterial->material->unit_id;
        $this->materialUnit = $productMaterial->material->unit_abbr;
        $this->materialPrice = $productMaterial->material->price;

        // Change the form type to edit
        $this->formType = 'edit';
        $this->showForm = true;
    }

    public function update()
    {
        // Validate form inputs
        $this->validate([
            'materialType' => ['required', 'string', 'min:3', 'max:50'],
            'materialName' => ['required', 'string', 'min:3', 'max:250'],
            'materialSid' => ['required', 'string', 'min:3', 'max:50'],
            'materialSupplier' => ['required', 'integer', 'exists:suppliers,id'],
            // 'materialUnit' => ['required', 'integer', 'exists:units,id'],
            'materialPrice' => ['required', 'numeric', 'min:1'],
        ]);

        // Fetch or create the material
        $material = $this->getMaterial();

        // Find the product material to update
        $productMaterial = $this->getProductMaterial();

        if (!$productMaterial) {

            return redirect()->route('products.show', $this->routeValue)->with('toast', [
                'class' => 'error',
                'text' => 'Material is not attached to the product.',
            ]);
        } else {

            // Update the material details
            $material->update([
                'category_type' => $this->materialType,
                'name' => $this->materialName,
                'sid' => $this->materialSid,
                'supplier_id' => $this->materialSupplier,
                'unit_name' => $this->materialUnit,
                'price' => $this->materialPrice,
            ]);

            // Update the product material relationship
            $productMaterial->update(['material_id' => $material->id]);

            return redirect()->route('products.show', $this->routeValue)->with('toast', [
                'class' => 'success',
                'text' => 'Material is updated successfully.',
            ]);
        }
    }

    public function delete($materialId)
    {
        // Detach the material from the product
        $productMaterial = $this->product->productMaterials()->where('material_id', $materialId)->first();

        // check if pomo or pomr exists that has this material
        $pomos = $productMaterial->material->pomos;
        $pomrs = $productMaterial->material->pomrs;
    
        if ($pomos->count() || $pomrs->count()) {
            return redirect()->route('products.show', $this->routeValue)->with('toast', [
                'class' => 'danger',
                'text' => 'Cannot delete, Material is used in product options or product ranges.',
            ]);
        }

        // Delete the product material
        $productMaterial->delete();

        return redirect()->route('products.show', $this->routeValue)->with('toast', [
            'class' => 'success',
            'text' => 'Material is detached successfully.',
        ]);
    }

    public function getProductMaterial($materialId = null)
    {
        if (!$materialId && !$this->productMaterialId) {
            throw new \Exception('Material ID or ProductMateria ID is required.');
        }

        if ($materialId) {
            $productMaterial = ProductMaterial::where('material_id', $materialId)->where('product_id', $this->productId)->firstOrFail();
            $this->productMaterialId = $productMaterial->id;
        } else {
            $productMaterial = ProductMaterial::find($this->productMaterialId);
        }

        return $productMaterial;
    }

    public function getMaterial(): Material
    {
        // Fetch Existing Material
        // Because i dont want to create duplicate material with same name and code
        $material = Material::find($this->productMaterialId);

        // Create a new material if it doesn't exist
        if (!$material) {
            $material = Material::firstOrCreate(
                [
                    'sid' => $this->materialSid,
                    'supplier_id' => $this->materialSupplier,
                ],
                [
                    'category_name' => $this->materialName,
                    'category_type' => $this->materialType,
                    'unit_name' => $this->materialUnit,
                    'name' => $this->materialName,
                    'price' => $this->materialPrice,
                    'details' => '',
                    'tags' => '',
                ]
            );
        }

        return $material;
    }

    public function render()
    {
        return view('prody::livewire.product-materials');
    }
}
