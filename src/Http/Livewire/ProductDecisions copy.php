<?php

namespace Fpaipl\Prody\Http\Livewire;

use Livewire\Component;
use Fpaipl\Prody\Models\Product;

class ProductDecisions extends Component
{
    public $modelId;
    public $model;
    public $decisions;
    public $productDecisions;

    public $currentTab;
    public $currentSection;

    public $costingSections;
    public $basicSections;
    
    public $routeValue;

    public $buyDecisions;
    public $sellDecisions;
    public $paymentDecisions;
    public $deliveryDecisions;

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

        $this->buyDecisions = [];
        foreach (config('prody.buy_decisions') as $decision) {
            $this->buyDecisions[] = [
                'name' => $decision['name'],
                'type' => $decision['type'],
                'image' => $decision['image'],
                'nature' => $decision['nature'],
                'details' => $decision['details'],
                'active' => $this->productDecisions[$decision['type']],
            ];
        }

        $this->sellDecisions = [];
        foreach (config('prody.sell_decisions') as $decision) {
            $this->sellDecisions[] = [
                'name' => $decision['name'],
                'type' => $decision['type'],
                'image' => $decision['image'],
                'nature' => $decision['nature'],
                'details' => $decision['details'],
                'active' => $this->productDecisions[$decision['type']],
            ];
        }

        $this->paymentDecisions = [];
        foreach (config('prody.payment_decisions') as $decision) {
            $this->paymentDecisions[] = [
                'name' => $decision['name'],
                'type' => $decision['type'],
                'image' => $decision['image'],
                'nature' => $decision['nature'],
                'details' => $decision['details'],
                'active' => $this->productDecisions[$decision['type']],
            ];
        }

        $this->deliveryDecisions = [];
        foreach (config('prody.delivery_decisions') as $decision) {
            $this->deliveryDecisions[] = [
                'name' => $decision['name'],
                'type' => $decision['type'],
                'image' => $decision['image'],
                'nature' => $decision['nature'],
                'details' => $decision['details'],
                'active' => $this->productDecisions[$decision['type']],
            ];
        }

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
                'name' => 'Overhead Costs',
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
                'name' => 'Party Ledger',
                'slug' => 'parties',
                'tab' => 'basic-details',
                'required' => true,
                'available' => $this->model->productDecisions->factory || $this->model->productDecisions->vendor,
            ],
        ]);
    }

    public function updateDecision($keyname)
    {
        $currentValue = $this->model->productDecisions->{$keyname};
        $this->model->productDecisions->{$keyname} = !$currentValue;
        $this->model->productDecisions->update();
        $this->checkGroupDecision($keyname);
        return redirect()->route('products.show', $this->routeValue)->with('toast', [
            'class' => 'success',
            'text' => 'Decision updated successfully',
        ]);
    }

    public function checkGroupDecision($keyname)
    {
        if (in_array($keyname, ['factory', 'vendor', 'market'])) {
            switch ($keyname) {
                case 'factory':
                    $this->model->productDecisions->vendor = false;
                    $this->model->productDecisions->market = false;
                    break;
                case 'vendor':
                    $this->model->productDecisions->factory = false;
                    $this->model->productDecisions->market = false;
                    break;
                case 'market':
                    $this->model->productDecisions->factory = false;
                    $this->model->productDecisions->vendor = false;
                    break;
                default: break;
            }
            $this->model->productDecisions->update();
        }

        if (in_array($keyname, ['pay_cod','pay_part','pay_half','pay_full'])) {
            switch ($keyname) {
                case 'pay_cod':
                    $this->model->productDecisions->pay_part = false;
                    $this->model->productDecisions->pay_half = false;
                    $this->model->productDecisions->pay_full = false;
                    break;
                case 'pay_part':
                    $this->model->productDecisions->pay_cod = false;
                    $this->model->productDecisions->pay_half = false;
                    $this->model->productDecisions->pay_full = false;
                    break;
                case 'pay_half':
                    $this->model->productDecisions->pay_cod = false;
                    $this->model->productDecisions->pay_part = false;
                    $this->model->productDecisions->pay_full = false;
                    break;
                case 'pay_full':
                    $this->model->productDecisions->pay_cod = false;
                    $this->model->productDecisions->pay_part = false;
                    $this->model->productDecisions->pay_half = false;
                    break;
                default: break;
            }
            $this->model->productDecisions->update();
        }

        if (in_array($keyname, ['del_pick','del_free','del_paid'])) {
            switch ($keyname) {
                case 'del_pick':
                    $this->model->productDecisions->del_free = false;
                    $this->model->productDecisions->del_paid = false;
                    break;
                case 'del_free':
                    $this->model->productDecisions->del_pick = false;
                    $this->model->productDecisions->del_paid = false;
                    break;
                case 'del_paid':
                    $this->model->productDecisions->del_pick = false;
                    $this->model->productDecisions->del_free = false;
                    break;
                default: break;
            }
            $this->model->productDecisions->update();
        }
    }

    public function shouldShowTab($decisionSlug)
    {
        return !in_array($decisionSlug, ['procurement', 'distribution', 'payments', 'deliveries']);
    }

    public function render()
    {
        return view('prody::livewire.product-decisions');
    }
}
