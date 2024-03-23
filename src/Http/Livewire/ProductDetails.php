<?php

namespace Fpaipl\Prody\Http\Livewire;

use Livewire\Component;
use Fpaipl\Prody\Models\Product;

class ProductDetails extends Component
{
    public $modelId;
    public $model;
    public $decisions;
    public $productDecisions;

    public $currentTab;
    public $currentSection;

    public $basicSections;
    public $costingSections;
    public $advanceSections;

    public $routeValue;

    public function mount($modelId)
    {
        $this->modelId = $modelId;
        $this->model = Product::find($modelId);
        $this->productDecisions = $this->model->productDecisions;
        $this->routeValue = ['product' => $this->model->slug];
        $this->arrangeData();
    }

    public function arrangeData()
    {
        $this->decisions = config('prody.decisions');

        $this->currentTab = request()->tab;
        $this->currentSection = request()->section;
        
        $this->costingSections = collect([
            [
                'name' => 'Fixed Costs',
                'slug' => 'fixed-costs',
                'tab' => 'costing',
                'required' => false,
                'available' => true,
            ],
            [
                'name' => 'Overheads',
                'slug' => 'overhead-costs',
                'tab' => 'costing',
                'required' => false,
                'available' => $this->model->productDecisions->factory,
            ],
            [
                'name' => 'Consumables',
                'slug' => 'consumables',
                'tab' => 'costing',
                'required' => false,
                'available' => $this->model->productDecisions->factory,
            ],
            [
                'name' => 'Cost Sheet',
                'slug' => 'cost-sheet',
                'tab' => 'costing',
                'required' => false,
                'available' => $this->model->productDecisions->factory,
            ],
        ]);

        $this->basicSections = collect([
            [
                'name' => 'Pair Material',
                'slug' => 'materials',
                'tab' => 'basic-details',
                'required' => true,
                'available' => $this->model->productDecisions->factory,
            ],
            [
                'name' => 'Color Options',
                'slug' => 'color-options',
                'tab' => 'basic-details',
                'required' => true,
                'available' => true,
            ],
            [
                'name' => 'Size Range',
                'slug' => 'size-range',
                'tab' => 'basic-details',
                'required' => true,
                'available' => true,
            ],
            [
                'name' => 'Stock',
                'slug' => 'stock',
                'tab' => 'basic-details',
                'required' => true,
                'available' => true,
            ],
            [
                'name' => 'Party Ledger',
                'slug' => 'parties',
                'tab' => 'basic-details',
                'required' => true,
                'available' => $this->model->productDecisions->factory || $this->model->productDecisions->vendor,
            ],
        ]);

        $this->advanceSections = collect([
            [
                'name' => 'Attributes',
                'slug' => 'attributes',
                'tab' => 'advance-details',
                'required' => false,
                'available' => $this->model->productDecisions->retail || $this->model->productDecisions->ecomm || $this->model->productDecisions->inbulk,
            ],
            [
                'name' => 'Measurments',
                'slug' => 'measurments',
                'tab' => 'advance-details',
                'required' => false,
                'available' => $this->model->productDecisions->retail || $this->model->productDecisions->ecomm,
            ],
            [
                'name' => 'Collections',
                'slug' => 'collections',
                'tab' => 'collections',
                'required' => false,
                'available' => $this->model->productDecisions->retail || $this->model->productDecisions->ecomm || $this->model->productDecisions->inbulk,
            ],
        ]);
    }

    public function render()
    {
        return view('prody::livewire.product-details');
    }
}
