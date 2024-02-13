<?php
  
namespace Fpaipl\Prody\Http\Livewire;
  
use Livewire\Component;
use Fpaipl\Prody\Models\Product;
  
class ProductCostsheet extends Component
{
    public $currentSection;
    public $sections;
    public $modelId;
    public $model;

    public function mount($modelId)
    {
        $this->modelId = $modelId;
        $this->model = Product::find($modelId);
        $this->currentSection = request()->section;
        
        $this->sections = collect([
            [
                'name' => 'Fixed Costs',
                'slug' => 'fixed-costs',
                'required' => false,
                'available' => true,
            ],
            [
                'name' => 'Overhead Costs',
                'slug' => 'overhead-costs',
                'required' => false,
                'available' => $this->model->productDecisions->factory,
            ],
            [
                'name' => 'Consumables',
                'slug' => 'consumables',
                'required' => false,
                'available' => $this->model->productDecisions->factory,
            ],
            [
                'name' => 'Cost Sheet',
                'slug' => 'cost-sheet',
                'required' => false,
                'available' => $this->model->productDecisions->factory,
            ],
            [
                'name' => 'Price Strategy',
                'slug' => 'pricing-strategy',
                'required' => false,
                'available' => $this->model->productDecisions->ecomm || $this->model->productDecisions->retail,
            ]
        ]);
    }

    public function render()
    {
        return view('prody::livewire.product-costsheet');
    }
}
