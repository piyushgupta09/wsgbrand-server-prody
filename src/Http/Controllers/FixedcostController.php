<?php

namespace Fpaipl\Prody\Http\Controllers;

use Illuminate\Http\Request;
use Fpaipl\Prody\Models\Fixedcost;
use Fpaipl\Panel\Http\Controllers\PanelController;
use Fpaipl\Prody\DataTables\FixedcostDatatable as Datatable;

class FixedcostController extends PanelController
{
    public function __construct()
    {
        parent::__construct(
            new Datatable(), 
            'Fpaipl\Prody\Models\Fixedcost',
            'fixedcost', 'fixedcosts.index'
        );
    }
   
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:fixedcosts',
            'amount' => 'required|numeric|min:0',
            'capacity' => 'required|numeric|min:0',
            'details' => 'required|string',
        ]);
        
        Fixedcost::create([
            'name' => $request->name,
            'amount' => $request->amount,
            'capacity' => $request->capacity,
            'rate' => $request->amount / $request->capacity,
            'details' => $request->details,
        ]);
        
        return redirect()->route('fixedcosts.index')->with('toast', [
            'class' => 'success',
            'text' => 'Fixedcost created successfully.'
        ]);
    }

    public function update(Request $request, Fixedcost $fixedcost)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'capacity' => 'required|numeric|min:0',
            'details' => 'required|string',
        ]);
        
        $fixedcost->update($validated);
        $fixedcost->rate = $fixedcost->amount / $fixedcost->capacity;
        $fixedcost->save();
        
        return redirect()->route('fixedcosts.index')->with('toast', [
            'class' => 'success',
            'text' => 'Fixedcost updated successfully.'
        ]);
    }

    public function destroy(Fixedcost $fixedcost)
    {
        $fixedcost->delete();
        
        return redirect()->route('fixedcosts.index')->with('toast', [
            'class' => 'success',
            'text' => 'Fixedcost deleted successfully.'
        ]);
    }
}
