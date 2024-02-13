<?php

namespace Fpaipl\Prody\Http\Livewire;

use Livewire\Component;
use Fpaipl\Prody\Models\Product;
use Fpaipl\Prody\Models\Consumable;
use Fpaipl\Prody\Models\ProductConsumable;

class ProductConsumables extends Component
{
    public $showForm;

    public $consumables;

    public $productConsumableId;
    public $productId;
    public $product;
    public $formType;

    public $productConsumables;
    public $selectedConsumable;

    // Process Form Fields
    public $overhead_stage;
    public $consumable;
    public $ratio;
    public $rate;
    public $reasons;
    public $routeValue;

    public function mount($modelId)
    {
        $this->showForm = config('prody.show_add_form');
        $this->productId = $modelId;
        $this->product = Product::find($this->productId);
        $this->consumables = Consumable::all();
        $this->reloadData();
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->consumable = null;
        $this->ratio = 1;
        $this->rate = 0;
        $this->reasons = '';
        $this->formType = 'create';
    }

    public function reloadData()
    {
        $this->productConsumables = $this->product->productConsumables()->get();
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

    public function updatedConsumable()
    {
        $this->selectedConsumable = Consumable::find($this->consumable);
        $this->rate = $this->selectedConsumable->rate;
    }

    public function store()
    {
        $validatedData = $this->validate([
            'consumable' => 'required|exists:consumables,id',
            'ratio' => 'required|numeric',
            'rate' => 'required|numeric',
            'reasons' => 'nullable|string',
        ]);

        $validatedData['consumable_id'] = $validatedData['consumable'];
        $validatedData['amount'] = $validatedData['ratio'] * $validatedData['rate'];
        $this->product->productConsumables()->create($validatedData);
        
        return redirect()->route('products.show', $this->routeValue)->with('toast', [
            'class' => 'success',
            'text' => 'Product Consumable created successfully.',
        ]);
    }

    public function edit($productConsumableId)
    {
        $productConsumable = ProductConsumable::findOrFail($productConsumableId);
        $this->productConsumableId = $productConsumable->id;
        $this->consumable = $productConsumable->consumable_id;
        $this->ratio = $productConsumable->ratio;
        $this->rate = $productConsumable->rate;
        $this->reasons = $productConsumable->reasons;

        $this->formType = 'edit';
        $this->showForm = true;
    }

    public function update()
    {
        $validatedData = $this->validate([
            'consumable' => 'required|exists:consumables,id',
            'ratio' => 'required|numeric',
            'rate' => 'required|numeric',
            'reasons' => 'nullable|string',
        ]);

        $validatedData['consumable_id'] = $validatedData['consumable'];
        $validatedData['amount'] = $validatedData['ratio'] * $validatedData['rate'];
        $productConsumable = ProductConsumable::findOrFail($this->productConsumableId);
        $productConsumable->update($validatedData);

        return redirect()->route('products.show', $this->routeValue)->with('toast', [
            'class' => 'success',
            'text' => 'Process updated successfully.',
        ]);
    }

    public function delete($productConsumableId)
    {
        $productConsumable = ProductConsumable::findOrFail($productConsumableId);
        $productConsumable->delete();

        return redirect()->route('products.show', $this->routeValue)->with('toast', [
            'class' => 'success',
            'text' => 'Product consumable deleted successfully.',
        ]);
    }

    public function render()
    {
        return view('prody::livewire.product-consumables');
    }
}
