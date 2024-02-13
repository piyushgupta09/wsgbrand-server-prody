<?php
  
namespace Fpaipl\Prody\Http\Livewire;
  
use Livewire\Component;
use Fpaipl\Prody\Models\Product;
  
class ProductSections extends Component
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
                'name' => 'Pair Material',
                'slug' => 'materials',
                'required' => true,
                'available' => $this->model->factory,
            ],
            [
                'name' => 'Color Options',
                'slug' => 'color-options',
                'required' => true,
                'available' => true,
            ],
            [
                'name' => 'Size Range',
                'slug' => 'size-range',
                'required' => true,
                'available' => true,
            ],
            [
                'name' => 'Party Ledger',
                'slug' => 'parties',
                'required' => true,
                'available' => $this->model->factory || $this->model->vendor,
            ],
        ]);
    }

    public function render()
    {
        return view('prody::livewire.product-sections');
    }
}
