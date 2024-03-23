<?php
  
namespace Fpaipl\Prody\Http\Livewire;
  
use Livewire\Component;
use Fpaipl\Prody\Models\Product;
use Illuminate\Support\Facades\DB;
use Fpaipl\Prody\Models\Measurekey;
use Fpaipl\Prody\Models\Measureval;
use Illuminate\Database\QueryException;
use Fpaipl\Prody\Models\ProductMeasurement;

class ProductMeasurements extends Component
{
    public $showForm;
    public $formType;
    
    public $name;
    public $value;
    public $productId;
    
    public $productRanges;
    public $measurekeys;
    public $measurekey;
    public $measurekey_vals;
    public $measureval = [];
    public $measurements;

    public $confirmingDelete = false;
    public $measurekeyIdToDelete;

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
        $this->productRanges = $this->model->productRanges;
        $this->measurekeys = Measurekey::all();
        $this->firstLoad($this->measurekeys->first()?->name);
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

    public function firstLoad($measurekeyName)
    {
        if (Measurekey::where('name', $measurekeyName)->first() == null) {
            $this->measurekey_info = 'This measurement key does not exist. Will be created new.';
            $this->measurekey_vals = collect();
            return;
        }

        $selectedMeasurekey = Measurekey::where('name', $measurekeyName)->first()?->load('measurevals');
        $this->measurekey_vals = $selectedMeasurekey->measurevals;
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

        // Prefill measureval based on default values
        $defaultValues = config("prody.default.measurements.{$measurekeyName}", []);
   
        // Reset measureval to ensure fresh start
        $this->measureval = [];

        // Assuming $this->productRanges holds ranges like ['XS', 'S', 'M', 'L', 'XL', 'XXL']
        foreach ($this->productRanges as $range) {
            // Check if there's a default value for the current range and measurekey
            if (isset($defaultValues[$range->slug])) {
                $this->measureval[$range->id] = $defaultValues[$range->slug];
            }
        }
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
        // Initialize validation rules for measurekey including a format check
        $validationRules = [
            'measurekey' => ['required', 'string'],
        ];
    
        // Initialize validation messages including a custom message for format check
        $validationMessages = [
            'measurekey.required' => 'The measurement name field is required.',
            'measurekey.string' => 'Invalid measurement name.',
        ];
    
        // Dynamically add validation rules for measureval and productRangeId
        foreach ($this->productRanges as $productRange) {
            $rangeId = $productRange->id;
            // $validationRules["measureval.$rangeId"] = 'required|string|exists:measurevals,value';
            $validationRules["measureval.$rangeId"] = 'required|string';
            $validationMessages["measureval.$rangeId.required"] = "The value for range $productRange->name is required.";
            $validationMessages["measureval.$rangeId.string"] = "The value for range $productRange->name must be a string.";
            // $validationMessages["measureval.$rangeId.exists"] = "The selected value for range $productRange->name does not exist.";
        }
    
        // Perform validation
        $this->validate($validationRules, $validationMessages);

        DB::beginTransaction();

        try {

            // Proceed to create or find the measurekey
            $measurekey = Measurekey::where('name', $this->measurekey)->first();
        
            // Loop through the measurevals and productRanges to save ProductMeasurements
            foreach ($this->measureval as $productRangeId => $value) {
                $measureval = Measureval::firstOrCreate([
                    'value' => $value,
                    'measurekey_id' => $measurekey->id,
                ]);
        
                ProductMeasurement::updateOrCreate(
                    [
                        'product_id' => $this->modelId,
                        'measurekey_id' => $measurekey->id,
                        'product_range_id' => $productRangeId,
                    ],
                    [
                        'measureval_id' => $measureval->id,
                    ]
                );
            }

            DB::commit();

            return redirect()->route('products.show', $this->routeValue)->with('toast', [
                'class' => 'success',
                'text' => 'Measurement attributes added successfully.',
            ]);

        } catch (QueryException $e) {

            DB::rollBack();

            $message = 'Unable to added atributes.';
            
            // Check if the error is a duplicate entry error
            if ($e->errorInfo[1] == 1062) {
                $message = 'Duplicate entry detected. Please check your inputs.';
            } else {
                // Handle other types of database errors
                $message = 'An unexpected error occurred. Please try again.';
            }
    
            return redirect()->route('products.show', $this->routeValue)->with('toast', [
                'class' => 'danger',
                'text' => $message,
            ]);
        }
       
    }

