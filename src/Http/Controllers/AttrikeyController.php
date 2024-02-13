<?php

namespace Fpaipl\Prody\Http\Controllers;

use Illuminate\Http\Request;
use Fpaipl\Prody\Models\Attrikey;
use Fpaipl\Panel\Http\Controllers\PanelController;
use Fpaipl\Prody\Datatables\AttrikeyDatatable as Datatable;

class AttrikeyController extends PanelController
{
    public function __construct()
    {
        parent::__construct(
            new Datatable(), 
            'Fpaipl\Prody\Models\Attrikey',
            'attrikey',
            'attrikeys.index'
        );
    }

    public function store(Request $request)
    {
        try {
            
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'detail' => 'nullable|string|max:255',
            ]);
    
            Attrikey::create($validated);
    
            return redirect()->route('attrikeys.index')->with('toast', [
                'class' => 'success',
                'text' => 'Attriment key created successfully'
            ]);

        } catch (\Throwable $th) {
            return redirect()->back()->withInput()->with('toast', [
                'class' => 'danger',
                'text' => $th->getMessage()
            ]);
        }
    }

    public function update(Request $request, Attrikey $attrikey)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'detail' => 'nullable|string|max:255',
        ]);

        $attrikey->name = $request->input('name');

        // if name is dirty and it has attrivals, then return not allowed
        if ($attrikey->isDirty('name') && $attrikey->attrivals()->count() > 0) {
            return redirect()->back()->with('toast', [
                'class' => 'danger',
                'text' => 'Attriment key has values, cannot change name'
            ]);
        }

        try {
            
            $attrikey->update($validated);
    
            return redirect()->route('attrikeys.index')->with('toast', [
                'class' => 'success',
                'text' => 'Attriment key updated successfully'
            ]);

        } catch (\Throwable $th) {
            return redirect()->back()->withInput()->with('toast', [
                'class' => 'danger',
                'text' => $th->getMessage()
            ]);
        }
    }

    public function destroy(Attrikey $attrikey)
    {
        if ($attrikey->attrivals()->count() > 0) {
            return redirect()->back()->with('toast', [
                'class' => 'danger',
                'text' => 'Attriment key has values, cannot delete'
            ]);
        }

        try {
            $attrikey->delete();
    
            return redirect()->route('attrikeys.index')->with('toast', [
                'class' => 'success',
                'text' => 'Attriment key deleted successfully'
            ]);

        } catch (\Throwable $th) {
            return redirect()->back()->with('toast', [
                'class' => 'danger',
                'text' => $th->getMessage()
            ]);
        }
    }
}
