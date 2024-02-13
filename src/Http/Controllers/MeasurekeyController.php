<?php

namespace Fpaipl\Prody\Http\Controllers;

use Illuminate\Http\Request;
use Fpaipl\Prody\Models\Measurekey;
use Fpaipl\Panel\Http\Controllers\PanelController;
use Fpaipl\Prody\DataTables\MeasurekeyDatatable as Datatable;

class MeasurekeyController extends PanelController
{
    public function __construct()
    {
        parent::__construct(
            new Datatable(), 
            'Fpaipl\Prody\Models\Measurekey',
            'measurekey',
            'measurekeys.index'
        );
    }

    public function store(Request $request)
    {
        try {
            
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'detail' => 'nullable|string|max:255',
                'unit' => 'required|string|max:255',
            ]);
    
            Measurekey::create($validated);
    
            return redirect()->route('measurekeys.index')->with('toast', [
                'class' => 'success',
                'text' => 'Measurement key created successfully'
            ]);

        } catch (\Throwable $th) {
            return redirect()->back()->withInput()->with('toast', [
                'class' => 'danger',
                'text' => $th->getMessage()
            ]);
        }
    }

    public function update(Request $request, Measurekey $measurekey)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'detail' => 'nullable|string|max:255',
            'unit' => 'required|string|max:255',
        ]);

        $measurekey->name = $request->input('name');

        // if name is dirty and it has measurevals, then return not allowed
        if ($measurekey->isDirty('name') && $measurekey->measurevals()->count() > 0) {
            return redirect()->back()->with('toast', [
                'class' => 'danger',
                'text' => 'Measurement key has values, cannot change name'
            ]);
        }

        try {
            
            $measurekey->update($validated);
    
            return redirect()->route('measurekeys.index')->with('toast', [
                'class' => 'success',
                'text' => 'Measurement key updated successfully'
            ]);

        } catch (\Throwable $th) {
            return redirect()->back()->withInput()->with('toast', [
                'class' => 'danger',
                'text' => $th->getMessage()
            ]);
        }
    }

    public function destroy(Measurekey $measurekey)
    {
        if ($measurekey->measurevals()->count() > 0) {
            return redirect()->back()->with('toast', [
                'class' => 'danger',
                'text' => 'Measurement key has values, cannot delete'
            ]);
        }

        try {
            $measurekey->delete();
    
            return redirect()->route('measurekeys.index')->with('toast', [
                'class' => 'success',
                'text' => 'Measurement key deleted successfully'
            ]);

        } catch (\Throwable $th) {
            return redirect()->back()->with('toast', [
                'class' => 'danger',
                'text' => $th->getMessage()
            ]);
        }
    }
}
