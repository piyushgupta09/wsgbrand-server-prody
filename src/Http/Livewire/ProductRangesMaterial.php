<?php

namespace Fpaipl\Prody\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Str;
use Fpaipl\Prody\Models\Pomr;
use Illuminate\Validation\Rule;
use Fpaipl\Prody\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Fpaipl\Prody\Models\ProductRange;

/**
 * This Livewire component handles product material
 */
class ProductRangesMaterial extends Component
{
    public $showForm;

    public $ranges;
    public $productMaterials;

    public $rangeType; // selected size range
    public $rangeMrp;
    public $rangeRate;
    public $consumption; // array of consumption qty and unit for each material

    public $consumptions; // range wise consumption qty and unit for each material

    public $productRanges;

    public $productRangeId;
    public $productId;
    public $product;
    public $formType;
    public $routeValue;

    public function mount($modelId)
    {
        $this->ranges = config('prody.sizes');
        $this->showForm = config('prody.show_add_form');
        $this->productId = $modelId;
        $this->product = Product::with([
            'productMaterials',
            'productOptions.pomos.materialOption',
        ])->find($this->productId);
        $this->productMaterials = $this->product->productMaterials;
        $this->productMaterials->load('material');
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->rangeType = 'free-size';
        $this->formType = 'create';

        $this->rangeMrp = null;
        $this->rangeRate = null;

        foreach ($this->productMaterials as $productMaterial) {
            $this->consumption[$productMaterial->id . '_0']['pmid'] = $productMaterial->id;
            $this->consumption[$productMaterial->id . '_0']['grade'] = $productMaterial->grade;
            $this->consumption[$productMaterial->id . '_0']['name'] = null;
            $this->consumption[$productMaterial->id . '_0']['cost'] = $productMaterial->material->price;
            $this->consumption[$productMaterial->id . '_0']['unit'] = $productMaterial->material->unit_abbr;
            // $this->consumption[$productMaterial->id . '_0']['unit'] = config('prody.units.fcpu')['inch'];
            foreach ($productMaterial->material->materialRanges as $productMaterialRange) {
                $this->consumption[$productMaterial->id . '_' . $productMaterialRange->id]['qty'] = null;
                $this->consumption[$productMaterial->id . '_' . $productMaterialRange->id]['mrid'] = $productMaterialRange->id;
                $this->consumption[$productMaterial->id . '_' . $productMaterialRange->id]['active'] = $productMaterialRange->width == 56 ? true : false;
            }
        }

        $this->reloadData();
    }

