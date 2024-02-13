<?php

namespace Fpaipl\Prody\Http\Livewire;

use Livewire\Component;
use Fpaipl\Prody\Models\Product;
use Fpaipl\Prody\Models\Overhead;
use Fpaipl\Prody\Models\ProductOverhead;

class ProductOverheads extends Component
{
    public $showForm;

    public $overheadStages;

    public $productOverheadId;
    public $productId;
    public $product;
    public $overheads;
    public $formType;

    public $productOverheads;

    // Process Form Fields
    public $overhead_stage;
    public $overhead;
    public $ratio;
    public $rate;
    public $reasons;
    public $routeValue;

    public function mount($modelId)
    {
        $this->showForm = config('prody.show_add_form');
        $this->productId = $modelId;
        $this->product = Product::find($this->productId);
        $this->overheadStages = Overhead::distinct()->get(['stage'])->toArray();
        $this->reloadData();
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->overheads = collect();
        $this->overhead = null;
        $this->ratio = 1;
        $this->rate = 0;
        $this->reasons = '';
        $this->formType = 'create';
    }

    public function reloadData()
    {
        $this->productOverheads = $this->product->productOverheads()->get();
        $this->routeValue = [
            'tab' => request()->tab,
            'product' => $this->product->slug,
            'section' => request()->section,
        ];
    }
    
    public function toggleForm()
    {
        $this->showForm = !$this->showForm;
    }

    public function updatedOverheadStage()
    {
        $this->overheads = Overhead::where('stage', $this->overhead_stage)->get();
    }

    public function updatedOverhead()
    {
        $selectedOverhead = Overhead::find($this->overhead);
        $this->rate = $selectedOverhead->rate;
    }

    public function store()
    {
        $validatedData = $this->validate([
            'overhead' => 'required|exists:overheads,id',
            'ratio' => 'required|numeric',
            'rate' => 'required|numeric',
            'reasons' => 'nullable|string',
        ]);

        $validatedData['overhead_id'] = $validatedData['overhead'];
        $validatedData['amount'] = $validatedData['ratio'] * $validatedData['rate'];
        $this->product->productOverheads()->create($validatedData);

        return redirect()->route('products.show', $this->routeValue)->with('toast', [
            'class' => 'success',
            'text' => 'Process created successfully.',
        ]);
    }

    public function edit($productOverheadId)
    {
        $productOverhead = ProductOverhead::findOrFail($productOverheadId);
        $this->productOverheadId = $productOverhead->id;
        $this->overhead = $productOverhead->overhead_id;
        $this->ratio = $productOverhead->ratio;
        $this->rate = $productOverhead->rate;
        $this->reasons = $productOverhead->reasons;

        $this->formType = 'edit';
        $this->showForm = true;
    }

    public function update()
    {
        $validatedData = $this->validate([
            'overhead' => 'required|exists:overheads,id',
            'ratio' => 'required|numeric',
            'rate' => 'required|numeric',
            'reasons' => 'nullable|string',
        ]);

        $validatedData['overhead_id'] = $validatedData['overhead'];
        $validatedData['amount'] = $validatedData['ratio'] * $validatedData['rate'];
        $productOverhead = ProductOverhead::findOrFail($this->productOverheadId);
        $productOverhead->update($validatedData);

        return redirect()->route('products.show', $this->routeValue)->with('toast', [
            'class' => 'success',
            'text' => 'Process updated successfully.',
        ]);
    }

    public function delete($productOverheadId)
    {
        $productOverhead = ProductOverhead::findOrFail($productOverheadId);
        $productOverhead->delete();

        return redirect()->route('products.show', $this->routeValue)->with('toast', [
            'class' => 'success',
            'text' => 'Product overhead deleted successfully.',
        ]);
    }

    public function render()
    {
        return view('prody::livewire.product-overheads');
    }
}
