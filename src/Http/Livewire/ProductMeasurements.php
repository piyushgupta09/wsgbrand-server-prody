<?php
  
namespace Fpaipl\Prody\Http\Livewire;
  
use Livewire\Component;
use Fpaipl\Prody\Models\Product;
use Fpaipl\Prody\Models\Measurekey;
use Fpaipl\Prody\Models\Measureval;
use Fpaipl\Prody\Models\ProductMeasurement;

class ProductMeasurements extends Component
{
    public $showForm;
    public $formType;
    
    public $name;
    public $value;
    public $productId;
    
    public $measurekeys;
    public $measurekey;
    public $measurekey_vals;
    public $measureval;
    public $measurements;

    public $measurekey_info;
    public $measureval_info;

    public $routeValue;
    public $currentTab;
    public $currentSection;

    public $modelId;
    public $model;

    public function mount($modelId)
    {
        $this->modelId = $modelId;
        $this->model = Product::find($modelId);
        $this->measurekeys = Measurekey::all();
        $this->measurekey_vals = collect();
        $this->resetForm();
    }

    public function toggleForm()
    {
        $this->showForm = !$this->showForm;
    }

    public function resetForm()
    {
        $this->reloadData();
    }

    public function updatedMeasurekey($measurekeyNameUnit)
    {
        $measurekeyName = explode(' | ', $measurekeyNameUnit)[0];
        if (Measurekey::where('name', $measurekeyName)->first() == null) {
            $this->measurekey_info = 'This measurement key does not exist. Will be created new.';
            $this->measurekey_vals = collect();
            return;
        }

        $selectedMeasurekey = Measurekey::where('name', $measurekeyName)->first()?->load('measurevals');
        $this->measurekey_vals = $selectedMeasurekey->measurevals;
    }

    public function updatedMeasureval($measurevalValue)
    {
        if (Measureval::where('value', $measurevalValue)->first() == null) {
            $this->measureval_info = 'This measurement value does not exist. Will be created new.';
            return;
        }
    }

    public function reloadData()
    {
        $this->measurements = $this->model->productMeasurements;
        $this->formType = 'create';
        $this->showForm = false;
       
        $this->routeValue = [
            'tab' => request()->tab,
            'product' => $this->model->slug,
            'section' => request()->section,
        ];
    }

    public function store()
    {
        // Validate the measurekey format and the measureval presence
        $this->validate([
            'measurekey' => ['required', 'string', 'regex:/^[^|]+\s*\|\s*[^|]+$/'], // Example regex, adjust as necessary
            'measureval' => 'required|string',
        ],[
            'measurekey.required' => 'The measurekey field is required.',
            'measurekey.string' => 'The measurekey must be a string.',
            'measurekey.regex' => 'The measurekey format is invalid. Expected format: "Name | Unit".', // Custom message with format example
            'measureval.required' => 'The measureval field is required.',
            'measureval.string' => 'The measureval must be a string.',
        ]);

        // Splitting measurekey into name and unit, with error handling
        $measurekeyParts = explode(' | ', $this->measurekey);
        if(count($measurekeyParts) < 2) {
            return back()->withErrors(['measurekey' => 'Invalid measurekey format.']);
        }
        [$measurekeyName, $measurekeyUnit] = $measurekeyParts;

        // Ensuring the creation or retrieval of Measurekey and Measureval entities
        $measurekey = Measurekey::firstOrCreate([
            'name' => $measurekeyName, 
            'unit' => $measurekeyUnit
        ]);

        $measureval = Measureval::firstOrCreate([
            'value' => $this->measureval, 
            'measurekey_id' => $measurekey->id
        ]);

        // Linking the product with the measurekey and measureval
        ProductMeasurement::firstOrCreate([
            'product_id' => $this->modelId,
            'measurekey_id' => $measurekey->id,
            'measureval_id' => $measureval->id,
        ]);

        // Redirecting with a success message appropriate to the action performed
        return redirect()->route('products.show', $this->routeValue)->with('toast', [
            'class' => 'success',
            'text' => 'Measurement attribute added successfully.',
        ]);
    }

    public function delete($measurementId)
    {
        $measurement = ProductMeasurement::findOrFail($measurementId);
        $measurement->delete();
        return redirect()->route('products.show', $this->routeValue)->with('toast', [
            'class' => 'success',
            'text' => 'Attribute deleted successfully.',
        ]);
    }
    
    public function render()
    {
        return view('prody::livewire.product-measurements');
    }
}
