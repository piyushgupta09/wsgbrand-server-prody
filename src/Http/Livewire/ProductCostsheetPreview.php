<?php

namespace Fpaipl\Prody\Http\Livewire;

use Fpaipl\Prody\Models\Fixedcost;
use Livewire\Component;
use Fpaipl\Prody\Models\Product;

class ProductCostsheetPreview extends Component
{
    public $product;
    public $producdId;

    public $productMaterials;
    public $overheads;
    public $consumables;
    public $fixedcosts;

    public $totalCost;
    public $totalMaterialCost;

    public function mount($modelId)
    {   
        $this->producdId = $modelId;
        $this->product = Product::find($this->producdId);
        $this->productMaterials = $this->product->productMaterials;
        $this->overheads = $this->product->productOverheads;
        $this->consumables = $this->product->productConsumables;
        $this->fixedcosts = Fixedcost::all();
        $this->calculateTotalMaterialCost();
        $this->calculateTotalCost();
    }

    public function calculateTotalMaterialCost()
    {
        $this->totalMaterialCost = 0;
        foreach ($this->productMaterials as $productMaterial) {
            // Ensure $productMaterial->product and $productMaterial->product->pomrs are not null
            $pomrs = optional($productMaterial->product)->pomrs;
            $pomr = $pomrs ? $pomrs->where('product_material_id', $productMaterial->id)->first() : null;
            if ($pomr) {
                $this->totalMaterialCost += ($pomr->quantity ?? 0) * ($pomr->cost ?? 0);
            }
        }
    }
    
    public function calculateTotalCost()
    {
        $this->totalCost = $this->totalMaterialCost;
        foreach ([$this->overheads, $this->consumables] as $costs) {
            foreach ($costs as $cost) {
                // Ensure $cost->amount is not null
                $this->totalCost += $cost->amount ?? 0;
            }
        }
        foreach ($this->fixedcosts as $fixedcost) {
            // Ensure $fixedcost->rate is not null
            $this->totalCost += $fixedcost->rate ?? 0;
        }
    }
    

    public function printout()
    {
        $this->dispatchBrowserEvent('print-costsheet');
    }

    public function render()
    {
        return view('prody::livewire.product-costsheet-preview');
    }
}
