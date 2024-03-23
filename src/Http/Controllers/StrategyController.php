<?php

namespace Fpaipl\Prody\Http\Controllers;

use Illuminate\Http\Request;
use Fpaipl\Prody\Models\Strategy;
use Fpaipl\Panel\Http\Controllers\PanelController;
use Fpaipl\Prody\Datatables\StrategyDatatable as Datatable;

class StrategyController extends PanelController
{
    public function __construct()
    {
        parent::__construct(
            new Datatable(), 
            'Fpaipl\Prody\Models\Strategy' , 
            'strategy', 'strategies.index'
        );
    }
   
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:strategies,name',
            'math' => 'required|in:add,less',
            'value' => 'required|numeric',
            'type' => 'required|in:percentage,amount',
            'details' => 'nullable',
        ], [
            'math.in' => 'The math field must be either add or less.',
            'type.in' => 'The type field must be either percentage or amount.',
        ]);

        Strategy::create([
            'name' => $request->name,
            'math' => $request->math,
            'value' => $request->value,
            'type' => $request->type,
            'details' => $request->details,
            'active' => $request->active ? 1 : 0,
        ]);

        return redirect()->route('strategies.index')->with('toast', [
            'class' => 'success',
            'text' => 'Strategy created successfully'
        ]); 
    }

    public function update(Request $request, Strategy $strategy)
    {
        $this->validate($request, [
            'name' => 'required|unique:strategies,name,' . $strategy->id,
            'abbr' => 'required|unique:strategies,abbr,' . $strategy->id,
        ]);

        $strategy->name = $request->name;
        $strategy->names = $request->names ? $request->names : $request->name;
        $strategy->abbr = $request->abbr;
        $strategy->abbrs = $request->abbrs ? $request->abbrs : $request->abbr;
        $strategy->save();

        return redirect()->route('strategies.index')->with('toast', [
            'class' => 'success',
            'text' => 'Strategy updated successfully.'
        ]);
    }

    public function destroy(Strategy $strategy)
    {
        $strategy->delete();

        return redirect()->route('strategies.index')->with('toast', [
            'class' => 'success',
            'text' => 'Strategy deleted successfully.'
        ]);
    }
}
