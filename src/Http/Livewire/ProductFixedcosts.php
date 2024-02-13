<?php

namespace Fpaipl\Prody\Http\Livewire;

use Fpaipl\Prody\Models\Fixedcost;
use Livewire\Component;
use Fpaipl\Prody\Models\Product;

class ProductFixedcosts extends Component
{
    public $fixedcosts;

    public function mount($modelId)
    {
        $this->fixedcosts = Fixedcost::all();
    }

    public function render()
    {
        return view('prody::livewire.product-fixedcosts');
    }
}
