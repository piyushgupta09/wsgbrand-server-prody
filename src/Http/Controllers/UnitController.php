<?php

namespace Fpaipl\Prody\Http\Controllers;

use Fpaipl\Prody\Models\Unit;
use Illuminate\Http\Request;
use Fpaipl\Panel\Http\Controllers\PanelController;
use Fpaipl\Prody\Datatables\UnitDatatable as Datatable;

class UnitController extends PanelController
{
    public function __construct()
    {
        parent::__construct(
            new Datatable(), 
            'Fpaipl\Prody\Models\Unit' , 
            'unit', 'units.index'
        );
    }
   
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:units,name',
            'abbr' => 'required|unique:units,abbr',
        ]);

        Unit::create([
            'name' => $request->name,
            'names' => $request->names ? $request->names : $request->name,
            'abbr' => $request->abbr,
            'abbrs' => $request->abbrs ? $request->abbrs : $request->abbr,
        ]);

        return redirect()->route('units.index')->with('toast', [
            'class' => 'success',
            'text' => 'Unit created successfully'
        ]); 
    }

    public function update(Request $request, Unit $unit)
    {
        $this->validate($request, [
            'name' => 'required|unique:units,name,' . $unit->id,
            'abbr' => 'required|unique:units,abbr,' . $unit->id,
        ]);

        $unit->name = $request->name;
        $unit->names = $request->names ? $request->names : $request->name;
        $unit->abbr = $request->abbr;
        $unit->abbrs = $request->abbrs ? $request->abbrs : $request->abbr;
        $unit->save();

        return redirect()->route('units.index')->with('toast', [
            'class' => 'success',
            'text' => 'Unit updated successfully.'
        ]);
    }

    public function destroy(Unit $unit)
    {
        $unit->delete();

        return redirect()->route('units.index')->with('toast', [
            'class' => 'success',
            'text' => 'Unit deleted successfully.'
        ]);
    }
}
