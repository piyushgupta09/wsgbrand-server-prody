<?php

namespace Fpaipl\Prody\Http\Livewire;

use Livewire\Component;
use Fpaipl\Prody\Models\Product;
use Fpaipl\Prody\Models\ProductProcess;
use Fpaipl\Prody\Models\ProductMaterial;

class ProductProcesses extends Component
{
    public $showForm;
    public $routeValue;

    public $productId;
    public $product;
    public $processes;
    public $formType;

    public $overheads;

    // Cost Sheet
    public $totalMaterialCost = 0;
    public $totalProcessCost = 0;
    public $totalCost = 0;

    // Process Form Fields
    public $processId;
    public $stage;
    public $nature;
    public $name;
    public $cost;
    public $time;
    public $order;
    public $instructions;
    public $description;
    public $specialNote;

    public function mount($modelId)
    {
        $this->showForm = false;
        $this->productId = $modelId;
        $this->product = Product::find($this->productId);
        $this->routeValue = ['product' => $this->product->slug, 'section' => 'process-costs'];
        $this->reloadData();
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->processId = null;
        $this->stage = '';
        $this->nature = 0;
        $this->name = '';
        $this->cost = 0.0;
        $this->time = 0.0;
        $this->order = 0;
        $this->instructions = '';
        $this->description = '';
        $this->specialNote = '';
        $this->formType = 'create';
    }

    public function reloadData()
    {
        $this->overheads = $this->product->productOverheads()->with('overhead')->get();
        $this->processes = $this->product->productProcesses()->orderBy('order')->get();

        // Calculate the total material cost
        $this->totalMaterialCost = ProductMaterial::where('product_id', $this->product->id)
            ->with(['material', 'product.pomrs'])
            ->get()
            ->sum(function ($productMaterial) {
                return optional($productMaterial->product->pomrs->firstWhere('product_material_id', $productMaterial->id))->cost * optional($productMaterial->product->pomrs->firstWhere('product_material_id', $productMaterial->id))->quantity;
                // return $productMaterial->material->price * optional($productMaterial->product->pomrs->firstWhere('product_material_id', $productMaterial->id))->quantity;
            });

        // Calculate the total process cost
        $this->totalProcessCost = $this->processes->sum('cost');

        // Calculate the total cost
        $this->totalCost = $this->totalMaterialCost + $this->totalProcessCost;
    }
    
    public function toggleForm()
    {
        $this->showForm = !$this->showForm;
    }

    public function store()
    {
        $validatedData = $this->validate([
            'stage' => 'required|string|max:255',
            'nature' => 'required|boolean',
            'name' => 'required|string|max:255',
            'cost' => 'required|numeric',
            'time' => 'required|numeric',
            'order' => 'required|integer',
            'instructions' => 'nullable|string',
            'description' => 'nullable|string',
            'specialNote' => 'nullable|string',
        ]);

        ProductProcess::create([
            'product_id' => $this->productId,
            'stage' => $validatedData['stage'],
            'nature' => $validatedData['nature'],
            'name' => $validatedData['name'],
            'cost' => $validatedData['cost'],
            'time' => $validatedData['time'],
            'order' => $validatedData['order'],
            'instructions' => $validatedData['instructions'],
            'description' => $validatedData['description'],
            'special_note' => $validatedData['specialNote'],
        ]);

        return redirect()->route('products.show', $this->routeValue)->with('toast', [
            'class' => 'success',
            'text' => 'Process created successfully.',
        ]);
    }

    public function edit($processId)
    {
        $process = ProductProcess::findOrFail($processId);
        $this->processId = $process->id;
        $this->stage = $process->stage;
        $this->nature = $process->nature;
        $this->name = $process->name;
        $this->cost = $process->cost;
        $this->time = $process->time;
        $this->order = $process->order;
        $this->instructions = $process->instructions;
        $this->description = $process->description;
        $this->specialNote = $process->special_note;

        $this->formType = 'edit';
        $this->showForm = true;
    }

    public function update()
    {
        $validatedData = $this->validate([
            'stage' => 'required|string|max:255',
            'nature' => 'required|boolean',
            'name' => 'required|string|max:255',
            'cost' => 'required|numeric',
            'time' => 'required|numeric',
            'order' => 'required|integer',
            'instructions' => 'nullable|string',
            'description' => 'nullable|string',
            'specialNote' => 'nullable|string',
        ]);

        $validatedData['special_note'] = $validatedData['specialNote'];

        $process = ProductProcess::findOrFail($this->processId);
        $process->update($validatedData);

        return redirect()->route('products.show', $this->routeValue)->with('toast', [
            'class' => 'success',
            'text' => 'Process updated successfully.',
        ]);
    }

    public function delete($processId)
    {
        $process = ProductProcess::findOrFail($processId);
        $process->delete();

        return redirect()->route('products.show', $this->routeValue)->with('toast', [
            'class' => 'success',
            'text' => 'Process deleted successfully.',
        ]);
    }

    public function render()
    {
        return view('prody::livewire.product-processes');
    }

    public function printout()
    {
        $this->dispatchBrowserEvent('print-costsheet');
    }
}