    public function reloadData()
    {
        $this->productRanges = $this->product->productRanges;
        // dd($this->productRanges->load('pomocs.materialOption.material'));
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
                'consumption.*.active' => ['boolean'],
            ]);

            // replace - with space and capitalize the first letter of each word
            $productRangeName = Str::title(str_replace('-', ' ', $validatedData['rangeType']));

            // Step 2: Find or Create ProductRange
            $productRange = ProductRange::firstOrCreate(
                [
                    'product_id' => $this->productId,
                    'name' => $productRangeName,
                ],
                [
                    'mrp' => $validatedData['rangeMrp'],
                    'rate' => $validatedData['rangeRate']
                ]
            );

            // Step 3A : Dynamically generate validation rules for consumption data.
            $consumptionRules = [];
            $customMessages = [];

            foreach ($this->consumption as $key => $value) {

                if (Str::after($key, '_') == 0) {

                    // Define rules for each consumption item
                    $consumptionRules["consumption.$key.grade"] = ['required', 'numeric'];
                    $consumptionRules["consumption.$key.pmid"] = ['required', 'numeric', 'exists:product_materials,id'];
                    $consumptionRules["consumption.$key.name"] = ['required', 'string', 'min:1', 'max:50'];
                    $consumptionRules["consumption.$key.cost"] = ['required', 'numeric', 'min:1'];
                    $consumptionRules["consumption.$key.unit"] = ['required'];

                    // Custom error messages for better user feedback
                    $customMessages = array_merge($customMessages, [
                        "consumption.$key.grade.required" => 'The grade field is required.',
                        "consumption.$key.name.required" => 'The name field is required.',
                        "consumption.$key.cost.required" => 'The cost field is required.',
                        "consumption.$key.unit.required" => 'The unit field is required.',
                        "consumption.$key.unit.in" => 'The selected unit is invalid.',
                        "consumption.$key.pmid.required" => 'The product material id field is required.',
                        "consumption.$key.pmid.exists" => "The selected material is invalid.",
                    ]);
                } else {

                    if ($value['active'] ?? false) {

                        // Define rules for each consumption item
                        $consumptionRules["consumption.$key.qty"] = ['required', 'numeric'];
                        $consumptionRules["consumption.$key.mrid"] = ['required', 'exists:material_ranges,id'];
                        $consumptionRules["consumption.$key.active"] = ['required', 'boolean'];

                        // Custom error messages for better user feedback
                        $customMessages = array_merge($customMessages, [
                            "consumption.$key.qty.required" => 'The quantity field is required.',
                            "consumption.$key.mrid.required" => 'The material range id field is required.',
                            "consumption.$key.mrid.exists" => 'The selected material range is invalid.',
                            "consumption.$key.active.required" => 'The active field is required.',
                        ]);
                    }
                }
            }

            // Step 3B : Perform validation on consumption with the dynamically generated rules
            $validatedData = $this->validate($consumptionRules, $customMessages);

            // Step 4 : Create the product material options

            // Create an array to store the data for bulk insert
            $pomrData = [];
            $generalAttributesMap = [];

            // First, map general attributes for each material
            foreach ($this->consumption as $key => $details) {
                if (Str::endsWith($key, '_0')) {
                    $materialId = explode('_', $key)[0];
                    $generalAttributesMap[$materialId] = $details;
                }
            }

            // Now, process specific attributes and combine with general attributes
            foreach ($this->consumption as $key => $details) {
                // Skip processing if the item is not active or if it's a general attribute
                if (!(isset($details['active']) && $details['active']) || Str::endsWith($key, '_0')) {
                    continue;
                }

                $materialId = explode('_', $key)[0];

                if (!isset($generalAttributesMap[$materialId])) {
                    throw new \Exception("General attributes are missing for material ID: $materialId");
                }

                $pomrData[] = [
                    'product_range_id' => $productRange->id,
                    'material_range_id' => $details['mrid'],
                    'grade' => $generalAttributesMap[$materialId]['grade'],
                    'product_material_id' => $generalAttributesMap[$materialId]['pmid'],
                    'name' => $generalAttributesMap[$materialId]['name'],
                    'cost' => $generalAttributesMap[$materialId]['cost'],
                    'unit' => $generalAttributesMap[$materialId]['unit'],
                    'quantity' => $details['qty'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Bulk insert the data
            Pomr::insert($pomrData);

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
                'text' => 'Failed to create product range.',
            ]);
        }
    }

    public function edit($productRangeId)
    {
        // Load the product range along with its related pomr records
        $productRange = ProductRange::with('pomrs.materialRange')->find($productRangeId);
        $this->productRangeId = $productRange->id;

        if (!$productRange) {
            // Handle the case where the product range is not found
            return;
        }

        // Populate the form fields with the existing data
        $this->rangeType = Str::slug($productRange->name, '-');
        $this->rangeMrp = $productRange->mrp;
        $this->rangeRate = $productRange->rate;

        // Map the existing pomr records to the consumption array
        foreach ($productRange->pomrs as $pomr) {
            $generalKey = $pomr->product_material_id . '_0';
            $specificKey = $pomr->product_material_id . '_' . $pomr->material_range_id;

            // Populate general attributes
            if (isset($this->consumption[$generalKey])) {
                $this->consumption[$generalKey]['name'] = $pomr->name;
                $this->consumption[$generalKey]['cost'] = $pomr->cost;
                $this->consumption[$generalKey]['unit'] = $pomr->unit;
            }

            // Populate specific material range attributes and determine 'active' status
            if (isset($this->consumption[$specificKey])) {
                $this->consumption[$specificKey]['mrid'] = $pomr->material_range_id;
                $this->consumption[$specificKey]['qty'] = $pomr->quantity;
                $this->consumption[$specificKey]['active'] = $pomr->quantity ? true : false;
            }
        }

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
                'consumption.*.active' => ['boolean'],
            ]);

            // Update the ProductRange
            $productRange = ProductRange::findOrFail($this->productRangeId);
            $productRangeName = Str::title(str_replace('-', ' ', $validatedData['rangeType']));
            $productRange->update([
                'name' => $productRangeName,
                'mrp' => $validatedData['rangeMrp'],
                'rate' => $validatedData['rangeRate']
            ]);

            // Update the Pomr records
            foreach ($this->consumption as $key => $details) {
                if (Str::endsWith($key, '_0')) {
                    // Update general attributes
                    $materialId = explode('_', $key)[0];
                    Pomr::where('product_range_id', $productRange->id)
                        ->where('product_material_id', $materialId)
                        ->update([
                            'name' => $details['name'],
                            'cost' => $details['cost'],
                            'unit' => $details['unit']
                        ]);
                } else {
                    // Update specific material range attributes
                    $materialId = explode('_', $key)[0];
                    $materialRangeId = $details['mrid'];

                    $pomr = Pomr::where('product_range_id', $productRange->id)
                        ->where('product_material_id', $materialId)
                        ->where('material_range_id', $materialRangeId)
                        ->first();
                   
                    if ($details['active']) {
                        if ($pomr) {
                            // Update existing Pomr record
                            $pomr->quantity = $details['qty'];
                            $pomr->save();
                        } else {
                            $generalKey = $materialId . '_0';
                            // Create new Pomr record
                            Pomr::create([
                                'product_range_id' => $productRange->id,
                                'product_material_id' => $materialId,
                                'material_range_id' => $materialRangeId,
                                'quantity' => $details['qty'],
                                'grade' => $this->consumption[$generalKey]['grade'],
                                'name' => $this->consumption[$generalKey]['name'],
                                'cost' => $this->consumption[$generalKey]['cost'],
                                'unit' => $this->consumption[$generalKey]['unit'],
                            ]);
                        }
                    } elseif ($pomr) {
                        // Delete existing Pomr record
                        $pomr->delete();
                    }
                }
            }
            

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

        // Check if the product option has stock item, if yes then return warning that it cant be deleted
        if ($productRange->stockItems->count() > 0) {
            return redirect()->route('products.show', $this->routeValue)->with('toast', [
                'class' => 'danger',
                'text' => 'Cannot delete, Stock exits.',
            ]);
        }

        $productRange->pomrs()->delete();
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

        // Resetting and preparing the consumption array for the new range
        $this->consumption = [];

        // Initialize the consumption array for each material and material range
        foreach ($this->productMaterials as $productMaterial) {
            $generalKey = $productMaterial->id . '_0';
            $this->consumption[$generalKey] = [
                'name' => '',
                'cost' => '',
                'unit' => '',
                'pmid' => $productMaterial->id,
                'grade' => $productMaterial->grade,
            ];

            foreach ($productMaterial->material->materialRanges as $productMaterialRange) {
                $specificKey = $productMaterial->id . '_' . $productMaterialRange->id;
                $this->consumption[$specificKey] = [
                    'qty' => '',
                    'mrid' => $productMaterialRange->id,
                    'active' => false
                ];
            }
        }

        // Copying pomr data to the consumption array
        foreach ($productRange->pomrs as $pomr) {
            $generalKey = $pomr->product_material_id . '_0';
            $specificKey = $pomr->product_material_id . '_' . $pomr->material_range_id;

            // Populate general attributes
            if (isset($this->consumption[$generalKey])) {
                $this->consumption[$generalKey]['name'] = $pomr->name;
                $this->consumption[$generalKey]['cost'] = $pomr->cost;
                $this->consumption[$generalKey]['unit'] = $pomr->unit;
            }

            // Populate specific material range attributes
            if (isset($this->consumption[$specificKey])) {
                $this->consumption[$specificKey]['qty'] = $pomr->quantity;
                $this->consumption[$specificKey]['active'] = $pomr->quantity ? true : false;
            }
        }

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
        return view('prody::livewire.product-ranges-material');
    }
}