    public function edit($measurekeyId)
    {
        $this->formType = 'edit';
        $this->showForm = true;
        $this->measurekey = Measurekey::find($measurekeyId)->name;
        $this->measurekey_vals = Measurekey::find($measurekeyId)->measurevals;
        $this->measurements = ProductMeasurement::where('product_id', $this->modelId)->where('measurekey_id', $measurekeyId)->get();
        $this->measureval = [];
        foreach ($this->measurements as $measurement) {
            $this->measureval[$measurement->product_range_id] = $measurement->measureval->value;
        }
    }

    public function update()
    {
        // Dynamically add validation rules for measureval and productRangeId
        foreach ($this->productRanges as $productRange) {
            $rangeId = $productRange->id;
            // $validationRules["measureval.$rangeId"] = 'required|string|exists:measurevals,value';
            $validationRules["measureval.$rangeId"] = 'required|string';
            $validationMessages["measureval.$rangeId.required"] = "The value for range $productRange->name is required.";
            $validationMessages["measureval.$rangeId.string"] = "The value for range $productRange->name must be a string.";
            // $validationMessages["measureval.$rangeId.exists"] = "The selected value for range $productRange->name does not exist.";
        }
    
        // Perform validation
        $this->validate($validationRules, $validationMessages);

        try {

            // Proceed to create or find the measurekey
            $measurekey = Measurekey::where('name', $this->measurekey)->first();
        
            // Loop through the measurevals and productRanges to save ProductMeasurements
            foreach ($this->measureval as $productRangeId => $value) {
                $measureval = Measureval::firstOrCreate([
                    'value' => $value,
                    'measurekey_id' => $measurekey->id,
                ]);
        
                ProductMeasurement::updateOrCreate(
                    [
                        'product_id' => $this->modelId,
                        'measurekey_id' => $measurekey->id,
                        'product_range_id' => $productRangeId,
                    ],
                    [
                        'measureval_id' => $measureval->id,
                    ]
                );
            }

            return redirect()->route('products.show', $this->routeValue)->with('toast', [
                'class' => 'success',
                'text' => 'Measurement attributes updated successfully.',
            ]);

        } catch (QueryException $e) {
            
            // Check if the error is a duplicate entry error
            if ($e->errorInfo[1] == 1062) {
                session()->flash('error', 'Duplicate entry detected. Please check your inputs.');
            } else {
                // Handle other types of database errors
                session()->flash('error', 'An unexpected error occurred. Please try again.');
            }
        }
    }

    public function confirmDelete($measurekeyId)
    {
        $this->confirmingDelete = true;
        $this->measurekeyIdToDelete = $measurekeyId;
    }

    public function deleteConfirmed()
    {
        $measurements = ProductMeasurement::where('product_id', $this->modelId)->where('measurekey_id', $this->measurekeyIdToDelete)->get();
        if ($measurements->isEmpty()) {
            return redirect()->route('products.show', $this->routeValue)->with('toast', [
                'class' => 'danger',
                'text' => 'Attribute not found.',
            ]);
        }

        foreach ($measurements as $measurement) {
            $measurement->delete();
        }

        return redirect()->route('products.show', $this->routeValue)->with('toast', [
            'class' => 'success',
            'text' => 'Attribute deleted successfully.',
        ]);
    
        if ($measurement) {
            $measurement->delete();
            
            session()->flash('message', 'Measurement deleted successfully.');
        } else {
            session()->flash('error', 'Measurement not found.');
        }
    
        // Refresh or reset component state as needed after deletion
        $this->resetForm();
    }
    
    public function render()
    {
        return view('prody::livewire.product-measurements');
    }
}
