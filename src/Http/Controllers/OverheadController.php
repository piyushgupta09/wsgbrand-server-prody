<?php

namespace Fpaipl\Prody\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Fpaipl\Prody\Models\Overhead;
use Fpaipl\Panel\Http\Controllers\PanelController;
use Fpaipl\Prody\Datatables\OverheadDatatable as Datatable;

class OverheadController extends PanelController
{
    public function __construct()
    {
        parent::__construct(new Datatable(), 'Fpaipl\Prody\Models\Overhead' , 'overhead', 'overheads.index');
    }
   
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:overheads',
            'amount' => 'required|numeric|min:0',
            'capacity' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'stage' => 'required|string|in:' . implode(',', array_column(config('prody.overhead_stages'), 'id')),
        ]);

        Overhead::create([
            'name' => Str::title($request->name),
            'amount' => $request->amount,
            'capacity' => $request->capacity,
            'rate' => $request->amount / $request->capacity,
            'description' => $request->description,
            'stage' => $request->stage,
        ]);
        
        return redirect()->route('overheads.index')->with('toast', [
            'class' => 'success',
            'text' => 'Overhead created successfully.'
        ]);
    }

    public function update(Request $request, Overhead $overhead)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:overheads,name,' . $overhead->id,
            'amount' => 'required|numeric|min:0',
            'capacity' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'stage' => 'required|string|in:' . implode(',', array_column(config('prody.overhead_stages'), 'id')),
        ]);

        $overhead->update([
            'name' => Str::title($request->name),
            'amount' => $request->amount,
            'capacity' => $request->capacity,
            'rate' => $request->amount / $request->capacity,
            'description' => $request->description,
            'stage' => $request->stage,
        ]);
        
        return redirect()->route('overheads.index')->with('toast', [
            'class' => 'success',
            'text' => 'Overhead updated successfully.'
        ]);
    }

    public function destroy(Overhead $overhead)
    {
        if ($overhead->productOverheads?->count()) {
            return redirect()->route('overheads.index')->with('toast', [
                'class' => 'danger',
                'text' => 'Overhead cannot be deleted as it is associated with products.'
            ]);
        }

        $overhead->delete();
        
        return redirect()->route('overheads.index')->with('toast', [
            'class' => 'success',
            'text' => 'Overhead deleted successfully.'
        ]);
    }
}
