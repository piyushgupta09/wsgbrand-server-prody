<?php
  
namespace Fpaipl\Prody\Http\Livewire;
  
use Livewire\Component;
use Fpaipl\Prody\Models\Product;
  
class ProductPricing extends Component
{
    public $modelId;
    public $model;

    public function mount($modelId)
    {
        $this->modelId = $modelId;
        $this->model = Product::find($modelId);
    }

    public function render()
    {
        return view('prody::livewire.product-pricing');
    }
}
