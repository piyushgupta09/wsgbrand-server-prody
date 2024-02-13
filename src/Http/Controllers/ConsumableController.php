<?php

namespace Fpaipl\Prody\Http\Controllers;

use Illuminate\Http\Request;
use Fpaipl\Prody\Models\Consumable;
use Fpaipl\Panel\Http\Controllers\PanelController;
use Fpaipl\Prody\Datatables\ConsumableDatatable as Datatable;

class ConsumableController extends PanelController
{
    public function __construct()
    {
        parent::__construct(
            new Datatable(), 
            'Fpaipl\Prody\Models\Consumable',
            'consumable', 'consumables.index'
        );
    }
   
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:consumables',
            'unit' => 'required|string|min:1',
            'rate' => 'nullable|numeric|min:1',
            'description' => 'nullable|string',
        ]);

        if (!isset($validated['rate'])) {
            $validated['rate'] = 1;
        }
        
        Consumable::create($validated);
        
        return redirect()->route('consumables.index')->with('toast', [
            'class' => 'success',
            'text' => 'Consumable created successfully.'
        ]);
    }

    public function update(Request $request, Consumable $consumable)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'unit' => 'required|string|min:1',
            'rate' => 'nullable|numeric|min:1',
            'description' => 'nullable|string',
        ]);
        
        $consumable->update($validated);
        
        return redirect()->route('consumables.index')->with('toast', [
            'class' => 'success',
            'text' => 'Consumable updated successfully.'
        ]);
    }

    public function destroy(Consumable $consumable)
    {
        $consumable->delete();
        
        return redirect()->route('consumables.index')->with('toast', [
            'class' => 'success',
            'text' => 'Consumable deleted successfully.'
        ]);
    }
}