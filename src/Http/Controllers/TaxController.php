<?php

namespace Fpaipl\Prody\Http\Controllers;

use Fpaipl\Prody\Models\Tax;
use Illuminate\Http\Request;
use Fpaipl\Panel\Http\Controllers\PanelController;
use Fpaipl\Prody\DataTables\TaxDatatable as Datatable;

class TaxController extends PanelController
{
    public function __construct()
    {
        parent::__construct(
            new Datatable(), 
            'Fpaipl\Prody\Models\Tax' , 
            'tax', 'taxes.index'
        );
    }
   
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'nullable|string|max:50',
            'hsncode' => 'required|string|min:4|max:12|unique:taxes,hsncode',
            'gstrate' => 'required|numeric|min:1|max:36',
        ]);
        
        $autoName = $request->input('hsncode') . ' - GST ' . $request->input('gstrate') . '%';
        if ($request->has('name')) {
            $autoName = $request->name . ' - ' . $autoName;
        }

        Tax::create([
            'name' => $autoName,
            'hsncode' => $request->input('hsncode'),
            'gstrate' => $request->input('gstrate'),
        ]);
        
        return redirect()->route('taxes.index')->with('toast', [
            'class' => 'success',
            'text' => 'Tax created successfully.'
        ]);
    }

    public function update(Request $request, Tax $tax)
    {
        $request->validate([
            'name' => 'nullable|string|max:50',
            'hsncode' => 'required|string|min:4|max:12|unique:taxes,hsncode,' . $tax->id,
            'gstrate' => 'required|numeric|min:1|max:36',
        ]);

        $tax->name = $request->input('hsncode') . ' - GST ' . $request->input('gstrate') . '%';
        $tax->hsncode = $request->input('hsncode');
        $tax->gstrate = $request->input('gstrate');
        $tax->save();

        return redirect()->route('taxes.index')->with('toast', [
            'class' => 'success',
            'text' => 'Tax updated successfully.'
        ]);
    }

    public function destroy(Tax $tax)
    {
        $tax->delete();

        return redirect()->route('taxes.index')->with('toast', [
            'class' => 'success',
            'text' => 'Tax deleted successfully.'
        ]);
    }
}
