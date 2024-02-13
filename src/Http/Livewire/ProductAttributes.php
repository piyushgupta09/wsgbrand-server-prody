<?php
  
namespace Fpaipl\Prody\Http\Livewire;
  
use Livewire\Component;
use Fpaipl\Prody\Models\Product;
use Fpaipl\Prody\Models\Attrikey;
use Fpaipl\Prody\Models\Attrival;
use Fpaipl\Prody\Models\ProductAttribute;

class ProductAttributes extends Component
{
    public $showForm;
    public $formType;
    
    public $name;
    public $value;
    public $productId;
    
    public $attrikeys;
    public $attrikey;
    public $attrikey_vals;
    public $attrival;
    public $attributes;

    public $attrikey_info;
    public $attrival_info;

    public $routeValue;
    public $currentTab;
    public $currentSection;

    public $modelId;
    public $model;

    public function mount($modelId)
    {
        $this->modelId = $modelId;
        $this->model = Product::find($modelId);
        $this->attrikeys = Attrikey::all();
        $this->productId = $this->model->attributable_id;
        $this->attrikey_vals = collect();
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

    public function updatedAttrikey($attrikeyName)
    {
        if (Attrikey::where('name', $attrikeyName)->first() == null) {
            $this->attrikey_info = 'This attribute key does not exist. Will be created new.';
            $this->attrikey_vals = collect();
            return;
        }

        $selectedAttrikey = Attrikey::where('name', $attrikeyName)->first()?->load('attrivals');
        $this->attrikey_vals = $selectedAttrikey->attrivals;
    }

    public function updatedAttrival($attrivalValue)
    {
        if (Attrival::where('value', $attrivalValue)->first() == null) {
            $this->attrival_info = 'This attribute value does not exist. Will be created new.';
            return;
        }
    }

    public function reloadData()
    {
        $this->attributes = $this->model->productAttributes;
        $this->formType = 'create';
        $this->showForm = false;
        $this->currentTab = request()->tab;
        $this->currentSection = request()->section;
        $this->routeValue = [
            'tab' => $this->currentTab,
            'product' => $this->model->slug,
            'section' => $this->currentSection,
        ];
    }

    public function store()
    {
        // find or create the attrikey
        $attrikey = Attrikey::firstOrCreate(['name' => $this->attrikey]);

        // find or create the attrival
        $attrival = Attrival::firstOrCreate(['value' => $this->attrival, 'attrikey_id' => $attrikey->id]);

        // find or create the productAttribute
        ProductAttribute::firstOrCreate([
            'product_id' => $this->modelId,
            'attrikey_id' => $attrikey->id,
            'attrival_id' => $attrival->id,
        ]);

        return redirect()->route('products.show', $this->routeValue)->with('toast', [
            'class' => 'success',
            'text' => 'Attribute deleted successfully.',
        ]);
    }

    public function delete($attributeId)
    {
        $attribute = ProductAttribute::findOrFail($attributeId);
        $attribute->delete();
        return redirect()->route('products.show', $this->routeValue)->with('toast', [
            'class' => 'success',
            'text' => 'Attribute deleted successfully.',
        ]);
    }
    
    public function render()
    {
        return view('prody::livewire.product-attributes');
    }
}
